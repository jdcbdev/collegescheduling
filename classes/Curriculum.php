<?php

require_once __DIR__ . '/Database.php';

class Curriculum extends Database {

    public $id;
    public $program_id;
    public $effective_start_year;
    public $effective_end_year;

    function addCurriculum() {
        $conn = $this->connect();

        $sql = "INSERT INTO curriculum 
                (program_id, effective_start_year, effective_end_year)
                VALUES (:program_id, :effective_start_year, :effective_end_year)";

        $query = $conn->prepare($sql);
        $query->bindParam(':program_id', $this->program_id);
        $query->bindParam(':effective_start_year', $this->effective_start_year);
        $query->bindParam(':effective_end_year', $this->effective_end_year);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getCurriculumById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.program_id,
                    c.effective_start_year,
                    c.effective_end_year,
                    p.program_code,
                    p.program_name,
                    c.created_at
                FROM curriculum c
                LEFT JOIN programs p ON c.program_id = p.id
                WHERE c.id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCurriculum() {

        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.program_id,
                    c.effective_start_year,
                    c.effective_end_year,
                    p.program_code,
                    p.program_name,
                    c.created_at
                FROM curriculum c
                LEFT JOIN programs p ON c.program_id = p.id
                ORDER BY p.program_name ASC, c.effective_start_year DESC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchCurriculum($searchQuery) {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.program_id,
                    c.effective_start_year,
                    c.effective_end_year,
                    p.program_code,
                    p.program_name,
                    c.created_at
                FROM curriculum c
                LEFT JOIN programs p ON c.program_id = p.id
                WHERE p.program_code LIKE :query
                OR p.program_name LIKE :query
                ORDER BY p.program_name ASC LIMIT 20";

        $query = $conn->prepare($sql);
        $searchParam = '%' . $searchQuery . '%';
        $query->bindParam(':query', $searchParam, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function curriculumExists($program_id, $start_year, $end_year, $excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count FROM curriculum
                WHERE program_id = :program_id
                AND effective_start_year = :start_year
                AND effective_end_year = :end_year";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':program_id', $program_id, PDO::PARAM_INT);
        $query->bindParam(':start_year', $start_year, PDO::PARAM_INT);
        $query->bindParam(':end_year', $end_year, PDO::PARAM_INT);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) && $result['count'] > 0;
    }

    public function updateCurriculum() {
        $conn = $this->connect();

        $sql = "UPDATE curriculum SET 
                program_id = :program_id,
                effective_start_year = :effective_start_year,
                effective_end_year = :effective_end_year
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':program_id', $this->program_id);
        $query->bindParam(':effective_start_year', $this->effective_start_year);
        $query->bindParam(':effective_end_year', $this->effective_end_year);

        return $query->execute();
    }

    public function deleteCurriculum($id) {
        $conn = $this->connect();

        // Check if curriculum has subjects
        $query = $conn->prepare("SELECT COUNT(*) as count FROM curriculum_subjects WHERE curriculum_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $subjectCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($subjectCount > 0) {
            return false;
        }

        $sql = "DELETE FROM curriculum WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getPrograms() {
        $conn = $this->connect();

        $sql = "SELECT id, program_code, program_name FROM programs ORDER BY program_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectsByCurriculum($curriculum_id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    subject_code,
                    subject_name,
                    credit_hours,
                    year_level,
                    semester,
                    created_at
                FROM subjects
                WHERE curriculum_id = :curriculum_id
                ORDER BY year_level ASC, semester ASC";

        $query = $conn->prepare($sql);
        $query->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSubject($curriculum_id, $subject_code, $subject_name, $credit_hours, $year_level, $semester) {
        $conn = $this->connect();

        $sql = "INSERT INTO subjects 
                (subject_code, subject_name, credit_hours, curriculum_id, year_level, semester)
                VALUES (:subject_code, :subject_name, :credit_hours, :curriculum_id, :year_level, :semester)";

        $query = $conn->prepare($sql);
        $query->bindParam(':subject_code', $subject_code);
        $query->bindParam(':subject_name', $subject_name);
        $query->bindParam(':credit_hours', $credit_hours, PDO::PARAM_INT);
        $query->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
        $query->bindParam(':year_level', $year_level, PDO::PARAM_INT);
        $query->bindParam(':semester', $semester, PDO::PARAM_INT);

        return $query->execute();
    }

    public function updateSubject($subject_id, $subject_code, $subject_name, $credit_hours, $year_level, $semester) {
        $conn = $this->connect();

        $sql = "UPDATE subjects SET 
                subject_code = :subject_code,
                subject_name = :subject_name,
                credit_hours = :credit_hours,
                year_level = :year_level,
                semester = :semester
                WHERE id = :subject_id";

        $query = $conn->prepare($sql);
        $query->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $query->bindParam(':subject_code', $subject_code);
        $query->bindParam(':subject_name', $subject_name);
        $query->bindParam(':credit_hours', $credit_hours, PDO::PARAM_INT);
        $query->bindParam(':year_level', $year_level, PDO::PARAM_INT);
        $query->bindParam(':semester', $semester, PDO::PARAM_INT);

        return $query->execute();
    }

    public function deleteSubject($subject_id) {
        $conn = $this->connect();

        $sql = "DELETE FROM subjects WHERE id = :subject_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);

        return $query->execute();
    }
}
