<?php
$page_name = "School Year Settings";
$activePage = 'schoolyear';
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
                    <h5 class="card-title mb-1">School Year Lookup</h5>
                    <p class="text-muted mb-0">Manage academic year and semester records.</p>
                  </div>
                  <button id="btnAddSchoolYear" class="btn btn-primary">Add School Year</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="schoolYearTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Start Year</th>
                          <th>End Year</th>
                          <th>Semester</th>
                          <th>Active</th>
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

          <div class="modal fade" id="schoolYearModal" tabindex="-1" aria-labelledby="schoolYearModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="schoolYearModalLabel">Add School Year</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="schoolYearForm">
                  <div class="modal-body">
                    <input type="hidden" id="schoolYearId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="startYear" class="form-label">Start Year</label>
                        <input type="number" class="form-control" id="startYear" name="start_year" min="2000" max="2100" required>
                      </div>
                      <div class="col-md-6">
                        <label for="endYear" class="form-label">End Year</label>
                        <input type="number" class="form-control" id="endYear" name="end_year" min="2000" max="2100" required>
                      </div>
                      <div class="col-md-6">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" name="semester" required>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3 - Summer</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Active</label>
                        <div class="form-check form-switch mt-2">
                          <input class="form-check-input" type="checkbox" id="isActive" name="is_active">
                          <label class="form-check-label" for="isActive">Activate this school year</label>
                        </div>
                      </div>
                    </div>
                    <div id="schoolYearMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="schoolYearSaveBtn">Save</button>
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
      var endpoint = './schoolyear_actions.php';

      function renderTable(data) {
        var tbody = $('#schoolYearTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="6" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          var checked = item.is_active == 1 || item.is_active === true ? 'checked' : '';
          var activeBadge = item.is_active == 1 || item.is_active === true ? '<span class="badge my-bg-color">Active</span>' : '<span class="badge my-bg-color-gray">Inactive</span>';
          var semesterLabel = item.semester == 3 ? 'Summer' : item.semester;
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + item.start_year + '</td>' +
            '<td>' + item.end_year + '</td>' +
            '<td>' + semesterLabel + '</td>' +
            '<td>' + activeBadge + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-year" data-id="' + item.id + '">Edit</button>' +
                '<div class="form-check form-switch mb-0">' +
                  '<input class="form-check-input schoolyear-active-toggle" type="checkbox" data-id="' + item.id + '" ' + checked + '>' +
                '</div>' +
              '</div>' +
            '</td>' +
          '</tr>';
        });

        tbody.html(rows.join(''));
      }

      function showMessage(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#schoolYearMessage').html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
      }

      function loadSchoolYears() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
              showMessage(response.message || 'Unable to load school years.', 'error');
            }
          })
          .fail(function() {
            renderTable([]);
            showMessage('Unable to load school years. Please try again.', 'error');
          });
      }

      function resetForm() {
        $('#schoolYearId').val('');
        $('#startYear').val(new Date().getFullYear());
        $('#endYear').val(new Date().getFullYear() + 1);
        $('#semester').val('1');
        $('#isActive').prop('checked', false);
        $('#schoolYearMessage').html('');
      }

      $(document).ready(function() {
        loadSchoolYears();

        $('#btnAddSchoolYear').on('click', function() {
          resetForm();
          $('#schoolYearModalLabel').text('Add School Year');
          $('#schoolYearSaveBtn').text('Save');
          $('#schoolYearModal').modal('show');
        });

        $(document).on('click', '.btn-edit-year', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#schoolYearId').val(item.id);
                $('#startYear').val(item.start_year);
                $('#endYear').val(item.end_year);
                $('#semester').val(item.semester);
                $('#isActive').prop('checked', item.is_active == 1 || item.is_active === true);
                $('#schoolYearModalLabel').text('Edit School Year');
                $('#schoolYearSaveBtn').text('Update');
                $('#schoolYearModal').modal('show');
              } else {
                showMessage(response.message || 'Unable to load record.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to load record. Please try again.', 'error');
            });
        });

        $(document).on('change', '.schoolyear-active-toggle', function() {
          var id = $(this).data('id');
          var active = $(this).is(':checked') ? 1 : 0;
          $.post(endpoint, { action: 'toggle', id: id, is_active: active }, function(response) {
            if (response.success) {
              loadSchoolYears();
            } else {
              showMessage(response.message || 'Unable to update active status.', 'error');
            }
          }, 'json').fail(function() {
            showMessage('Unable to update active status. Please try again.', 'error');
          });
        });

        $('#schoolYearForm').on('submit', function(e) {
          e.preventDefault();
          var id = $('#schoolYearId').val();
          var action = id ? 'update' : 'add';
          var formData = {
            action: action,
            id: id,
            start_year: $('#startYear').val(),
            end_year: $('#endYear').val(),
            semester: $('#semester').val(),
            is_active: $('#isActive').is(':checked') ? 1 : 0
          };

          $('#schoolYearSaveBtn').prop('disabled', true).text('Saving...');
          $.post(endpoint, formData, function(response) {
            if (response.success) {
              $('#schoolYearModal').modal('hide');
              loadSchoolYears();
            } else {
              showMessage(response.message || 'Unable to save record.', 'error');
            }
          }, 'json').fail(function() {
            showMessage('Unable to save record. Please try again.', 'error');
          }).always(function() {
            $('#schoolYearSaveBtn').prop('disabled', false).text(id ? 'Update' : 'Save');
          });
        });
      });
    })(jQuery);
  </script>
