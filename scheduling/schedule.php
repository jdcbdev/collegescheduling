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
          <div id="scheduleLayoutRow" class="row g-3 flex-nowrap align-items-stretch schedule-layout-row">
            <!-- Panel 1: 60% -->
            <div id="panel1Column" class="h-100 schedule-col-main" style="width:60%; min-width:0; flex:0 0 60%;">
              <div id="panel1Card" class="card h-100">
                <div class="card-header d-flex justify-content-end align-items-center p-4 py-2 pt-3">
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
                    <button id="btnPrintSchedule" type="button" class="btn btn-outline-secondary" title="Print/Export Schedule">
                      <i class="ti ti-printer"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body p-4 pt-2 pb-2" style="padding-bottom: 5px !important;">
                  <div id="scheduleTableContainer" class="table-responsive"></div>
                </div>
              </div>
            </div>
            <!-- Panel 2: 40% -->
            <div id="panel2Column" class="h-100 schedule-col-side" style="width:40%; min-width:0; flex:0 0 40%;">
              <div class="d-flex flex-column gap-3 h-100 schedule-side-expanded">
                <div id="panel2Card" class="card mb-0 flex-fill">
                  <div class="card-header d-flex justify-content-end align-items-center p-4 py-2 pt-3">
                    <div class="text-end me-3">
                      <h5 id="scheduleLabel2" class="card-title mb-0">
                        <span id="scheduleLabel2Text">Instructor</span>
                        <i id="btnTogglePanel2" class="ti ti-layout-sidebar panel2-icon-toggle ms-1 align-middle" title="Collapse side panels" role="button" tabindex="0" aria-label="Collapse side panels"></i>
                      </h5>
                    </div>
                    <div class="schedule-header-controls d-flex align-items-center ms-auto gap-2">
                      <select id="instructorDropdown2" class="form-select form-select w-auto">
                        <option value="">Select Instructor</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body p-4 pt-2 pb-2">
                    <div id="scheduleTableContainer2" class="table-responsive"></div>
                  </div>
                </div>

                <div class="card flex-fill">
                  <div class="card-header d-flex justify-content-end align-items-center p-4 py-2 pt-3">
                    <div class="text-end me-3">
                      <h5 id="scheduleLabel3" class="card-title mb-0">Room</h5>
                    </div>
                    <div class="schedule-header-controls d-flex align-items-center ms-auto gap-2">
                      <select id="roomDropdown3" class="form-select form-select w-auto">
                        <option value="">Select Room</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body p-4 pt-2 pb-2">
                    <div id="scheduleTableContainer3" class="table-responsive"></div>
                  </div>
                </div>
              </div>

              <div class="schedule-side-collapsed d-none h-100">
                <div class="card h-100 mb-0">
                  <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3 p-2">
                    <i id="btnExpandPanel2" class="ti ti-layout-sidebar panel2-icon-toggle" title="Expand side panels" role="button" tabindex="0" aria-label="Expand side panels"></i>
                    <span class="text-muted small fw-semibold text-uppercase" style="writing-mode: vertical-rl; transform: rotate(180deg);">Panels</span>
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
