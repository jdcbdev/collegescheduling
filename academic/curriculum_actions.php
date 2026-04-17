<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Curriculum.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$curriculum = new Curriculum();

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
        $data = $curriculum->getAllCurriculum();
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid curriculum ID.');
        }
        $data = $curriculum->getCurriculumById($id);
        if (!$data) {
            jsonResponse(false, null, 'Curriculum not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'programs':
        $data = $curriculum->getPrograms();
        jsonResponse(true, $data);
        break;

    case 'add':
        $programId = isset($_POST['program_id']) ? (int) $_POST['program_id'] : 0;
        $effectiveStartYear = isset($_POST['effective_start_year']) ? (int) $_POST['effective_start_year'] : 0;
        $effectiveEndYear = isset($_POST['effective_end_year']) ? (int) $_POST['effective_end_year'] : 0;

        if ($programId <= 0 || $effectiveStartYear <= 0 || $effectiveEndYear <= 0) {
            jsonResponse(false, null, 'Please provide valid curriculum information.');
        }

        if ($effectiveEndYear <= $effectiveStartYear) {
            jsonResponse(false, null, 'End year must be greater than start year.');
        }

        if ($curriculum->curriculumExists($programId, $effectiveStartYear, $effectiveEndYear)) {
            jsonResponse(false, null, 'This curriculum record already exists for the selected program and years.');
        }

        $curriculum->program_id = $programId;
        $curriculum->effective_start_year = $effectiveStartYear;
        $curriculum->effective_end_year = $effectiveEndYear;

        $result = $curriculum->addCurriculum();
        if ($result) {
            jsonResponse(true, null, 'Curriculum added successfully.');
        }
        jsonResponse(false, null, 'Failed to add curriculum.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $programId = isset($_POST['program_id']) ? (int) $_POST['program_id'] : 0;
        $effectiveStartYear = isset($_POST['effective_start_year']) ? (int) $_POST['effective_start_year'] : 0;
        $effectiveEndYear = isset($_POST['effective_end_year']) ? (int) $_POST['effective_end_year'] : 0;

        if ($id <= 0 || $programId <= 0 || $effectiveStartYear <= 0 || $effectiveEndYear <= 0) {
            jsonResponse(false, null, 'Please provide valid curriculum information.');
        }

        if ($effectiveEndYear <= $effectiveStartYear) {
            jsonResponse(false, null, 'End year must be greater than start year.');
        }

        if ($curriculum->curriculumExists($programId, $effectiveStartYear, $effectiveEndYear, $id)) {
            jsonResponse(false, null, 'This curriculum record already exists for the selected program and years.');
        }

        $curriculum->id = $id;
        $curriculum->program_id = $programId;
        $curriculum->effective_start_year = $effectiveStartYear;
        $curriculum->effective_end_year = $effectiveEndYear;

        if ($curriculum->updateCurriculum()) {
            jsonResponse(true, null, 'Curriculum updated successfully.');
        }
        jsonResponse(false, null, 'Failed to update curriculum.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid curriculum ID.');
        }

        if ($curriculum->deleteCurriculum($id)) {
            jsonResponse(true, null, 'Curriculum deleted successfully.');
        }
        jsonResponse(false, null, 'Cannot delete curriculum. It may have associated subjects.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}
