// schedule.js
// Handles rendering of schedule tables for class, instructor, and room

document.addEventListener('DOMContentLoaded', function () {
  const scheduleTypeSelect = document.getElementById('scheduleType');
  if (scheduleTypeSelect) {
    scheduleTypeSelect.addEventListener('change', renderMainSchedule);
    renderMainSchedule();
  }
  renderInstructorSchedule();
  renderRoomSchedule();
  setupDropdowns();
});

function renderMainSchedule() {
  const type = document.getElementById('scheduleType').value;
  // For now, just render the table structure
  const container = document.getElementById('scheduleTableContainer');
  container.innerHTML = generateScheduleTableHtml(type);
}

function renderInstructorSchedule() {
  const container = document.getElementById('instructorScheduleTable');
  container.innerHTML = generateScheduleTableHtml('instructor', true);
}

function renderRoomSchedule() {
  const container = document.getElementById('roomScheduleTable');
  container.innerHTML = generateScheduleTableHtml('room', true);
}

function generateScheduleTableHtml(type, small=false) {
  // Days and time slots
  const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  const startHour = 7;
  const endHour = 19;
  const interval = 30; // minutes
  let html = `<table class="table table-bordered table-sm mb-0 ${small ? 'table-schedule-small' : 'table-schedule-main'}">
    <thead><tr><th style="width:70px">Time</th>`;
  days.forEach(day => {
    html += `<th>${day}</th>`;
  });
  html += '</tr></thead><tbody>';
  for (let hour = startHour; hour < endHour; hour++) {
    for (let min = 0; min < 60; min += interval) {
      const timeLabel = `${String(hour).padStart(2, '0')}:${min === 0 ? '00' : '30'}`;
      html += `<tr><td>${timeLabel}</td>`;
      for (let d = 0; d < days.length; d++) {
        html += `<td></td>`;
      }
      html += '</tr>';
    }
  }
  html += '</tbody></table>';
  return html;
}

// --- Dropdown population and event logic ---
function setupDropdowns() {
  // Show/hide class filters based on schedule type
  const scheduleTypeSelect = document.getElementById('scheduleType');
  const classFilters = document.getElementById('classFilters');
  if (scheduleTypeSelect && classFilters) {
    function updateFilters() {
      if (scheduleTypeSelect.value === 'class') {
        classFilters.style.display = '';
      } else {
        classFilters.style.display = 'none';
      }
    }
    scheduleTypeSelect.addEventListener('change', updateFilters);
    updateFilters();
  }
  // Populate dropdowns
  loadPrograms();
  loadInstructors();
  loadRooms();
  // Event listeners
  const programDropdown = document.getElementById('programDropdown');
  if (programDropdown) {
    programDropdown.addEventListener('change', function () {
      loadClassSections(this.value);
    });
  }
}

function loadPrograms() {
  fetch('schedule_actions.php?action=getPrograms')
    .then(r => r.json())
    .then(data => {
      const dropdown = document.getElementById('programDropdown');
      if (dropdown && data.success && Array.isArray(data.data)) {
        dropdown.innerHTML = '<option value="">Select Program</option>' +
          data.data.map(p => `<option value="${p.id}">${p.program_code} - ${p.program_name}</option>`).join('');
      }
    });
}

function loadClassSections(programId) {
  const dropdown = document.getElementById('classSectionDropdown');
  if (!programId) {
    if (dropdown) dropdown.innerHTML = '<option value="">Select Class Section</option>';
    return;
  }
  fetch('schedule_actions.php?action=getClassSections&program_id=' + programId)
    .then(r => r.json())
    .then(data => {
      if (dropdown && data.success && Array.isArray(data.data)) {
        dropdown.innerHTML = '<option value="">Select Class Section</option>' +
          data.data.map(c => `<option value="${c.id}">${c.section_name}</option>`).join('');
      }
    });
}

function loadInstructors() {
  fetch('schedule_actions.php?action=getInstructors')
    .then(r => r.json())
    .then(data => {
      const dropdown = document.getElementById('instructorDropdown');
      if (dropdown && data.success && Array.isArray(data.data)) {
        dropdown.innerHTML = '<option value="">Select Instructor</option>' +
          data.data.map(i => `<option value="${i.id}">${i.instructor_code} - ${i.lastname}, ${i.firstname}</option>`).join('');
      }
    });
}

function loadRooms() {
  fetch('schedule_actions.php?action=getRooms')
    .then(r => r.json())
    .then(data => {
      const dropdown = document.getElementById('roomDropdown');
      if (dropdown && data.success && Array.isArray(data.data)) {
        dropdown.innerHTML = '<option value="">Select Room</option>' +
          data.data.map(rm => `<option value="${rm.id}">${rm.room_name}</option>`).join('');
      }
    });
}
