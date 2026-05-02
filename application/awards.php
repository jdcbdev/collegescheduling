<?php
$page_name    = "Awards Application";
$activePage   = 'awards';
$assetBasePath = '../assets';
$navBasePath  = '../';

require_once __DIR__ . '/../classes/SchoolYear.php';
require_once __DIR__ . '/../classes/CollegeOfficial.php';

$syObj        = new SchoolYear();
$activeSY     = $syObj->getActiveSchoolYear();
$semLabels    = [1 => '1st Sem', 2 => '2nd Sem', 3 => 'Summer'];
$syLabel      = $activeSY
    ? $activeSY['start_year'] . '-' . $activeSY['end_year'] . ' ' . ($semLabels[$activeSY['semester']] ?? 'Sem ' . $activeSY['semester'])
    : 'No Active School Year';

$coObj      = new CollegeOfficial();
$officials  = $coObj->getAllOfficials();
$secretary  = null;
foreach ($officials as $o) {
    if ($o['is_secretary']) { $secretary = $o; break; }
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

          <!-- Page Header -->
          <div class="row">
            <div class="col-12">
              <div class="card mb-4">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                      <h5 class="card-title mb-1">Awards Application</h5>
                      <p class="text-muted mb-0">Manage academic award applicants.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <div style="min-width:180px;">
                        <select id="filterSchoolYear" class="form-select">
                          <option value="">-- All School Years --</option>
                        </select>
                      </div>
                      <div style="min-width:160px;">
                        <select id="filterProgram" class="form-select">
                          <option value="">-- All Programs --</option>
                        </select>
                      </div>
                      <button id="btnAddApplicant" class="btn btn-primary">Add Applicant</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">

                  <div class="table-responsive">
                    <table id="applicantTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Student No.</th>
                          <th>Program</th>
                          <th>Curriculum</th>
                          <th>School Year</th>
                          <th>GWA</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Add / Edit Modal -->
          <div class="modal fade" id="applicantModal" tabindex="-1" aria-labelledby="applicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="applicantModalLabel">Add Applicant</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="applicantForm">
                  <div class="modal-body">
                    <input type="hidden" id="applicantId" name="id" value="">
                    <div class="mb-3">
                      <label for="studentNo" class="form-label">Student No. <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="studentNo" name="student_no" placeholder="e.g., 2021-00001" required>
                    </div>
                    <div class="mb-3">
                      <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="lastName" name="ln" placeholder="Last name" required>
                    </div>
                    <div class="mb-3">
                      <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="firstName" name="fn" placeholder="First name" required>
                    </div>
                    <div class="mb-3">
                      <label for="middleName" class="form-label">Middle Name</label>
                      <input type="text" class="form-control" id="middleName" name="mn" placeholder="Middle name (optional)">
                    </div>
                    <div class="mb-3">
                      <label for="programId" class="form-label">Program <span class="text-danger">*</span></label>
                      <select class="form-control" id="programId" name="program_id" required>
                        <option value="">-- Select Program --</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="curriculumId" class="form-label">Curriculum <span class="text-danger">*</span></label>
                      <select class="form-control" id="curriculumId" name="curriculum_id" required>
                        <option value="">-- Select Curriculum --</option>
                      </select>
                    </div>
                    <div id="applicantMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="applicantSaveBtn">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
  <script>
    (function ($) {
      var endpoint = './awards_actions.php';

      function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
      }

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      /* ---------- Programs ---------- */
      function loadPrograms(callback) {
        $.getJSON(endpoint, { action: 'programs' }).done(function (response) {
          if (response.success && response.data) {
            var filterSelect = $('#filterProgram');
            var modalSelect  = $('#programId');
            var currentFilter = filterSelect.val();

            filterSelect.html('<option value="">-- All Programs --</option>');
            modalSelect.html('<option value="">-- Select Program --</option>');

            response.data.forEach(function (p) {
              filterSelect.append('<option value="' + p.id + '">' + escapeHtml(p.program_code) + '</option>');
              modalSelect.append('<option value="' + p.id + '">' + escapeHtml(p.program_code) + ' - ' + escapeHtml(p.program_name) + '</option>');
            });

            filterSelect.val(currentFilter);
            if (typeof callback === 'function') callback();
          }
        });
      }

      /* ---------- Curricula ---------- */
      function loadCurricula(programId, selectedId) {
        var select = $('#curriculumId');
        select.html('<option value="">-- Select Curriculum --</option>');
        if (!programId) return;

        $.getJSON(endpoint, { action: 'curricula', program_id: programId }).done(function (response) {
          if (response.success && response.data) {
            response.data.forEach(function (c) {
              select.append('<option value="' + c.id + '">' + escapeHtml(c.curriculum_years) + '</option>');
            });
            if (selectedId) select.val(selectedId);
          }
        });
      }

      /* ---------- Table ---------- */
      function renderTable(data) {
        var tbody = $('#applicantTable tbody');
        if (!data || !data.length) {
          tbody.html('<tr><td colspan="8" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function (item, index) {
          var badgeColors = ['bg-light-info text-info','bg-light-success text-success','bg-light-warning text-warning','bg-light-danger text-danger','bg-light-primary text-primary','bg-light-secondary text-secondary'];
          var badgeClass = item.program_id ? badgeColors[(item.program_id - 1) % badgeColors.length] : 'bg-secondary text-white';

          var syDisplay = item.school_year_label
            ? escapeHtml(item.school_year_label)
            : '<span class="text-muted">—</span>';

          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + escapeHtml(item.ln) + ', ' + escapeHtml(item.fn) + (item.mn ? ' ' + escapeHtml(item.mn) : '') + '</td>' +
            '<td><strong>' + escapeHtml(item.student_no) + '</strong></td>' +
            '<td>' +
              (item.program_code
                ? '<span class="badge ' + badgeClass + '">' + escapeHtml(item.program_code) + '</span>'
                : '<span class="badge bg-secondary text-white">Unassigned</span>') +
            '</td>' +
            '<td>' + (item.curriculum_years ? escapeHtml(item.curriculum_years) : '—') + '</td>' +
            '<td>' + syDisplay + '</td>' +
            '<td>' + (item.gwa ? escapeHtml(item.gwa) : '<span class="text-muted fst-italic">Not computed</span>') + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-applicant" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-applicant" data-id="' + item.id + '">Delete</button>' +
              '</div>' +
            '</td>' +
          '</tr>';
        });

        tbody.html(rows.join(''));
      }

      /* ---------- School Years ---------- */
      function loadSchoolYears(callback) {
        $.getJSON(endpoint, { action: 'schoolyears' }).done(function (response) {
          if (response.success && response.data) {
            var select = $('#filterSchoolYear');
            var activeSyId = <?= $activeSY ? (int)$activeSY['id'] : 'null' ?>;
            select.html('<option value="">-- All School Years --</option>');
            response.data.forEach(function (sy) {
              var label = sy.start_year + '-' + sy.end_year + ' ' + (sy.semester == 1 ? '1st Sem' : sy.semester == 2 ? '2nd Sem' : 'Summer');
              select.append('<option value="' + sy.id + '">' + escapeHtml(label) + '</option>');
            });
            if (activeSyId) select.val(activeSyId);
          }
          if (typeof callback === 'function') callback();
        });
      }

      function loadApplicants() {
        var programId    = $('#filterProgram').val();
        var schoolyearId = $('#filterSchoolYear').val();
        $.getJSON(endpoint, { action: 'list', program_id: programId, schoolyear_id: schoolyearId })
          .done(function (response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
            }
          })
          .fail(function () {
            renderTable([]);
          });
      }

      /* ---------- Messages ---------- */
      function showMessage(message, type) {
        var cls = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#applicantMessage').html('<div class="alert ' + cls + ' mb-0">' + escapeHtml(message) + '</div>');
      }

      function resetForm() {
        $('#applicantId').val('');
        $('#studentNo').val('');
        $('#firstName').val('');
        $('#middleName').val('');
        $('#lastName').val('');
        $('#programId').val('');
        $('#curriculumId').html('<option value="">-- Select Curriculum --</option>');
        $('#applicantMessage').html('');
      }

      /* ---------- Event: Program change in modal ---------- */
      $('#programId').on('change', function () {
        loadCurricula($(this).val(), null);
      });

      /* ---------- Event: Filter change ---------- */
      $('#filterProgram, #filterSchoolYear').on('change', function () {
        loadApplicants();
      });

      /* ---------- Event: Add button ---------- */
      $('#btnAddApplicant').on('click', function () {
        resetForm();
        $('#applicantModalLabel').text('Add Applicant');
        $('#applicantSaveBtn').text('Save');
        $('#applicantModal').modal('show');
      });

      /* ---------- Event: Edit button ---------- */
      $(document).on('click', '.btn-edit-applicant', function () {
        var id = $(this).data('id');
        $.getJSON(endpoint, { action: 'get', id: id })
          .done(function (response) {
            if (response.success && response.data) {
              var item = response.data;
              resetForm();
              $('#applicantId').val(item.id);
              $('#studentNo').val(item.student_no);
              $('#firstName').val(item.fn);
              $('#middleName').val(item.mn || '');
              $('#lastName').val(item.ln);
              $('#programId').val(item.program_id);
              loadCurricula(item.program_id, item.curriculum_id);
              $('#applicantModalLabel').text('Edit Applicant');
              $('#applicantSaveBtn').text('Update');
              $('#applicantModal').modal('show');
            } else {
              alert(response.message || 'Applicant not found.');
            }
          });
      });

      /* ---------- Event: Delete button ---------- */
      $(document).on('click', '.btn-delete-applicant', function () {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this applicant?')) {
          $.post(endpoint, { action: 'delete', id: id })
            .done(function (response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                loadApplicants();
              } else {
                alert(response.message || 'Unable to delete applicant.');
              }
            });
        }
      });

      /* ---------- Event: Form submit ---------- */
      $('#applicantForm').on('submit', function (e) {
        e.preventDefault();

        var id         = $('#applicantId').val();
        var studentNo  = $('#studentNo').val().trim();
        var fn         = $('#firstName').val().trim();
        var mn         = $('#middleName').val().trim();
        var ln         = $('#lastName').val().trim();
        var programId  = $('#programId').val();
        var currId     = $('#curriculumId').val();

        if (!studentNo || !fn || !ln || !programId || !currId) {
          showMessage('Please fill in all required fields.', 'error');
          return;
        }

        var data = {
          action:        id ? 'update' : 'add',
          student_no:    studentNo,
          fn:            fn,
          mn:            mn,
          ln:            ln,
          program_id:    programId,
          curriculum_id: currId
        };
        if (id) data.id = id;

        $.post(endpoint, data)
          .done(function (response) {
            response = typeof response === 'string' ? JSON.parse(response) : response;
            if (response.success) {
              $('#applicantModal').modal('hide');
              loadApplicants();
            } else {
              showMessage(response.message || 'An error occurred.', 'error');
            }
          })
          .fail(function () {
            showMessage('Unable to save applicant. Please try again.', 'error');
          });
      });

      /* ---------- Init ---------- */
      $(document).ready(function () {
        loadSchoolYears(function () {
          loadPrograms(function () {
            loadApplicants();
          });
        });
      });

    })(jQuery);
  </script>
</body>
</html>
