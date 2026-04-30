<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/CollegeOfficial.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$official = new CollegeOfficial();

function ensureVpaaColumn(CollegeOfficial $official): void {
    $conn = $official->connect();
    $stmt = $conn->query("SHOW COLUMNS FROM college_officials LIKE 'is_vpaa'");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$exists) {
        $conn->exec("ALTER TABLE college_officials ADD COLUMN is_vpaa TINYINT(1) NOT NULL DEFAULT 0 AFTER is_secretary");
    }
}

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

try {
    ensureVpaaColumn($official);

    switch ($action) {
        case 'list':
            jsonResponse(true, $official->getAllOfficials());
            break;

        case 'get':
            $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            if ($id <= 0) {
                jsonResponse(false, null, 'Invalid official ID.');
            }

            $data = $official->getOfficialById($id);
            if (!$data) {
                jsonResponse(false, null, 'Official not found.');
            }

            jsonResponse(true, $data);
            break;

        case 'add':
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $isDean = isset($_POST['is_dean']) ? (int)$_POST['is_dean'] : 0;
            $isSecretary = isset($_POST['is_secretary']) ? (int)$_POST['is_secretary'] : 0;
            $isVpaa = isset($_POST['is_vpaa']) ? (int)$_POST['is_vpaa'] : 0;
            $departmentIdRaw = $_POST['department_id'] ?? '';
            $departmentId = ($departmentIdRaw === '' || $departmentIdRaw === null) ? null : (int)$departmentIdRaw;

            if ($name === '' || $title === '') {
                jsonResponse(false, null, 'Please provide name and title.');
            }

            if ($isDean !== 0 && $isDean !== 1) {
                jsonResponse(false, null, 'Invalid dean flag value.');
            }

            if ($isSecretary !== 0 && $isSecretary !== 1) {
                jsonResponse(false, null, 'Invalid secretary flag value.');
            }

            if ($isVpaa !== 0 && $isVpaa !== 1) {
                jsonResponse(false, null, 'Invalid VPAA flag value.');
            }

            if ($isVpaa === 1) {
                $departmentId = null;
                if ($official->hasVPAA()) {
                    jsonResponse(false, null, 'A Vice President for Academic Affairs record already exists.');
                }
            }

            if ($isDean === 1) {
                $departmentId = null;
                if ($official->hasDean()) {
                    jsonResponse(false, null, 'A dean record already exists.');
                }
            } elseif ($isSecretary !== 1 && $isVpaa !== 1 && ($departmentId === null || $departmentId <= 0)) {
                jsonResponse(false, null, 'Please select a department unless official is marked as dean, secretary, or VPAA.');
            }

            $official->name = $name;
            $official->title = $title;
            $official->department_id = $departmentId;
            $official->is_dean = $isDean;
            $official->is_secretary = $isSecretary;
            $official->is_vpaa = $isVpaa;

            if ($official->addOfficial()) {
                jsonResponse(true, null, 'College official added successfully.');
            }

            jsonResponse(false, null, 'Failed to add college official.');
            break;

        case 'update':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $isDean = isset($_POST['is_dean']) ? (int)$_POST['is_dean'] : 0;
            $isSecretary = isset($_POST['is_secretary']) ? (int)$_POST['is_secretary'] : 0;
            $isVpaa = isset($_POST['is_vpaa']) ? (int)$_POST['is_vpaa'] : 0;
            $departmentIdRaw = $_POST['department_id'] ?? '';
            $departmentId = ($departmentIdRaw === '' || $departmentIdRaw === null) ? null : (int)$departmentIdRaw;

            if ($id <= 0 || $name === '' || $title === '') {
                jsonResponse(false, null, 'Please provide valid official values.');
            }

            if ($isDean !== 0 && $isDean !== 1) {
                jsonResponse(false, null, 'Invalid dean flag value.');
            }

            if ($isSecretary !== 0 && $isSecretary !== 1) {
                jsonResponse(false, null, 'Invalid secretary flag value.');
            }

            if ($isVpaa !== 0 && $isVpaa !== 1) {
                jsonResponse(false, null, 'Invalid VPAA flag value.');
            }

            if ($isVpaa === 1) {
                $departmentId = null;
                if ($official->hasVPAA($id)) {
                    jsonResponse(false, null, 'A Vice President for Academic Affairs record already exists.');
                }
            }

            if ($isDean === 1) {
                $departmentId = null;
                if ($official->hasDean($id)) {
                    jsonResponse(false, null, 'A dean record already exists.');
                }
            } elseif ($isSecretary !== 1 && $isVpaa !== 1 && ($departmentId === null || $departmentId <= 0)) {
                jsonResponse(false, null, 'Please select a department unless official is marked as dean, secretary, or VPAA.');
            }

            $official->id = $id;
            $official->name = $name;
            $official->title = $title;
            $official->department_id = $departmentId;
            $official->is_dean = $isDean;
            $official->is_secretary = $isSecretary;
            $official->is_vpaa = $isVpaa;

            if ($official->updateOfficial()) {
                jsonResponse(true, null, 'College official updated successfully.');
            }

            jsonResponse(false, null, 'Failed to update college official.');
            break;

        case 'delete':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) {
                jsonResponse(false, null, 'Invalid official ID.');
            }

            if ($official->deleteOfficial($id)) {
                jsonResponse(true, null, 'College official deleted successfully.');
            }

            jsonResponse(false, null, 'Failed to delete college official.');
            break;

        default:
            jsonResponse(false, null, 'Invalid action.');
    }
} catch (Exception $e) {
    jsonResponse(false, null, 'Error: ' . $e->getMessage());
}
