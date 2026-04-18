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

        <!-- Scheduling -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Scheduling</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'schedule' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>scheduling/schedule.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Schedules">
            <iconify-icon icon="solar:calendar-mark-line-duotone"></iconify-icon>
            <span class="hide-menu">Schedules</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'classes' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>scheduling/classes.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Class">
            <iconify-icon icon="solar:layers-line-duotone"></iconify-icon>
            <span class="hide-menu">Class</span>
          </a>
        </li>

        <!-- Academic Management -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Academic</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'curriculum' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>academic/curriculum.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Curriculum">
            <iconify-icon icon="solar:diploma-line-duotone"></iconify-icon>
            <span class="hide-menu">Curriculum</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'programs' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>academic/programs.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Programs">
            <iconify-icon icon="solar:notebook-bookmark-broken"></iconify-icon>
            <span class="hide-menu">Programs</span>
          </a>
        </li>

        <!-- Users & Enrollment -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Users</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'instructors' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>users/instructors.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Instructors">
            <iconify-icon icon="solar:user-check-line-duotone"></iconify-icon>
            <span class="hide-menu">Instructors</span>
          </a>
        </li>

        <!-- Settings & Lookup Tables -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Settings</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'rooms' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/rooms.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Rooms">
            <iconify-icon icon="solar:garage-line-duotone"></iconify-icon>
            <span class="hide-menu">Rooms</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'departments' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/departments.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Departments">
            <iconify-icon icon="solar:buildings-2-line-duotone"></iconify-icon>
            <span class="hide-menu">Departments</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link<?= $activePage === 'schoolyear' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/schoolyear.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="School Year">
            <iconify-icon icon="solar:calendar-line-duotone"></iconify-icon>
            <span class="hide-menu">School Year</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider lg"></span>
        </li>

        <!-- Logout -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="<?= htmlspecialchars($apiBasePath) ?>logout.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
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
<script>
(function () {
  const wrapper = document.getElementById('main-wrapper');
  const sidebarLinks = document.querySelectorAll('.left-sidebar .sidebar-link[data-bs-toggle="tooltip"]');
  let tooltipInstances = [];

  function enableTooltips() {
    tooltipInstances = Array.from(sidebarLinks).map(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));
  }

  function disableTooltips() {
    tooltipInstances.forEach(t => t.dispose());
    tooltipInstances = [];
  }

  function syncTooltips() {
    if (wrapper && wrapper.classList.contains('mini-sidebar')) {
      if (tooltipInstances.length === 0) enableTooltips();
    } else {
      disableTooltips();
    }
  }

  // Sync on page load
  document.addEventListener('DOMContentLoaded', syncTooltips);

  // Sync whenever the toggle button is clicked
  document.addEventListener('click', function (e) {
    if (e.target.closest('.sidebartoggler')) {
      setTimeout(syncTooltips, 50);
    }
  });
})();
</script>
<!-- Sidebar End -->