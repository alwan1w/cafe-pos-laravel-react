import { create } from "zustand";
import { persist } from "zustand/middleware";

const useAuthStore = create(
  persist(
    (set) => ({
      token: null,
      user: null,

      // Fungsi untuk menyimpan data saat login sukses
      setAuth: (token, user) => set({ token, user }),

      // Fungsi untuk membersihkan data saat logout
      logout: () => set({ token: null, user: null }),
    }),
    {
      name: "auth-storage", // Nama key di localStorage
    },
  ),
);

export default useAuthStore;
