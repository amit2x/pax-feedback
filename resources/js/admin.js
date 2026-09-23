import 'bootstrap';
import DataTable from 'datatables.net-bs5';
import axios from 'axios';

window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
}

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('feedback-table');
    if (table) {
        new DataTable(table, {
            processing: true,
            serverSide: true,
            ajax: table.dataset.url,
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
        });
    }
});
