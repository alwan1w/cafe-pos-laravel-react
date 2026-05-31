import axios from 'axios';

const api = axios.create({
    baseURL: 'http://backend.test/api', // Ganti sesuai virtual host backend Laragon
    withCredentials: true, // Wajib untuk Laravel Sanctum CSRF cookies
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token'); // Atau ambil dari Zustand
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;