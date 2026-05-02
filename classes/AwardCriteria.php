<?php
require_once __DIR__ . '/Database.php';

class AwardCriteria {

    private function connect() {
        $db = new Database();
        return $db->connect();
    }

    public function getAllCriteria() {
        $conn = $this->connect();

        $sql = "SELECT ac.id, ac.title, ac.schoolyear_id, ac.excluded_subjects, ac.created_at,
                       CONCAT(sy.start_year, '-', sy.end_year, ' ',
                           CASE sy.semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', sy.semester) END
                       ) AS school_year_label
                FROM awards_criteria ac
                LEFT JOIN schoolyear sy ON ac.schoolyear_id = sy.id
                ORDER BY ac.id DESC";

        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCriteriaById($id) {
        $conn = $this->connect();

        $sql = "SELECT id, title, schoolyear_id, excluded_subjects, created_at
                FROM awards_criteria
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addCriteria($title, $schoolyearId, $excludedSubjects) {
        $conn = $this->connect();

        $sql = "INSERT INTO awards_criteria (title, schoolyear_id, excluded_subjects)
                VALUES (:title, :schoolyear_id, :excluded_subjects)";

        $query = $conn->prepare($sql);
        $query->bindValue(':title', trim($title));
        $query->bindValue(':schoolyear_id', (int) $schoolyearId, PDO::PARAM_INT);
        $query->bindValue(':excluded_subjects', trim($excludedSubjects));

        return $query->execute();
    }

    public function updateCriteria($id, $title, $schoolyearId, $excludedSubjects) {
        $conn = $this->connect();

        $sql = "UPDATE awards_criteria
                SET title = :title,
                    schoolyear_id = :schoolyear_id,
                    excluded_subjects = :excluded_subjects
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $query->bindValue(':title', trim($title));
        $query->bindValue(':schoolyear_id', (int) $schoolyearId, PDO::PARAM_INT);
        $query->bindValue(':excluded_subjects', trim($excludedSubjects));

        return $query->execute();
    }

    public function deleteCriteria($id) {
        $conn = $this->connect();

        $sql = "DELETE FROM awards_criteria WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int) $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getCriteriaBySchoolYear($schoolyearId) {
        $conn = $this->connect();

        $sql = "SELECT id, title FROM awards_criteria WHERE schoolyear_id = :schoolyear_id ORDER BY title ASC";
        $query = $conn->prepare($sql);
        $query->bindValue(':schoolyear_id', (int) $schoolyearId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchoolYears() {
        $conn = $this->connect();

        $sql = "SELECT id, start_year, end_year, semester, is_active,
                       CONCAT(start_year, '-', end_year, ' ',
                           CASE semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', semester) END
                       ) AS label
                FROM schoolyear
                ORDER BY start_year DESC, end_year DESC, semester ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
