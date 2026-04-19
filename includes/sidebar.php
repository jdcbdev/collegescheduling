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
          <a class="sidebar-link<?= $activePage === 'college-officials' ? ' active' : '' ?>" href="<?= htmlspecialchars($navBasePath) ?>settings/college_officials.php" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="right" title="College Officials">
            <iconify-icon icon="solar:users-group-rounded-line-duotone"></iconify-icon>
            <span class="hide-menu">College Officials</span>
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
  const SIDEBAR_STATE_KEY = 'leftSidebarDesktopState';
  let tooltipInstances = [];

  function isDesktopViewport() {
    return window.innerWidth >= 1199;
  }

  function setSidebarState(state) {
    if (!wrapper) return;
    const isMini = state === 'mini';
    wrapper.classList.toggle('mini-sidebar', isMini);
    wrapper.setAttribute('data-sidebartype', isMini ? 'mini-sidebar' : 'full');
  }

  function getCurrentSidebarState() {
    if (!wrapper) return 'full';
    return wrapper.classList.contains('mini-sidebar') ? 'mini' : 'full';
  }

  function saveSidebarState() {
    if (!isDesktopViewport()) return;
    try {
      localStorage.setItem(SIDEBAR_STATE_KEY, getCurrentSidebarState());
    } catch (e) {
      // Ignore storage failures (private mode / blocked storage).
    }
  }

  function applySavedSidebarState() {
    if (!wrapper || !isDesktopViewport()) return;
    try {
      const saved = localStorage.getItem(SIDEBAR_STATE_KEY);
      if (saved === 'mini' || saved === 'full') {
        setSidebarState(saved);
      }
    } catch (e) {
      // Ignore storage failures (private mode / blocked storage).
    }
  }

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

  // Restore saved state after core scripts initialize layout defaults.
  document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
      applySavedSidebarState();
      syncTooltips();
    }, 60);
  });

  // Re-apply saved desktop state on resize because app.min.js resets sidebartype.
  window.addEventListener('resize', function () {
    setTimeout(function () {
      applySavedSidebarState();
      syncTooltips();
    }, 60);
  });

  // Save + sync whenever the toggle button is clicked.
  document.addEventListener('click', function (e) {
    if (e.target.closest('.sidebartoggler')) {
      setTimeout(function () {
        saveSidebarState();
        syncTooltips();
      }, 60);
    }
  });
})();
</script>
<!-- Sidebar End -->