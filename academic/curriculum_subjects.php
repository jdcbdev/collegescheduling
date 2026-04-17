<?php
$page_name = "Curriculum Subjects";
$activePage = 'curriculum';
$assetBasePath = '../assets';
$navBasePath = '../';

require_once __DIR__ . '/../classes/Curriculum.php';

$curriculum_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($curriculum_id <= 0) {
    die('Invalid curriculum ID');
}

$curriculum = new Curriculum();
$curriculumData = $curriculum->getCurriculumById($curriculum_id);
if (!$curriculumData) {
    die('Curriculum not found');
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="body-wrapper">
      <?php require_once __DIR__ . '/../includes/topnav.php'; ?>
      <div class="body-wrapper-inner">
        <div class="container-fluid content-page">
          <div class="row">
            <div class="col-12">
              <div class="card mb-4">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h5 class="card-title mb-1">Curriculum Subjects</h5>
                      <p class="text-muted mb-0">
                        <strong><?php echo htmlspecialchars($curriculumData['program_code'] . ' - ' . $curriculumData['program_name']); ?></strong><br>
                        Effective Year: <?php echo $curriculumData['effective_start_year']; ?> - <?php echo $curriculumData['effective_end_year']; ?>
                      </p>
                    </div>
                    <div>
                      <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add Subject</button>
                      <a href="./curriculum.php" class="btn btn-primary btn-cancel-all">Back</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div id="subjectsContainer">
            <!-- Will be populated by JavaScript -->
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Add Subject Modal -->
  <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addSubjectModalLabel">Add Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addSubjectForm">
          <div class="modal-body">
            <div class="mb-3">
              <label for="subjectCode" class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="subjectCode" name="subject_code" required>
            </div>
            <div class="mb-3">
              <label for="subjectName" class="form-label">Subject Name</label>
              <input type="text" class="form-control" id="subjectName" name="subject_name" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="lecCredits" class="form-label">Lecture Credits</label>
                  <input type="number" class="form-control" id="lecCredits" name="lec_credits" min="0" max="10" value="0">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="labCredits" class="form-label">Lab Credits</label>
                  <input type="number" class="form-control" id="labCredits" name="lab_credits" min="0" max="10" value="0">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="yearLevel" class="form-label">Year Level</label>
              <select class="form-control" id="yearLevel" name="year_level" required>
                <option value="">Select year level</option>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
                <option value="4">Year 4</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="semester" class="form-label">Semester</label>
              <select class="form-control" id="semester" name="semester" required>
                <option value="">Select semester</option>
                <option value="1">1st Semester</option>
                <option value="2">2nd Semester</option>
                <option value="3">Summer</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Subject</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Subject Modal -->
  <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editSubjectModalLabel">Edit Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editSubjectForm">
          <div class="modal-body">
            <input type="hidden" id="editSubjectId" name="subject_id">
            <div class="mb-3">
              <label for="editSubjectCode" class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="editSubjectCode" name="subject_code" required>
            </div>
            <div class="mb-3">
              <label for="editSubjectName" class="form-label">Subject Name</label>
              <input type="text" class="form-control" id="editSubjectName" name="subject_name" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editLecCredits" class="form-label">Lecture Credits</label>
                  <input type="number" class="form-control" id="editLecCredits" name="lec_credits" min="0" max="10" value="0">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editLabCredits" class="form-label">Lab Credits</label>
                  <input type="number" class="form-control" id="editLabCredits" name="lab_credits" min="0" max="10" value="0">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="editYearLevel" class="form-label">Year Level</label>
              <select class="form-control" id="editYearLevel" name="year_level" required>
                <option value="">Select year level</option>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
                <option value="4">Year 4</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editSemester" class="form-label">Semester</label>
              <select class="form-control" id="editSemester" name="semester" required>
                <option value="">Select semester</option>
                <option value="1">1st Semester</option>
                <option value="2">2nd Semester</option>
                <option value="3">Summer</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="deleteSubjectBtn">Delete</button>
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Subject</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
  <script>
    (function($) {
      var curriculumId = <?php echo $curriculum_id; ?>;
      var endpoint = './curriculum_subjects_actions.php';

      function renderSubjects(data) {
        var container = $('#subjectsContainer');
        
        if (!data.length) {
          container.html('<div class="alert alert-info">No subjects found for this curriculum.</div>');
          return;
        }

        // Group subjects by year level
        var grouped = {};
        data.forEach(function(subject) {
          var yearLevel = subject.year_level;
          if (!grouped[yearLevel]) {
            grouped[yearLevel] = {};
          }
          var semester = subject.semester;
          if (!grouped[yearLevel][semester]) {
            grouped[yearLevel][semester] = [];
          }
          grouped[yearLevel][semester].push(subject);
        });

        var html = '';
        var yearLevels = Object.keys(grouped).sort(function(a, b) {
          return parseInt(a) - parseInt(b);
        });
        var grandTotal = 0;

        yearLevels.forEach(function(yearLevel) {
          var yearText = '';
          switch(parseInt(yearLevel)) {
            case 1: yearText = 'First Year'; break;
            case 2: yearText = 'Second Year'; break;
            case 3: yearText = 'Third Year'; break;
            case 4: yearText = 'Fourth Year'; break;
            default: yearText = 'Year ' + yearLevel;
          }

          html += '<div class="card mb-4">';
          html += '<div class="card-header bg-light text-center">';
          html += '<h6 class="card-title mb-0"><strong>' + yearText + '</strong></h6>';
          html += '</div>';
          html += '<div class="card-body">';
          html += '<div class="row g-3">';

          // Get semesters for this year level
          var semesters = Object.keys(grouped[yearLevel]).sort(function(a, b) {
            return parseInt(a) - parseInt(b);
          });

          // Process 1st and 2nd semester in columns
          var sem1Subjects = grouped[yearLevel]['1'] || [];
          var sem2Subjects = grouped[yearLevel]['2'] || [];
          var sem3Subjects = grouped[yearLevel]['3'] || [];

          // Calculate max rows for alignment
          var maxRows = Math.max(sem1Subjects.length, sem2Subjects.length);

          // 1st Semester
          html += '<div class="col-md-6">';
          html += '<h6 class="mb-3 text-center">1st Semester</h6>';
          if (sem1Subjects.length > 0) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-hover">';
            html += '<thead><tr><th>Code</th><th>Subject</th><th>Lec</th><th>Lab</th><th>Total</th></tr></thead>';
            html += '<tbody>';
            var sem1LecTotal = 0, sem1LabTotal = 0, sem1Total = 0;
            sem1Subjects.forEach(function(subject) {
              var total = parseInt(subject.total_credits);
              html += '<tr>';
              html += '<td><a href="#" class="btn-edit-subject" data-id="' + subject.id + '" data-code="' + escapeHtml(subject.subject_code) + '" data-name="' + escapeHtml(subject.subject_name) + '" data-lec="' + subject.lec_credits + '" data-lab="' + subject.lab_credits + '" data-year="' + subject.year_level + '" data-semester="' + subject.semester + '"><strong style="color: #007bff; cursor: pointer;">' + escapeHtml(subject.subject_code) + '</strong></a></td>';
              html += '<td>' + escapeHtml(subject.subject_name) + '</td>';
              html += '<td>' + subject.lec_credits + '</td>';
              html += '<td>' + subject.lab_credits + '</td>';
              html += '<td><strong>' + total + '</strong></td>';
              html += '</tr>';
              sem1LecTotal += parseInt(subject.lec_credits);
              sem1LabTotal += parseInt(subject.lab_credits);
              sem1Total += total;
              grandTotal += total;
            });

            // Pad with empty rows to match sem2 if needed
            for (var i = sem1Subjects.length; i < maxRows; i++) {
              html += '<tr>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '</tr>';
            }

            html += '<tr class="table-active"><td colspan="2"><strong>Subtotal</strong></td><td><strong>' + sem1LecTotal + '</strong></td><td><strong>' + sem1LabTotal + '</strong></td><td><strong>' + sem1Total + '</strong></td></tr>';
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
          } else {
            html += '<p class="text-muted text-center">No subjects</p>';
          }
          html += '</div>';

          // 2nd Semester
          html += '<div class="col-md-6">';
          html += '<h6 class="mb-3 text-center">2nd Semester</h6>';
          if (sem2Subjects.length > 0) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-hover">';
            html += '<thead><tr><th>Code</th><th>Subject</th><th>Lec</th><th>Lab</th><th>Total</th></tr></thead>';
            html += '<tbody>';
            var sem2LecTotal = 0, sem2LabTotal = 0, sem2Total = 0;
            sem2Subjects.forEach(function(subject) {
              var total = parseInt(subject.total_credits);
              html += '<tr>';
              html += '<td><a href="#" class="btn-edit-subject" data-id="' + subject.id + '" data-code="' + escapeHtml(subject.subject_code) + '" data-name="' + escapeHtml(subject.subject_name) + '" data-lec="' + subject.lec_credits + '" data-lab="' + subject.lab_credits + '" data-year="' + subject.year_level + '" data-semester="' + subject.semester + '"><strong style="color: #007bff; cursor: pointer;">' + escapeHtml(subject.subject_code) + '</strong></a></td>';
              html += '<td>' + escapeHtml(subject.subject_name) + '</td>';
              html += '<td>' + subject.lec_credits + '</td>';
              html += '<td>' + subject.lab_credits + '</td>';
              html += '<td><strong>' + total + '</strong></td>';
              html += '</tr>';
              sem2LecTotal += parseInt(subject.lec_credits);
              sem2LabTotal += parseInt(subject.lab_credits);
              sem2Total += total;
              grandTotal += total;
            });

            // Pad with empty rows to match sem1 if needed
            for (var i = sem2Subjects.length; i < maxRows; i++) {
              html += '<tr>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '<td>&nbsp;</td>';
              html += '</tr>';
            }

            html += '<tr class="table-active"><td colspan="2"><strong>Subtotal</strong></td><td><strong>' + sem2LecTotal + '</strong></td><td><strong>' + sem2LabTotal + '</strong></td><td><strong>' + sem2Total + '</strong></td></tr>';
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
          } else {
            html += '<p class="text-muted text-center">No subjects</p>';
          }
          html += '</div>';

          // Summer semester below
          if (sem3Subjects.length > 0) {
            html += '<div class="row g-3 justify-content-center">';
            html += '<div class="col-md-6">';
            html += '<h6 class="mb-3 text-center">Summer</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-hover">';
            html += '<thead><tr><th>Code</th><th>Subject</th><th>Lec</th><th>Lab</th><th>Total</th></tr></thead>';
            html += '<tbody>';
            var sem3LecTotal = 0, sem3LabTotal = 0, sem3Total = 0;
            sem3Subjects.forEach(function(subject) {
              var total = parseInt(subject.total_credits);
              html += '<tr>';
              html += '<td><a href="#" class="btn-edit-subject" data-id="' + subject.id + '" data-code="' + escapeHtml(subject.subject_code) + '" data-name="' + escapeHtml(subject.subject_name) + '" data-lec="' + subject.lec_credits + '" data-lab="' + subject.lab_credits + '" data-year="' + subject.year_level + '" data-semester="' + subject.semester + '"><strong style="color: #007bff; cursor: pointer;">' + escapeHtml(subject.subject_code) + '</strong></a></td>';
              html += '<td>' + escapeHtml(subject.subject_name) + '</td>';
              html += '<td>' + subject.lec_credits + '</td>';
              html += '<td>' + subject.lab_credits + '</td>';
              html += '<td><strong>' + total + '</strong></td>';
              html += '</tr>';
              sem3LecTotal += parseInt(subject.lec_credits);
              sem3LabTotal += parseInt(subject.lab_credits);
              sem3Total += total;
              grandTotal += total;
            });
            html += '<tr class="table-active"><td colspan="2"><strong>Subtotal</strong></td><td><strong>' + sem3LecTotal + '</strong></td><td><strong>' + sem3LabTotal + '</strong></td><td><strong>' + sem3Total + '</strong></td></tr>';
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
          }

          html += '</div>';
          html += '</div>';
          html += '</div>';
        });

        // Add grand total
        html += '<div class="card">';
        html += '<div class="card-body">';
        html += '<h6 class="mb-0">Total Credits: <strong class="text-primary">' + grandTotal + '</strong></h6>';
        html += '</div>';
        html += '</div>';

        container.html(html);
      }

      function escapeHtml(text) {
        var map = {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
      }

      $(document).ready(function() {
        loadSubjects();

        // Handle add subject form submission
        $('#addSubjectForm').on('submit', function(e) {
          e.preventDefault();
          var formData = {
            action: 'add',
            curriculum_id: curriculumId,
            subject_code: $('#subjectCode').val(),
            subject_name: $('#subjectName').val(),
            lec_credits: $('#lecCredits').val(),
            lab_credits: $('#labCredits').val(),
            year_level: $('#yearLevel').val(),
            semester: $('#semester').val()
          };

          $.post(endpoint.replace('curriculum_subjects_actions.php', 'curriculum_subjects_actions.php'), formData, function(response) {
            if (response.success) {
              var modal = bootstrap.Modal.getInstance(document.getElementById('addSubjectModal'));
              modal.hide();
              $('#addSubjectForm')[0].reset();
              loadSubjects();
              showAlert('Subject added successfully!', 'success');
            } else {
              showAlert(response.message || 'Error adding subject.', 'danger');
            }
          }, 'json').fail(function() {
            showAlert('Error adding subject. Please try again.', 'danger');
          });
        });

        // Handle edit subject button click
        $(document).on('click', '.btn-edit-subject', function(e) {
          e.preventDefault();
          var subjectId = $(this).data('id');
          var subjectCode = $(this).data('code');
          var subjectName = $(this).data('name');
          var lecCredits = $(this).data('lec');
          var labCredits = $(this).data('lab');
          var yearLevel = $(this).data('year');
          var semester = $(this).data('semester');

          $('#editSubjectId').val(subjectId);
          $('#editSubjectCode').val(subjectCode);
          $('#editSubjectName').val(subjectName);
          $('#editLecCredits').val(lecCredits);
          $('#editLabCredits').val(labCredits);
          $('#editYearLevel').val(yearLevel);
          $('#editSemester').val(semester);

          var editModal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
          editModal.show();
        });

        // Handle edit subject form submission
        $('#editSubjectForm').on('submit', function(e) {
          e.preventDefault();
          var formData = {
            action: 'update',
            curriculum_id: curriculumId,
            subject_id: $('#editSubjectId').val(),
            subject_code: $('#editSubjectCode').val(),
            subject_name: $('#editSubjectName').val(),
            lec_credits: $('#editLecCredits').val(),
            lab_credits: $('#editLabCredits').val(),
            year_level: $('#editYearLevel').val(),
            semester: $('#editSemester').val()
          };

          $.post(endpoint.replace('curriculum_subjects_actions.php', 'curriculum_subjects_actions.php'), formData, function(response) {
            if (response.success) {
              var modal = bootstrap.Modal.getInstance(document.getElementById('editSubjectModal'));
              modal.hide();
              loadSubjects();
              showAlert('Subject updated successfully!', 'success');
            } else {
              showAlert(response.message || 'Error updating subject.', 'danger');
            }
          }, 'json').fail(function() {
            showAlert('Error updating subject. Please try again.', 'danger');
          });
        });

        // Handle delete subject button click
        $(document).on('click', '#deleteSubjectBtn', function() {
          if (confirm('Are you sure you want to delete this subject?')) {
            var subjectId = $('#editSubjectId').val();
            var formData = {
              action: 'delete',
              subject_id: subjectId
            };

            $.post(endpoint.replace('curriculum_subjects_actions.php', 'curriculum_subjects_actions.php'), formData, function(response) {
              if (response.success) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('editSubjectModal'));
                modal.hide();
                loadSubjects();
                showAlert('Subject deleted successfully!', 'success');
              } else {
                showAlert(response.message || 'Error deleting subject.', 'danger');
              }
            }, 'json').fail(function() {
              showAlert('Error deleting subject. Please try again.', 'danger');
            });
          }
        });
      });

      function loadSubjects() {
        $.getJSON(endpoint, { action: 'get_subjects', curriculum_id: curriculumId })
          .done(function(response) {
            if (response.success) {
              renderSubjects(response.data);
            } else {
              $('#subjectsContainer').html('<div class="alert alert-danger">' + (response.message || 'Unable to load subjects.') + '</div>');
            }
          })
          .fail(function() {
            $('#subjectsContainer').html('<div class="alert alert-danger">Unable to load subjects. Please try again.</div>');
          });
      }

      function showAlert(message, type) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
          message +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>';
        $(alertHtml).prependTo('.content-page').delay(5000).fadeOut(function() { $(this).remove(); });
      }
    })(jQuery);
  </script>
</body>

</html>
