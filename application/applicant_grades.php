<?php
$page_name = "Applicant Grades";
$activePage = 'awards';
$assetBasePath = '../assets';
$navBasePath = '../';

require_once __DIR__ . '/../classes/Applicant.php';

$applicantId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($applicantId <= 0) {
    die('Invalid applicant ID');
}

$appObj = new Applicant();
$applicant = $appObj->getApplicantById($applicantId);
if (!$applicant) {
    die('Applicant not found');
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
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-1">Applicant Grades</h5>
                      <p class="text-muted mb-0">
                        <strong><?php echo htmlspecialchars($applicant['ln'] . ', ' . $applicant['fn'] . ($applicant['mn'] ? ' ' . $applicant['mn'] : '')); ?></strong><br>
                        <?php echo htmlspecialchars($applicant['student_no']); ?> • <?php echo htmlspecialchars($applicant['program_code'] ?: 'N/A'); ?> • Curriculum <?php echo htmlspecialchars($applicant['curriculum_years'] ?: 'N/A'); ?>
                      </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                      <div id="overallSummary"></div>
                      <button type="button" class="btn btn-primary" id="btnSaveAll">Save Grades</button>
                      <a href="./awards.php" class="btn btn-primary btn-cancel-all">Back</a>
                    </div>
                  </div>
                  <div id="saveMessage" class="mt-3"></div>
                </div>
              </div>
            </div>
          </div>

          <div id="subjectsContainer">
            <div class="text-center text-muted p-4">Loading subjects...</div>
          </div>

          <div class="mt-3 d-flex align-items-center justify-content-end gap-2 flex-wrap">
            <div id="overallSummaryBottom"></div>
            <button type="button" class="btn btn-primary" id="btnSaveAllBottom">Save Grades</button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
  <script>
    (function ($) {
      var applicantId  = <?php echo (int) $applicantId; ?>;
      var curriculumId = <?php echo (int) $applicant['curriculum_id']; ?>;
      var endpoint     = './grades_actions.php';

      function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
      }

      function isValidGrade(gradeRaw) {
        var g = String(gradeRaw || '').trim().toUpperCase();
        if (!g) return true;
        if (g === 'INC') return true;
        var num = Number(g);
        if (isNaN(num)) return false;
        var scaled = Math.round(num * 100);
        var passRange = scaled >= 100 && scaled <= 300 && scaled % 25 === 0;
        var fail = scaled === 500;
        return passRange || fail;
      }

      function parseNumericGrade(gradeRaw) {
        var g = String(gradeRaw || '').trim().toUpperCase();
        if (!g || g === 'INC') return null;
        if (!isValidGrade(g)) return null;
        var num = Number(g);
        return isNaN(num) ? null : num;
      }

      function format2(num) {
        return (Math.round(num * 100) / 100).toFixed(2);
      }

      function loadSubjects() {
        $.getJSON(endpoint, { action: 'get_subjects', applicant_id: applicantId, curriculum_id: curriculumId })
          .done(function (response) {
            if (response.success) {
              renderSubjects(response.data || []);
            } else {
              $('#subjectsContainer').html('<div class="alert alert-danger">' + escapeHtml(response.message || 'Failed to load subjects.') + '</div>');
            }
          })
          .fail(function () {
            $('#subjectsContainer').html('<div class="alert alert-danger">Request failed while loading subjects.</div>');
          });
      }

      function renderSubjects(data) {
        var container = $('#subjectsContainer');

        if (!data.length) {
          container.html('<div class="alert alert-info">No subjects found for this curriculum.</div>');
          return;
        }

        var grouped = {};
        data.forEach(function (subject) {
          var yearLevel = subject.year_level;
          if (!grouped[yearLevel]) grouped[yearLevel] = {};
          var semester = subject.semester;
          if (!grouped[yearLevel][semester]) grouped[yearLevel][semester] = [];
          grouped[yearLevel][semester].push(subject);
        });

        var html = '<form id="gradesForm">';
        var yearLevels = Object.keys(grouped).sort(function(a, b) { return parseInt(a) - parseInt(b); });

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

          var sem1Subjects = grouped[yearLevel]['1'] || [];
          var sem2Subjects = grouped[yearLevel]['2'] || [];
          var sem3Subjects = grouped[yearLevel]['3'] || [];

          var maxSemRows = Math.max(sem1Subjects.length, sem2Subjects.length);

          function buildSemesterTable(title, subjects, targetRows) {
            var shtml = '<div class="col-md-6">';
            shtml += '<h6 class="mb-3 text-center">' + title + '</h6>';
            if (!subjects.length) {
              shtml += '<div class="text-muted text-center p-3"><small>No subjects</small></div>';
              shtml += '</div>';
              return shtml;
            }
            shtml += '<div class="table-responsive">';
            shtml += '<table class="table table-sm table-hover align-middle grades-table">';
            shtml += '<thead><tr><th>Code</th><th>Subject</th><th class="text-center">Units</th><th style="width:120px;">Grade</th><th style="width:130px;">Units x GWA</th></tr></thead><tbody>';
            subjects.forEach(function (s) {
              var gradeVal = s.grade ? escapeHtml(s.grade) : '';
              var unitsNum = Number(s.total_credits) || 0;
              var gradeNum = parseNumericGrade(s.grade);
              var weighted = gradeNum !== null ? (unitsNum * gradeNum) : null;
              shtml += '<tr>';
              shtml += '<td><strong>' + escapeHtml(s.subject_code) + '</strong></td>';
              shtml += '<td>' + escapeHtml(s.subject_name) + '</td>';
              shtml += '<td class="text-center">' + escapeHtml(s.total_credits) + '</td>';
              shtml += '<td><input type="text" class="form-control form-control-sm grade-input" style="max-width:110px;" name="subjects[' + s.subject_id + '][grade]" value="' + gradeVal + '" data-subject-id="' + s.subject_id + '" data-units="' + escapeHtml(s.total_credits) + '"></td>';
              shtml += '<td class="text-center units-x-gwa-cell">' + (weighted !== null ? '<strong>' + format2(weighted) + '</strong>' : '—') + '</td>';
              shtml += '</tr>';
            });

            for (var i = subjects.length; i < targetRows; i++) {
              shtml += '<tr class="empty-row">';
              shtml += '<td>&nbsp;</td>';
              shtml += '<td>&nbsp;</td>';
              shtml += '<td class="text-center">&nbsp;</td>';
              shtml += '<td>&nbsp;</td>';
              shtml += '<td>&nbsp;</td>';
              shtml += '</tr>';
            }

            shtml += '<tr class="table-light subtotal-row">';
            shtml += '<td colspan="2" class="fw-bold text-end">Subtotal</td>';
            shtml += '<td class="text-center fw-bold subtotal-units">0</td>';
            shtml += '<td></td>';
            shtml += '<td class="text-center fw-bold subtotal-weighted">0.00</td>';
            shtml += '</tr>';
            shtml += '</tbody></table></div></div>';
            return shtml;
          }

          html += buildSemesterTable('1st Semester', sem1Subjects, maxSemRows);
          html += buildSemesterTable('2nd Semester', sem2Subjects, maxSemRows);

          if (sem3Subjects.length) {
            html += '<div class="col-md-6 mx-auto">';
            html += '<h6 class="mb-3 text-center">Summer</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-hover align-middle grades-table">';
            html += '<thead><tr><th>Code</th><th>Subject</th><th class="text-center">Units</th><th style="width:120px;">Grade</th><th style="width:130px;">Units x GWA</th></tr></thead><tbody>';
            sem3Subjects.forEach(function (s) {
              var gradeVal = s.grade ? escapeHtml(s.grade) : '';
              var unitsNum = Number(s.total_credits) || 0;
              var gradeNum = parseNumericGrade(s.grade);
              var weighted = gradeNum !== null ? (unitsNum * gradeNum) : null;
              html += '<tr>';
              html += '<td><strong>' + escapeHtml(s.subject_code) + '</strong></td>';
              html += '<td>' + escapeHtml(s.subject_name) + '</td>';
              html += '<td class="text-center">' + escapeHtml(s.total_credits) + '</td>';
              html += '<td><input type="text" class="form-control form-control-sm grade-input" style="max-width:110px;" name="subjects[' + s.subject_id + '][grade]" value="' + gradeVal + '" data-subject-id="' + s.subject_id + '" data-units="' + escapeHtml(s.total_credits) + '"></td>';
              html += '<td class="text-center units-x-gwa-cell">' + (weighted !== null ? '<strong>' + format2(weighted) + '</strong>' : '—') + '</td>';
              html += '</tr>';
            });
            html += '<tr class="table-light subtotal-row">';
            html += '<td colspan="2" class="fw-bold text-end">Subtotal</td>';
            html += '<td class="text-center fw-bold subtotal-units">0</td>';
            html += '<td></td>';
            html += '<td class="text-center fw-bold subtotal-weighted">0.00</td>';
            html += '</tr>';
            html += '</tbody></table></div></div>';
          }

          html += '</div></div></div>';
        });

        html += '</form>';
        container.html(html);

        recomputeAllTables();
      }

      function recomputeAllTables() {
        var grandUnits = 0;
        var grandWeighted = 0;

        $('.grades-table').each(function () {
          var table = $(this);
          var subtotalUnits = 0;
          var subtotalWeighted = 0;

          table.find('.grade-input').each(function () {
            var input = $(this);
            var units = Number(input.data('units')) || 0;
            var gradeNum = parseNumericGrade(input.val());
            var cell = input.closest('tr').find('.units-x-gwa-cell');

            subtotalUnits += units;
            grandUnits += units;

            if (gradeNum !== null) {
              var weighted = units * gradeNum;
              subtotalWeighted += weighted;
              grandWeighted += weighted;
              cell.html('<strong>' + format2(weighted) + '</strong>');
            } else {
              cell.text('—');
            }
          });

          table.find('.subtotal-units').text(subtotalUnits);
          table.find('.subtotal-weighted').text(format2(subtotalWeighted));
        });

        var gwaComputed = grandUnits > 0 ? (grandWeighted / grandUnits) : null;
        var summaryHtml =
          '<span class="badge bg-light-primary text-primary me-2">Total Units: ' + grandUnits + '</span>' +
          '<span class="badge bg-light-info text-info me-2">Total Units x GWA: ' + format2(grandWeighted) + '</span>' +
          '<span class="badge bg-light-success text-success">Computed GWA: ' + (gwaComputed !== null ? format2(gwaComputed) : 'Not computed') + '</span>';

        $('#overallSummary').html(summaryHtml);
        $('#overallSummaryBottom').html(summaryHtml);
      }

      $(document).on('input', '.grade-input', function () {
        var value = $(this).val().trim().toUpperCase();
        if (value === 'INC') {
          $(this).val('INC');
        }

        if (!isValidGrade($(this).val())) {
          $(this).addClass('is-invalid');
        } else {
          $(this).removeClass('is-invalid');
        }

        recomputeAllTables();
      });

      $(document).on('keydown', '.grade-input', function (e) {
        if (e.key !== 'Tab') return;

        var gradeInputs = $('.grade-input:visible');
        var currentIndex = gradeInputs.index(this);
        if (currentIndex === -1) return;

        if (!e.shiftKey) {
          e.preventDefault();
          var next = gradeInputs.eq(currentIndex + 1);
          if (next.length) next.focus().select();
        } else {
          e.preventDefault();
          var prev = gradeInputs.eq(currentIndex - 1);
          if (prev.length) prev.focus().select();
        }
      });

      function saveGrades() {
        var invalid = false;
        $('.grade-input').each(function () {
          var val = $(this).val();
          var upper = String(val || '').trim().toUpperCase();
          if (upper === 'INC') {
            $(this).val('INC');
          }

          if (!isValidGrade(val)) {
            invalid = true;
            $(this).addClass('is-invalid');
          } else {
            $(this).removeClass('is-invalid');
          }
        });

        if (invalid) {
          $('#saveMessage').html('<div class="alert alert-danger mb-0">Invalid grade found. Allowed values: 1.00-3.00 (0.25 interval), 5.00, or INC.</div>');
          return;
        }

        var payload = $('#gradesForm').serializeArray();
        payload.push({ name: 'action', value: 'save' });
        payload.push({ name: 'applicant_id', value: applicantId });

        var btn = $('#btnSaveAll').prop('disabled', true).text('Saving...');
        $.post(endpoint, $.param(payload))
          .done(function (response) {
            response = typeof response === 'string' ? JSON.parse(response) : response;
            if (response.success) {
              var gwa = response.data && response.data.gwa !== null ? response.data.gwa : 'Not computed';
              $('#saveMessage').html('<div class="alert alert-success mb-0">Saved successfully. Current GWA: ' + escapeHtml(String(gwa)) + '</div>');
              loadSubjects();
            } else {
              $('#saveMessage').html('<div class="alert alert-danger mb-0">' + escapeHtml(response.message || 'Failed to save grades.') + '</div>');
            }
          })
          .fail(function () {
            $('#saveMessage').html('<div class="alert alert-danger mb-0">Request failed while saving grades.</div>');
          })
          .always(function () {
            btn.prop('disabled', false).text('Save Grades');
          });
      }

      $('#btnSaveAll, #btnSaveAllBottom').on('click', saveGrades);

      $(document).ready(function () {
        loadSubjects();
      });

    })(jQuery);
  </script>
</body>
</html>
