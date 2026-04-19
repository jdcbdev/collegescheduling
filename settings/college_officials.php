<?php
$page_name = 'College Officials';
$activePage = 'college-officials';
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
                    <h5 class="card-title mb-1">College Officials</h5>
                    <p class="text-muted mb-0">Manage dean and college officials used in print forms.</p>
                  </div>
                  <button id="btnAddOfficial" class="btn btn-primary">Add Official</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="officialTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Title</th>
                          <th>Department</th>
                          <th>Dean</th>
                          <th>Created Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="officialModal" tabindex="-1" aria-labelledby="officialModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="officialModalLabel">Add College Official</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="officialForm">
                  <div class="modal-body">
                    <input type="hidden" id="officialId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="officialName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="officialName" name="name" placeholder="e.g., Juan Dela Cruz" required>
                      </div>
                      <div class="col-md-12">
                        <label for="officialTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="officialTitle" name="title" placeholder="e.g., College Secretary" required>
                      </div>
                      <div class="col-md-12">
                        <label for="officialDepartment" class="form-label">Department</label>
                        <select class="form-select" id="officialDepartment" name="department_id">
                          <option value="">Select Department</option>
                        </select>
                        <small class="text-muted">Department can be empty only if this official is marked as dean or secretary.</small>
                      </div>
                      <div class="col-md-12">
                        <div class="form-check mt-2">
                          <input class="form-check-input" type="checkbox" id="officialIsDean" name="is_dean" value="1">
                          <label class="form-check-label" for="officialIsDean">Mark as Dean</label>
                        </div>
                        <div class="form-check mt-2">
                          <input class="form-check-input" type="checkbox" id="officialIsSecretary" name="is_secretary" value="1">
                          <label class="form-check-label" for="officialIsSecretary">Mark as Secretary</label>
                        </div>
                      </div>
                    </div>
                    <div id="officialMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="officialSaveBtn">Save</button>
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
      var endpoint = './college_officials_actions.php';
      var departmentsEndpoint = './departments_actions.php';
      var cachedDepartments = [];

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var map = {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
      }

      function showMessage(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#officialMessage').html('<div class="alert ' + alertClass + ' mb-0">' + escapeHtml(message) + '</div>');
      }

      function renderTable(data) {
        var tbody = $('#officialTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          var deanBadge = Number(item.is_dean) === 1
            ? '<span class="badge bg-success">Yes</span>'
            : '<span class="badge bg-secondary">No</span>';

          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td><strong>' + escapeHtml(item.name) + '</strong></td>' +
            '<td>' + escapeHtml(item.title) + '</td>' +
            '<td>' + escapeHtml(item.department_name || '-') + '</td>' +
            '<td>' + deanBadge + '</td>' +
            '<td>' + formatDate(item.created_at) + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-official" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-official" data-id="' + item.id + '">Delete</button>' +
              '</div>' +
            '</td>' +
          '</tr>';
        });

        tbody.html(rows.join(''));
      }

      function fillDepartments(selectedValue) {
        var select = $('#officialDepartment');
        select.html('<option value="">Select Department</option>');

        cachedDepartments.forEach(function(dep) {
          var selected = String(selectedValue || '') === String(dep.id) ? ' selected' : '';
          select.append('<option value="' + dep.id + '"' + selected + '>' + escapeHtml(dep.department_name) + '</option>');
        });
      }

      function loadDepartments(callback) {
        $.getJSON(departmentsEndpoint, { action: 'list' })
          .done(function(response) {
            cachedDepartments = response && response.success && Array.isArray(response.data) ? response.data : [];
            fillDepartments('');
            if (typeof callback === 'function') callback();
          })
          .fail(function() {
            cachedDepartments = [];
            fillDepartments('');
            if (typeof callback === 'function') callback();
          });
      }

      function loadOfficials() {
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
        $('#officialId').val('');
        $('#officialName').val('');
        $('#officialTitle').val('');
        $('#officialIsDean').prop('checked', false);
        $('#officialIsSecretary').prop('checked', false);
        fillDepartments('');
        $('#officialDepartment').prop('disabled', false);
        $('#officialMessage').html('');
      }

      function syncDeanToggle() {
        var isDean = $('#officialIsDean').is(':checked');
        var isSecretary = $('#officialIsSecretary').is(':checked');
        if (isDean || isSecretary) {
          $('#officialDepartment').val('');
        }
        $('#officialDepartment').prop('disabled', isDean || isSecretary);
      }

      $(document).ready(function() {
        loadDepartments(function() {
          loadOfficials();
        });

        $('#btnAddOfficial').on('click', function() {
          resetForm();
          $('#officialModalLabel').text('Add College Official');
          $('#officialSaveBtn').text('Save');
          $('#officialModal').modal('show');
        });

        $('#officialIsDean').on('change', syncDeanToggle);
        $('#officialIsSecretary').on('change', syncDeanToggle);

        $(document).on('click', '.btn-edit-official', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#officialId').val(item.id);
                $('#officialName').val(item.name || '');
                $('#officialTitle').val(item.title || '');
                $('#officialIsDean').prop('checked', Number(item.is_dean) === 1);
                $('#officialIsSecretary').prop('checked', Number(item.is_secretary) === 1);
                fillDepartments(item.department_id || '');
                syncDeanToggle();
                $('#officialModalLabel').text('Edit College Official');
                $('#officialSaveBtn').text('Update');
                $('#officialMessage').html('');
                $('#officialModal').modal('show');
              } else {
                showMessage(response.message || 'Official not found.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to retrieve official details.', 'error');
            });
        });

        $(document).on('click', '.btn-delete-official', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this official?')) {
            $.post(endpoint, { action: 'delete', id: id })
              .done(function(response) {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                  loadOfficials();
                } else {
                  showMessage(response.message || 'Unable to delete official.', 'error');
                }
              })
              .fail(function() {
                showMessage('Unable to delete official. Please try again.', 'error');
              });
          }
        });

        $('#officialForm').on('submit', function(e) {
          e.preventDefault();

          var id = $('#officialId').val();
          var name = $('#officialName').val().trim();
          var title = $('#officialTitle').val().trim();
          var isDean = $('#officialIsDean').is(':checked') ? 1 : 0;
          var isSecretary = $('#officialIsSecretary').is(':checked') ? 1 : 0;
          var departmentId = $('#officialDepartment').val();

          if (!name || !title) {
            showMessage('Please provide name and title.', 'error');
            return;
          }

          if (!isDean && !isSecretary && !departmentId) {
            showMessage('Please select a department unless official is marked as dean or secretary.', 'error');
            return;
          }

          var action = id ? 'update' : 'add';
          var data = {
            action: action,
            name: name,
            title: title,
            is_dean: isDean,
            is_secretary: isSecretary,
            department_id: isDean ? '' : departmentId
          };

          if (id) {
            data.id = id;
          }

          $.post(endpoint, data)
            .done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                $('#officialModal').modal('hide');
                loadOfficials();
              } else {
                showMessage(response.message || 'Unable to save official.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to save official. Please try again.', 'error');
            });
        });
      });
    })(jQuery);
  </script>
</body>
</html>
