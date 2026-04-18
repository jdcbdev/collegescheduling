<?php

require_once __DIR__ . '/Database.php';

class SchoolClass extends Database {

    public $id;
    public $schoolyear_id;
    public $curriculum_id;
    public $section_name;
    public $year_level;

    function addSection() {
        $conn = $this->connect();

        $sql = "INSERT INTO class 
                (schoolyear_id, curriculum_id, section_name, year_level)
                VALUES (:schoolyear_id, :curriculum_id, :section_name, :year_level)";

        $query = $conn->prepare($sql);
        $query->bindParam(':schoolyear_id', $this->schoolyear_id);
        $query->bindParam(':curriculum_id', $this->curriculum_id);
        $query->bindParam(':section_name', $this->section_name);
        $query->bindParam(':year_level', $this->year_level);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getSectionById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.schoolyear_id,
                    c.curriculum_id,
                    c.section_name,
                    c.year_level,
                    c.created_at,
                    cur.program_id,
                    p.program_code,
                    p.program_name
                FROM class c
                LEFT JOIN curriculum cur ON c.curriculum_id = cur.id
                LEFT JOIN programs p ON cur.program_id = p.id
                WHERE c.id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllSections() {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.schoolyear_id,
                    c.curriculum_id,
                    c.section_name,
                    c.year_level,
                    c.created_at,
                    sy.start_year,
                    sy.end_year,
                    sy.semester,
                    cur.program_id,
                    p.program_code,
                    p.program_name
                FROM class c
                LEFT JOIN schoolyear sy ON c.schoolyear_id = sy.id
                LEFT JOIN curriculum cur ON c.curriculum_id = cur.id
                LEFT JOIN programs p ON cur.program_id = p.id
                ORDER BY p.program_code, c.year_level, c.section_name";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSectionsBySchoolYear($schoolyear_id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.schoolyear_id,
                    c.curriculum_id,
                    c.section_name,
                    c.year_level,
                    c.created_at,
                    sy.start_year,
                    sy.end_year,
                    sy.semester,
                    cur.program_id,
                    p.program_code,
                    p.program_name
                FROM class c
                LEFT JOIN schoolyear sy ON c.schoolyear_id = sy.id
                LEFT JOIN curriculum cur ON c.curriculum_id = cur.id
                LEFT JOIN programs p ON cur.program_id = p.id
                WHERE c.schoolyear_id = :schoolyear_id
                ORDER BY p.program_code, c.year_level, c.section_name";

        $query = $conn->prepare($sql);
        $query->bindParam(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSectionsByCurriculum($curriculum_id, $schoolyear_id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    c.id,
                    c.schoolyear_id,
                    c.curriculum_id,
                    c.section_name,
                    c.year_level,
                    c.created_at,
                    p.program_code,
                    p.program_name
                FROM class c
                LEFT JOIN curriculum cur ON c.curriculum_id = cur.id
                LEFT JOIN programs p ON cur.program_id = p.id
                WHERE c.curriculum_id = :curriculum_id 
                AND c.schoolyear_id = :schoolyear_id
                ORDER BY c.year_level, c.section_name";

        $query = $conn->prepare($sql);
        $query->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
        $query->bindParam(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSectionsPerYearLevel($curriculum_id, $schoolyear_id, $year_level) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count
                FROM class c
                WHERE c.curriculum_id = :curriculum_id 
                AND c.schoolyear_id = :schoolyear_id
                AND c.year_level = :year_level";

        $query = $conn->prepare($sql);
        $query->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
        $query->bindParam(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $query->bindParam(':year_level', $year_level, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function generateSectionName($program_code, $year_level, $section_letter) {
        return $program_code . $year_level . $section_letter;
    }

    public function deleteSection($id) {
        $conn = $this->connect();

        $sql = "DELETE FROM class WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getCurriculaWithYearLevels($schoolyear_id) {
        $conn = $this->connect();

        $sql = "SELECT DISTINCT
                    cur.id as curriculum_id,
                    cur.program_id,
                    p.program_code,
                    p.program_name,
                    s.year_level
                FROM subjects s
                JOIN curriculum cur ON s.curriculum_id = cur.id
                JOIN programs p ON cur.program_id = p.id
                ORDER BY p.program_name, s.year_level";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
