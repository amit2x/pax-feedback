import 'bootstrap';
import DataTable from 'datatables.net-bs5';
import axios from 'axios';

window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
}

// ============================================================
// Feedback DataTable (server-side)
// ============================================================
function initFeedbackTable() {
    const table = document.getElementById('feedback-table');
    if (!table) return;

    const url = table.dataset.url;

    new DataTable(table, {
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            data: (d) => {
                // Pull current filter form values
                const form = document.getElementById('feedback-filters');
                if (!form) return d;
                new FormData(form).forEach((v, k) => { d[k] = v; });
                return d;
            },
        },
        columns: [
            { data: 'reference_no', name: 'reference_no' },
            { data: 'submitted_at', name: 'submitted_at' },
            { data: 'feedback_type', name: 'feedback_type' },
            { data: 'overall_rating', name: 'overall_rating' },
            { data: 'category_name', name: 'category_name', orderable: false, searchable: false },
            { data: 'location_name', name: 'location_name', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'priority', name: 'priority' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            search: '',
            searchPlaceholder: 'Search reference, comment…',
            emptyTable: 'No feedback found.',
            processing: '<span class="paf-spinner"></span> Loading…',
        },
    });
}

// ============================================================
// Filter submit — reset DataTable on submit
// ============================================================
function initFeedbackFilters() {
    const form = document.getElementById('feedback-filters');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const table = document.getElementById('feedback-table');
        if (table) {
            const dt = DataTable.isDataTable(table) ? DataTable.tables({ api: true }).eq(0) : null;
            // Trigger AJAX reload
            const event = new Event('change');
            table.dispatchEvent(event);
            // Simplest: reload page with query string for filters
            const params = new URLSearchParams(new FormData(form));
            window.location.href = window.location.pathname + '?' + params.toString();
        }
    });

    form.querySelectorAll('[data-reset]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    });
}

// ============================================================
// Confirm delete forms
// ============================================================
function initConfirmForms() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const msg = form.dataset.confirm || 'Are you sure?';
            if (!window.confirm(msg)) {
                e.preventDefault();
            }
        });
    });
}

// ============================================================
// Copy-to-clipboard buttons
// ============================================================
function initCopyButtons() {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const text = btn.dataset.copy;
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(() => { btn.textContent = original; }, 1500);
            });
        });
    });
}

// ============================================================
// Toggle inline CRUD forms (categories, departments, etc.)
// ============================================================
function initInlineToggles() {
    document.querySelectorAll('[data-toggle-target]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.toggleTarget);
            if (!target) return;
            target.hidden = !target.hidden;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initFeedbackTable();
    initFeedbackFilters();
    initConfirmForms();
    initCopyButtons();
    initInlineToggles();
});
