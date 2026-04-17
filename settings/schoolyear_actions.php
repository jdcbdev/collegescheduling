<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/SchoolYear.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$schoolYear = new SchoolYear();

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
        $data = $schoolYear->getAllSchoolYears();
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid school year ID.');
        }
        $data = $schoolYear->getSchoolYearById($id);
        if (!$data) {
            jsonResponse(false, null, 'School year not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'add':
        $startYear = isset($_POST['start_year']) ? (int) $_POST['start_year'] : 0;
        $endYear = isset($_POST['end_year']) ? (int) $_POST['end_year'] : 0;
        $semester = isset($_POST['semester']) ? (int) $_POST['semester'] : 0;
        $isActive = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 0;

        if ($startYear <= 0 || $endYear <= 0 || $semester <= 0 || $semester > 3) {
            jsonResponse(false, null, 'Please provide valid school year values.');
        }
        if ($endYear <= $startYear) {
            jsonResponse(false, null, 'End year must be greater than start year.');
        }
        if ($schoolYear->schoolYearExists($startYear, $endYear, $semester)) {
            jsonResponse(false, null, 'This school year and semester combination already exists.');
        }

        $schoolYear->start_year = $startYear;
        $schoolYear->end_year = $endYear;
        $schoolYear->semester = $semester;
        $schoolYear->is_active = $isActive ? 1 : 0;

        $result = $schoolYear->addSchoolYear();
        if ($result) {
            if ($isActive) {
                $schoolYear->setActiveSchoolYear($result);
            }
            jsonResponse(true, null, 'School year added successfully.');
        }
        jsonResponse(false, null, 'Failed to add school year.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $startYear = isset($_POST['start_year']) ? (int) $_POST['start_year'] : 0;
        $endYear = isset($_POST['end_year']) ? (int) $_POST['end_year'] : 0;
        $semester = isset($_POST['semester']) ? (int) $_POST['semester'] : 0;
        $isActive = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 0;

        if ($id <= 0 || $startYear <= 0 || $endYear <= 0 || $semester <= 0 || $semester > 3) {
            jsonResponse(false, null, 'Please provide valid school year values.');
        }
        if ($endYear <= $startYear) {
            jsonResponse(false, null, 'End year must be greater than start year.');
        }
        if ($schoolYear->schoolYearExists($startYear, $endYear, $semester, $id)) {
            jsonResponse(false, null, 'This school year and semester combination already exists.');
        }

        $schoolYear->id = $id;
        $schoolYear->start_year = $startYear;
        $schoolYear->end_year = $endYear;
        $schoolYear->semester = $semester;
        $schoolYear->is_active = $isActive ? 1 : 0;

        if ($isActive) {
            if ($schoolYear->setActiveSchoolYear($id)) {
                jsonResponse(true, null, 'School year updated successfully.');
            }
        } else {
            if ($schoolYear->updateSchoolYear()) {
                jsonResponse(true, null, 'School year updated successfully.');
            }
        }

        jsonResponse(false, null, 'Failed to update school year.');
        break;

    case 'toggle':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $isActive = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid school year ID.');
        }

        if ($schoolYear->toggleSchoolYearActive($id, $isActive ? 1 : 0)) {
            jsonResponse(true, null, 'Active status updated.');
        }
        jsonResponse(false, null, 'Unable to update active status.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}
