<?php
$page_name = "Curriculum";
$activePage = 'curriculum';
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
                    <h5 class="card-title mb-1">Curriculum</h5>
                    <p class="text-muted mb-0">Manage program curriculum and effective years.</p>
                  </div>
                  <button id="btnAddCurriculum" class="btn btn-primary">Add Curriculum</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="curriculumTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Program</th>
                          <th>Program Code</th>
                          <th>Effective Start Year</th>
                          <th>Effective End Year</th>
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

          <div class="modal fade" id="curriculumModal" tabindex="-1" aria-labelledby="curriculumModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="curriculumModalLabel">Add Curriculum</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="curriculumForm">
                  <div class="modal-body">
                    <input type="hidden" id="curriculumId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="programId" class="form-label">Program</label>
                        <select class="form-select" id="programId" name="program_id" required>
                          <option value="">-- Select Program --</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="effectiveStartYear" class="form-label">Effective Start Year</label>
                        <input type="number" class="form-control" id="effectiveStartYear" name="effective_start_year" min="2000" max="2100" required>
                      </div>
                      <div class="col-md-6">
                        <label for="effectiveEndYear" class="form-label">Effective End Year</label>
                        <input type="number" class="form-control" id="effectiveEndYear" name="effective_end_year" min="2000" max="2100" required>
                      </div>
                    </div>
                    <div id="curriculumMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="curriculumSaveBtn">Save</button>
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
      var endpoint = './curriculum_actions.php';

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function loadPrograms() {
        $.getJSON(endpoint, { action: 'programs' })
          .done(function(response) {
            if (response.success && response.data) {
              var select = $('#programId');
              var currentValue = select.val();
              select.html('<option value="">-- Select Program --</option>');
              
              response.data.forEach(function(prog) {
                select.append('<option value="' + prog.id + '">' + escapeHtml(prog.program_code + ' - ' + prog.program_name) + '</option>');
              });
              
              if (currentValue) {
                select.val(currentValue);
              }
            }
          });
      }

      function renderTable(data) {
        var tbody = $('#curriculumTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + escapeHtml(item.program_name || 'N/A') + '</td>' +
            '<td><strong>' + escapeHtml(item.program_code || 'N/A') + '</strong></td>' +
            '<td>' + item.effective_start_year + '</td>' +
            '<td>' + item.effective_end_year + '</td>' +
            '<td>' + formatDate(item.created_at) + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-info btn-view-curriculum" data-id="' + item.id + '">View</button>' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-curriculum" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-curriculum" data-id="' + item.id + '">Delete</button>' +
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
        $('#curriculumMessage').html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
      }

      function loadCurriculum() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
              showMessage(response.message || 'Unable to load curriculum.', 'error');
            }
          })
          .fail(function() {
            renderTable([]);
            showMessage('Unable to load curriculum. Please try again.', 'error');
          });
      }

      function resetForm() {
        $('#curriculumId').val('');
        $('#programId').val('');
        $('#effectiveStartYear').val('');
        $('#effectiveEndYear').val('');
        $('#curriculumMessage').html('');
      }

      $(document).ready(function() {
        loadPrograms();
        loadCurriculum();

        $('#btnAddCurriculum').on('click', function() {
          resetForm();
          $('#curriculumModalLabel').text('Add Curriculum');
          $('#curriculumSaveBtn').text('Save');
          $('#curriculumModal').modal('show');
        });

        $(document).on('click', '.btn-view-curriculum', function() {
          var id = $(this).data('id');
          window.location.href = './curriculum_subjects.php?id=' + id;
        });

        $(document).on('click', '.btn-edit-curriculum', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#curriculumId').val(item.id);
                $('#programId').val(item.program_id);
                $('#effectiveStartYear').val(item.effective_start_year);
                $('#effectiveEndYear').val(item.effective_end_year);
                $('#curriculumModalLabel').text('Edit Curriculum');
                $('#curriculumSaveBtn').text('Update');
                $('#curriculumModal').modal('show');
              } else {
                showMessage(response.message || 'Curriculum not found.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to retrieve curriculum details.', 'error');
            });
        });

        $(document).on('click', '.btn-delete-curriculum', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this curriculum?')) {
            $.post(endpoint, { action: 'delete', id: id })
              .done(function(response) {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                  loadCurriculum();
                  showMessage(response.message || 'Curriculum deleted successfully.', 'success');
                } else {
                  showMessage(response.message || 'Unable to delete curriculum.', 'error');
                }
              })
              .fail(function() {
                showMessage('Unable to delete curriculum. Please try again.', 'error');
              });
          }
        });

        $('#curriculumForm').on('submit', function(e) {
          e.preventDefault();
          
          var id = $('#curriculumId').val();
          var programId = $('#programId').val();
          var effectiveStartYear = $('#effectiveStartYear').val();
          var effectiveEndYear = $('#effectiveEndYear').val();

          if (!programId || !effectiveStartYear || !effectiveEndYear) {
            showMessage('Please fill in all required fields.', 'error');
            return;
          }

          if (effectiveEndYear <= effectiveStartYear) {
            showMessage('End year must be greater than start year.', 'error');
            return;
          }

          var action = id ? 'update' : 'add';
          var data = {
            action: action,
            program_id: programId,
            effective_start_year: effectiveStartYear,
            effective_end_year: effectiveEndYear
          };

          if (id) {
            data.id = id;
          }

          $.post(endpoint, data)
            .done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                $('#curriculumModal').modal('hide');
                loadCurriculum();
                showMessage(response.message || 'Curriculum saved successfully.', 'success');
              } else {
                showMessage(response.message || 'Unable to save curriculum.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to save curriculum. Please try again.', 'error');
            });
        });
      });
    })(jQuery);
  </script>
</body>

</html>
