<?php
session_start();

$page_name = "Class Scheduling System";
$activePage = 'dashboard';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <!--  Main wrapper -->
    <div class="body-wrapper">
      <?php require_once __DIR__ . '/includes/topnav.php'; ?>
      <div class="body-wrapper-inner">
        <div class="container-fluid content-page">
          <!--  Row 1 - Dashboard Title -->
          <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch padding-adjustment">
              <div class="card w-100 mb-3">
                <div class="card-body p-4">
                  <h5 class="card-title fw-semibold mb-0">Class Scheduling System</h5>
                  <p class="text-muted mb-0">Manage academic programs, schedules, and enrollments</p>
                </div>
              </div>
            </div>
          </div>

          <!--  Row 2 - Main Menu -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header bg-primary">
                  <h5 class="card-title text-white mb-0">Main Menu</h5>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                      <a href="./programs/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-books fs-5 text-primary mb-2"></i>
                          <h6 class="card-title fw-semibold">Programs</h6>
                          <p class="text-muted small mb-0">Manage academic programs</p>
                        </div>
                      </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <a href="./curriculum/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-layout-list fs-5 text-success mb-2"></i>
                          <h6 class="card-title fw-semibold">Curriculum</h6>
                          <p class="text-muted small mb-0">Manage program curriculum</p>
                        </div>
                      </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <a href="./schedules/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-clock fs-5 text-warning mb-2"></i>
                          <h6 class="card-title fw-semibold">Schedules</h6>
                          <p class="text-muted small mb-0">Manage class schedules</p>
                        </div>
                      </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <a href="./enrollments/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-users fs-5 text-info mb-2"></i>
                          <h6 class="card-title fw-semibold">Enrollments</h6>
                          <p class="text-muted small mb-0">Manage student enrollments</p>
                        </div>
                      </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <a href="./students/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-user-check fs-5 text-danger mb-2"></i>
                          <h6 class="card-title fw-semibold">Students</h6>
                          <p class="text-muted small mb-0">Manage student records</p>
                        </div>
                      </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <a href="./instructors/list.php" class="card text-decoration-none h-100 menu-card">
                        <div class="card-body text-center py-4">
                          <i class="ti ti-users-group fs-5 text-secondary mb-2"></i>
                          <h6 class="card-title fw-semibold">Instructors</h6>
                          <p class="text-muted small mb-0">Manage instructor profiles</p>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .menu-card {
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }
    .menu-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-color: #0d6efd;
      transform: translateY(-2px);
    }
  </style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>