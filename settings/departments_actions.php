<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Department.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$department = new Department();

function jsonResponse($success, $data = null, $message = '') {
    echo json_encode(array_filter([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ], function ($value) {
        return $value !== null;
    }));
    exit;
}

switch ($action) {
    case 'list':
        $data = $department->getAllDepartments();
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid department ID.');
        }
        $data = $department->getDepartmentById($id);
        if (!$data) {
            jsonResponse(false, null, 'Department not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'add':
        $departmentCode = isset($_POST['department_code']) ? trim($_POST['department_code']) : '';
        $departmentName = isset($_POST['department_name']) ? trim($_POST['department_name']) : '';

        if (empty($departmentCode) || empty($departmentName)) {
            jsonResponse(false, null, 'Please provide both department code and name.');
        }

        if ($department->departmentExists($departmentCode)) {
            jsonResponse(false, null, 'This department code already exists.');
        }

        $department->department_code = $departmentCode;
        $department->department_name = $departmentName;

        $result = $department->addDepartment();
        if ($result) {
            jsonResponse(true, null, 'Department added successfully.');
        }
        jsonResponse(false, null, 'Failed to add department.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $departmentCode = isset($_POST['department_code']) ? trim($_POST['department_code']) : '';
        $departmentName = isset($_POST['department_name']) ? trim($_POST['department_name']) : '';

        if ($id <= 0 || empty($departmentCode) || empty($departmentName)) {
            jsonResponse(false, null, 'Please provide valid department values.');
        }

        if ($department->departmentExists($departmentCode, $id)) {
            jsonResponse(false, null, 'This department code already exists.');
        }

        $department->id = $id;
        $department->department_code = $departmentCode;
        $department->department_name = $departmentName;

        if ($department->updateDepartment()) {
            jsonResponse(true, null, 'Department updated successfully.');
        }
        jsonResponse(false, null, 'Failed to update department.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid department ID.');
        }

        if ($department->deleteDepartment($id)) {
            jsonResponse(true, null, 'Department deleted successfully.');
        }
        jsonResponse(false, null, 'Cannot delete department. It may have associated records.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}
