<?php
$page_name = "Instructors";
$activePage = 'instructors';
$assetBasePath = '../assets';
$navBasePath = '../';

require_once __DIR__ . '/../classes/Instructor.php';

$instructor = new Instructor();
$departments = $instructor->getDepartments();
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
                      <h5 class="card-title mb-1">Instructors</h5>
                      <p class="text-muted mb-0">Manage faculty and instructors</p>
                    </div>
                    <div>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">Add Instructor</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover" id="instructorsTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Department</th>
                          <th>Specialization</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="instructorsList">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Add Instructor Modal -->
  <div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addInstructorModalLabel">Add Instructor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addInstructorForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="instructorCode" class="form-label">Instructor Code</label>
                  <input type="text" class="form-control" id="instructorCode" name="instructor_code" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="department" class="form-label">Department</label>
                  <select class="form-control" id="department" name="department_id" required>
                    <option value="">Select department</option>
                    <?php foreach($departments as $dept): ?>
                      <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="firstname" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="middlename" class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="middlename" name="middlename">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="lastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="phone" name="phone">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="specialization" class="form-label">Specialization</label>
              <input type="text" class="form-control" id="specialization" name="specialization">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Instructor</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Instructor Modal -->
  <div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editInstructorModalLabel">Edit Instructor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editInstructorForm">
          <div class="modal-body">
            <input type="hidden" id="editInstructorId" name="instructor_id">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editInstructorCode" class="form-label">Instructor Code</label>
                  <input type="text" class="form-control" id="editInstructorCode" name="instructor_code" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editDepartment" class="form-label">Department</label>
                  <select class="form-control" id="editDepartment" name="department_id" required>
                    <option value="">Select department</option>
                    <?php foreach($departments as $dept): ?>
                      <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editFirstname" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="editFirstname" name="firstname" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editMiddlename" class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="editMiddlename" name="middlename">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="editLastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="editLastname" name="lastname" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editEmail" class="form-label">Email</label>
                  <input type="email" class="form-control" id="editEmail" name="email">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editPhone" class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="editPhone" name="phone">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="editSpecialization" class="form-label">Specialization</label>
              <input type="text" class="form-control" id="editSpecialization" name="specialization">
            </div>
            <div class="mb-3">
              <label for="editActiveStatus" class="form-label">Status</label>
              <select class="form-control" id="editActiveStatus" name="active_status" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="deleteInstructorBtn">Delete</button>
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Instructor</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
  <script>
    (function($) {
      var endpoint = './instructors_actions.php';

      function renderInstructors(data) {
        var tbody = $('#instructorsList');
        tbody.empty();

        if (!data.length) {
          tbody.html('<tr><td colspan="9" class="text-center text-muted">No instructors found.</td></tr>');
          return;
        }

        data.forEach(function(instructor, index) {
          var statusBadge = instructor.active_status ? '<span class="badge bg-success my-bg-color">Active</span>' : '<span class="badge bg-secondary my-bg-color-gray">Inactive</span>';
          var row = '<tr>' +            '<td>' + (index + 1) + '</td>' +            '<td><strong>' + escapeHtml(instructor.instructor_code) + '</strong></td>' +
            '<td>' + escapeHtml(instructor.firstname + (instructor.middlename ? ' ' + instructor.middlename : '') + ' ' + instructor.lastname) + '</td>' +
            '<td>' + (instructor.email ? escapeHtml(instructor.email) : '-') + '</td>' +
            '<td>' + (instructor.phone ? escapeHtml(instructor.phone) : '-') + '</td>' +
            '<td>' + (instructor.department_name ? escapeHtml(instructor.department_name) : '-') + '</td>' +
            '<td>' + (instructor.specialization ? escapeHtml(instructor.specialization) : '-') + '</td>' +
            '<td>' + statusBadge + '</td>' +
            '<td>' +
            '<div class="d-flex gap-2 align-items-center">' +
            '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-instructor" data-id="' + instructor.id + '" data-code="' + escapeHtml(instructor.instructor_code) + '" data-firstname="' + escapeHtml(instructor.firstname) + '" data-middlename="' + escapeHtml(instructor.middlename || '') + '" data-lastname="' + escapeHtml(instructor.lastname) + '" data-email="' + escapeHtml(instructor.email || '') + '" data-phone="' + escapeHtml(instructor.phone || '') + '" data-department="' + (instructor.department_id || '') + '" data-specialization="' + escapeHtml(instructor.specialization || '') + '" data-status="' + (instructor.active_status ? 1 : 0) + '">Edit</button>' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-inline-instructor" data-id="' + instructor.id + '">Delete</button>' +
            '</div>' +
            '</td>' +
            '</tr>';
          tbody.append(row);
        });
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
        loadInstructors();

        // Handle add instructor form submission
        $('#addInstructorForm').on('submit', function(e) {
          e.preventDefault();
          var formData = {
            action: 'add',
            instructor_code: $('#instructorCode').val(),
            firstname: $('#firstname').val(),
            middlename: $('#middlename').val(),
            lastname: $('#lastname').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            department_id: $('#department').val(),
            specialization: $('#specialization').val()
          };

          $.post(endpoint, formData, function(response) {
            if (response.success) {
              var modal = bootstrap.Modal.getInstance(document.getElementById('addInstructorModal'));
              modal.hide();
              $('#addInstructorForm')[0].reset();
              loadInstructors();
              showAlert('Instructor added successfully!', 'success');
            } else {
              showAlert(response.message || 'Error adding instructor.', 'danger');
            }
          }, 'json').fail(function() {
            showAlert('Error adding instructor. Please try again.', 'danger');
          });
        });

        // Handle edit instructor button click
        $(document).on('click', '.btn-edit-instructor', function(e) {
          e.preventDefault();
          var instructorId = $(this).data('id');
          var instructorCode = $(this).data('code');
          var firstname = $(this).data('firstname');
          var middlename = $(this).data('middlename');
          var lastname = $(this).data('lastname');
          var email = $(this).data('email');
          var phone = $(this).data('phone');
          var department = $(this).data('department');
          var specialization = $(this).data('specialization');
          var status = $(this).data('status');

          $('#editInstructorId').val(instructorId);
          $('#editInstructorCode').val(instructorCode);
          $('#editFirstname').val(firstname);
          $('#editMiddlename').val(middlename);
          $('#editLastname').val(lastname);
          $('#editEmail').val(email);
          $('#editPhone').val(phone);
          $('#editDepartment').val(department);
          $('#editSpecialization').val(specialization);
          $('#editActiveStatus').val(status);

          var editModal = new bootstrap.Modal(document.getElementById('editInstructorModal'));
          editModal.show();
        });

        // Handle edit instructor form submission
        $('#editInstructorForm').on('submit', function(e) {
          e.preventDefault();
          var formData = {
            action: 'update',
            instructor_id: $('#editInstructorId').val(),
            instructor_code: $('#editInstructorCode').val(),
            firstname: $('#editFirstname').val(),
            middlename: $('#editMiddlename').val(),
            lastname: $('#editLastname').val(),
            email: $('#editEmail').val(),
            phone: $('#editPhone').val(),
            department_id: $('#editDepartment').val(),
            specialization: $('#editSpecialization').val(),
            active_status: $('#editActiveStatus').val()
          };

          $.post(endpoint, formData, function(response) {
            if (response.success) {
              var modal = bootstrap.Modal.getInstance(document.getElementById('editInstructorModal'));
              modal.hide();
              loadInstructors();
              showAlert('Instructor updated successfully!', 'success');
            } else {
              showAlert(response.message || 'Error updating instructor.', 'danger');
            }
          }, 'json').fail(function() {
            showAlert('Error updating instructor. Please try again.', 'danger');
          });
        });

        // Handle delete instructor button click
        $(document).on('click', '#deleteInstructorBtn', function() {
          if (confirm('Are you sure you want to delete this instructor?')) {
            var instructorId = $('#editInstructorId').val();
            var formData = {
              action: 'delete',
              instructor_id: instructorId
            };

            $.post(endpoint, formData, function(response) {
              if (response.success) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('editInstructorModal'));
                modal.hide();
                loadInstructors();
                showAlert('Instructor deleted successfully!', 'success');
              } else {
                showAlert(response.message || 'Error deleting instructor.', 'danger');
              }
            }, 'json').fail(function() {
              showAlert('Error deleting instructor. Please try again.', 'danger');
            });
          }
        });

        // Handle inline delete instructor button click
        $(document).on('click', '.btn-delete-inline-instructor', function() {
          var instructorId = $(this).data('id');
          if (confirm('Are you sure you want to delete this instructor?')) {
            var formData = {
              action: 'delete',
              instructor_id: instructorId
            };

            $.post(endpoint, formData, function(response) {
              if (response.success) {
                loadInstructors();
                showAlert('Instructor deleted successfully!', 'success');
              } else {
                showAlert(response.message || 'Error deleting instructor.', 'danger');
              }
            }, 'json').fail(function() {
              showAlert('Error deleting instructor. Please try again.', 'danger');
            });
          }
        });
      });

      function loadInstructors() {
        $.getJSON(endpoint, { action: 'get_all' })
          .done(function(response) {
            if (response.success) {
              renderInstructors(response.data);
            } else {
              $('#instructorsList').html('<tr><td colspan="8" class="alert alert-danger">' + (response.message || 'Unable to load instructors.') + '</td></tr>');
            }
          })
          .fail(function() {
            $('#instructorsList').html('<tr><td colspan="8" class="alert alert-danger">Unable to load instructors. Please try again.</td></tr>');
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
