import axios from 'axios';

const csrfMeta = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

const http = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
    },
});

http.interceptors.response.use(
    (res) => res,
    (error) => {
        const message =
            error?.response?.data?.message ||
            (error?.response?.data?.errors &&
                Object.values(error.response.data.errors).flat().join(' ')) ||
            'Something went wrong. Please try again.';
        error.userMessage = message;
        return Promise.reject(error);
    }
);

export default http;
