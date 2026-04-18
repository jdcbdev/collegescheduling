<?php
$page_name = "Class Sections";
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
          <!-- Header Card with Active School Year -->
          <div class="row">
            <div class="col-12">
              <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title mb-1">Class Sections</h5>
                    <p class="text-muted mb-0">Manage class sections per curriculum per year level for the active school year.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active School Year Info -->
          <div class="row">
            <div class="col-12">
              <div class="card bg-light border-primary mb-4">
                <div class="card-body">
                  <h6 class="card-title mb-2"><i class="ti ti-calendar-check"></i> Active School Year</h6>
                  <p class="mb-0"><strong id="schoolYearText">Loading...</strong></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Program Cards -->
          <div class="row" id="programCardsContainer">
            <div class="col-12">
              <div class="text-center text-muted p-5">Loading programs...</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Adding Section -->
  <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addSectionForm">
          <div class="modal-body">
            <input type="hidden" id="hiddenCurriculumId" name="curriculum_id" value="">
            <input type="hidden" id="hiddenSchoolyearId" name="schoolyear_id" value="">
            <input type="hidden" id="hiddenYearLevel" name="year_level" value="">

            <div class="mb-3">
              <label class="form-label"><strong>Program:</strong></label>
              <p id="programDisplay" class="mb-0">-</p>
            </div>

            <div class="mb-3">
              <label for="curriculumSelect" class="form-label">Select Curriculum</label>
              <select class="form-select" id="curriculumSelect" name="curriculum_select" required>
                <option value="">-- Choose Curriculum --</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label"><strong>Year Level:</strong></label>
              <p id="yearLevelDisplay" class="mb-0">-</p>
            </div>

            <div class="mb-3">
              <label for="sectionLetter" class="form-label">Section Letter</label>
              <input type="text" class="form-control text-uppercase" id="sectionLetter" name="section_letter" 
                     placeholder="A" maxlength="1" required>
              <small class="form-text text-muted">Enter a letter (A, B, C, etc.)</small>
            </div>

            <div class="mb-3">
              <label class="form-label"><strong>Section Name Preview:</strong></label>
              <p id="sectionNamePreview" class="mb-0 text-primary fw-bold">-</p>
            </div>

            <div id="addSectionMessage" class="mt-3"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="addSectionBtn">Add Section</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script>
    let activeSchoolYearId = null;
    let allPrograms = [];
    let allCurriculums = [];
    let currentProgramCode = '';

    document.addEventListener('DOMContentLoaded', function () {
      loadActiveSchoolYear();
      loadPrograms();

      // Handle form submission
      document.getElementById('addSectionForm').addEventListener('submit', function (e) {
        e.preventDefault();
        addSection();
      });

      // Handle curriculum selection change
      document.getElementById('curriculumSelect').addEventListener('change', function () {
        updateYearLevelFromCurriculum();
      });

      // Update preview when section letter changes
      document.getElementById('sectionLetter').addEventListener('input', function () {
        updateSectionNamePreview();
      });
    });

    function loadActiveSchoolYear() {
      fetch('classes_actions.php?action=getActiveSchoolYear')
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data) {
            activeSchoolYearId = data.data.id;
            document.getElementById('schoolYearText').textContent = 
              data.data.start_year + '-' + data.data.end_year + ' (Semester ' + data.data.semester + ')';
          } else {
            document.getElementById('schoolYearText').textContent = 'No active school year found';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('schoolYearText').textContent = 'Error loading school year';
        });
    }

    function loadPrograms() {
      fetch('classes_actions.php?action=getPrograms')
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data) {
            allPrograms = data.data;
            loadCurriculums();
          }
        })
        .catch(error => console.error('Error:', error));
    }

    function loadCurriculums() {
      fetch('classes_actions.php?action=getCurriculums')
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data) {
            allCurriculums = data.data;
            if (activeSchoolYearId) {
              renderProgramCards();
            } else {
              setTimeout(renderProgramCards, 500);
            }
          }
        })
        .catch(error => console.error('Error:', error));
    }

    function renderProgramCards() {
      const container = document.getElementById('programCardsContainer');
      
      // Filter programs to only show those with curricula that have subjects
      const programsWithCurriculums = allPrograms.filter(program => {
        return allCurriculums.some(c => c.program_id === program.id);
      });

      if (programsWithCurriculums.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="text-center text-muted p-5">No programs with subjects found for the active school year. Please create curricula and subjects first.</div></div>';
        return;
      }

      let html = '';
      programsWithCurriculums.forEach(program => {
        const programId = program.id;
        const programCode = program.program_code;
        const programName = program.program_name;

        // Get curriculums for this program
        const programCurriculums = allCurriculums.filter(c => c.program_id === programId);

        // Get sections for this program
        fetch('classes_actions.php?action=listByProgram&program_id=' + programId + '&schoolyear_id=' + activeSchoolYearId)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const sections = data.data || [];
              
              // Group by curriculum and year level
              const grouped = {};
              sections.forEach(section => {
                const key = section.curriculum_id + '_' + section.year_level;
                if (!grouped[key]) {
                  grouped[key] = {
                    curriculum_id: section.curriculum_id,
                    curriculum_years: section.start_year + '-' + section.end_year,
                    year_level: section.year_level,
                    sections: []
                  };
                }
                grouped[key].sections.push(section);
              });

              let cardContent = '';
              if (Object.keys(grouped).length === 0) {
                cardContent = '<div class="text-muted text-center py-4"><small>No sections created yet</small></div>';
              } else {
                Object.values(grouped).forEach(group => {
                  const sectionBadges = group.sections
                    .map(s => `
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-info">${s.section_name}</span>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSection(${s.id})">
                          <i class="ti ti-trash"></i>
                        </button>
                      </div>
                    `)
                    .join('');

                  cardContent += `
                    <div class="mb-3 p-2 border-bottom">
                      <small class="text-muted">
                        <strong>Year ${group.year_level}</strong> (${group.curriculum_years}) - ${group.sections.length} section${group.sections.length !== 1 ? 's' : ''}
                      </small>
                      <div class="mt-2">
                        ${sectionBadges}
                      </div>
                    </div>
                  `;
                });
              }

              // Update the card in DOM
              const cardElement = document.getElementById('card-' + programId);
              if (cardElement) {
                cardElement.querySelector('.card-body').innerHTML = cardContent;
              }
            }
          })
          .catch(error => console.error('Error loading sections:', error));

        html += `
          <div class="col-lg-6 col-xxl-4 mb-4">
            <div class="card h-100" id="card-${programId}">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title mb-1">${programCode}</h6>
                  <small class="text-muted">${programName}</small>
                </div>
              </div>
              <div class="card-body">
                <div class="text-center text-muted py-3">Loading...</div>
              </div>
              <div class="card-footer bg-light">
                <button class="btn btn-sm btn-primary w-100" onclick="openAddSectionModal(${programId}, '${programCode}')">
                  <i class="ti ti-plus"></i> Add Section
                </button>
              </div>
            </div>
          </div>
        `;
      });

      container.innerHTML = html;
    }

    function openAddSectionModal(programId, programCode) {
      currentProgramCode = programCode;
      
      // Get curriculums for this program
      const programCurriculums = allCurriculums.filter(c => c.program_id === programId);
      
      if (programCurriculums.length === 0) {
        alert('No curricula found for this program.');
        return;
      }

      // Reset modal HTML to remove any dynamically created elements
      const modalBody = document.getElementById('addSectionModal').querySelector('.modal-body');
      const yearLevelContainer = modalBody.querySelector('#yearLevelContainer');
      if (yearLevelContainer) {
        yearLevelContainer.remove();
      }

      // Populate curriculum select
      const curriculumSelect = document.getElementById('curriculumSelect');
      if (curriculumSelect) {
        curriculumSelect.innerHTML = '<option value="">-- Choose Curriculum --</option>';
        programCurriculums.forEach(curriculum => {
          const option = document.createElement('option');
          option.value = curriculum.id;
          option.textContent = curriculum.effective_start_year + '-' + curriculum.effective_end_year;
          curriculumSelect.appendChild(option);
        });
      }

      // Restore yearLevelDisplay if it was replaced
      if (!document.getElementById('yearLevelDisplay')) {
        const newYearDisplay = document.createElement('div');
        newYearDisplay.className = 'mb-3';
        newYearDisplay.innerHTML = `
          <label class="form-label"><strong>Year Level:</strong></label>
          <p id="yearLevelDisplay" class="mb-0">-</p>
        `;
        
        const curriculumSelect = document.getElementById('curriculumSelect');
        if (curriculumSelect && curriculumSelect.parentElement) {
          curriculumSelect.parentElement.insertAdjacentElement('afterend', newYearDisplay);
        }
      }

      const programDisplayEl = document.getElementById('programDisplay');
      const sectionLetterEl = document.getElementById('sectionLetter');
      const sectionNamePreviewEl = document.getElementById('sectionNamePreview');
      const yearLevelDisplayEl = document.getElementById('yearLevelDisplay');
      const addSectionMessageEl = document.getElementById('addSectionMessage');

      if (programDisplayEl) programDisplayEl.textContent = programCode;
      if (sectionLetterEl) sectionLetterEl.value = '';
      if (sectionNamePreviewEl) sectionNamePreviewEl.textContent = '-';
      if (yearLevelDisplayEl) yearLevelDisplayEl.textContent = '-';
      if (addSectionMessageEl) addSectionMessageEl.innerHTML = '';
      
      document.getElementById('hiddenCurriculumId').value = '';
      document.getElementById('hiddenSchoolyearId').value = activeSchoolYearId;
      document.getElementById('hiddenYearLevel').value = '';
      
      const modal = new bootstrap.Modal(document.getElementById('addSectionModal'));
      modal.show();
    }

    function updateYearLevelFromCurriculum() {
      const curriculumId = document.getElementById('curriculumSelect').value;
      const modalBody = document.getElementById('addSectionModal').querySelector('.modal-body');
      
      if (!curriculumId) {
        const yearLevelDisplay = document.getElementById('yearLevelDisplay');
        if (yearLevelDisplay) {
          yearLevelDisplay.textContent = '-';
        }
        document.getElementById('hiddenCurriculumId').value = '';
        document.getElementById('hiddenYearLevel').value = '';
        return;
      }

      // Find the selected curriculum to get available year levels
      fetch('classes_actions.php?action=getCurriculumDetail&id=' + curriculumId)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data) {
            const curriculum = data.data;
            document.getElementById('hiddenCurriculumId').value = curriculumId;
            
            // Get year levels for this curriculum from subjects
            fetch('classes_actions.php?action=getYearLevelsByCurriculum&curriculum_id=' + curriculumId)
              .then(response => response.json())
              .then(yearData => {
                if (yearData.success && yearData.data && yearData.data.length > 0) {
                  if (yearData.data.length > 1) {
                    // Multiple year levels - show selector
                    let yearLevelContainer = document.getElementById('yearLevelContainer');
                    
                    if (!yearLevelContainer) {
                      yearLevelContainer = document.createElement('div');
                      yearLevelContainer.id = 'yearLevelContainer';
                      yearLevelContainer.className = 'mb-3';
                    }
                    
                    yearLevelContainer.innerHTML = `
                      <label for="yearLevelSelect" class="form-label">Year Level</label>
                      <select class="form-select" id="yearLevelSelect" required>
                        <option value="">-- Choose Year Level --</option>
                        ${yearData.data.map(y => `<option value="${y}">Year ${y}</option>`).join('')}
                      </select>
                    `;
                    
                    // Remove old year level display if it exists
                    const oldDisplay = modalBody.querySelector('#yearLevelDisplay');
                    if (oldDisplay && oldDisplay.parentElement.id !== 'yearLevelContainer') {
                      oldDisplay.parentElement.style.display = 'none';
                    }
                    
                    // Add container if not already in modal
                    if (!document.getElementById('yearLevelContainer')) {
                      const sectionLetterDiv = document.getElementById('curriculumSelect').parentElement.nextElementSibling;
                      sectionLetterDiv.insertAdjacentElement('beforebegin', yearLevelContainer);
                    }

                    document.getElementById('yearLevelSelect').addEventListener('change', function() {
                      document.getElementById('hiddenYearLevel').value = this.value;
                      updateSectionNamePreview();
                    });
                  } else {
                    // Only one year level
                    document.getElementById('hiddenYearLevel').value = yearData.data[0];
                    
                    // Hide the year level container if it exists
                    const yearLevelContainer = document.getElementById('yearLevelContainer');
                    if (yearLevelContainer) {
                      yearLevelContainer.style.display = 'none';
                    }
                    
                    // Show and update the year level display
                    const yearLevelDisplay = document.getElementById('yearLevelDisplay');
                    if (yearLevelDisplay && yearLevelDisplay.parentElement) {
                      yearLevelDisplay.parentElement.style.display = 'block';
                      yearLevelDisplay.textContent = 'Year ' + yearData.data[0];
                    }
                    
                    updateSectionNamePreview();
                  }
                }
              });
          }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateSectionNamePreview() {
      const letter = document.getElementById('sectionLetter').value.toUpperCase();
      const yearLevel = document.getElementById('hiddenYearLevel').value;
      
      if (letter && yearLevel) {
        const preview = currentProgramCode + yearLevel + letter;
        document.getElementById('sectionNamePreview').textContent = preview;
      } else {
        document.getElementById('sectionNamePreview').textContent = '-';
      }
    }

    function addSection() {
      const formData = new FormData(document.getElementById('addSectionForm'));
      formData.append('action', 'add');

      fetch('classes_actions.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          const messageDiv = document.getElementById('addSectionMessage');
          if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            setTimeout(() => {
              bootstrap.Modal.getInstance(document.getElementById('addSectionModal')).hide();
              renderProgramCards();
            }, 1000);
          } else {
            messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('addSectionMessage').innerHTML = '<div class="alert alert-danger">Error adding section.</div>';
        });
    }

    function deleteSection(sectionId) {
      if (confirm('Are you sure you want to delete this section?')) {
        fetch('classes_actions.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=delete&id=' + sectionId
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              renderProgramCards();
            } else {
              alert(data.message || 'Error deleting section.');
            }
          })
          .catch(error => console.error('Error:', error));
      }
    }
  </script>

</body>
</html>
