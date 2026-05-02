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
     * Returns subject IDs that are excluded from GWA for the given criteria.
     * excluded_subjects is stored as comma-separated subject codes.
     */
    private function getExcludedSubjectIds($criteriaId, $curriculumId) {
        if (!$criteriaId || !$curriculumId) return [];
        $conn = $this->connect();

        $sql = "SELECT excluded_subjects FROM awards_criteria WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int) $criteriaId, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['excluded_subjects'])) return [];

        $codes = array_values(array_filter(array_map('trim', explode(',', $row['excluded_subjects']))));
        if (empty($codes)) return [];

        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $sql2 = "SELECT id FROM subjects WHERE curriculum_id = ? AND subject_code IN ($placeholders)";
        $query2 = $conn->prepare($sql2);
        $query2->execute(array_merge([(int) $curriculumId], $codes));

        return array_column($query2->fetchAll(PDO::FETCH_ASSOC), 'id');
    }

    /**
     * Returns all subjects for the curriculum with grades joined in.
     * Grades are shared from sibling applicants (same student_no + curriculum_id) when not yet entered.
     * Subjects excluded by criteria are flagged with excluded_from_gwa = 1.
     */
    public function getSubjectsWithGrades($applicantId, $curriculumId, $criteriaId = null) {
        $conn = $this->connect();

        $sql = "SELECT s.id AS subject_id, s.subject_code, s.subject_name,
                       s.lec_credits, s.lab_credits, s.total_credits,
                       s.year_level, s.semester,
                       ag.id AS grade_id,
                       COALESCE(ag.grade,
                           (SELECT ag2.grade FROM applicant_grades ag2
                            JOIN applicants a2    ON ag2.applicant_id = a2.id
                            JOIN applicants a_ref ON a_ref.id = :applicant_id2
                            WHERE a2.student_no    = a_ref.student_no
                              AND a2.curriculum_id = a_ref.curriculum_id
                              AND a2.id != :applicant_id3
                              AND ag2.subject_id = s.id
                              AND ag2.grade IS NOT NULL
                            ORDER BY ag2.id DESC
                            LIMIT 1)
                       ) AS grade,
                       ag.remarks
                FROM subjects s
                LEFT JOIN applicant_grades ag
                       ON ag.subject_id = s.id AND ag.applicant_id = :applicant_id
                WHERE s.curriculum_id = :curriculum_id
                ORDER BY s.year_level ASC, s.semester ASC";

        $query = $conn->prepare($sql);
        $query->bindValue(':applicant_id',  (int) $applicantId,  PDO::PARAM_INT);
        $query->bindValue(':applicant_id2', (int) $applicantId,  PDO::PARAM_INT);
        $query->bindValue(':applicant_id3', (int) $applicantId,  PDO::PARAM_INT);
        $query->bindValue(':curriculum_id', (int) $curriculumId, PDO::PARAM_INT);
        $query->execute();

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $excludedIds = $this->getExcludedSubjectIds($criteriaId, $curriculumId);
        foreach ($rows as &$row) {
            $row['excluded_from_gwa'] = in_array((int) $row['subject_id'], $excludedIds) ? 1 : 0;
        }
        unset($row);

        return $rows;
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
     * Subjects excluded by the criteria are skipped.
     * Returns the computed GWA or null if no grades exist.
     */
    public function computeAndSaveGWA($applicantId, $criteriaId = null) {
        $conn = $this->connect();

        // Get curriculum_id to resolve excluded subject codes
        $appRow = $conn->prepare("SELECT curriculum_id FROM applicants WHERE id = :id");
        $appRow->bindValue(':id', (int) $applicantId, PDO::PARAM_INT);
        $appRow->execute();
        $appData = $appRow->fetch(PDO::FETCH_ASSOC);
        $curriculumId = $appData ? (int) $appData['curriculum_id'] : 0;

        $excludedIds = $this->getExcludedSubjectIds($criteriaId, $curriculumId);

        $sql = "SELECT ag.grade, s.total_credits, s.id AS subject_id
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
            if (in_array((int) $row['subject_id'], $excludedIds)) continue;

            $gradeRaw = strtoupper(trim((string) $row['grade']));
            if (!is_numeric($gradeRaw)) continue;
            $grade = (float) $gradeRaw;
            $scaled = (int) round($grade * 100);
            $isAllowedNumeric = (($scaled >= 100 && $scaled <= 300 && $scaled % 25 === 0) || $scaled === 500);
            if (!$isAllowedNumeric) continue;

            $totalWeighted += $grade * (int) $row['total_credits'];
            $totalUnits    += (int) $row['total_credits'];
        }

        $gwa = $totalUnits > 0 ? round($totalWeighted / $totalUnits, 5) : null;

        $update = $conn->prepare("UPDATE applicants SET gwa = :gwa WHERE id = :id");
        $update->bindValue(':gwa', $gwa);
        $update->bindValue(':id',  (int) $applicantId, PDO::PARAM_INT);
        $update->execute();

        return $gwa;
    }
}
