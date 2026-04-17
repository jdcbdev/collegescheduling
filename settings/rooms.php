<?php
$page_name = "Rooms";
$activePage = 'rooms';
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
                    <h5 class="card-title mb-1">Rooms/Classrooms</h5>
                    <p class="text-muted mb-0">Manage classroom and laboratory rooms.</p>
                  </div>
                  <button id="btnAddRoom" class="btn btn-primary">Add Room</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="roomTable" class="table table-hover align-middle">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Room Name</th>
                          <th>Capacity</th>
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

          <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="roomModalLabel">Add Room</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="roomForm">
                  <div class="modal-body">
                    <input type="hidden" id="roomId" name="id" value="">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="roomName" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="roomName" name="room_name" placeholder="e.g., Lab 1" required>
                      </div>
                      <div class="col-md-12">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="50" required>
                      </div>
                    </div>
                    <div id="roomMessage" class="mt-3"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="roomSaveBtn">Save</button>
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
      var endpoint = './rooms_actions.php';

      function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }

      function renderTable(data) {
        var tbody = $('#roomTable tbody');
        if (!data.length) {
          tbody.html('<tr><td colspan="5" class="text-center text-muted">No records found.</td></tr>');
          return;
        }

        var rows = data.map(function(item, index) {
          return '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td><strong>' + escapeHtml(item.room_name) + '</strong></td>' +
            '<td>' + item.capacity + '</td>' +
            '<td>' + formatDate(item.created_at) + '</td>' +
            '<td>' +
              '<div class="d-flex gap-2 align-items-center">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-room" data-id="' + item.id + '">Edit</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-room" data-id="' + item.id + '">Delete</button>' +
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
        $('#roomMessage').html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
      }

      function loadRooms() {
        $.getJSON(endpoint, { action: 'list' })
          .done(function(response) {
            if (response.success) {
              renderTable(response.data);
            } else {
              renderTable([]);
              showMessage(response.message || 'Unable to load rooms.', 'error');
            }
          })
          .fail(function() {
            renderTable([]);
            showMessage('Unable to load rooms. Please try again.', 'error');
          });
      }

      function resetForm() {
        $('#roomId').val('');
        $('#roomName').val('');
        $('#capacity').val('50');
        $('#roomMessage').html('');
      }

      $(document).ready(function() {
        loadRooms();

        $('#btnAddRoom').on('click', function() {
          resetForm();
          $('#roomModalLabel').text('Add Room');
          $('#roomSaveBtn').text('Save');
          $('#roomModal').modal('show');
        });

        $(document).on('click', '.btn-edit-room', function() {
          var id = $(this).data('id');
          $.getJSON(endpoint, { action: 'get', id: id })
            .done(function(response) {
              if (response.success && response.data) {
                var item = response.data;
                $('#roomId').val(item.id);
                $('#roomName').val(item.room_name);
                $('#capacity').val(item.capacity);
                $('#roomModalLabel').text('Edit Room');
                $('#roomSaveBtn').text('Update');
                $('#roomModal').modal('show');
              } else {
                showMessage(response.message || 'Room not found.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to retrieve room details.', 'error');
            });
        });

        $(document).on('click', '.btn-delete-room', function() {
          var id = $(this).data('id');
          if (confirm('Are you sure you want to delete this room?')) {
            $.post(endpoint, { action: 'delete', id: id })
              .done(function(response) {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                  loadRooms();
                  showMessage(response.message || 'Room deleted successfully.', 'success');
                } else {
                  showMessage(response.message || 'Unable to delete room.', 'error');
                }
              })
              .fail(function() {
                showMessage('Unable to delete room. Please try again.', 'error');
              });
          }
        });

        $('#roomForm').on('submit', function(e) {
          e.preventDefault();
          
          var id = $('#roomId').val();
          var roomName = $('#roomName').val().trim();
          var capacity = $('#capacity').val();

          if (!roomName || !capacity) {
            showMessage('Please fill in all required fields.', 'error');
            return;
          }

          if (capacity < 1) {
            showMessage('Capacity must be at least 1.', 'error');
            return;
          }

          var action = id ? 'update' : 'add';
          var data = {
            action: action,
            room_name: roomName,
            capacity: capacity
          };

          if (id) {
            data.id = id;
          }

          $.post(endpoint, data)
            .done(function(response) {
              response = typeof response === 'string' ? JSON.parse(response) : response;
              if (response.success) {
                $('#roomModal').modal('hide');
                loadRooms();
                showMessage(response.message || 'Room saved successfully.', 'success');
              } else {
                showMessage(response.message || 'Unable to save room.', 'error');
              }
            })
            .fail(function() {
              showMessage('Unable to save room. Please try again.', 'error');
            });
        });
      });
    })(jQuery);
  </script>
</body>

</html>
