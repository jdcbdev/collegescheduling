<?php
$page_name = "Departments";
$activePage = 'departments';
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
                    <h5 class="card-title mb-1">Departments</h5>
                    <p class="text-muted mb-0">Manage academic departments and their information.</p>
                  </div>
                  <button id="btnAddDepartment" class="btn btn-primary">Add Department</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="departmentTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Department Code</th>
                          <th>Department Name</th>
                          <th>Created Date</th>
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

          <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="departmentModalLabel">Add Department</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="departmentForm">
                  <div class="modal-body">
                    <input type="hidden" id="departmentId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="departmentCode" class="form-label">Department Code</label>
                        <input type="text" class="form-control" id="departmentCode" name="department_code" placeholder="e.g., CSC" required>
                      </div>
                      <div class="col-md-12">
                        <label for="departmentName" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="departmentName" name="department_name" placeholder="e.g., Computer Science" required>
                      </div>
                    </div>
                    <div id="departmentMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="departmentSaveBtn">Save</button>
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
      var endpoint = './departments_actions.php';

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function renderTable(data) {
        var tbody = $('#departmentTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="5" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td><strong>' + escapeHtml(item.department_code) + '</strong></td>' +
            '<td>' + escapeHtml(item.department_name) + '</td>' +
            '<td>' + formatDate(item.created_at) + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-department" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-department" data-id="' + item.id + '">Delete</button>' +
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
        $('#departmentMessage').html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
      }

      function loadDepartments() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
              showMessage(response.message || 'Unable to load departments.', 'error');
            }
          })
          .fail(function() {
            renderTable([]);
            showMessage('Unable to load departments. Please try again.', 'error');
          });
      }

      function resetForm() {
        $('#departmentId').val('');
        $('#departmentCode').val('');
        $('#departmentName').val('');
        $('#departmentMessage').html('');
      }

      $(document).ready(function() {
        loadDepartments();

        $('#btnAddDepartment').on('click', function() {
          resetForm();
          $('#departmentModalLabel').text('Add Department');
          $('#departmentSaveBtn').text('Save');
          $('#departmentModal').modal('show');
        });

        $(document).on('click', '.btn-edit-department', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#departmentId').val(item.id);
                $('#departmentCode').val(item.department_code);
                $('#departmentName').val(item.department_name);
                $('#departmentModalLabel').text('Edit Department');
                $('#departmentSaveBtn').text('Update');
                $('#departmentModal').modal('show');
              } else {
                showMessage(response.message || 'Department not found.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to retrieve department details.', 'error');
            });
        });

        $(document).on('click', '.btn-delete-department', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this department?')) {
            $.post(endpoint, { action: 'delete', id: id })
              .done(function(response) {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                  loadDepartments();
                  showMessage(response.message || 'Department deleted successfully.', 'success');
                } else {
                  showMessage(response.message || 'Unable to delete department.', 'error');
                }
              })
              .fail(function() {
                showMessage('Unable to delete department. Please try again.', 'error');
              });
          }
        });

        $('#departmentForm').on('submit', function(e) {
          e.preventDefault();
          
          var id = $('#departmentId').val();
          var departmentCode = $('#departmentCode').val().trim();
          var departmentName = $('#departmentName').val().trim();

          if (!departmentCode || !departmentName) {
            showMessage('Please fill in all required fields.', 'error');
            return;
          }

          var action = id ? 'update' : 'add';
          var data = {
            action: action,
            department_code: departmentCode,
            department_name: departmentName
          };

          if (id) {
            data.id = id;
          }

          $.post(endpoint, data)
            .done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                $('#departmentModal').modal('hide');
                loadDepartments();
                showMessage(response.message || 'Department saved successfully.', 'success');
              } else {
                showMessage(response.message || 'Unable to save department.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to save department. Please try again.', 'error');
            });
        });
      });
    })(jQuery);
  </script>
</body>

</html>
