<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Applicant.php';

$action    = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$applicant = new Applicant();

function jsonResponse($success, $data = null, $message = '') {
    echo json_encode(array_filter([
        'success' => $success,
        'data'    => $data,
        'message' => $message
    ], function ($value) {
        return $value !== null;
    }));
    exit;
}

switch ($action) {

    case 'list':
        $programId    = isset($_REQUEST['program_id'])    && $_REQUEST['program_id']    !== '' ? (int) $_REQUEST['program_id']    : null;
        $schoolyearId = isset($_REQUEST['schoolyear_id']) && $_REQUEST['schoolyear_id'] !== '' ? (int) $_REQUEST['schoolyear_id'] : null;
        $data         = $applicant->getAllApplicants($programId, $schoolyearId);
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid applicant ID.');
        }
        $data = $applicant->getApplicantById($id);
        if (!$data) {
            jsonResponse(false, null, 'Applicant not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'programs':
        $data = $applicant->getPrograms();
        jsonResponse(true, $data);
        break;

    case 'curricula':
        $programId = isset($_REQUEST['program_id']) ? (int) $_REQUEST['program_id'] : 0;
        if ($programId <= 0) {
            jsonResponse(true, []);
        }
        $data = $applicant->getCurriculaByProgram($programId);
        jsonResponse(true, $data);
        break;

    case 'schoolyears':
        $data = $applicant->getSchoolYears();
        jsonResponse(true, $data);
        break;

    case 'add':
        $studentNo    = isset($_POST['student_no'])    ? trim($_POST['student_no'])    : '';
        $fn           = isset($_POST['fn'])            ? trim($_POST['fn'])            : '';
        $mn           = isset($_POST['mn'])            ? trim($_POST['mn'])            : '';
        $ln           = isset($_POST['ln'])            ? trim($_POST['ln'])            : '';
        $programId    = isset($_POST['program_id'])    ? (int) $_POST['program_id']    : 0;
        $curriculumId = isset($_POST['curriculum_id']) ? (int) $_POST['curriculum_id'] : 0;

        if (empty($studentNo) || empty($fn) || empty($ln) || $programId <= 0 || $curriculumId <= 0) {
            jsonResponse(false, null, 'Please fill in all required fields.');
        }

        if ($applicant->studentNoExists($studentNo)) {
            jsonResponse(false, null, 'Student number already exists.');
        }

        $activeSY = $applicant->getActiveSchoolYear();

        $applicant->student_no    = $studentNo;
        $applicant->fn            = $fn;
        $applicant->mn            = $mn ?: null;
        $applicant->ln            = $ln;
        $applicant->program_id    = $programId;
        $applicant->curriculum_id = $curriculumId;
        $applicant->gwa           = null;
        $applicant->schoolyear_id = $activeSY;

        $result = $applicant->addApplicant();
        if ($result) {
            jsonResponse(true, null, 'Applicant added successfully.');
        }
        jsonResponse(false, null, 'Failed to add applicant.');
        break;

    case 'update':
        $id           = isset($_POST['id'])            ? (int) $_POST['id']            : 0;
        $studentNo    = isset($_POST['student_no'])    ? trim($_POST['student_no'])    : '';
        $fn           = isset($_POST['fn'])            ? trim($_POST['fn'])            : '';
        $mn           = isset($_POST['mn'])            ? trim($_POST['mn'])            : '';
        $ln           = isset($_POST['ln'])            ? trim($_POST['ln'])            : '';
        $programId    = isset($_POST['program_id'])    ? (int) $_POST['program_id']    : 0;
        $curriculumId = isset($_POST['curriculum_id']) ? (int) $_POST['curriculum_id'] : 0;

        if ($id <= 0 || empty($studentNo) || empty($fn) || empty($ln) || $programId <= 0 || $curriculumId <= 0) {
            jsonResponse(false, null, 'Please fill in all required fields.');
        }

        if ($applicant->studentNoExists($studentNo, $id)) {
            jsonResponse(false, null, 'Student number already exists.');
        }

        // Preserve existing school year; do not overwrite on update
        $existing = $applicant->getApplicantById($id);

        $applicant->id            = $id;
        $applicant->student_no    = $studentNo;
        $applicant->fn            = $fn;
        $applicant->mn            = $mn ?: null;
        $applicant->ln            = $ln;
        $applicant->program_id    = $programId;
        $applicant->curriculum_id = $curriculumId;
        $applicant->gwa           = $existing ? $existing['gwa'] : null;
        $applicant->schoolyear_id = $existing ? $existing['schoolyear_id'] : null;

        if ($applicant->updateApplicant()) {
            jsonResponse(true, null, 'Applicant updated successfully.');
        }
        jsonResponse(false, null, 'Failed to update applicant.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid applicant ID.');
        }
        if ($applicant->deleteApplicant($id)) {
            jsonResponse(true, null, 'Applicant deleted successfully.');
        }
        jsonResponse(false, null, 'Failed to delete applicant.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}
