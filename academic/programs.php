<?php
$page_name = "Programs";
$activePage = 'programs';
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
                    <h5 class="card-title mb-1">Programs</h5>
                    <p class="text-muted mb-0">Manage academic programs and courses.</p>
                  </div>
                  <button id="btnAddProgram" class="btn btn-primary">Add Program</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="programTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Program Code</th>
                          <th>Program Name</th>
                          <th>Department</th>
                          <th>Created Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="programModalLabel">Add Program</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="programForm">
                  <div class="modal-body">
                    <input type="hidden" id="programId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="programCode" class="form-label">Program Code</label>
                        <input type="text" class="form-control" id="programCode" name="program_code" placeholder="e.g., BSCS" required>
                      </div>
                      <div class="col-md-12">
                        <label for="programName" class="form-label">Program Name</label>
                        <input type="text" class="form-control" id="programName" name="program_name" placeholder="e.g., Bachelor of Science in Computer Science" required>
                      </div>
                      <div class="col-md-12">
                        <label for="departmentId" class="form-label">Department</label>
                        <select class="form-select" id="departmentId" name="department_id">
                          <option value="">-- Select Department --</option>
                        </select>
                      </div>
                    </div>
                    <div id="programMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="programSaveBtn">Save</button>
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
      var endpoint = './programs_actions.php';

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function loadDepartments() {
        $.getJSON(endpoint, { action: 'departments' })
          .done(function(response) {
            if (response.success && response.data) {
              var select = $('#departmentId');
              var currentValue = select.val();
              select.html('<option value="">-- Select Department --</option>');
              
              response.data.forEach(function(dept) {
                select.append('<option value="' + dept.id + '">' + escapeHtml(dept.department_name) + '</option>');
              });
              
              if (currentValue) {
                select.val(currentValue);
              }
            }
          });
      }

      function renderTable(data) {
        var tbody = $('#programTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="6" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td><strong>' + escapeHtml(item.program_code) + '</strong></td>' +
            '<td>' + escapeHtml(item.program_name) + '</td>' +
            '<td>' + (item.department_name ? escapeHtml(item.department_name) : '<span class="badge bg-secondary">Unassigned</span>') + '</td>' +
            '<td>' + formatDate(item.created_at) + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-program" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-program" data-id="' + item.id + '">Delete</button>' +
              '</div>' +
            '</td>' +
          '</tr>';
        });

        tbody.html(rows.join(''));
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

      function showMessage(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#programMessage').html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
      }

      function loadPrograms() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
              showMessage(response.message || 'Unable to load programs.', 'error');
            }
          })
          .fail(function() {
            renderTable([]);
            showMessage('Unable to load programs. Please try again.', 'error');
          });
      }

      function resetForm() {
        $('#programId').val('');
        $('#programCode').val('');
        $('#programName').val('');
        $('#departmentId').val('');
        $('#programMessage').html('');
      }

      $(document).ready(function() {
        loadDepartments();
        loadPrograms();

        $('#btnAddProgram').on('click', function() {
          resetForm();
          $('#programModalLabel').text('Add Program');
          $('#programSaveBtn').text('Save');
          $('#programModal').modal('show');
        });

        $(document).on('click', '.btn-edit-program', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#programId').val(item.id);
                $('#programCode').val(item.program_code);
                $('#programName').val(item.program_name);
                $('#departmentId').val(item.department_id || '');
                $('#programModalLabel').text('Edit Program');
                $('#programSaveBtn').text('Update');
                $('#programModal').modal('show');
              } else {
                showMessage(response.message || 'Program not found.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to retrieve program details.', 'error');
            });
        });

        $(document).on('click', '.btn-delete-program', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this program?')) {
            $.post(endpoint, { action: 'delete', id: id })
              .done(function(response) {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                  loadPrograms();
                  showMessage(response.message || 'Program deleted successfully.', 'success');
                } else {
                  showMessage(response.message || 'Unable to delete program.', 'error');
                }
              })
              .fail(function() {
                showMessage('Unable to delete program. Please try again.', 'error');
              });
          }
        });

        $('#programForm').on('submit', function(e) {
          e.preventDefault();
          
          var id = $('#programId').val();
          var programCode = $('#programCode').val().trim();
          var programName = $('#programName').val().trim();
          var departmentId = $('#departmentId').val();

          if (!programCode || !programName) {
            showMessage('Please fill in all required fields.', 'error');
            return;
          }

          var action = id ? 'update' : 'add';
          var data = {
            action: action,
            program_code: programCode,
            program_name: programName,
            department_id: departmentId || null
          };

          if (id) {
            data.id = id;
          }

          $.post(endpoint, data)
            .done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                $('#programModal').modal('hide');
                loadPrograms();
                showMessage(response.message || 'Program saved successfully.', 'success');
              } else {
                showMessage(response.message || 'Unable to save program.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to save program. Please try again.', 'error');
            });
        });
      });
    })(jQuery);
  </script>
</body>

</html>
