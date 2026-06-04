import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Coffee, Loader2 } from "lucide-react";
import { Toaster, toast } from "sonner";
import api from "../../services/api";
import useAuthStore from "../../stores/useAuthStore";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const navigate = useNavigate();
  const setAuth = useAuthStore((state) => state.setAuth);

  const handleLogin = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      const response = await api.post("/auth/login", { email, password });

      // Simpan ke Zustand Store
      setAuth(response.data.access_token, response.data.user);

      toast.success("Login berhasil!");

      // Redirect berdasarkan role
      const role = response.data.user.role;
      setTimeout(() => {
        if (role === "admin" || role === "owner") navigate("/admin/dashboard");
        else if (role === "kasir") navigate("/pos");
        else if (role === "kitchen") navigate("/kitchen");
        else if (role === "bar") navigate("/bar");
        else navigate("/");
      }, 1000);
    } catch (error) {
      const message =
        error.response?.data?.message || "Terjadi kesalahan saat login";
      toast.error(message);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex flex-col justify-center min-h-screen py-12 bg-gray-50 sm:px-6 lg:px-8">
      <Toaster position="top-right" richColors />

      <div className="text-center sm:mx-auto sm:w-full sm:max-w-md">
        <div className="flex justify-center">
          <div className="p-3 shadow-lg bg-citric-500 rounded-xl">
            <Coffee className="w-10 h-10 text-white" strokeWidth={2.5} />
          </div>
        </div>
        <h2 className="mt-6 text-3xl font-extrabold text-center text-gray-900 font-playfair">
          Sistem Manajemen Kafe
        </h2>
        <p className="mt-2 text-sm text-center text-gray-600">
          Sign in ke akun internal Anda
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="px-4 py-8 bg-white border border-gray-100 shadow-xl sm:rounded-2xl sm:px-10">
          <form className="space-y-6" onSubmit={handleLogin}>
            <div>
              <label className="block text-sm font-medium text-gray-700">
                Email address
              </label>
              <div className="mt-1">
                <input
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm transition-colors"
                  placeholder="admin@kafe.com"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Password
              </label>
              <div className="mt-1">
                <input
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm transition-colors"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div>
              <button
                type="submit"
                disabled={isLoading}
                className="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-citric-600 hover:bg-citric-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-citric-500 disabled:opacity-70 disabled:cursor-not-allowed transition-all"
              >
                {isLoading ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  "Sign in"
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
