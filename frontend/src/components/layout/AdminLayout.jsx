import { Outlet, Link, useNavigate, useLocation } from "react-router-dom";
import {
  LayoutDashboard,
  UtensilsCrossed,
  Users,
  Grid,
  Tags,
  LogOut,
  ReceiptText,
} from "lucide-react";
import useAuthStore from "../../stores/useAuthStore";
import api from "../../services/api";
import { toast } from "sonner";

export default function AdminLayout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { user, logout } = useAuthStore();

  const handleLogout = async () => {
    try {
      await api.post("/auth/logout");
    } catch (error) {
      console.error("Logout failed on server", error);
    } finally {
      logout(); // Hapus state di frontend
      toast.success("Berhasil logout");
      navigate("/login");
    }
  };

  const navigation = [
    { name: "Dashboard", href: "/admin/dashboard", icon: LayoutDashboard },
    { name: "Reservasi", href: "/admin/reservations", icon: ReceiptText },
    { name: "Menu & Produk", href: "/admin/products", icon: UtensilsCrossed },
    { name: "Kategori", href: "/admin/categories", icon: Grid },
    { name: "Meja", href: "/admin/tables", icon: LayoutDashboard },
    { name: "Voucher", href: "/admin/vouchers", icon: Tags },
    { name: "Users", href: "/admin/users", icon: Users },
  ];

  return (
    <div className="flex min-h-screen bg-gray-50">
      {/* Sidebar Desktop */}
      <div className="hidden w-64 bg-white border-r border-gray-200 md:flex md:flex-col">
        <div className="flex items-center h-16 px-6 border-b border-gray-200">
          <h1 className="text-xl font-bold font-playfair text-citric-600">
            Kafe Admin
          </h1>
        </div>
        <div className="flex-1 py-4 overflow-y-auto">
          <nav className="px-3 space-y-1">
            {navigation.map((item) => {
              const isActive = location.pathname.startsWith(item.href);
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ${
                    isActive
                      ? "bg-citric-50 text-citric-700"
                      : "text-gray-700 hover:bg-gray-100"
                  }`}
                >
                  <item.icon
                    className={`flex-shrink-0 -ml-1 mr-3 h-5 w-5 ${
                      isActive
                        ? "text-citric-600"
                        : "text-gray-400 group-hover:text-gray-500"
                    }`}
                  />
                  <span className="truncate">{item.name}</span>
                </Link>
              );
            })}
          </nav>
        </div>
        <div className="p-4 border-t border-gray-200">
          <div className="flex items-center gap-3 px-2 mb-4">
            <div className="flex items-center justify-center w-8 h-8 font-bold rounded-full bg-citric-100 text-citric-700">
              {user?.name?.charAt(0) || "A"}
            </div>
            <div className="overflow-hidden text-sm">
              <p className="font-medium text-gray-900 truncate">{user?.name}</p>
              <p className="text-xs text-gray-500 capitalize">{user?.role}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="flex items-center w-full gap-2 px-3 py-2 text-sm font-medium text-red-600 transition-colors rounded-lg hover:bg-red-50"
          >
            <LogOut className="w-4 h-4" /> Logout
          </button>
        </div>
      </div>

      {/* Main Content Area */}
      <div className="flex flex-col flex-1 overflow-hidden">
        <main className="flex-1 p-6 overflow-y-auto md:p-8">
          {/* Outlet adalah tempat di mana halaman anak (seperti DashboardPage) akan dirender */}
          <Outlet />
        </main>
      </div>
    </div>
  );
}
