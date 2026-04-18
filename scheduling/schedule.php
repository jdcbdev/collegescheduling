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
            <div class="col-lg-8 mb-4">
              <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">Class Schedule</h5>
                  <select id="scheduleType" class="form-select w-auto">
                    <option value="class">Class</option>
                    <option value="instructor">Instructor</option>
                    <option value="room">Room</option>
                  </select>
                </div>
                <div class="card-body p-0">
                  <div id="classFilters" class="row mb-2" style="display:none;">
                    <div class="col-md-6">
                      <select id="programDropdown" class="form-select form-select-sm mb-2">
                        <option value="">Select Program</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <select id="classSectionDropdown" class="form-select form-select-sm mb-2">
                        <option value="">Select Class Section</option>
                      </select>
                    </div>
                  </div>
                  <div id="scheduleTableContainer" class="table-responsive"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="row h-100">
                <div class="col-12 mb-4" style="height: 50%">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h6 class="mb-0">Instructor Schedule</h6>
                      <select id="instructorDropdown" class="form-select form-select-sm w-auto ms-2">
                        <option value="">Select Instructor</option>
                      </select>
                    </div>
                    <div class="card-body p-0">
                      <div id="instructorScheduleTable" class="table-responsive"></div>
                    </div>
                  </div>
                </div>
                <div class="col-12" style="height: 50%">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h6 class="mb-0">Room Schedule</h6>
                      <select id="roomDropdown" class="form-select form-select-sm w-auto ms-2">
                        <option value="">Select Room</option>
                      </select>
                    </div>
                    <div class="card-body p-0">
                      <div id="roomScheduleTable" class="table-responsive"></div>
                    </div>
                  </div>
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
