<?php

require_once __DIR__ . '/Database.php';

class Program extends Database {

    public $id;
    public $program_code;
    public $program_name;
    public $department_id;

    function addProgram() {
        $conn = $this->connect();

        $sql = "INSERT INTO programs 
                (program_code, program_name, department_id)
                VALUES (:program_code, :program_name, :department_id)";

        $query = $conn->prepare($sql);
        $query->bindParam(':program_code', $this->program_code);
        $query->bindParam(':program_name', $this->program_name);
        $query->bindParam(':department_id', $this->department_id);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getProgramById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    p.id,
                    p.program_code,
                    p.program_name,
                    p.department_id,
                    d.department_name,
                    p.created_at
                FROM programs p
                LEFT JOIN departments d ON p.department_id = d.id
                WHERE p.id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPrograms() {

        $conn = $this->connect();

        $sql = "SELECT 
                    p.id,
                    p.program_code,
                    p.program_name,
                    p.department_id,
                    d.department_name,
                    p.created_at
                FROM programs p
                LEFT JOIN departments d ON p.department_id = d.id
                ORDER BY p.program_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProgram($searchQuery) {
        $conn = $this->connect();

        $sql = "SELECT 
                    p.id,
                    p.program_code,
                    p.program_name,
                    p.department_id,
                    d.department_name,
                    p.created_at
                FROM programs p
                LEFT JOIN departments d ON p.department_id = d.id
                WHERE p.program_code LIKE :query
                OR p.program_name LIKE :query
                OR d.department_name LIKE :query
                ORDER BY p.program_name ASC LIMIT 20";

        $query = $conn->prepare($sql);
        $searchParam = '%' . $searchQuery . '%';
        $query->bindParam(':query', $searchParam, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function programExists($program_code, $excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count FROM programs
                WHERE program_code = :program_code";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':program_code', $program_code, PDO::PARAM_STR);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) && $result['count'] > 0;
    }

    public function updateProgram() {
        $conn = $this->connect();

        $sql = "UPDATE programs SET 
                program_code = :program_code,
                program_name = :program_name,
                department_id = :department_id
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':program_code', $this->program_code);
        $query->bindParam(':program_name', $this->program_name);
        $query->bindParam(':department_id', $this->department_id);

        return $query->execute();
    }

    public function deleteProgram($id) {
        $conn = $this->connect();

        // Check if program has curriculum
        $query = $conn->prepare("SELECT COUNT(*) as count FROM curriculum WHERE program_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $curriculumCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($curriculumCount > 0) {
            return false;
        }

        $sql = "DELETE FROM programs WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getDepartments() {
        $conn = $this->connect();

        $sql = "SELECT id, department_name FROM departments ORDER BY department_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
