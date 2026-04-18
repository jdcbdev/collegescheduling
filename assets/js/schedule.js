// schedule.js
// Handles rendering of schedule tables for class, instructor, and room

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const START_HOUR = 7;
const END_HOUR = 20;
const INTERVAL_MINUTES = 60;

document.addEventListener('DOMContentLoaded', function () {
  renderMainSchedule();
  setupDropdowns();
});

function renderMainSchedule() {
  const container = document.getElementById('scheduleTableContainer');
  if (container) {
    container.innerHTML = generateScheduleTableHtml('class');
  }
}

function clearMainSchedule() {
  const container = document.getElementById('scheduleTableContainer');
  if (container) {
    container.innerHTML = generateScheduleTableHtml('class');
  }
}

function to12H(hour, min) {
  let h = hour % 12;
  if (h === 0) h = 12;
  const ampm = hour < 12 ? 'AM' : 'PM';
  return `${h} ${ampm}`;
}

function generateScheduleTableHtml(type, small=false) {
  let html = `<table class="table table-bordered table-sm mb-0 ${small ? 'table-schedule-small' : 'table-schedule-main'}">
    <thead><tr><th class="gc-time-head" style="width:52px"></th>`;
  DAYS.forEach(day => {
    html += `<th>${day}</th>`;
  });
  html += '</tr></thead><tbody>';
  for (let hour = START_HOUR; hour < END_HOUR; hour++) {
    for (let min = 0; min < 60; min += INTERVAL_MINUTES) {
      const minutes = hour * 60 + min;
      const timeLabel = to12H(hour, min);
      html += `<tr data-time-minutes="${minutes}"><td class="gc-time-cell"><span class="gc-time-label">${timeLabel}</span></td>`;
      for (let d = 0; d < DAYS.length; d++) {
        html += `<td data-day-index="${d}" data-time-minutes="${minutes}"></td>`;
      }
      html += '</tr>';
    }
  }
  html += '</tbody></table>';
  return html;
}

function timeStringToMinutes(timeStr) {
  if (!timeStr || typeof timeStr !== 'string') return NaN;
  const parts = timeStr.split(':');
  if (parts.length < 2) return NaN;
  const h = Number(parts[0]);
  const m = Number(parts[1]);
  return (h * 60) + m;
}

function normalizeDayIndex(day) {
  if (!day) return -1;
  const normalized = String(day).trim().toLowerCase();
  const map = {
    monday: 0, mon: 0,
    tuesday: 1, tue: 1, tues: 1,
    wednesday: 2, wed: 2,
    thursday: 3, thu: 3, thurs: 3,
    friday: 4, fri: 4,
    saturday: 5, sat: 5,
    sunday: 6, sun: 6
  };
  return Object.prototype.hasOwnProperty.call(map, normalized) ? map[normalized] : -1;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function buildScheduleCellHtml(item, small) {
  const title = item.subject_code || item.subject_name || 'Scheduled';
  const details = [];

  if (item.class_section) details.push(item.class_section);
  if (item.instructor_name) details.push(item.instructor_name);
  if (item.room_name) details.push(item.room_name);

  const timeRange = (item.start_time && item.end_time) ? `${item.start_time.slice(0, 5)} - ${item.end_time.slice(0, 5)}` : '';
  if (timeRange) details.push(timeRange);

  return `
    <div class="p-1 h-100" style="background:#eaf4ff; border-left:3px solid #0d6efd; font-size:${small ? '10px' : '11px'}; line-height:1.25;">
      <div style="font-weight:600;">${escapeHtml(title)}</div>
      <div>${escapeHtml(details.join(' | '))}</div>
    </div>
  `;
}

function plotSchedules(containerId, schedules, small=false) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const table = container.querySelector('table');
  if (!table) return;

  const dayStart = START_HOUR * 60;
  const dayEnd = END_HOUR * 60;

  schedules.forEach(item => {
    const dayIndex = normalizeDayIndex(item.day_of_week);
    const startMinutes = timeStringToMinutes(item.start_time);
    const endMinutes = timeStringToMinutes(item.end_time);

    if (dayIndex < 0 || Number.isNaN(startMinutes) || Number.isNaN(endMinutes) || endMinutes <= startMinutes) {
      return;
    }

    const clampedStart = Math.max(startMinutes, dayStart);
    const clampedEnd = Math.min(endMinutes, dayEnd);
    if (clampedEnd <= clampedStart) {
      return;
    }

    const startSlot = Math.floor((clampedStart - dayStart) / INTERVAL_MINUTES);
    const endSlot = Math.ceil((clampedEnd - dayStart) / INTERVAL_MINUTES);
    const rowspan = Math.max(1, endSlot - startSlot);
    const startCellMinutes = dayStart + (startSlot * INTERVAL_MINUTES);

    const startCell = table.querySelector(`td[data-day-index="${dayIndex}"][data-time-minutes="${startCellMinutes}"]`);
    if (!startCell) {
      return;
    }

    startCell.rowSpan = rowspan;
    startCell.innerHTML = buildScheduleCellHtml(item, small);

    for (let slot = startSlot + 1; slot < endSlot; slot++) {
      const coveredMinutes = dayStart + (slot * INTERVAL_MINUTES);
      const coveredCell = table.querySelector(`td[data-day-index="${dayIndex}"][data-time-minutes="${coveredMinutes}"]`);
      if (coveredCell) {
        coveredCell.remove();
      }
    }
  });
}

function fetchAndRenderSchedules(type, id, containerId, small=false) {
  if (!id) {
    return;
  }

  fetch(`schedule_actions.php?action=getSchedule&type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}`)
    .then(r => r.json())
    .then(data => {
      if (data && data.success && Array.isArray(data.data)) {
        plotSchedules(containerId, data.data, small);
      }
    })
    .catch(() => {
      // Keep the empty grid if fetching fails.
    });
}

// --- Dropdown population and event logic ---
function setupDropdowns() {
  const scheduleType = document.getElementById('scheduleType');
  const scheduleLabel = document.querySelector('.schedule-label');
  const programDropdown = document.getElementById('programDropdown');
  const classSectionDropdown = document.getElementById('classSectionDropdown');
  const instructorDropdown = document.getElementById('instructorDropdown');
  const roomDropdown = document.getElementById('roomDropdown');

  function activeType() {
    return scheduleType ? scheduleType.value : 'class';
  }

  function updateHeaderAndFilters() {
    const type = activeType();

    if (scheduleLabel) {
      if (type === 'instructor') {
        scheduleLabel.textContent = 'Instructor Schedule';
      } else if (type === 'room') {
        scheduleLabel.textContent = 'Room Schedule';
      } else {
        scheduleLabel.textContent = 'Class Schedule';
      }
    }

    if (programDropdown) {
      programDropdown.classList.toggle('d-none', type !== 'class');
    }
    if (classSectionDropdown) {
      classSectionDropdown.classList.toggle('d-none', type !== 'class');
    }
    if (instructorDropdown) {
      instructorDropdown.classList.toggle('d-none', type !== 'instructor');
    }
    if (roomDropdown) {
      roomDropdown.classList.toggle('d-none', type !== 'room');
    }
  }

  function renderByCurrentSelection() {
    const type = activeType();
    renderMainSchedule();

    if (type === 'class' && classSectionDropdown && classSectionDropdown.value) {
      fetchAndRenderSchedules('class', classSectionDropdown.value, 'scheduleTableContainer', false);
      return;
    }

    if (type === 'instructor' && instructorDropdown && instructorDropdown.value) {
      fetchAndRenderSchedules('instructor', instructorDropdown.value, 'scheduleTableContainer', false);
      return;
    }

    if (type === 'room' && roomDropdown && roomDropdown.value) {
      fetchAndRenderSchedules('room', roomDropdown.value, 'scheduleTableContainer', false);
    }
  }

  // Load programs and set up class section loading
  loadPrograms();
  loadInstructors();
  loadRooms();
  updateHeaderAndFilters();

  if (scheduleType) {
    scheduleType.addEventListener('change', function () {
      updateHeaderAndFilters();
      renderByCurrentSelection();
    });
  }

  if (programDropdown && classSectionDropdown) {
    programDropdown.addEventListener('change', function () {
      loadClassSections(this.value);
      if (activeType() === 'class') {
        clearMainSchedule();
      }
    });

    classSectionDropdown.addEventListener('change', function () {
      if (activeType() === 'class') {
        renderByCurrentSelection();
      }
    });
  }

  if (instructorDropdown) {
    instructorDropdown.addEventListener('change', function () {
      if (activeType() === 'instructor') {
        renderByCurrentSelection();
      }
    });
  }

  if (roomDropdown) {
    roomDropdown.addEventListener('change', function () {
      if (activeType() === 'room') {
        renderByCurrentSelection();
      }
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
