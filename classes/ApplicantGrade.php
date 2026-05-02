<?php
require_once __DIR__ . '/Database.php';

class ApplicantGrade {

    private function connect() {
        $db = new Database();
        return $db->connect();
    }

    private function normalizeGrade($grade) {
        $value = strtoupper(trim((string) $grade));
        if ($value === '') {
            return null;
        }
        if ($value === 'INC') {
            return 'INC';
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Invalid grade format. Use 1.00-3.00 (0.25 interval), 5.00, or INC.');
        }

        $num = (float) $value;
        $scaled = (int) round($num * 100);

        $isPassedRange = ($scaled >= 100 && $scaled <= 300 && $scaled % 25 === 0);
        $isFailed = ($scaled === 500);

        if (!$isPassedRange && !$isFailed) {
            throw new InvalidArgumentException('Invalid grade value. Allowed: 1.00-3.00 (0.25 interval), 5.00, or INC.');
        }

        return number_format($num, 2, '.', '');
    }

    private function detectRemarks($normalizedGrade) {
        if ($normalizedGrade === null) {
            return null;
        }
        if ($normalizedGrade === 'INC') {
            return 'INC';
        }

        $num = (float) $normalizedGrade;
        if ($num >= 1.00 && $num <= 3.00) {
            return 'PASSED';
        }
        if ($num == 5.00) {
            return 'FAILED';
        }

        return null;
    }

    /**
     * Returns all subjects for the curriculum, with the applicant's grade/remarks joined in.
     */
    public function getSubjectsWithGrades($applicantId, $curriculumId) {
        $conn = $this->connect();

        $sql = "SELECT s.id AS subject_id, s.subject_code, s.subject_name,
                       s.lec_credits, s.lab_credits, s.total_credits,
                       s.year_level, s.semester,
                       ag.id AS grade_id, ag.grade, ag.remarks
                FROM subjects s
                LEFT JOIN applicant_grades ag
                       ON ag.subject_id = s.id AND ag.applicant_id = :applicant_id
                WHERE s.curriculum_id = :curriculum_id
                ORDER BY s.year_level ASC, s.semester ASC";

        $query = $conn->prepare($sql);
        $query->bindValue(':applicant_id',  (int) $applicantId,  PDO::PARAM_INT);
        $query->bindValue(':curriculum_id', (int) $curriculumId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Upserts a single grade row.
     */
    public function saveGrade($applicantId, $subjectId, $grade, $remarks) {
        $conn = $this->connect();

        $normalizedGrade = $this->normalizeGrade($grade);
        $autoRemarks = $this->detectRemarks($normalizedGrade);

        $sql = "INSERT INTO applicant_grades (applicant_id, subject_id, grade, remarks)
                VALUES (:applicant_id, :subject_id, :grade, :remarks)
                ON DUPLICATE KEY UPDATE grade = VALUES(grade), remarks = VALUES(remarks)";

        $query = $conn->prepare($sql);
        $query->bindValue(':applicant_id', (int) $applicantId, PDO::PARAM_INT);
        $query->bindValue(':subject_id',   (int) $subjectId,   PDO::PARAM_INT);
        $query->bindValue(':grade',   $normalizedGrade);
        $query->bindValue(':remarks', $autoRemarks);

        return $query->execute();
    }

    /**
     * Computes weighted GWA from all graded subjects and updates applicants.gwa.
     * Returns the computed GWA or null if no grades exist.
     */
    public function computeAndSaveGWA($applicantId) {
        $conn = $this->connect();

        $sql = "SELECT ag.grade, s.total_credits
                FROM applicant_grades ag
                JOIN subjects s ON ag.subject_id = s.id
                WHERE ag.applicant_id = :applicant_id AND ag.grade IS NOT NULL";

        $query = $conn->prepare($sql);
        $query->bindValue(':applicant_id', (int) $applicantId, PDO::PARAM_INT);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $conn->prepare("UPDATE applicants SET gwa = NULL WHERE id = :id")
                 ->execute([':id' => (int) $applicantId]);
            return null;
        }

        $totalWeighted = 0;
        $totalUnits    = 0;
        foreach ($rows as $row) {
            $gradeRaw = strtoupper(trim((string) $row['grade']));
            if (!is_numeric($gradeRaw)) {
                continue;
            }
            $grade = (float) $gradeRaw;
            $scaled = (int) round($grade * 100);
            $isAllowedNumeric = (($scaled >= 100 && $scaled <= 300 && $scaled % 25 === 0) || $scaled === 500);
            if (!$isAllowedNumeric) {
                continue;
            }

            $totalWeighted += $grade * (int) $row['total_credits'];
            $totalUnits    += (int) $row['total_credits'];
        }

        $gwa = $totalUnits > 0 ? round($totalWeighted / $totalUnits, 4) : null;

        $update = $conn->prepare("UPDATE applicants SET gwa = :gwa WHERE id = :id");
        $update->bindValue(':gwa', $gwa);
        $update->bindValue(':id',  (int) $applicantId, PDO::PARAM_INT);
        $update->execute();

        return $gwa;
    }
}
