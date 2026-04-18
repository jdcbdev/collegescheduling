// schedule.js
// Handles rendering, loading, and adding schedules for class/instructor/room views.

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const START_HOUR = 7;
const END_HOUR = 20;
const INTERVAL_MINUTES = 30;
const EVENT_PALETTE = [
  { bg: '#eaf2ff', border: '#1d4ed8' },
  { bg: '#e9f9ef', border: '#15803d' },
  { bg: '#fff8e8', border: '#b45309' },
  { bg: '#ffecec', border: '#b91c1c' },
  { bg: '#f4ecff', border: '#7e22ce' },
  { bg: '#e8fbff', border: '#0f766e' },
  { bg: '#fff1e8', border: '#c2410c' },
  { bg: '#eceffb', border: '#4338ca' },
  { bg: '#f4ffe8', border: '#4d7c0f' },
  { bg: '#ffe8f3', border: '#be185d' }
];

const appState = {
  programs: [],
  classes: [],
  instructors: [],
  rooms: [],
  activeSchoolYearText: 'SY: --',
  subjectsByClass: {},
  currentSchedulesById: {}
};

let scheduleModalInstance = null;
let modalBusy = false;

document.addEventListener('DOMContentLoaded', function () {
  renderMainSchedule();
  ensureAddScheduleModal();
  setupDropdowns();
  loadActiveSchoolYearLabel();
});

function getEl(id) {
  return document.getElementById(id);
}

function getActiveType() {
  const typeEl = getEl('scheduleType');
  return typeEl ? typeEl.value : 'class';
}

function getActiveTypeLabel() {
  const type = getActiveType();
  if (type === 'instructor') return 'Instructor Schedule';
  if (type === 'room') return 'Room Schedule';
  return 'Class Schedule';
}

function updateScheduleHeaderTitle() {
  const scheduleLabel = document.querySelector('.schedule-label');
  if (!scheduleLabel) return;
  scheduleLabel.innerHTML = `${escapeHtml(getActiveTypeLabel())} <span class="text-muted">| ${escapeHtml(appState.activeSchoolYearText)}</span>`;
}

function getCurrentContextSelection() {
  const type = getActiveType();
  const classId = getEl('classSectionDropdown') ? getEl('classSectionDropdown').value : '';
  const instructorId = getEl('instructorDropdown') ? getEl('instructorDropdown').value : '';
  const roomId = getEl('roomDropdown') ? getEl('roomDropdown').value : '';

  if (type === 'class') {
    return { type, id: classId, label: 'class section' };
  }
  if (type === 'instructor') {
    return { type, id: instructorId, label: 'instructor' };
  }
  return { type, id: roomId, label: 'room' };
}

function renderMainSchedule() {
  const container = getEl('scheduleTableContainer');
  if (container) {
    container.innerHTML = generateScheduleTableHtml();
    bindGridCellClickHandlers();
  }
}

function clearMainSchedule() {
  renderMainSchedule();
}

function to12H(hour, min) {
  let h = hour % 12;
  if (h === 0) h = 12;
  const ampm = hour < 12 ? 'AM' : 'PM';
  return `${h}:${min === 0 ? '00' : '30'} ${ampm}`;
}

function formatTimeTo12H(timeValue) {
  if (!timeValue) return '';
  const normalized = String(timeValue).slice(0, 5);
  const [hStr, mStr] = normalized.split(':');
  const hour = Number(hStr);
  const min = Number(mStr);
  if (Number.isNaN(hour) || Number.isNaN(min)) return normalized;
  return to12H(hour, min);
}

function minutesToTimeString(totalMinutes) {
  const hour = Math.floor(totalMinutes / 60);
  const min = totalMinutes % 60;
  return `${String(hour).padStart(2, '0')}:${String(min).padStart(2, '0')}`;
}

function getSelectedModalDays() {
  return Array.from(document.querySelectorAll('input[name="addScheduleDays[]"]:checked')).map(input => input.value);
}

function setModalDaySelection(selectedDays) {
  const selected = new Set((selectedDays || []).map(day => String(day).toLowerCase()));
  const checkboxes = document.querySelectorAll('input[name="addScheduleDays[]"]');
  checkboxes.forEach(cb => {
    cb.checked = selected.has(String(cb.value).toLowerCase());
    cb.disabled = false;
  });
}

function generateScheduleTableHtml() {
  let html = `<table class="table table-bordered table-sm mb-0 table-schedule-main">
    <thead><tr><th class="gc-time-head" style="width:52px"></th>`;
  DAYS.forEach(day => {
    html += `<th>${day}</th>`;
  });
  html += '</tr></thead><tbody>';

  for (let hour = START_HOUR; hour < END_HOUR; hour++) {
    for (let min = 0; min < 60; min += INTERVAL_MINUTES) {
      const minutes = hour * 60 + min;
      const timeLabel = min === 0 ? to12H(hour, min) : '';
      html += `<tr data-time-minutes="${minutes}"><td class="gc-time-cell"><span class="gc-time-label">${timeLabel}</span></td>`;
      for (let dayIndex = 0; dayIndex < DAYS.length; dayIndex++) {
        html += `<td class="sched-grid-cell" data-day-index="${dayIndex}" data-time-minutes="${minutes}"></td>`;
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

function hashString(value) {
  const str = String(value || '');
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = ((hash << 5) - hash) + str.charCodeAt(i);
    hash |= 0;
  }
  return Math.abs(hash);
}

function hslToHex(h, s, l) {
  const sat = s / 100;
  const lig = l / 100;
  const c = (1 - Math.abs((2 * lig) - 1)) * sat;
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
  const m = lig - (c / 2);
  let r = 0;
  let g = 0;
  let b = 0;

  if (h < 60) {
    r = c; g = x; b = 0;
  } else if (h < 120) {
    r = x; g = c; b = 0;
  } else if (h < 180) {
    r = 0; g = c; b = x;
  } else if (h < 240) {
    r = 0; g = x; b = c;
  } else if (h < 300) {
    r = x; g = 0; b = c;
  } else {
    r = c; g = 0; b = x;
  }

  const toHex = (v) => {
    const n = Math.round((v + m) * 255);
    return n.toString(16).padStart(2, '0');
  };

  return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

function makeGeneratedColor(index) {
  const hue = (index * 137.508) % 360;
  return {
    bg: hslToHex(hue, 88, 94),
    border: hslToHex(hue, 72, 38)
  };
}

function getColorKeyByView(item, viewType) {
  const subject = item.subject_code || item.subject_name || '';
  const section = item.class_section || '';
  const instructor = item.instructor_name || '';

  if (viewType === 'instructor') {
    return `${section}|${subject}`;
  }

  return `${subject}|${instructor}|${section}`;
}

function buildColorMapForSchedules(schedules, viewType) {
  const colorMap = {};
  let colorIndex = 0;

  schedules.forEach(item => {
    const key = getColorKeyByView(item, viewType);
    if (!colorMap[key]) {
      if (colorIndex < EVENT_PALETTE.length) {
        colorMap[key] = EVENT_PALETTE[colorIndex];
      } else {
        colorMap[key] = makeGeneratedColor(colorIndex - EVENT_PALETTE.length);
      }
      colorIndex += 1;
    }
  });

  return colorMap;
}

function buildScheduleCellHtml(item, palette) {
  const title = item.subject_name || 'Scheduled';
  const details = [];
  if (item.class_mode) details.push(item.class_mode);
  if (item.class_section) details.push(item.class_section);

  const timeRange = (item.start_time && item.end_time) ? `${formatTimeTo12H(item.start_time)} - ${formatTimeTo12H(item.end_time)}` : '';
  if (timeRange) details.push(timeRange);

  const scheduleId = item && item.id ? Number(item.id) : 0;
  const eventColor = palette || EVENT_PALETTE[0];

  const detailsLine = details.length ? `<div>${escapeHtml(details.join(' | '))}</div>` : '';
  const instructorLine = item.instructor_name ? `<div>${escapeHtml(item.instructor_name)}</div>` : '';
  const roomLine = `<div>${escapeHtml(item.room_name || 'TBA')}</div>`;

  return `
    <div class="p-1 h-100 sched-event-card" data-schedule-id="${scheduleId}" style="background:${eventColor.bg}; border-left:3px solid ${eventColor.border}; font-size:11px; line-height:1.25;">
      <div style="font-weight:600;">${escapeHtml(title)}</div>
      ${detailsLine}
      ${instructorLine}
      ${roomLine}
    </div>
  `;
}

function bindScheduleCardClickHandlers() {
  const container = getEl('scheduleTableContainer');
  if (!container) return;

  const cards = container.querySelectorAll('.sched-event-card[data-schedule-id]');
  cards.forEach(card => {
    card.addEventListener('click', function (event) {
      event.stopPropagation();
      const scheduleId = Number(this.dataset.scheduleId || 0);
      if (scheduleId > 0 && appState.currentSchedulesById[scheduleId]) {
        openEditScheduleModal(scheduleId);
      }
    });
  });
}

function bindGridCellClickHandlers() {
  const container = getEl('scheduleTableContainer');
  if (!container) return;

  const cells = container.querySelectorAll('td[data-day-index][data-time-minutes]');
  cells.forEach(cell => {
    cell.addEventListener('click', function () {
      const context = getCurrentContextSelection();
      if (!context.id) {
        alert(`Please select a ${context.label} first before adding a schedule.`);
        return;
      }

      if (this.classList.contains('sched-occupied-cell')) {
        return;
      }

      const dayIndex = Number(this.dataset.dayIndex);
      const startMinutes = Number(this.dataset.timeMinutes);
      if (Number.isNaN(dayIndex) || Number.isNaN(startMinutes) || dayIndex < 0 || dayIndex > 6) {
        return;
      }

      openAddScheduleModal({
        dayIndex,
        startMinutes,
        endMinutes: Math.min(startMinutes + INTERVAL_MINUTES, END_HOUR * 60),
        contextType: context.type,
        contextId: context.id
      });
    });
  });
}

function plotSchedules(containerId, schedules, viewType='class') {
  const container = getEl(containerId);
  if (!container) return;

  const table = container.querySelector('table');
  if (!table) return;

  const dayStart = START_HOUR * 60;
  const dayEnd = END_HOUR * 60;
  const colorMap = buildColorMapForSchedules(schedules, viewType);
  appState.currentSchedulesById = {};

  schedules.forEach(item => {
    if (item && item.id) {
      appState.currentSchedulesById[Number(item.id)] = item;
    }
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

    startCell.classList.add('sched-occupied-cell');
    if (item && item.id) {
      startCell.dataset.scheduleId = String(item.id);
    }
    startCell.rowSpan = rowspan;
    const key = getColorKeyByView(item, viewType);
    startCell.innerHTML = buildScheduleCellHtml(item, colorMap[key]);

    for (let slot = startSlot + 1; slot < endSlot; slot++) {
      const coveredMinutes = dayStart + (slot * INTERVAL_MINUTES);
      const coveredCell = table.querySelector(`td[data-day-index="${dayIndex}"][data-time-minutes="${coveredMinutes}"]`);
      if (coveredCell) {
        coveredCell.remove();
      }
    }
  });

  bindGridCellClickHandlers();
  bindScheduleCardClickHandlers();
}

function fetchAndRenderSchedules(type, id, containerId) {
  if (!id) {
    return Promise.resolve();
  }

  return fetch(`schedule_actions.php?action=getSchedule&type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}`)
    .then(r => r.json())
    .then(data => {
      if (data && data.success && Array.isArray(data.data)) {
        plotSchedules(containerId, data.data, type);
      }
    })
    .catch(() => {
      // Keep the empty grid if fetching fails.
    });
}

function refreshScheduleByCurrentSelection() {
  const current = getCurrentContextSelection();
  renderMainSchedule();
  if (current.id) {
    return fetchAndRenderSchedules(current.type, current.id, 'scheduleTableContainer');
  }
  return Promise.resolve();
}

function populateSelectOptions(selectEl, placeholder, items, valueKey, labelBuilder, includePlaceholder=true) {
  if (!selectEl) return;
  const placeholderHtml = includePlaceholder ? `<option value="">${placeholder}</option>` : '';
  selectEl.innerHTML = placeholderHtml + items
    .map(item => `<option value="${item[valueKey]}">${escapeHtml(labelBuilder(item))}</option>`)
    .join('');
}

function loadPrograms() {
  return fetch('schedule_actions.php?action=getPrograms')
    .then(r => r.json())
    .then(data => {
      if (data.success && Array.isArray(data.data)) {
        appState.programs = data.data;
      }
      const dropdown = getEl('programDropdown');
      if (dropdown && data.success && Array.isArray(data.data)) {
        populateSelectOptions(dropdown, 'Select Program', data.data, 'id', p => `${p.program_code}`);
      }
    });
}

function loadActiveSchoolYearLabel() {
  fetch('schedule_actions.php?action=getActiveSchoolYear')
    .then(r => r.json())
    .then(data => {
      if (!data || !data.success || !data.data) {
        appState.activeSchoolYearText = 'SY: Not set';
        updateScheduleHeaderTitle();
        return;
      }

      const sy = data.data;
      const semMap = {
        '1': '1st Sem',
        '2': '2nd Sem',
        '3': 'Summer'
      };
      const semText = semMap[String(sy.semester)] || `Sem ${sy.semester}`;
      appState.activeSchoolYearText = `SY: ${sy.start_year}-${sy.end_year} | ${semText}`;
      updateScheduleHeaderTitle();
    })
    .catch(() => {
      appState.activeSchoolYearText = 'SY: Not set';
      updateScheduleHeaderTitle();
    });
}

function loadInstructors() {
  return fetch('schedule_actions.php?action=getInstructors')
    .then(r => r.json())
    .then(data => {
      const dropdown = getEl('instructorDropdown');
      if (data.success && Array.isArray(data.data)) {
        appState.instructors = data.data;
      }
      if (dropdown && data.success && Array.isArray(data.data)) {
        populateSelectOptions(dropdown, 'Select Instructor', data.data, 'id', i => `${i.instructor_code} - ${i.lastname}, ${i.firstname}`);
      }
    });
}

function loadRooms() {
  return fetch('schedule_actions.php?action=getRooms')
    .then(r => r.json())
    .then(data => {
      const dropdown = getEl('roomDropdown');
      if (data.success && Array.isArray(data.data)) {
        appState.rooms = data.data;
      }
      if (dropdown && data.success && Array.isArray(data.data)) {
        populateSelectOptions(dropdown, 'Select Room', data.data, 'id', rm => rm.room_name);
      }
    });
}

function loadAllClassSections() {
  return fetch('schedule_actions.php?action=getAllClassSections')
    .then(r => r.json())
    .then(data => {
      if (data.success && Array.isArray(data.data)) {
        appState.classes = data.data;
      }
    });
}

function loadClassSections(programId) {
  const dropdown = getEl('classSectionDropdown');
  if (!programId) {
    if (dropdown) dropdown.innerHTML = '<option value="">Select Class Section</option>';
    return Promise.resolve();
  }

  return fetch(`schedule_actions.php?action=getClassSections&program_id=${encodeURIComponent(programId)}`)
    .then(r => r.json())
    .then(data => {
      if (dropdown && data.success && Array.isArray(data.data)) {
        populateSelectOptions(dropdown, 'Select Class Section', data.data, 'id', c => c.section_name);
      }
    });
}

function loadSubjectsByClass(classId) {
  if (!classId) {
    return Promise.resolve([]);
  }
  if (appState.subjectsByClass[classId]) {
    return Promise.resolve(appState.subjectsByClass[classId]);
  }

  return fetch(`schedule_actions.php?action=getSubjectsByClass&class_id=${encodeURIComponent(classId)}`)
    .then(r => r.json())
    .then(data => {
      const subjects = (data && data.success && Array.isArray(data.data)) ? data.data : [];
      appState.subjectsByClass[classId] = subjects;
      return subjects;
    })
    .catch(() => []);
}

function getClassById(classId) {
  return appState.classes.find(c => String(c.id) === String(classId)) || null;
}

function getProgramIdByClass(classId) {
  const classData = getClassById(classId);
  return classData && classData.program_id ? String(classData.program_id) : '';
}

function fillModalPrograms(selectedProgramId='') {
  const programSelect = getEl('addScheduleProgramSelect');
  populateSelectOptions(programSelect, 'Select Program', appState.programs, 'id', p => `${p.program_code} - ${p.program_name}`);
  if (programSelect && selectedProgramId) {
    programSelect.value = String(selectedProgramId);
  }
}

function fillModalClassSections(programId, selectedClassId='') {
  const classSelect = getEl('addScheduleClass');
  const classes = programId
    ? appState.classes.filter(c => String(c.program_id) === String(programId))
    : [];

  populateSelectOptions(classSelect, 'Select Class Section', classes, 'id', c => c.section_name);
  if (classSelect && selectedClassId) {
    classSelect.value = String(selectedClassId);
  }
}

function calculateRequiredHours(subject, classMode, semester) {
  if (!subject) return 0;
  const isSummer = semester.includes('Summer');
  let credits = 0;
  if (classMode === 'LAB') {
    credits = Number(subject.lab_credits || 0);
  } else {
    credits = Number(subject.lec_credits || 0);
  }
  let hours = 0;
  if (isSummer) {
    hours = classMode === 'LAB' ? credits * 9 : credits * 3;
  } else {
    hours = classMode === 'LAB' ? credits * 3 : credits * 1;
  }
  return hours;
}

function calculateScheduledHours(subjectCode, classMode) {
  let totalMinutes = 0;
  Object.values(appState.currentSchedulesById).forEach(schedule => {
    if (String(schedule.subject_code || '') === String(subjectCode) && String(schedule.class_mode || '') === String(classMode)) {
      const start = timeStringToMinutes(schedule.start_time);
      const end = timeStringToMinutes(schedule.end_time);
      if (!Number.isNaN(start) && !Number.isNaN(end) && end > start) {
        totalMinutes += (end - start);
      }
    }
  });
  return Math.round((totalMinutes / 60) * 10) / 10;
}

function updateRequiredHours() {
  const subjectSelect = getEl('addScheduleSubject');
  const classModeSelect = getEl('addScheduleClassMode');
  const hoursDisplay = getEl('requiredHoursDisplay');
  if (!hoursDisplay) return;
  const subjectId = subjectSelect ? subjectSelect.value : '';
  const classMode = classModeSelect ? classModeSelect.value : 'LEC';
  const classId = getEl('addScheduleClass') ? getEl('addScheduleClass').value : '';
  if (!subjectId || !classId) {
    hoursDisplay.innerHTML = '';
    return;
  }
  const subjects = appState.subjectsByClass[classId] || [];
  const subject = subjects.find(s => String(s.id) === String(subjectId));
  if (!subject) {
    hoursDisplay.innerHTML = '';
    return;
  }
  const semesterMatch = appState.activeSchoolYearText.match(/(1st Sem|2nd Sem|Summer)/);
  const semester = semesterMatch ? semesterMatch[1] : '1st Sem';
  const requiredHours = calculateRequiredHours(subject, classMode, semester);
  const scheduledHours = calculateScheduledHours(subject.subject_code, classMode);
  const remainingHours = Math.max(0, requiredHours - scheduledHours);
  hoursDisplay.innerHTML = `<small class="text-muted">Required: ${requiredHours}h | Scheduled: ${scheduledHours}h | Remaining: ${remainingHours}h</small>`;
}

function updateClassModeBySubject(subjectId) {
  const classModeSelect = getEl('addScheduleClassMode');
  const classId = getEl('addScheduleClass') ? getEl('addScheduleClass').value : '';
  if (!classModeSelect || !classId) {
    return;
  }

  const subjectList = appState.subjectsByClass[classId] || [];
  const subject = subjectList.find(s => String(s.id) === String(subjectId));

  classModeSelect.disabled = false;
  classModeSelect.innerHTML = '<option value="LEC">LEC</option><option value="LAB">LAB</option>';

  if (!subject) {
    classModeSelect.value = 'LEC';
    return;
  }

  const lecCredits = Number(subject.lec_credits || 0);
  const labCredits = Number(subject.lab_credits || 0);

  if (lecCredits > 0 && labCredits > 0) {
    classModeSelect.value = 'LEC';
    return;
  }

  if (labCredits > 0) {
    classModeSelect.innerHTML = '<option value="LAB">LAB</option>';
    classModeSelect.value = 'LAB';
    classModeSelect.disabled = true;
    return;
  }

  classModeSelect.innerHTML = '<option value="LEC">LEC</option>';
  classModeSelect.value = 'LEC';
  classModeSelect.disabled = true;
}

function ensureAddScheduleModal() {
  if (getEl('addScheduleModal')) {
    scheduleModalInstance = bootstrap.Modal.getOrCreateInstance(getEl('addScheduleModal'));
    return;
  }

  const modalHtml = `
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Schedule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="addScheduleAlert" class="alert alert-danger d-none py-2" role="alert"></div>
            <form id="addScheduleForm">
              <div class="mb-2">
                <label class="form-label mb-1">Day</label>
                <div id="addScheduleDaysWrap" class="d-flex flex-wrap gap-2">
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDayMonday" value="Monday"><label class="form-check-label" for="addScheduleDayMonday">Mon</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDayTuesday" value="Tuesday"><label class="form-check-label" for="addScheduleDayTuesday">Tue</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDayWednesday" value="Wednesday"><label class="form-check-label" for="addScheduleDayWednesday">Wed</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDayThursday" value="Thursday"><label class="form-check-label" for="addScheduleDayThursday">Thu</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDayFriday" value="Friday"><label class="form-check-label" for="addScheduleDayFriday">Fri</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDaySaturday" value="Saturday"><label class="form-check-label" for="addScheduleDaySaturday">Sat</label></div>
                  <div class="form-check form-check-inline m-0"><input class="form-check-input" type="checkbox" name="addScheduleDays[]" id="addScheduleDaySunday" value="Sunday"><label class="form-check-label" for="addScheduleDaySunday">Sun</label></div>
                </div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <label class="form-label mb-1">Start Time</label>
                  <input id="addScheduleStartTime" type="time" class="form-control" required>
                </div>
                <div class="col-6">
                  <label class="form-label mb-1">End Time</label>
                  <input id="addScheduleEndTime" type="time" class="form-control" required>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1">Program</label>
                <select id="addScheduleProgramSelect" class="form-select" required></select>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1">Class Section</label>
                <select id="addScheduleClass" class="form-select" required></select>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1">Subject</label>
                <select id="addScheduleSubject" class="form-select"></select>
                <div id="requiredHoursDisplay" class="mt-1"></div>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1">Class Mode</label>
                <select id="addScheduleClassMode" class="form-select" required>
                  <option value="LEC">LEC</option>
                  <option value="LAB">LAB</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1">Instructor</label>
                <select id="addScheduleInstructor" class="form-select"></select>
              </div>
              <div class="mb-0">
                <label class="form-label mb-1">Room</label>
                <select id="addScheduleRoom" class="form-select"></select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger me-auto d-none" id="deleteScheduleBtn">Delete</button>
            <button type="button" class="btn btn-primary btn-cancel-all" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveScheduleBtn">Save Schedule</button>
          </div>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
  scheduleModalInstance = bootstrap.Modal.getOrCreateInstance(getEl('addScheduleModal'));

  const classSelect = getEl('addScheduleClass');
  const programSelect = getEl('addScheduleProgramSelect');
  if (programSelect) {
    programSelect.addEventListener('change', function () {
      fillModalClassSections(this.value);
      populateSelectOptions(getEl('addScheduleSubject'), 'Select Subject', [], 'id', s => `${s.subject_code} - ${s.subject_name}`);
      updateClassModeBySubject('');
      updateRequiredHours();
    });
  }

  if (classSelect) {
    classSelect.addEventListener('change', function () {
      const programId = getProgramIdByClass(this.value);
      if (programSelect && programId) {
        programSelect.value = programId;
      }
      loadSubjectsByClass(this.value).then(subjects => {
        populateSelectOptions(getEl('addScheduleSubject'), 'Select Subject', subjects, 'id', s => `${s.subject_code} - ${s.subject_name}`);
        updateClassModeBySubject(getEl('addScheduleSubject') ? getEl('addScheduleSubject').value : '');
        updateRequiredHours();
      });
    });
  }

  const subjectSelect = getEl('addScheduleSubject');
  if (subjectSelect) {
    subjectSelect.addEventListener('change', function () {
      updateClassModeBySubject(this.value);
      updateRequiredHours();
    });
  }

  const classModeSelect = getEl('addScheduleClassMode');
  if (classModeSelect) {
    classModeSelect.addEventListener('change', function () {
      updateRequiredHours();
    });
  }

  const saveBtn = getEl('saveScheduleBtn');
  if (saveBtn) {
    saveBtn.addEventListener('click', saveScheduleFromModal);
  }

  const deleteBtn = getEl('deleteScheduleBtn');
  if (deleteBtn) {
    deleteBtn.addEventListener('click', deleteScheduleFromModal);
  }

  const modalEl = getEl('addScheduleModal');
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function () {
      modalBusy = false;
      setModalAlert('');
      const form = getEl('addScheduleForm');
      if (form) form.reset();
      const saveBtnRef = getEl('saveScheduleBtn');
      if (saveBtnRef) saveBtnRef.disabled = false;
      const deleteBtnRef = getEl('deleteScheduleBtn');
      if (deleteBtnRef) deleteBtnRef.classList.add('d-none');
      setModalDaySelection([]);
      const programRef = getEl('addScheduleProgramSelect');
      if (programRef) programRef.disabled = false;
      const classRef = getEl('addScheduleClass');
      if (classRef) classRef.disabled = false;
      const modalTitleRef = modalEl.querySelector('.modal-title');
      if (modalTitleRef) modalTitleRef.textContent = 'Add Schedule';
      modalEl.dataset.mode = 'add';
      modalEl.dataset.scheduleId = '';
      updateRequiredHours();

      // Hard cleanup to prevent leftover overlay/z-index issues.
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('overflow');
      document.body.style.removeProperty('padding-right');
    });
  }
}

function setModalAlert(message) {
  const alertBox = getEl('addScheduleAlert');
  if (!alertBox) return;
  if (!message) {
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
    return;
  }
  alertBox.textContent = message;
  alertBox.classList.remove('d-none');
}

function openAddScheduleModal(payload) {
  ensureAddScheduleModal();
  const dayName = DAYS[payload.dayIndex];

  setModalAlert('');
  setModalDaySelection([dayName]);
  getEl('addScheduleStartTime').value = minutesToTimeString(payload.startMinutes);
  getEl('addScheduleEndTime').value = minutesToTimeString(payload.endMinutes);

  const modalEl = getEl('addScheduleModal');
  if (modalEl) {
    modalEl.dataset.mode = 'add';
    modalEl.dataset.scheduleId = '';
    const titleEl = modalEl.querySelector('.modal-title');
    if (titleEl) titleEl.textContent = 'Add Schedule';
  }
  const saveBtn = getEl('saveScheduleBtn');
  if (saveBtn) {
    saveBtn.textContent = 'Save Schedule';
  }
  const deleteBtn = getEl('deleteScheduleBtn');
  if (deleteBtn) {
    deleteBtn.classList.add('d-none');
  }

  fillModalPrograms();
  fillModalClassSections('');
  populateSelectOptions(getEl('addScheduleInstructor'), 'Optional Instructor', appState.instructors, 'id', i => `${i.instructor_code} - ${i.lastname}, ${i.firstname}`);
  populateSelectOptions(getEl('addScheduleRoom'), 'Optional Room', appState.rooms, 'id', rm => rm.room_name);
  populateSelectOptions(getEl('addScheduleSubject'), 'Select Subject', [], 'id', s => `${s.subject_code} - ${s.subject_name}`);
  const classModeSelect = getEl('addScheduleClassMode');
  if (classModeSelect) {
    classModeSelect.disabled = false;
    classModeSelect.innerHTML = '<option value="LEC">LEC</option><option value="LAB">LAB</option>';
    classModeSelect.value = 'LEC';
  }

  const classSelect = getEl('addScheduleClass');
  const programSelect = getEl('addScheduleProgramSelect');
  const instructorSelect = getEl('addScheduleInstructor');
  const roomSelect = getEl('addScheduleRoom');

  if (programSelect) programSelect.disabled = false;
  classSelect.disabled = false;
  instructorSelect.disabled = false;
  roomSelect.disabled = false;

  let selectedProgramId = '';
  if (payload.contextType === 'class') {
    selectedProgramId = getProgramIdByClass(payload.contextId);
    fillModalPrograms(selectedProgramId);
    fillModalClassSections(selectedProgramId, payload.contextId);
    classSelect.value = payload.contextId;
    if (programSelect) programSelect.disabled = true;
    classSelect.disabled = true;
  } else if (payload.contextType === 'instructor') {
    instructorSelect.value = payload.contextId;
    instructorSelect.disabled = true;
  } else if (payload.contextType === 'room') {
    roomSelect.value = payload.contextId;
    roomSelect.disabled = true;
  }

  if (!selectedProgramId && programSelect && programSelect.value) {
    selectedProgramId = programSelect.value;
    fillModalClassSections(selectedProgramId);
  }

  const selectedClassId = classSelect.value;
  if (selectedClassId) {
    loadSubjectsByClass(selectedClassId).then(subjects => {
      populateSelectOptions(getEl('addScheduleSubject'), 'Select Subject', subjects, 'id', s => `${s.subject_code} - ${s.subject_name}`);
      updateClassModeBySubject(getEl('addScheduleSubject') ? getEl('addScheduleSubject').value : '');
      updateRequiredHours();
    });
  }

  if (modalEl) {
    modalEl.dataset.dayIndex = String(payload.dayIndex);
  }
  scheduleModalInstance.show();
}

function openEditScheduleModal(scheduleId) {
  const schedule = appState.currentSchedulesById[scheduleId];
  if (!schedule) {
    return;
  }

  ensureAddScheduleModal();
  setModalAlert('');

  const modalEl = getEl('addScheduleModal');
  const programSelect = getEl('addScheduleProgramSelect');
  const classSelect = getEl('addScheduleClass');
  const instructorSelect = getEl('addScheduleInstructor');
  const roomSelect = getEl('addScheduleRoom');
  const subjectSelect = getEl('addScheduleSubject');
  const classModeSelect = getEl('addScheduleClassMode');
  const saveBtn = getEl('saveScheduleBtn');
  const deleteBtn = getEl('deleteScheduleBtn');

  if (modalEl) {
    modalEl.dataset.mode = 'edit';
    modalEl.dataset.scheduleId = String(scheduleId);
    const titleEl = modalEl.querySelector('.modal-title');
    if (titleEl) titleEl.textContent = 'Edit Schedule';
  }
  if (saveBtn) {
    saveBtn.textContent = 'Update Schedule';
  }
  if (deleteBtn) {
    deleteBtn.classList.remove('d-none');
  }

  setModalDaySelection([schedule.day_of_week || '']);
  getEl('addScheduleStartTime').value = String(schedule.start_time || '').slice(0, 5);
  getEl('addScheduleEndTime').value = String(schedule.end_time || '').slice(0, 5);

  const selectedProgramId = getProgramIdByClass(schedule.class_id);
  fillModalPrograms(selectedProgramId);
  fillModalClassSections(selectedProgramId, schedule.class_id);
  populateSelectOptions(instructorSelect, 'Optional Instructor', appState.instructors, 'id', i => `${i.instructor_code} - ${i.lastname}, ${i.firstname}`);
  populateSelectOptions(roomSelect, 'Optional Room', appState.rooms, 'id', rm => rm.room_name);

  if (programSelect) {
    programSelect.disabled = false;
  }

  if (classSelect) {
    classSelect.disabled = false;
    classSelect.value = schedule.class_id ? String(schedule.class_id) : '';
  }
  if (instructorSelect) {
    instructorSelect.disabled = false;
    instructorSelect.value = schedule.instructor_id ? String(schedule.instructor_id) : '';
  }
  if (roomSelect) {
    roomSelect.disabled = false;
    roomSelect.value = schedule.room_id ? String(schedule.room_id) : '';
  }

  if (classModeSelect) {
    classModeSelect.disabled = false;
    classModeSelect.innerHTML = '<option value="LEC">LEC</option><option value="LAB">LAB</option>';
    classModeSelect.value = (schedule.class_mode === 'LAB') ? 'LAB' : 'LEC';
  }

  const selectedClassId = classSelect ? classSelect.value : '';
  loadSubjectsByClass(selectedClassId).then(subjects => {
    populateSelectOptions(subjectSelect, 'Select Subject', subjects, 'id', s => `${s.subject_code} - ${s.subject_name}`);
    if (subjectSelect) {
      const matched = subjects.find(s => String(s.subject_code || '') === String(schedule.subject_code || ''));
      subjectSelect.value = matched ? String(matched.id) : '';
      updateClassModeBySubject(subjectSelect.value);
      if (classModeSelect) {
        classModeSelect.value = (schedule.class_mode === 'LAB') ? 'LAB' : 'LEC';
      }
      updateRequiredHours();
    }
  });

  scheduleModalInstance.show();
}

function getModalMode() {
  const modalEl = getEl('addScheduleModal');
  return modalEl && modalEl.dataset.mode ? modalEl.dataset.mode : 'add';
}

function saveScheduleFromModal() {
  if (modalBusy) return;

  const days = getSelectedModalDays();
  const programId = getEl('addScheduleProgramSelect').value;
  const classId = getEl('addScheduleClass').value;
  const instructorId = getEl('addScheduleInstructor').value;
  const roomId = getEl('addScheduleRoom').value;
  const subjectId = getEl('addScheduleSubject').value;
  const classMode = getEl('addScheduleClassMode').value;
  const startTime = getEl('addScheduleStartTime').value;
  const endTime = getEl('addScheduleEndTime').value;

  if (!classId) {
    setModalAlert('Class section is required.');
    return;
  }
  if (!programId) {
    setModalAlert('Program is required.');
    return;
  }
  if (!subjectId) {
    setModalAlert('Subject is required.');
    return;
  }
  if (days.length === 0) {
    setModalAlert('Select at least one day.');
    return;
  }
  if (!startTime || !endTime) {
    setModalAlert('Start time and end time are required.');
    return;
  }
  if (startTime >= endTime) {
    setModalAlert('End time must be later than start time.');
    return;
  }

  modalBusy = true;
  const saveBtn = getEl('saveScheduleBtn');
  if (saveBtn) saveBtn.disabled = true;
  setModalAlert('');

  const payload = new URLSearchParams();
  const mode = getModalMode();
  const scheduleId = getEl('addScheduleModal') ? Number(getEl('addScheduleModal').dataset.scheduleId || 0) : 0;

  if (mode === 'edit' && days.length !== 1) {
    setModalAlert('Edit mode allows only one selected day.');
    modalBusy = false;
    if (saveBtn) saveBtn.disabled = false;
    return;
  }

  payload.set('action', mode === 'edit' ? 'updateSchedule' : 'addSchedule');
  if (mode === 'edit' && scheduleId > 0) {
    payload.set('id', String(scheduleId));
  }
  payload.set('class_id', classId);
  payload.set('subject_id', subjectId || '');
  payload.set('class_mode', classMode || 'LEC');
  payload.set('instructor_id', instructorId || '');
  payload.set('room_id', roomId || '');
  payload.set('start_time', startTime);
  payload.set('end_time', endTime);

  if (mode === 'edit') {
    payload.set('day_of_week', days[0]);
    fetch('schedule_actions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: payload.toString()
    })
      .then(r => r.json())
      .then(data => {
        if (!data || !data.success) {
          if (data && Array.isArray(data.conflicts) && data.conflicts.length > 0) {
            const first = data.conflicts[0];
            const msg = `Conflict: ${first.day_of_week} ${formatTimeTo12H(first.start_time)}-${formatTimeTo12H(first.end_time)} (${first.class_section || 'No class'} | ${first.instructor_name || 'No instructor'} | ${first.room_name || 'No room'})`;
            setModalAlert(msg);
          } else {
            setModalAlert((data && data.message) ? data.message : 'Unable to save schedule.');
          }
          return;
        }

        scheduleModalInstance.hide();
        refreshScheduleByCurrentSelection();
      })
      .catch(() => {
        setModalAlert('Failed to save schedule. Please try again.');
      })
      .finally(() => {
        modalBusy = false;
        if (saveBtn) saveBtn.disabled = false;
      });
    return;
  }

  const requests = days.map(day => {
    const addPayload = new URLSearchParams(payload.toString());
    addPayload.set('action', 'addSchedule');
    addPayload.set('day_of_week', day);
    return fetch('schedule_actions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: addPayload.toString()
    }).then(r => r.json()).catch(() => ({ success: false, message: 'Failed to save schedule.' }));
  });

  Promise.all(requests)
    .then(results => {
      const failed = results.filter(r => !r || !r.success);
      if (failed.length > 0) {
        const first = failed[0];
        if (first && Array.isArray(first.conflicts) && first.conflicts.length > 0) {
          const c = first.conflicts[0];
          setModalAlert(`Saved ${results.length - failed.length}/${results.length}. Conflict: ${c.day_of_week} ${formatTimeTo12H(c.start_time)}-${formatTimeTo12H(c.end_time)}.`);
        } else {
          setModalAlert(`Saved ${results.length - failed.length}/${results.length}. ${(first && first.message) ? first.message : 'Unable to save some schedules.'}`);
        }
        refreshScheduleByCurrentSelection();
        return;
      }

      scheduleModalInstance.hide();
      refreshScheduleByCurrentSelection();
    })
    .finally(() => {
      modalBusy = false;
      if (saveBtn) saveBtn.disabled = false;
    });
}

function deleteScheduleFromModal() {
  if (modalBusy) return;

  const mode = getModalMode();
  const scheduleId = getEl('addScheduleModal') ? Number(getEl('addScheduleModal').dataset.scheduleId || 0) : 0;
  if (mode !== 'edit' || scheduleId <= 0) {
    return;
  }

  const proceed = confirm('Delete this schedule?');
  if (!proceed) {
    return;
  }

  modalBusy = true;
  const deleteBtn = getEl('deleteScheduleBtn');
  if (deleteBtn) deleteBtn.disabled = true;
  setModalAlert('');

  const payload = new URLSearchParams();
  payload.set('action', 'deleteSchedule');
  payload.set('id', String(scheduleId));

  fetch('schedule_actions.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: payload.toString()
  })
    .then(r => r.json())
    .then(data => {
      if (!data || !data.success) {
        setModalAlert((data && data.message) ? data.message : 'Unable to delete schedule.');
        return;
      }
      scheduleModalInstance.hide();
      refreshScheduleByCurrentSelection();
    })
    .catch(() => {
      setModalAlert('Failed to delete schedule. Please try again.');
    })
    .finally(() => {
      modalBusy = false;
      if (deleteBtn) deleteBtn.disabled = false;
    });
}

// --- Dropdown population and event logic ---
function setupDropdowns() {
  const scheduleType = getEl('scheduleType');
  const programDropdown = getEl('programDropdown');
  const classSectionDropdown = getEl('classSectionDropdown');
  const instructorDropdown = getEl('instructorDropdown');
  const roomDropdown = getEl('roomDropdown');

  function updateHeaderAndFilters() {
    const type = getActiveType();
    updateScheduleHeaderTitle();

    if (programDropdown) programDropdown.classList.toggle('d-none', type !== 'class');
    if (classSectionDropdown) classSectionDropdown.classList.toggle('d-none', type !== 'class');
    if (instructorDropdown) instructorDropdown.classList.toggle('d-none', type !== 'instructor');
    if (roomDropdown) roomDropdown.classList.toggle('d-none', type !== 'room');
  }

  Promise.all([
    loadPrograms(),
    loadInstructors(),
    loadRooms(),
    loadAllClassSections()
  ]).finally(() => {
    updateHeaderAndFilters();
    refreshScheduleByCurrentSelection();
  });

  if (scheduleType) {
    scheduleType.addEventListener('change', function () {
      updateHeaderAndFilters();
      refreshScheduleByCurrentSelection();
    });
  }

  if (programDropdown && classSectionDropdown) {
    programDropdown.addEventListener('change', function () {
      loadClassSections(this.value).finally(() => {
        if (getActiveType() === 'class') {
          clearMainSchedule();
        }
      });
    });

    classSectionDropdown.addEventListener('change', function () {
      if (getActiveType() === 'class') {
        refreshScheduleByCurrentSelection();
      }
    });
  }

  if (instructorDropdown) {
    instructorDropdown.addEventListener('change', function () {
      if (getActiveType() === 'instructor') {
        refreshScheduleByCurrentSelection();
      }
    });
  }

  if (roomDropdown) {
    roomDropdown.addEventListener('change', function () {
      if (getActiveType() === 'room') {
        refreshScheduleByCurrentSelection();
      }
    });
  }
}
