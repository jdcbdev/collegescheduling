<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Program.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$program = new Program();

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
        $data = $program->getAllPrograms();
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid program ID.');
        }
        $data = $program->getProgramById($id);
        if (!$data) {
            jsonResponse(false, null, 'Program not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'departments':
        $data = $program->getDepartments();
        jsonResponse(true, $data);
        break;

    case 'add':
        $programCode = isset($_POST['program_code']) ? trim($_POST['program_code']) : '';
        $programName = isset($_POST['program_name']) ? trim($_POST['program_name']) : '';
        $departmentId = isset($_POST['department_id']) ? (int) $_POST['department_id'] : null;

        if (empty($programCode) || empty($programName)) {
            jsonResponse(false, null, 'Please provide both program code and name.');
        }

        if ($program->programExists($programCode)) {
            jsonResponse(false, null, 'This program code already exists.');
        }

        $program->program_code = $programCode;
        $program->program_name = $programName;
        $program->department_id = $departmentId > 0 ? $departmentId : null;

        $result = $program->addProgram();
        if ($result) {
            jsonResponse(true, null, 'Program added successfully.');
        }
        jsonResponse(false, null, 'Failed to add program.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $programCode = isset($_POST['program_code']) ? trim($_POST['program_code']) : '';
        $programName = isset($_POST['program_name']) ? trim($_POST['program_name']) : '';
        $departmentId = isset($_POST['department_id']) ? (int) $_POST['department_id'] : null;

        if ($id <= 0 || empty($programCode) || empty($programName)) {
            jsonResponse(false, null, 'Please provide valid program values.');
        }

        if ($program->programExists($programCode, $id)) {
            jsonResponse(false, null, 'This program code already exists.');
        }

        $program->id = $id;
        $program->program_code = $programCode;
        $program->program_name = $programName;
        $program->department_id = $departmentId > 0 ? $departmentId : null;

        if ($program->updateProgram()) {
            jsonResponse(true, null, 'Program updated successfully.');
        }
        jsonResponse(false, null, 'Failed to update program.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid program ID.');
        }

        if ($program->deleteProgram($id)) {
            jsonResponse(true, null, 'Program deleted successfully.');
        }
        jsonResponse(false, null, 'Cannot delete program. It may have associated curriculum.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}
