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
    case 'get_subjects':
        $curriculum_id = isset($_REQUEST['curriculum_id']) ? (int) $_REQUEST['curriculum_id'] : 0;
        if ($curriculum_id <= 0) {
            jsonResponse(false, null, 'Invalid curriculum ID.');
        }
        $data = $curriculum->getSubjectsByCurriculum($curriculum_id);
        jsonResponse(true, $data);
        break;

    case 'add':
        $curriculum_id = isset($_REQUEST['curriculum_id']) ? (int) $_REQUEST['curriculum_id'] : 0;
        $subject_code = isset($_REQUEST['subject_code']) ? trim($_REQUEST['subject_code']) : '';
        $subject_name = isset($_REQUEST['subject_name']) ? trim($_REQUEST['subject_name']) : '';
        $credit_hours = isset($_REQUEST['credit_hours']) ? (int) $_REQUEST['credit_hours'] : 0;
        $year_level = isset($_REQUEST['year_level']) ? (int) $_REQUEST['year_level'] : 0;
        $semester = isset($_REQUEST['semester']) ? (int) $_REQUEST['semester'] : 0;

        if (!$curriculum_id || !$subject_code || !$subject_name || !$credit_hours || !$year_level || !$semester) {
            jsonResponse(false, null, 'All fields are required.');
        }

        if ($credit_hours < 1 || $credit_hours > 10) {
            jsonResponse(false, null, 'Credit hours must be between 1 and 10.');
        }

        if ($year_level < 1 || $year_level > 4) {
            jsonResponse(false, null, 'Invalid year level.');
        }

        if ($semester < 1 || $semester > 3) {
            jsonResponse(false, null, 'Invalid semester.');
        }

        if ($curriculum->addSubject($curriculum_id, $subject_code, $subject_name, $credit_hours, $year_level, $semester)) {
            jsonResponse(true, null, 'Subject added successfully.');
        } else {
            jsonResponse(false, null, 'Error adding subject. Subject code may already exist.');
        }
        break;

    case 'update':
        $curriculum_id = isset($_REQUEST['curriculum_id']) ? (int) $_REQUEST['curriculum_id'] : 0;
        $subject_id = isset($_REQUEST['subject_id']) ? (int) $_REQUEST['subject_id'] : 0;
        $subject_code = isset($_REQUEST['subject_code']) ? trim($_REQUEST['subject_code']) : '';
        $subject_name = isset($_REQUEST['subject_name']) ? trim($_REQUEST['subject_name']) : '';
        $credit_hours = isset($_REQUEST['credit_hours']) ? (int) $_REQUEST['credit_hours'] : 0;
        $year_level = isset($_REQUEST['year_level']) ? (int) $_REQUEST['year_level'] : 0;
        $semester = isset($_REQUEST['semester']) ? (int) $_REQUEST['semester'] : 0;

        if (!$curriculum_id || !$subject_id || !$subject_code || !$subject_name || !$credit_hours || !$year_level || !$semester) {
            jsonResponse(false, null, 'All fields are required.');
        }

        if ($credit_hours < 1 || $credit_hours > 10) {
            jsonResponse(false, null, 'Credit hours must be between 1 and 10.');
        }

        if ($year_level < 1 || $year_level > 4) {
            jsonResponse(false, null, 'Invalid year level.');
        }

        if ($semester < 1 || $semester > 3) {
            jsonResponse(false, null, 'Invalid semester.');
        }

        if ($curriculum->updateSubject($subject_id, $subject_code, $subject_name, $credit_hours, $year_level, $semester)) {
            jsonResponse(true, null, 'Subject updated successfully.');
        } else {
            jsonResponse(false, null, 'Error updating subject. Subject code may already exist.');
        }
        break;

    case 'delete':
        $subject_id = isset($_REQUEST['subject_id']) ? (int) $_REQUEST['subject_id'] : 0;

        if (!$subject_id) {
            jsonResponse(false, null, 'Invalid subject ID.');
        }

        if ($curriculum->deleteSubject($subject_id)) {
            jsonResponse(true, null, 'Subject deleted successfully.');
        } else {
            jsonResponse(false, null, 'Error deleting subject.');
        }
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}

