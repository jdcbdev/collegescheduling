<?php

require_once __DIR__ . '/Database.php';

class Applicant extends Database {

    public $id;
    public $student_no;
    public $fn;
    public $mn;
    public $ln;
    public $program_id;
    public $curriculum_id;
    public $gwa;
    public $schoolyear_id;
    public $criteria_id;

    public function addApplicant() {
        $conn = $this->connect();

        $sql = "INSERT INTO applicants (student_no, fn, mn, ln, program_id, curriculum_id, gwa, schoolyear_id, criteria_id)
                VALUES (:student_no, :fn, :mn, :ln, :program_id, :curriculum_id, :gwa, :schoolyear_id, :criteria_id)";

        $query = $conn->prepare($sql);
        $query->bindParam(':student_no',    $this->student_no);
        $query->bindParam(':fn',            $this->fn);
        $query->bindParam(':mn',            $this->mn);
        $query->bindParam(':ln',            $this->ln);
        $query->bindParam(':program_id',    $this->program_id);
        $query->bindParam(':curriculum_id', $this->curriculum_id);
        $query->bindParam(':gwa',           $this->gwa);
        $query->bindParam(':schoolyear_id', $this->schoolyear_id);
        $query->bindParam(':criteria_id',   $this->criteria_id);

        if ($query->execute()) {
            return $conn->lastInsertId();
        }
        return false;
    }

    public function updateApplicant() {
        $conn = $this->connect();

        $sql = "UPDATE applicants
                SET student_no    = :student_no,
                    fn            = :fn,
                    mn            = :mn,
                    ln            = :ln,
                    program_id    = :program_id,
                    curriculum_id = :curriculum_id,
                    gwa           = :gwa,
                    schoolyear_id = :schoolyear_id,
                    criteria_id   = :criteria_id
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id',            $this->id,            PDO::PARAM_INT);
        $query->bindParam(':student_no',    $this->student_no);
        $query->bindParam(':fn',            $this->fn);
        $query->bindParam(':mn',            $this->mn);
        $query->bindParam(':ln',            $this->ln);
        $query->bindParam(':program_id',    $this->program_id);
        $query->bindParam(':curriculum_id', $this->curriculum_id);
        $query->bindParam(':gwa',           $this->gwa);
        $query->bindParam(':schoolyear_id', $this->schoolyear_id);
        $query->bindParam(':criteria_id',   $this->criteria_id);

        return $query->execute();
    }

    public function deleteApplicant($id) {
        $conn = $this->connect();

        $sql   = "DELETE FROM applicants WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getApplicantById($id) {
        $conn = $this->connect();

        $sql = "SELECT a.id, a.student_no, a.fn, a.mn, a.ln,
                       a.program_id, p.program_code, p.program_name,
                       a.curriculum_id, CONCAT(c.effective_start_year, '-', c.effective_end_year) AS curriculum_years,
                       a.gwa,
                       a.schoolyear_id,
                       CONCAT(sy.start_year, '-', sy.end_year, ' ',
                           CASE sy.semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', sy.semester) END
                       ) AS school_year_label,
                       a.criteria_id, ac.title AS criteria_title, ac.gwa_cutoff,
                       a.created_at
                FROM applicants a
                LEFT JOIN programs       p  ON a.program_id    = p.id
                LEFT JOIN curriculum     c  ON a.curriculum_id = c.id
                LEFT JOIN schoolyear     sy ON a.schoolyear_id = sy.id
                LEFT JOIN awards_criteria ac ON a.criteria_id  = ac.id
                WHERE a.id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllApplicants($programId = null, $schoolyearId = null, $criteriaId = null) {
        $conn = $this->connect();

        $sql = "SELECT a.id, a.student_no, a.fn, a.mn, a.ln,
                       a.program_id, p.program_code, p.program_name,
                       a.curriculum_id, CONCAT(c.effective_start_year, '-', c.effective_end_year) AS curriculum_years,
                       a.gwa,
                       a.schoolyear_id,
                       CONCAT(sy.start_year, '-', sy.end_year, ' ',
                           CASE sy.semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', sy.semester) END
                       ) AS school_year_label,
                       a.criteria_id, ac.title AS criteria_title, ac.gwa_cutoff,
                       a.created_at
                FROM applicants a
                LEFT JOIN programs       p  ON a.program_id    = p.id
                LEFT JOIN curriculum     c  ON a.curriculum_id = c.id
                LEFT JOIN schoolyear     sy ON a.schoolyear_id = sy.id
                LEFT JOIN awards_criteria ac ON a.criteria_id  = ac.id";

        $conditions = [];
        $params = [];
        if ($programId) {
            $conditions[] = "a.program_id = :program_id";
            $params[':program_id'] = (int) $programId;
        }
        if ($schoolyearId) {
            $conditions[] = "a.schoolyear_id = :schoolyear_id";
            $params[':schoolyear_id'] = (int) $schoolyearId;
        }
        if ($criteriaId) {
            $conditions[] = "a.criteria_id = :criteria_id";
            $params[':criteria_id'] = (int) $criteriaId;
        }
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY CASE WHEN a.gwa IS NULL THEN 1 ELSE 0 END ASC, a.gwa ASC, a.ln ASC, a.fn ASC";

        $query = $conn->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue($key, $value);
        }
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function studentNoExists($studentNo, $excludeId = null, $criteriaId = null) {
        $conn = $this->connect();

        // Uniqueness is scoped per criteria; if no criteria, no duplicate check
        if (!$criteriaId) {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM applicants WHERE student_no = :student_no AND criteria_id = :criteria_id";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':student_no', $studentNo);
        $query->bindValue(':criteria_id', (int) $criteriaId, PDO::PARAM_INT);
        if ($excludeId) {
            $query->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        return (int) $query->fetchColumn() > 0;
    }

    public function getPrograms() {
        $conn = $this->connect();

        $sql   = "SELECT id, program_code, program_name FROM programs ORDER BY program_name ASC";
        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurriculaByProgram($programId) {
        $conn = $this->connect();

        $sql = "SELECT id, CONCAT(effective_start_year, '-', effective_end_year) AS curriculum_years
                FROM curriculum WHERE program_id = :program_id
                ORDER BY effective_start_year ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':program_id', $programId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveSchoolYear() {
        $conn = $this->connect();

        $sql   = "SELECT id FROM schoolyear WHERE is_active = 1 LIMIT 1";
        $query = $conn->prepare($sql);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function getSchoolYears() {
        $conn = $this->connect();

        $sql = "SELECT id, start_year, end_year, semester, is_active,
                       CONCAT(start_year, '-', end_year, ' Sem ', semester) AS label
                FROM schoolyear
                ORDER BY start_year DESC, end_year DESC, semester ASC";
        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
