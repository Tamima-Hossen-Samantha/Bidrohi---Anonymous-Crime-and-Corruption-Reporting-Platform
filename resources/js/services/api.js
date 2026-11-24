import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept':       'application/json',
        'Content-Type': 'application/json',
    },
});

// Attach Sanctum token if present
api.interceptors.request.use(config => {
    const token = localStorage.getItem('bidrohi_token');
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

export const fetchZones      = ()           => api.get('/zones');
export const fetchHeatmap    = ()           => api.get('/zones/heatmap');
export const fetchZone       = (id)         => api.get(`/zones/${id}`);
export const fetchReports    = (params)     => api.get('/reports', { params });
export const fetchLeaderboard= ()           => api.get('/leaderboard');
export const fetchZoneControl= ()           => api.get('/leaderboard/zones');
export const fetchRankDist   = ()           => api.get('/leaderboard/ranks');

export default api;