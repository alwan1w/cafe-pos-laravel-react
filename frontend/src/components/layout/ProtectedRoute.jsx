import { Navigate, Outlet } from "react-router-dom";
import useAuthStore from "../../stores/useAuthStore";

export default function ProtectedRoute({ allowedRoles }) {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);

  // Jika tidak ada token (belum login), lempar ke halaman login
  if (!token || !user) {
    return <Navigate to="/login" replace />;
  }

  // Jika route ini butuh role tertentu, dan role user tidak termasuk di dalamnya
  if (allowedRoles && !allowedRoles.includes(user.role)) {
    // Arahkan kembali ke tempat yang sesuai dengan role-nya
    if (user.role === "kasir") return <Navigate to="/pos" replace />;
    if (user.role === "kitchen") return <Navigate to="/kitchen" replace />;
    if (user.role === "bar") return <Navigate to="/bar" replace />;
    return <Navigate to="/admin/dashboard" replace />;
  }

  // Jika aman, render child components (halaman yang dituju)
  return <Outlet />;
}
