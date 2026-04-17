<?php
require_once __DIR__ . '/Database.php';

class Instructor extends Database {

    public function getAllInstructors() {
        $conn = $this->connect();

        $sql = "SELECT 
                    i.id,
                    i.instructor_code,
                    i.firstname,
                    i.middlename,
                    i.lastname,
                    i.email,
                    i.phone,
                    i.department_id,
                    i.specialization,
                    i.active_status,
                    d.department_name,
                    i.created_at
                FROM instructors i
                LEFT JOIN departments d ON i.department_id = d.id
                ORDER BY i.firstname ASC, i.lastname ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInstructorById($instructor_id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    i.id,
                    i.instructor_code,
                    i.firstname,
                    i.middlename,
                    i.lastname,
                    i.email,
                    i.phone,
                    i.department_id,
                    i.specialization,
                    i.active_status,
                    d.department_name,
                    i.created_at
                FROM instructors i
                LEFT JOIN departments d ON i.department_id = d.id
                WHERE i.id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $instructor_id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addInstructor($instructor_code, $firstname, $middlename, $lastname, $email, $phone, $department_id, $specialization) {
        $conn = $this->connect();

        $sql = "INSERT INTO instructors 
                (instructor_code, firstname, middlename, lastname, email, phone, department_id, specialization, active_status)
                VALUES 
                (:instructor_code, :firstname, :middlename, :lastname, :email, :phone, :department_id, :specialization, TRUE)";

        $query = $conn->prepare($sql);
        $query->bindParam(':instructor_code', $instructor_code, PDO::PARAM_STR);
        $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $query->bindParam(':middlename', $middlename, PDO::PARAM_STR);
        $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $query->bindParam(':specialization', $specialization, PDO::PARAM_STR);

        return $query->execute();
    }

    public function updateInstructor($instructor_id, $instructor_code, $firstname, $middlename, $lastname, $email, $phone, $department_id, $specialization, $active_status) {
        $conn = $this->connect();

        $sql = "UPDATE instructors 
                SET instructor_code = :instructor_code,
                    firstname = :firstname,
                    middlename = :middlename,
                    lastname = :lastname,
                    email = :email,
                    phone = :phone,
                    department_id = :department_id,
                    specialization = :specialization,
                    active_status = :active_status
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $instructor_id, PDO::PARAM_INT);
        $query->bindParam(':instructor_code', $instructor_code, PDO::PARAM_STR);
        $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $query->bindParam(':middlename', $middlename, PDO::PARAM_STR);
        $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $query->bindParam(':specialization', $specialization, PDO::PARAM_STR);
        $query->bindParam(':active_status', $active_status, PDO::PARAM_BOOL);

        return $query->execute();
    }

    public function deleteInstructor($instructor_id) {
        $conn = $this->connect();

        $sql = "DELETE FROM instructors WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $instructor_id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getDepartments() {
        $conn = $this->connect();

        $sql = "SELECT id, department_code, department_name FROM departments ORDER BY department_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
