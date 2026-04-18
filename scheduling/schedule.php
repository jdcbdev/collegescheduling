<?php
$page_name = "Class Schedule";
$activePage = 'scheduling';
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
            <div class="col-12 mb-4">
              <div class="card h-100">
                <div class="card-header d-flex justify-content-end align-items-center">
                  <div class="text-end me-3">
                    <h5 class="card-title mb-0 schedule-label">Class Schedule <span class="text-muted">| SY: --</span></h5>
                  </div>
                  <div class="schedule-header-controls d-flex align-items-center ms-auto gap-2">
                    <select id="scheduleType" class="form-select form-select w-auto">
                      <option value="class" selected>Class</option>
                      <option value="instructor">Instructor</option>
                      <option value="room">Room</option>
                    </select>
                    <select id="programDropdown" class="form-select form-select w-auto">
                      <option value="">Select Program</option>
                    </select>
                    <select id="classSectionDropdown" class="form-select form-select w-auto">
                      <option value="">Select Class Section</option>
                    </select>
                    <select id="instructorDropdown" class="form-select form-select w-auto d-none">
                      <option value="">Select Instructor</option>
                    </select>
                    <select id="roomDropdown" class="form-select form-select w-auto d-none">
                      <option value="">Select Room</option>
                    </select>
                  </div>
                </div>
                <div class="card-body p-4">
                  <div id="scheduleTableContainer" class="table-responsive"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
  <script src="../assets/js/schedule.js"></script>
</body>
</html>
