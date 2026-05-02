<?php
$page_name = "Awards Criteria";
$activePage = 'awards-criteria';
$assetBasePath = '../assets';
$navBasePath = '../';
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
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title mb-1">Awards Criteria</h5>
                    <p class="text-muted mb-0">Manage awards criteria titles, school year scope, and excluded subjects for computation.</p>
                  </div>
                  <button id="btnAddCriteria" class="btn btn-primary">Add Criteria</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="criteriaTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Title</th>
                          <th>School Year</th>
                          <th>Excluded Subjects</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="criteriaModal" tabindex="-1" aria-labelledby="criteriaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="criteriaModalLabel">Add Criteria</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="criteriaForm">
                  <div class="modal-body">
                    <input type="hidden" id="criteriaId" name="id" value="">
                    <div class="mb-3">
                      <label for="criteriaTitle" class="form-label">Title</label>
                      <input type="text" class="form-control" id="criteriaTitle" name="title" placeholder="e.g., Dean's List" required>
                    </div>
                    <div class="mb-3">
                      <label for="criteriaSchoolYear" class="form-label">School Year</label>
                      <select class="form-control" id="criteriaSchoolYear" name="schoolyear_id" required>
                        <option value="">-- Select School Year --</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="criteriaExcludedSubjects" class="form-label">Excluded Subjects from Computation</label>
                      <input type="text" class="form-control" id="criteriaExcludedSubjects" name="excluded_subjects" placeholder="e.g., PE, NSTP, OJT">
                      <small class="text-muted">Comma-separated subject codes or names</small>
                    </div>
                    <div id="criteriaMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="criteriaSaveBtn">Save</button>
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
    (function($) {
      var endpoint = './awards_criteria_actions.php';

      function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
      }

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function showMessage(message, type) {
        var cls = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#criteriaMessage').html('<div class="alert ' + cls + ' mb-0">' + escapeHtml(message) + '</div>');
      }

      function loadSchoolYears(selectedId) {
        $.getJSON(endpoint, { action: 'schoolyears' }).done(function(response) {
          if (response.success && response.data) {
            var select = $('#criteriaSchoolYear');
            select.html('<option value="">-- Select School Year --</option>');
            response.data.forEach(function(sy) {
              select.append('<option value="' + sy.id + '">' + escapeHtml(sy.label) + '</option>');
            });
            if (selectedId) select.val(selectedId);
          }
        });
      }

      function renderTable(data) {
        var tbody = $('#criteriaTable tbody');
        if (!data || !data.length) {
          tbody.html('<tr><td colspan="5" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td><strong>' + escapeHtml(item.title) + '</strong></td>' +
            '<td>' + escapeHtml(item.school_year_label || '—') + '</td>' +
            '<td>' + (item.excluded_subjects ? escapeHtml(item.excluded_subjects) : '<span class="text-muted">—</span>') + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-criteria" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-criteria" data-id="' + item.id + '">Delete</button>' +
              '</div>' +
            '</td>' +
          '</tr>';
        });

        tbody.html(rows.join(''));
      }

      function loadCriteria() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data || []);
            } else {
              renderTable([]);
            }
          })
          .fail(function() {
            renderTable([]);
          });
      }

      function resetForm() {
        $('#criteriaId').val('');
        $('#criteriaTitle').val('');
        $('#criteriaExcludedSubjects').val('');
        $('#criteriaMessage').html('');
        loadSchoolYears(null);
      }

      $(document).ready(function() {
        loadCriteria();

        $('#btnAddCriteria').on('click', function() {
          resetForm();
          $('#criteriaModalLabel').text('Add Criteria');
          $('#criteriaSaveBtn').text('Save');
          $('#criteriaModal').modal('show');
        });

        $(document).on('click', '.btn-edit-criteria', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id }).done(function(response) {
            if (response.success && response.data) {
              var item = response.data;
              $('#criteriaId').val(item.id);
              $('#criteriaTitle').val(item.title);
              $('#criteriaExcludedSubjects').val(item.excluded_subjects || '');
              loadSchoolYears(item.schoolyear_id);
              $('#criteriaModalLabel').text('Edit Criteria');
              $('#criteriaSaveBtn').text('Update');
              $('#criteriaMessage').html('');
              $('#criteriaModal').modal('show');
            }
          });
        });

        $(document).on('click', '.btn-delete-criteria', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this criteria?')) {
            $.post(endpoint, { action: 'delete', id: id }).done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                loadCriteria();
              } else {
                alert(response.message || 'Unable to delete criteria.');
              }
            });
          }
        });

        $('#criteriaForm').on('submit', function(e) {
          e.preventDefault();

          var id = $('#criteriaId').val();
          var data = {
            action: id ? 'update' : 'add',
            title: $('#criteriaTitle').val().trim(),
            schoolyear_id: $('#criteriaSchoolYear').val(),
            excluded_subjects: $('#criteriaExcludedSubjects').val().trim()
          };
          if (id) data.id = id;

          if (!data.title || !data.schoolyear_id) {
            showMessage('Title and School Year are required.', 'error');
            return;
          }

          $.post(endpoint, data).done(function(response) {
            response = typeof response === 'string' ? JSON.parse(response) : response;
            if (response.success) {
              $('#criteriaModal').modal('hide');
              loadCriteria();
            } else {
              showMessage(response.message || 'Unable to save criteria.', 'error');
            }
          }).fail(function() {
            showMessage('Request failed. Please try again.', 'error');
          });
        });
      });

    })(jQuery);
  </script>
</body>
</html>
