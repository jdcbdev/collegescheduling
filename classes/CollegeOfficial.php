<?php

require_once __DIR__ . '/Database.php';

class CollegeOfficial extends Database {

    public $id;
    public $name;
    public $title;
    public $department_id;
    public $is_dean;

    public function addOfficial() {
        $conn = $this->connect();

        $sql = "INSERT INTO college_officials (name, title, department_id, is_dean)
                VALUES (:name, :title, :department_id, :is_dean)";

        $query = $conn->prepare($sql);
        $query->bindParam(':name', $this->name, PDO::PARAM_STR);
        $query->bindParam(':title', $this->title, PDO::PARAM_STR);

        if ($this->department_id === null) {
            $query->bindValue(':department_id', null, PDO::PARAM_NULL);
        } else {
            $query->bindValue(':department_id', (int)$this->department_id, PDO::PARAM_INT);
        }

        $query->bindValue(':is_dean', (int)$this->is_dean, PDO::PARAM_INT);

        if ($query->execute()) {
            return $conn->lastInsertId();
        }

        return false;
    }

    public function getOfficialById($id) {
        $conn = $this->connect();

        $sql = "SELECT co.id,
                       co.name,
                       co.title,
                       co.department_id,
                       co.is_dean,
                       co.created_at,
                       d.department_name
                FROM college_officials co
                LEFT JOIN departments d ON d.id = co.department_id
                WHERE co.id = :id
                LIMIT 1";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllOfficials() {
        $conn = $this->connect();

        $sql = "SELECT co.id,
                       co.name,
                       co.title,
                       co.department_id,
                       co.is_dean,
                       co.created_at,
                       d.department_name
                FROM college_officials co
                LEFT JOIN departments d ON d.id = co.department_id
                ORDER BY co.is_dean DESC, co.name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasDean($excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) AS total
                FROM college_officials
                WHERE is_dean = 1";

        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
        }

        $query = $conn->prepare($sql);
        if ($excludeId !== null) {
            $query->bindValue(':exclude_id', (int)$excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return !empty($row['total']);
    }

    public function updateOfficial() {
        $conn = $this->connect();

        $sql = "UPDATE college_officials
                SET name = :name,
                    title = :title,
                    department_id = :department_id,
                    is_dean = :is_dean
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int)$this->id, PDO::PARAM_INT);
        $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        $query->bindValue(':title', $this->title, PDO::PARAM_STR);

        if ($this->department_id === null) {
            $query->bindValue(':department_id', null, PDO::PARAM_NULL);
        } else {
            $query->bindValue(':department_id', (int)$this->department_id, PDO::PARAM_INT);
        }

        $query->bindValue(':is_dean', (int)$this->is_dean, PDO::PARAM_INT);

        return $query->execute();
    }

    public function deleteOfficial($id) {
        $conn = $this->connect();

        $sql = "DELETE FROM college_officials WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $query->execute();
    }
}
