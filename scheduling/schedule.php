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
          <div class="row g-3 flex-nowrap">
            <!-- Panel 1: 60% -->
            <div style="width:60%; min-width:0; flex:0 0 60%;">
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
                      <option value="">Select Class</option>
                    </select>
                    <button id="btnSubjectProgress" type="button" class="btn btn-outline-primary btn-cancel-all d-none" title="View Subject Progress">
                      <i class="ti ti-checklist"></i>
                    </button>
                    <select id="instructorDropdown" class="form-select form-select w-auto d-none">
                      <option value="">Select Instructor</option>
                    </select>
                    <select id="roomDropdown" class="form-select form-select w-auto d-none">
                      <option value="">Select Room</option>
                    </select>
                  </div>
                </div>
                <div class="card-body p-4 pt-2">
                  <div id="scheduleTableContainer" class="table-responsive"></div>
                </div>
              </div>
            </div>
            <!-- Panel 2: 40% -->
            <div style="width:40%; min-width:0; flex:0 0 40%;">
              <div class="d-flex flex-column gap-3 h-100">
                <div class="card mb-0">
                  <div class="card-header d-flex justify-content-end align-items-center">
                    <div class="text-end me-3">
                      <h5 id="scheduleLabel2" class="card-title mb-0">Instructor</h5>
                    </div>
                    <div class="schedule-header-controls d-flex align-items-center ms-auto gap-2">
                      <select id="instructorDropdown2" class="form-select form-select w-auto">
                        <option value="">Select Instructor</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body p-4 pt-2">
                    <div id="scheduleTableContainer2" class="table-responsive"></div>
                  </div>
                </div>

                <div class="card">
                  <div class="card-header d-flex justify-content-end align-items-center">
                    <div class="text-end me-3">
                      <h5 id="scheduleLabel3" class="card-title mb-0">Room</h5>
                    </div>
                    <div class="schedule-header-controls d-flex align-items-center ms-auto gap-2">
                      <select id="roomDropdown3" class="form-select form-select w-auto">
                        <option value="">Select Room</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body p-4 pt-2">
                    <div id="scheduleTableContainer3" class="table-responsive"></div>
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
