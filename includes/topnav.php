<!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown1" aria-expanded="false">
                <iconify-icon icon="solar:bell-linear" class="fs-6"></iconify-icon>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 1
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 2
                  </a>
                </div>
              </div>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center gap-2" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <iconify-icon icon="solar:user-linear" class="fs-6"></iconify-icon>
                  <div class="d-flex flex-column" style="line-height: 1.2;">
                    <span class="user-name fw-bold" id="loggedUser" style="font-size: 0.95rem;"><?php echo "College Secretary"; ?></span>
                    <span class="user-department" id="loggedUserDepartment" style="font-size: 0.75rem; color: #98A4AE; font-weight: normal;"><?= "College of Computing Studies" ?></span>
                  </div>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->