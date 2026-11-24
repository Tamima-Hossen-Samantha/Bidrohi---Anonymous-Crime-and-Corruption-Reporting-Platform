import axios from 'axios';

const api = axios.create({
    baseURL:         '/',
    withCredentials: true,
    headers: {
        'Accept':           'application/json',
        'Content-Type':     'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Get CSRF cookie first
export const initCsrf = () =>
    axios.get('/sanctum/csrf-cookie', { withCredentials: true });

export const checkSession   = ()         => api.get('/admin/session');
export const adminLogout    = ()         => api.post('/admin/logout');
export const getReports     = (params)   => api.get('/api/reports',           { params });
export const getReport      = (id)       => api.get(`/api/reports/${id}`);
export const updateStatus   = (id, data) => api.patch(`/api/reports/${id}/status`, data);
export const getZones       = ()         => api.get('/api/zones');
export const getLeaderboard = ()         => api.get('/api/leaderboard');

export default api;