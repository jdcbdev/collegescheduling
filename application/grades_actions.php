<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/ApplicantGrade.php';

$action    = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$gradeObj  = new ApplicantGrade();

function jsonResponse($success, $data = null, $message = '') {
    echo json_encode(array_filter([
        'success' => $success,
        'data'    => $data,
        'message' => $message,
    ], function ($v) { return $v !== null; }));
    exit;
}

switch ($action) {

    case 'get_subjects':
        $applicantId  = isset($_REQUEST['applicant_id'])  ? (int) $_REQUEST['applicant_id']  : 0;
        $curriculumId = isset($_REQUEST['curriculum_id']) ? (int) $_REQUEST['curriculum_id'] : 0;
        if (!$applicantId || !$curriculumId) {
            jsonResponse(false, null, 'Invalid parameters.');
        }
        $data = $gradeObj->getSubjectsWithGrades($applicantId, $curriculumId);
        jsonResponse(true, $data);
        break;

    case 'save':
        $applicantId = isset($_POST['applicant_id']) ? (int) $_POST['applicant_id'] : 0;
        $subjects    = isset($_POST['subjects'])     ? $_POST['subjects']            : [];

        if (!$applicantId) {
            jsonResponse(false, null, 'Invalid applicant ID.');
        }
        if (!is_array($subjects)) {
            jsonResponse(false, null, 'Invalid grade data.');
        }

        try {
            foreach ($subjects as $subjectId => $fields) {
                $grade = isset($fields['grade']) ? trim($fields['grade']) : '';
                $gradeObj->saveGrade($applicantId, (int) $subjectId, $grade, null);
            }
        } catch (InvalidArgumentException $e) {
            jsonResponse(false, null, $e->getMessage());
        }

        $gwa = $gradeObj->computeAndSaveGWA($applicantId);
        jsonResponse(true, ['gwa' => $gwa], 'Grades saved successfully.');
        break;

    default:
        jsonResponse(false, null, 'Unknown action.');
}
