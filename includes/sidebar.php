<?php $activePage = isset($activePage) ? $activePage : ''; ?>
<?php $navBasePath = isset($navBasePath) ? rtrim((string)$navBasePath, '/') . '/' : './'; ?>
<?php $apiBasePath = isset($apiBasePath) ? rtrim((string)$apiBasePath, '/') . '/' : '../api/'; ?>
<!-- Sidebar Start -->
<aside class="left-sidebar">
  <!-- Sidebar scroll -->
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="<?= htmlspecialchars($navBasePath) ?>home.php" class="text-nowrap logo-img d-flex align-items-center gap-2">
        <img src="<?= htmlspecialchars($navBasePath) ?>assets/images/logos/logo.png" alt="College Logo" class="img-fluid" style="max-height: 72px; width: auto;" />
        <span class="fs-5 fw-bold">Class Scheduling</span>
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>

    <!-- Sidebar navigation -->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <!-- Dashboard -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Main</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'dashboard' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>home.php" aria-expanded="false">
            <iconify-icon icon="solar:home-smile-line-duotone"></iconify-icon>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- Academic Management -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Academic</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'curriculum' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>academic/curriculum.php" aria-expanded="false">
            <iconify-icon icon="solar:diploma-line-duotone"></iconify-icon>
            <span class="hide-menu">Curriculum</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'programs' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>academic/programs.php" aria-expanded="false">
            <iconify-icon icon="solar:notebook-bookmark-broken"></iconify-icon>
            <span class="hide-menu">Programs</span>
          </a>
        </li>

        <!-- Scheduling -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Scheduling</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'schedules' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>schedules/list.php" aria-expanded="false">
            <iconify-icon icon="solar:clock-circle-line-duotone"></iconify-icon>
            <span class="hide-menu">Schedules</span>
          </a>
        </li>

        <!-- Users & Enrollment -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Users</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'students' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>students/list.php" aria-expanded="false">
            <iconify-icon icon="solar:user-check-line-duotone"></iconify-icon>
            <span class="hide-menu">Students</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'instructors' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>instructors/list.php" aria-expanded="false">
            <iconify-icon icon="solar:users-group-line-duotone"></iconify-icon>
            <span class="hide-menu">Instructors</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'enrollments' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>enrollments/list.php" aria-expanded="false">
            <iconify-icon icon="solar:users-line-duotone"></iconify-icon>
            <span class="hide-menu">Enrollments</span>
          </a>
        </li>

        <!-- Settings & Lookup Tables -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Settings</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'rooms' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/rooms.php" aria-expanded="false">
            <iconify-icon icon="solar:garage-line-duotone"></iconify-icon>
            <span class="hide-menu">Rooms</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'departments' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/departments.php" aria-expanded="false">
            <iconify-icon icon="solar:buildings-2-line-duotone"></iconify-icon>
            <span class="hide-menu">Departments</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'schoolyear' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/schoolyear.php" aria-expanded="false">
            <iconify-icon icon="solar:calendar-line-duotone"></iconify-icon>
            <span class="hide-menu">School Year</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider lg"></span>
        </li>

        <!-- Logout -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="<?= htmlspecialchars($apiBasePath) ?>logout.php" aria-expanded="false">
            <iconify-icon icon="solar:logout-2-line-duotone"></iconify-icon>
            <span class="hide-menu">Logout</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll -->
</aside>
<!-- Sidebar End -->