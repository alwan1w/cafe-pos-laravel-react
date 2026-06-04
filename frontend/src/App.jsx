import { Routes, Route } from "react-router-dom";
import LoginPage from "./pages/auth/LoginPage";
import ProtectedRoute from "./components/layout/ProtectedRoute";
import ProductPage from "./pages/admin/ProductPage";
import POSPage from "./pages/pos/POSPage";
import AdminLayout from "./components/layout/AdminLayout"; // Import Layout
import DashboardPage from "./pages/admin/DashboardPage"; // Import Dashboard

function App() {
  return (
    <Routes>
      <Route
        path="/"
        element={
          <div className="p-10 text-center">
            Home Public... <a href="/login">Login</a>
          </div>
        }
      />
      <Route path="/login" element={<LoginPage />} />

      {/* RUTE ADMIN (Dibungkus AdminLayout) */}
      <Route element={<ProtectedRoute allowedRoles={["admin", "owner"]} />}>
        <Route element={<ProtectedRoute allowedRoles={["admin", "owner"]} />}>
          <Route element={<AdminLayout />}>
            <Route path="/admin/dashboard" element={<DashboardPage />} />
            {/* Ganti placeholder dengan komponen asli */}
            <Route path="/admin/products" element={<ProductPage />} />
            <Route
              path="/admin/categories"
              element={<div>Halaman Kategori</div>}
            />
            <Route path="/admin/tables" element={<div>Halaman Meja</div>} />
          </Route>
        </Route>
      </Route>

      {/* Rute Kasir (Tidak pakai AdminLayout karena full screen) */}
      <Route
        element={<ProtectedRoute allowedRoles={["kasir", "admin", "owner"]} />}
      >
        {/* Ganti placeholder dengan komponen POSPage */}
        <Route path="/pos" element={<POSPage />} />
      </Route>

      {/* Rute Kasir, Kitchen, Bar (Tidak pakai AdminLayout, punya UI sendiri) */}
      <Route element={<ProtectedRoute allowedRoles={["kasir", "admin"]} />}>
        <Route
          path="/pos"
          element={<div className="p-10">Mesin POS Kasir</div>}
        />
      </Route>
      <Route element={<ProtectedRoute allowedRoles={["kitchen", "admin"]} />}>
        <Route
          path="/kitchen"
          element={<div className="p-10">Layar Dapur</div>}
        />
      </Route>
      <Route element={<ProtectedRoute allowedRoles={["bar", "admin"]} />}>
        <Route path="/bar" element={<div className="p-10">Layar Bar</div>} />
      </Route>
    </Routes>
  );
}

export default App;
