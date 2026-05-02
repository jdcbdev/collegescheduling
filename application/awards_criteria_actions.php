<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/AwardCriteria.php';

$action   = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$criteria = new AwardCriteria();

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
        jsonResponse(true, $criteria->getAllCriteria());
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid criteria ID.');
        }
        $item = $criteria->getCriteriaById($id);
        if (!$item) {
            jsonResponse(false, null, 'Criteria not found.');
        }
        jsonResponse(true, $item);
        break;

    case 'schoolyears':
        jsonResponse(true, $criteria->getSchoolYears());
        break;

    case 'add':
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $schoolyearId = isset($_POST['schoolyear_id']) ? (int) $_POST['schoolyear_id'] : 0;
        $excludedSubjects = isset($_POST['excluded_subjects']) ? trim($_POST['excluded_subjects']) : '';

        if ($title === '' || $schoolyearId <= 0) {
            jsonResponse(false, null, 'Title and School Year are required.');
        }

        $ok = $criteria->addCriteria($title, $schoolyearId, $excludedSubjects);
        jsonResponse((bool)$ok, null, $ok ? 'Criteria added successfully.' : 'Failed to add criteria.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $schoolyearId = isset($_POST['schoolyear_id']) ? (int) $_POST['schoolyear_id'] : 0;
        $excludedSubjects = isset($_POST['excluded_subjects']) ? trim($_POST['excluded_subjects']) : '';

        if ($id <= 0 || $title === '' || $schoolyearId <= 0) {
            jsonResponse(false, null, 'ID, Title, and School Year are required.');
        }

        $ok = $criteria->updateCriteria($id, $title, $schoolyearId, $excludedSubjects);
        jsonResponse((bool)$ok, null, $ok ? 'Criteria updated successfully.' : 'Failed to update criteria.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid criteria ID.');
        }

        $ok = $criteria->deleteCriteria($id);
        jsonResponse((bool)$ok, null, $ok ? 'Criteria deleted successfully.' : 'Failed to delete criteria.');
        break;

    default:
        jsonResponse(false, null, 'Unknown action.');
}
