<?php

require_once __DIR__ . '/database.php';

class SchoolYear extends Database {

    public $id;
    public $start_year;
    public $end_year;
    public $semester;
    public $is_active;

    function addSchoolYear() {
        $conn = $this->connect();

        $sql = "INSERT INTO schoolyear 
                (start_year, end_year, semester, is_active)
                VALUES (:start_year, :end_year, :semester, :active)";

        $query = $conn->prepare($sql);
        $query->bindParam(':start_year', $this->start_year);
        $query->bindParam(':end_year', $this->end_year);
        $query->bindParam(':semester', $this->semester);
        $query->bindParam(':active', $this->is_active);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getSchoolYearById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    start_year,
                    end_year,
                    semester,
                    is_active,
                    created_at
                FROM schoolyear
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllSchoolYears() {

        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    start_year,
                    end_year,
                    semester,
                    is_active,
                    created_at
                FROM schoolyear
                ORDER BY start_year DESC, semester DESC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveSchoolYear() {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    start_year,
                    end_year,
                    semester,
                    is_active,
                    created_at
                FROM schoolyear
                WHERE is_active = TRUE
                LIMIT 1";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function searchSchoolYear($searchQuery) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    start_year,
                    end_year,
                    semester,
                    is_active,
                    created_at
                FROM schoolyear
                WHERE CONCAT(start_year, '-', end_year) LIKE :query
                OR semester LIKE :query
                ORDER BY start_year DESC LIMIT 20";

        $query = $conn->prepare($sql);
        $searchParam = '%' . $searchQuery . '%';
        $query->bindParam(':query', $searchParam, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function schoolYearExists($start_year, $end_year, $semester, $excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count FROM schoolyear
                WHERE start_year = :start_year
                AND end_year = :end_year
                AND semester = :semester";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':start_year', $start_year, PDO::PARAM_INT);
        $query->bindParam(':end_year', $end_year, PDO::PARAM_INT);
        $query->bindParam(':semester', $semester, PDO::PARAM_INT);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) && $result['count'] > 0;
    }

    public function updateSchoolYear() {
        $conn = $this->connect();

        $sql = "UPDATE schoolyear SET 
                start_year = :start_year,
                end_year = :end_year,
                semester = :semester,
                is_active = :active
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':start_year', $this->start_year);
        $query->bindParam(':end_year', $this->end_year);
        $query->bindParam(':semester', $this->semester);
        $query->bindParam(':active', $this->is_active);

        return $query->execute();
    }

    public function setActiveSchoolYear($id) {
        $conn = $this->connect();

        $deactivateSql = "UPDATE schoolyear SET is_active = FALSE";
        $deactivateQuery = $conn->prepare($deactivateSql);
        $deactivateQuery->execute();

        $sql = "UPDATE schoolyear SET is_active = TRUE WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function toggleSchoolYearActive($id, $active) {
        $conn = $this->connect();

        if ($active) {
            $deactivateSql = "UPDATE schoolyear SET is_active = FALSE";
            $deactivateQuery = $conn->prepare($deactivateSql);
            $deactivateQuery->execute();
        }

        $sql = "UPDATE schoolyear SET is_active = :active WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':active', $active, PDO::PARAM_BOOL);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function deleteSchoolYear($id) {
        $conn = $this->connect();

        $sql = "DELETE FROM schoolyear WHERE id = :id AND is_active = FALSE";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

}
?>
