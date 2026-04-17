<?php
require_once __DIR__ . '/../classes/Instructor.php';

header('Content-Type: application/json');

$instructor = new Instructor();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

$response = array('success' => false, 'message' => 'Invalid action');

try {
    switch($action) {
        case 'get_all':
            $result = $instructor->getAllInstructors();
            $response = array('success' => true, 'data' => $result);
            break;

        case 'add':
            $instructor_code = isset($_POST['instructor_code']) ? trim($_POST['instructor_code']) : '';
            $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
            $middlename = isset($_POST['middlename']) ? trim($_POST['middlename']) : '';
            $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';

            // Validation
            if (empty($instructor_code)) {
                $response = array('success' => false, 'message' => 'Instructor code is required.');
            } elseif (empty($firstname)) {
                $response = array('success' => false, 'message' => 'First name is required.');
            } elseif (empty($lastname)) {
                $response = array('success' => false, 'message' => 'Last name is required.');
            } else {
                if ($instructor->addInstructor($instructor_code, $firstname, $middlename, $lastname, $email, $phone, $department_id, $specialization)) {
                    $response = array('success' => true, 'message' => 'Instructor added successfully.');
                } else {
                    $response = array('success' => false, 'message' => 'Error adding instructor. Instructor code may already exist.');
                }
            }
            break;

        case 'update':
            $instructor_id = isset($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : 0;
            $instructor_code = isset($_POST['instructor_code']) ? trim($_POST['instructor_code']) : '';
            $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
            $middlename = isset($_POST['middlename']) ? trim($_POST['middlename']) : '';
            $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';
            $active_status = isset($_POST['active_status']) ? (bool)(int)$_POST['active_status'] : true;

            // Validation
            if ($instructor_id <= 0) {
                $response = array('success' => false, 'message' => 'Invalid instructor ID.');
            } elseif (empty($instructor_code)) {
                $response = array('success' => false, 'message' => 'Instructor code is required.');
            } elseif (empty($firstname)) {
                $response = array('success' => false, 'message' => 'First name is required.');
            } elseif (empty($lastname)) {
                $response = array('success' => false, 'message' => 'Last name is required.');
            } else {
                if ($instructor->updateInstructor($instructor_id, $instructor_code, $firstname, $middlename, $lastname, $email, $phone, $department_id, $specialization, $active_status)) {
                    $response = array('success' => true, 'message' => 'Instructor updated successfully.');
                } else {
                    $response = array('success' => false, 'message' => 'Error updating instructor. Code may already exist.');
                }
            }
            break;

        case 'delete':
            $instructor_id = isset($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : 0;

            if ($instructor_id <= 0) {
                $response = array('success' => false, 'message' => 'Invalid instructor ID.');
            } else {
                if ($instructor->deleteInstructor($instructor_id)) {
                    $response = array('success' => true, 'message' => 'Instructor deleted successfully.');
                } else {
                    $response = array('success' => false, 'message' => 'Error deleting instructor.');
                }
            }
            break;

        default:
            $response = array('success' => false, 'message' => 'Invalid action.');
    }
} catch (Exception $e) {
    $response = array('success' => false, 'message' => 'Error: ' . $e->getMessage());
}

echo json_encode($response);
?>
