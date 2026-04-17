<?php

require_once __DIR__ . '/Database.php';

class Department extends Database {

    public $id;
    public $department_code;
    public $department_name;

    function addDepartment() {
        $conn = $this->connect();

        $sql = "INSERT INTO departments 
                (department_code, department_name)
                VALUES (:department_code, :department_name)";

        $query = $conn->prepare($sql);
        $query->bindParam(':department_code', $this->department_code);
        $query->bindParam(':department_name', $this->department_name);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getDepartmentById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    department_code,
                    department_name,
                    created_at
                FROM departments
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllDepartments() {

        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    department_code,
                    department_name,
                    created_at
                FROM departments
                ORDER BY department_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchDepartment($searchQuery) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    department_code,
                    department_name,
                    created_at
                FROM departments
                WHERE department_code LIKE :query
                OR department_name LIKE :query
                ORDER BY department_name ASC LIMIT 20";

        $query = $conn->prepare($sql);
        $searchParam = '%' . $searchQuery . '%';
        $query->bindParam(':query', $searchParam, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function departmentExists($department_code, $excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count FROM departments
                WHERE department_code = :department_code";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':department_code', $department_code, PDO::PARAM_STR);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) && $result['count'] > 0;
    }

    public function updateDepartment() {
        $conn = $this->connect();

        $sql = "UPDATE departments SET 
                department_code = :department_code,
                department_name = :department_name
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':department_code', $this->department_code);
        $query->bindParam(':department_name', $this->department_name);

        return $query->execute();
    }

    public function deleteDepartment($id) {
        $conn = $this->connect();

        // Check if department has associated records
        $sql = "SELECT COUNT(*) as count FROM instructors WHERE department_id = :id
                UNION ALL
                SELECT COUNT(*) as count FROM subjects WHERE department_id = :id
                UNION ALL
                SELECT COUNT(*) as count FROM programs WHERE department_id = :id";

        $query = $conn->prepare("SELECT COUNT(*) as count FROM instructors WHERE department_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $instructorCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        $query = $conn->prepare("SELECT COUNT(*) as count FROM subjects WHERE department_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $subjectCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        $query = $conn->prepare("SELECT COUNT(*) as count FROM programs WHERE department_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $programCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($instructorCount > 0 || $subjectCount > 0 || $programCount > 0) {
            return false;
        }

        $sql = "DELETE FROM departments WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }
}
