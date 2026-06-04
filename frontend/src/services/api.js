import axios from "axios";

const api = axios.create({
  // UBAH BARIS INI: Sesuaikan dengan server php artisan serve yang sedang berjalan
  baseURL: "http://127.0.0.1:8000/api",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Interceptor untuk menyisipkan Token di setiap request
api.interceptors.request.use(
  (config) => {
    // Mengambil token dari localStorage yang disimpan oleh Zustand
    const authStorage = localStorage.getItem("auth-storage");
    if (authStorage) {
      const { state } = JSON.parse(authStorage);
      if (state.token) {
        config.headers.Authorization = `Bearer ${state.token}`;
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

export default api;
