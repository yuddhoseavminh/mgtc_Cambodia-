import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

function showSubmitLoadingDialog(message = 'សូមចាំបន្តិច....') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'swal2-kh-popup',
            title: 'swal2-kh-title',
            htmlContainer: 'swal2-kh-content',
        },
    });
}

function hideSubmitLoadingDialog() {
    if (Swal.isVisible()) {
        Swal.close();
    }
}

window.ArmyRegistrationLoading = {
    show: showSubmitLoadingDialog,
    hide: hideSubmitLoadingDialog,
};

const monthFormatter = new Intl.DateTimeFormat('km-KH', {
    month: 'long',
    year: 'numeric',
});

const dayNumberFormatter = new Intl.NumberFormat('km-KH');
const yearNumberFormatter = new Intl.NumberFormat('km-KH', {
    useGrouping: false,
});
const khmerMonthNames = [
    'មករា',
    'កុម្ភៈ',
    'មីនា',
    'មេសា',
    'ឧសភា',
    'មិថុនា',
    'កក្កដា',
    'សីហា',
    'កញ្ញា',
    'តុលា',
    'វិច្ឆិកា',
    'ធ្នូ',
];

function pad(value) {
    return String(value).padStart(2, '0');
}

function createLocalDate(year, month, day) {
    return new Date(year, month, day, 12, 0, 0, 0);
}

function parseIsoDate(value) {
    if (!value) {
        return null;
    }

    const [year, month, day] = value.split('-').map(Number);

    if (!year || !month || !day) {
        return null;
    }

    return createLocalDate(year, month - 1, day);
}

function formatIsoDate(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

function isSameDay(left, right) {
    return left.getFullYear() === right.getFullYear()
        && left.getMonth() === right.getMonth()
        && left.getDate() === right.getDate();
}

function formatKhmerDisplayDate(date) {
    return `ថ្ងៃទី ${dayNumberFormatter.format(date.getDate())} ខែ ${khmerMonthNames[date.getMonth()]} ឆ្នាំ ${yearNumberFormatter.format(date.getFullYear())}`;
}

function setupDatePicker(root) {
    const hiddenInput = root.querySelector('[data-date-value]');
    const trigger = root.querySelector('[data-date-toggle]');
    const display = root.querySelector('[data-date-display]');
    const panel = root.querySelector('[data-date-panel]');
    const stageLabel = root.querySelector('[data-date-stage]');
    const stepItems = root.querySelectorAll('[data-step]');
    const monthLabel = root.querySelector('[data-date-month-label]');
    const weekdays = root.querySelector('.date-picker-weekdays');
    const grid = root.querySelector('[data-date-grid]');
    const prevButton = root.querySelector('[data-date-prev]');
    const nextButton = root.querySelector('[data-date-next]');
    const clearButton = root.querySelector('[data-date-clear]');
    const todayButton = root.querySelector('[data-date-today]');

    if (!hiddenInput || !trigger || !display || !panel || !stageLabel || !monthLabel || !weekdays || !grid || !prevButton || !nextButton || !clearButton || !todayButton) {
        return;
    }

    const placeholder = root.dataset.placeholder || display.textContent.trim();
    const today = createLocalDate(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    const maxDate = parseIsoDate(hiddenInput.dataset.max);
    const maxYear = maxDate ? maxDate.getFullYear() : null;
    const maxMonth = maxDate ? maxDate.getMonth() : null;

    let selectedDate = parseIsoDate(hiddenInput.value);
    let viewDate = selectedDate ?? maxDate ?? today;
    let viewMode = 'year';
    let yearPageStart = getYearPageStart(viewDate.getFullYear());

    function getYearPageStart(year) {
        return year - 5;
    }

    function setGridMode(mode) {
        grid.classList.remove('date-picker-grid--days', 'date-picker-grid--months', 'date-picker-grid--years');
        grid.classList.add(`date-picker-grid--${mode}`);
    }

    function syncStepUi(mode) {
        const labels = {
            year: 'ជ្រើសរើសឆ្នាំ',
            month: 'ជ្រើសរើសខែ',
            day: 'ជ្រើសរើសថ្ងៃ',
        };

        stageLabel.textContent = labels[mode] ?? '';
        stepItems.forEach((item) => {
            item.classList.toggle('date-picker-step-active', item.dataset.step === mode);
        });
    }

    const syncDisplay = () => {
        display.textContent = selectedDate ? formatKhmerDisplayDate(selectedDate) : placeholder;
        display.classList.toggle('date-picker-placeholder', !selectedDate);
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        root.classList.remove('date-picker-open');
    };

    const openPanel = () => {
        panel.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        root.classList.add('date-picker-open');
        viewMode = 'year';
        yearPageStart = getYearPageStart((selectedDate ?? viewDate).getFullYear());
    };

    const setValue = (date) => {
        selectedDate = date;
        hiddenInput.value = date ? formatIsoDate(date) : '';
        viewDate = date ?? maxDate ?? today;
        yearPageStart = getYearPageStart(viewDate.getFullYear());
        syncDisplay();
        renderCalendar();
    };

    function createOptionButton(label, classes = []) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'date-picker-option';
        classes.forEach((className) => button.classList.add(className));
        button.textContent = label;

        return button;
    }

    function renderYearView() {
        syncStepUi('year');
        monthLabel.textContent = `${yearNumberFormatter.format(yearPageStart)} - ${yearNumberFormatter.format(yearPageStart + 11)}`;
        weekdays.classList.add('hidden');
        setGridMode('years');
        grid.innerHTML = '';

        for (let year = yearPageStart; year < yearPageStart + 12; year += 1) {
            const button = createOptionButton(yearNumberFormatter.format(year));
            const disabled = maxYear !== null && year > maxYear;

            if (disabled) {
                button.disabled = true;
                button.classList.add('date-picker-option-disabled');
            }

            if (selectedDate && selectedDate.getFullYear() === year) {
                button.classList.add('date-picker-option-selected');
            }

            button.addEventListener('click', () => {
                const nextMonth = maxYear === year && maxMonth !== null
                    ? Math.min(viewDate.getMonth(), maxMonth)
                    : viewDate.getMonth();

                viewDate = createLocalDate(year, nextMonth, 1);
                viewMode = 'month';
                renderCalendar();
            });

            grid.appendChild(button);
        }

        prevButton.disabled = false;
        nextButton.disabled = maxYear !== null && yearPageStart + 12 > maxYear;
    }

    function renderMonthView() {
        const year = viewDate.getFullYear();
        syncStepUi('month');
        monthLabel.textContent = `ឆ្នាំ ${yearNumberFormatter.format(year)}`;
        weekdays.classList.add('hidden');
        setGridMode('months');
        grid.innerHTML = '';

        khmerMonthNames.forEach((monthName, monthIndex) => {
            const button = createOptionButton(monthName);
            const disabled = maxYear === year && maxMonth !== null && monthIndex > maxMonth;

            if (disabled) {
                button.disabled = true;
                button.classList.add('date-picker-option-disabled');
            }

            if (selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === monthIndex) {
                button.classList.add('date-picker-option-selected');
            }

            button.addEventListener('click', () => {
                viewDate = createLocalDate(year, monthIndex, 1);
                viewMode = 'day';
                renderCalendar();
            });

            grid.appendChild(button);
        });

        prevButton.disabled = false;
        nextButton.disabled = maxYear !== null && year >= maxYear;
    }

    function renderDayView() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const firstDay = createLocalDate(year, month, 1);
        const startOffset = firstDay.getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPreviousMonth = new Date(year, month, 0).getDate();

        syncStepUi('day');
        monthLabel.textContent = monthFormatter.format(firstDay);
        weekdays.classList.remove('hidden');
        setGridMode('days');
        grid.innerHTML = '';

        for (let index = 0; index < 42; index += 1) {
            let dayNumber = index - startOffset + 1;
            let cellDate;
            let outsideMonth = false;

            if (dayNumber <= 0) {
                cellDate = createLocalDate(year, month - 1, daysInPreviousMonth + dayNumber);
                outsideMonth = true;
            } else if (dayNumber > daysInMonth) {
                cellDate = createLocalDate(year, month + 1, dayNumber - daysInMonth);
                outsideMonth = true;
            } else {
                cellDate = createLocalDate(year, month, dayNumber);
            }

            const disabled = maxDate ? cellDate > maxDate : false;
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'date-picker-day';
            cell.textContent = dayNumberFormatter.format(cellDate.getDate());

            if (outsideMonth) {
                cell.classList.add('date-picker-day-muted');
            }

            if (disabled) {
                cell.disabled = true;
                cell.classList.add('date-picker-day-disabled');
            }

            if (isSameDay(cellDate, today)) {
                cell.classList.add('date-picker-day-today');
            }

            if (selectedDate && isSameDay(cellDate, selectedDate)) {
                cell.classList.add('date-picker-day-selected');
            }

            cell.addEventListener('click', () => {
                setValue(cellDate);
                closePanel();
            });

            grid.appendChild(cell);
        }

        const nextMonthDate = createLocalDate(year, month + 1, 1);
        nextButton.disabled = maxDate ? nextMonthDate > createLocalDate(maxDate.getFullYear(), maxDate.getMonth(), 1) : false;
    }

    function renderCalendar() {
        if (viewMode === 'year') {
            renderYearView();
            return;
        }

        if (viewMode === 'month') {
            renderMonthView();
            return;
        }

        renderDayView();
    }

    trigger.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (panel.classList.contains('hidden')) {
            openPanel();
            renderCalendar();
            return;
        }

        closePanel();
    });

    prevButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (viewMode === 'year') {
            yearPageStart -= 12;
        } else if (viewMode === 'month') {
            viewDate = createLocalDate(viewDate.getFullYear() - 1, viewDate.getMonth(), 1);
        } else {
            viewDate = createLocalDate(viewDate.getFullYear(), viewDate.getMonth() - 1, 1);
        }

        renderCalendar();
    });

    nextButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (viewMode === 'year') {
            yearPageStart += 12;
        } else if (viewMode === 'month') {
            viewDate = createLocalDate(viewDate.getFullYear() + 1, viewDate.getMonth(), 1);
        } else {
            viewDate = createLocalDate(viewDate.getFullYear(), viewDate.getMonth() + 1, 1);
        }

        renderCalendar();
    });

    monthLabel.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (viewMode === 'day') {
            viewMode = 'month';
            renderCalendar();
            return;
        }

        if (viewMode === 'month') {
            viewMode = 'year';
            yearPageStart = getYearPageStart(viewDate.getFullYear());
            renderCalendar();
        }
    });

    clearButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setValue(null);
        closePanel();
    });

    todayButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const targetDate = maxDate && today > maxDate ? maxDate : today;
        setValue(targetDate);
        closePanel();
    });

    panel.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            closePanel();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closePanel();
        }
    });

    const form = root.closest('form');

    if (form) {
        form.addEventListener('reset', () => {
            window.setTimeout(() => {
                selectedDate = parseIsoDate(hiddenInput.value);
                viewDate = selectedDate ?? maxDate ?? today;
                viewMode = 'year';
                yearPageStart = getYearPageStart(viewDate.getFullYear());
                syncDisplay();
                renderCalendar();
                closePanel();
            }, 0);
        });
    }

    syncDisplay();
    renderCalendar();
}

function initDatePickers() {
    document.querySelectorAll('[data-date-picker]').forEach(setupDatePicker);
}

function initSweetAlerts() {
    const payloadElement = document.getElementById('app-sweetalert-data');

    if (!payloadElement) {
        return;
    }

    let payload;

    try {
        payload = JSON.parse(payloadElement.textContent ?? '{}');
    } catch {
        return;
    }

    if (!payload?.text) {
        return;
    }

    Swal.fire({
        icon: payload.icon ?? 'info',
        title: payload.title ?? '',
        text: payload.text,
        confirmButtonText: payload.confirmButtonText ?? 'យល់ព្រម',
        confirmButtonColor: '#356AE6',
        timer: payload.icon === 'success' ? 2600 : undefined,
        timerProgressBar: payload.icon === 'success',
        customClass: {
            popup: 'swal2-kh-popup',
            title: 'swal2-kh-title',
            htmlContainer: 'swal2-kh-content',
            confirmButton: 'swal2-kh-confirm',
            cancelButton: 'swal2-kh-cancel',
        },
    });
}

function initSweetDeleteConfirmations() {
    document.querySelectorAll('form[data-swal-confirm]').forEach((form) => {
        if (form.dataset.swalBound === 'true') {
            return;
        }

        form.dataset.swalBound = 'true';

        form.addEventListener('submit', async (event) => {
            if (form.dataset.swalConfirmed === 'true') {
                delete form.dataset.swalConfirmed;
                return;
            }

            event.preventDefault();

            const result = await Swal.fire({
                icon: 'warning',
                title: form.dataset.swalTitle || 'បញ្ជាក់ការលុប',
                text: form.dataset.swalText || 'តើអ្នកពិតជាចង់លុបទិន្នន័យនេះមែនទេ?',
                confirmButtonText: form.dataset.swalConfirmText || 'បាទ/ចាស លុប',
                cancelButtonText: form.dataset.swalCancelText || 'បោះបង់',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                showCancelButton: true,
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'swal2-kh-popup',
                    title: 'swal2-kh-title',
                    htmlContainer: 'swal2-kh-content',
                    confirmButton: 'swal2-kh-confirm',
                    cancelButton: 'swal2-kh-cancel',
                },
            });

            if (!result.isConfirmed) {
                return;
            }

            const spoofedMethod = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
            const isDeleteRequest = spoofedMethod === 'DELETE';

            if (!isDeleteRequest) {
                form.dataset.swalConfirmed = 'true';
                HTMLFormElement.prototype.submit.call(form);
                return;
            }

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

            submitButtons.forEach((button) => {
                button.disabled = true;
            });

            try {
                const response = await window.axios({
                    url: form.action,
                    method: (form.method || 'POST').toLowerCase(),
                    data: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                    },
                    validateStatus: () => true,
                });

                if (response.status < 200 || response.status >= 300) {
                    const message = response?.data?.message
                        || Object.values(response?.data?.errors || {})?.[0]?.[0]
                        || 'Please check the data and try again.';

                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#356AE6',
                        customClass: {
                            popup: 'swal2-kh-popup',
                            title: 'swal2-kh-title',
                            htmlContainer: 'swal2-kh-content',
                            confirmButton: 'swal2-kh-confirm',
                        },
                    });

                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: form.dataset.swalSuccessTitle || 'ជោគជ័យ',
                    text: form.dataset.swalSuccessText || 'បានលុបទិន្នន័យដោយជោគជ័យ។',
                    confirmButtonText: 'យល់ព្រម',
                    confirmButtonColor: '#356AE6',
                    timer: 1800,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-kh-popup',
                        title: 'swal2-kh-title',
                        htmlContainer: 'swal2-kh-content',
                        confirmButton: 'swal2-kh-confirm',
                    },
                });

                window.location.reload();
            } catch (error) {
                const message = error?.response?.data?.message
                    || Object.values(error?.response?.data?.errors || {})?.[0]?.[0]
                    || 'សូមពិនិត្យទិន្នន័យ ហើយព្យាយាមម្តងទៀត។';

                await Swal.fire({
                    icon: 'error',
                    title: 'មានបញ្ហា',
                    text: message,
                    confirmButtonText: 'បិទ',
                    confirmButtonColor: '#356AE6',
                    customClass: {
                        popup: 'swal2-kh-popup',
                        title: 'swal2-kh-title',
                        htmlContainer: 'swal2-kh-content',
                        confirmButton: 'swal2-kh-confirm',
                    },
                });
            } finally {
                submitButtons.forEach((button) => {
                    button.disabled = false;
                });
            }
        });
    });
}

function initAjaxForms() {
    document.querySelectorAll('form[data-ajax-form]').forEach((form) => {
        if (form.dataset.ajaxBound === 'true') {
            return;
        }

        form.dataset.ajaxBound = 'true';

        const fieldErrorElements = Array.from(form.querySelectorAll('[data-field-error]'));

        const clearFieldErrors = () => {
            fieldErrorElements.forEach((element) => {
                element.textContent = '';
                element.classList.add('hidden');
            });
        };

        const setFieldError = (name, message) => {
            const element = fieldErrorElements.find((candidate) => candidate.dataset.fieldError === name);

            if (!element) {
                return;
            }

            element.textContent = message;
            element.classList.remove('hidden');
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFieldErrors();

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

            submitButtons.forEach((button) => {
                button.disabled = true;
            });

            try {
                await window.axios({
                    url: form.action,
                    method: (form.method || 'POST').toLowerCase(),
                    data: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                    },
                });

                await Swal.fire({
                    icon: 'success',
                    title: form.dataset.ajaxSuccessTitle || 'ជោគជ័យ',
                    text: form.dataset.ajaxSuccessText || 'បានរក្សាទុកទិន្នន័យដោយជោគជ័យ។',
                    confirmButtonText: 'យល់ព្រម',
                    confirmButtonColor: '#356AE6',
                    timer: 1800,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-kh-popup',
                        title: 'swal2-kh-title',
                        htmlContainer: 'swal2-kh-content',
                        confirmButton: 'swal2-kh-confirm',
                    },
                });

                if (form.dataset.ajaxRedirect) {
                    window.location.href = form.dataset.ajaxRedirect;
                    return;
                }

                window.location.reload();
            } catch (error) {
                const errors = error?.response?.data?.errors || {};

                Object.entries(errors).forEach(([name, messages]) => {
                    if (Array.isArray(messages) && messages[0]) {
                        setFieldError(name, messages[0]);
                    }
                });

                await Swal.fire({
                    icon: 'error',
                    title: 'មានបញ្ហា',
                    text: error?.response?.data?.message
                        || Object.values(errors)?.[0]?.[0]
                        || 'សូមពិនិត្យទិន្នន័យ ហើយព្យាយាមម្តងទៀត។',
                    confirmButtonText: 'បិទ',
                    confirmButtonColor: '#356AE6',
                    customClass: {
                        popup: 'swal2-kh-popup',
                        title: 'swal2-kh-title',
                        htmlContainer: 'swal2-kh-content',
                        confirmButton: 'swal2-kh-confirm',
                    },
                });
            } finally {
                submitButtons.forEach((button) => {
                    button.disabled = false;
                });
            }
        });
    });
}

function initSubmitLoadingDialogs() {
    document.querySelectorAll('form[data-submit-loading-text]').forEach((form) => {
        if (form.dataset.submitLoadingBound === 'true') {
            return;
        }

        form.dataset.submitLoadingBound = 'true';

        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented || form.dataset.submitLoadingShown === 'true') {
                return;
            }

            form.dataset.submitLoadingShown = 'true';

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

            submitButtons.forEach((button) => {
                button.disabled = true;
            });

            Swal.fire({
                title: form.dataset.submitLoadingText || 'សូមចាំបន្តិច....',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'swal2-kh-popup',
                    title: 'swal2-kh-title',
                    htmlContainer: 'swal2-kh-content',
                },
            });
        });
    });
}

function initAdminSidebarScrollPersistence() {
    const sidebar = document.querySelector('.admin-sidebar-scroll');

    if (!sidebar) {
        return;
    }

    const storageKey = 'admin-sidebar-scroll';

    const saveScrollPosition = () => {
        window.sessionStorage.setItem(storageKey, String(sidebar.scrollTop));
    };

    const savedScrollTop = Number(window.sessionStorage.getItem(storageKey) ?? '0');

    if (savedScrollTop > 0) {
        window.requestAnimationFrame(() => {
            sidebar.scrollTop = savedScrollTop;
        });
    }

    sidebar.addEventListener('scroll', saveScrollPosition, { passive: true });
    window.addEventListener('beforeunload', saveScrollPosition);

    sidebar.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', saveScrollPosition);
    });
}

function initAdminSidebar() {
    const sidebar = document.querySelector('[data-admin-sidebar]');

    if (!sidebar) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 1024px)');
    const openButtons = document.querySelectorAll('[data-admin-sidebar-open]');
    const closeButtons = sidebar.querySelectorAll('[data-admin-sidebar-close]');
    const navigationLinks = sidebar.querySelectorAll('a[href]');

    const setSidebarState = (isOpen) => {
        if (desktopQuery.matches) {
            delete document.body.dataset.adminSidebarOpen;
            return;
        }

        if (isOpen) {
            document.body.dataset.adminSidebarOpen = 'true';
            return;
        }

        delete document.body.dataset.adminSidebarOpen;
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => setSidebarState(true));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => setSidebarState(false));
    });

    navigationLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (!desktopQuery.matches) {
                setSidebarState(false);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setSidebarState(false);
        }
    });

    desktopQuery.addEventListener('change', () => {
        if (desktopQuery.matches) {
            setSidebarState(false);
        }
    });
}

function initAdminClock() {
    const clock = document.querySelector('[data-admin-clock]');

    if (!clock || clock.dataset.clockReady === 'true') {
        return;
    }

    clock.dataset.clockReady = 'true';

    const timezone = clock.dataset.adminClockTimezone || 'Asia/Phnom_Penh';
    const formatter = new Intl.DateTimeFormat('en-GB', {
        timeZone: timezone,
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });

    const updateClock = () => {
        const parts = formatter.formatToParts(new Date());
        const values = Object.fromEntries(
            parts
                .filter((part) => part.type !== 'literal')
                .map((part) => [part.type, part.value]),
        );

        clock.textContent = `${values.day}/${values.month}/${values.year} / ${values.hour}:${values.minute}:${values.second}`;
    };

    updateClock();
    window.setInterval(updateClock, 1000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            updateClock();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initDatePickers();
        initSweetAlerts();
        initSweetDeleteConfirmations();
        initAjaxForms();
        initSubmitLoadingDialogs();
        initAdminSidebar();
        initAdminSidebarScrollPersistence();
        initAdminClock();
    });
} else {
    initDatePickers();
    initSweetAlerts();
    initSweetDeleteConfirmations();
    initAjaxForms();
    initSubmitLoadingDialogs();
    initAdminSidebar();
    initAdminSidebarScrollPersistence();
    initAdminClock();
}
