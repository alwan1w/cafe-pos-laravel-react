import { useEffect, useState } from "react";
import { DollarSign, ShoppingBag, Receipt, TrendingUp } from "lucide-react";
import {
  BarChart,
  Bar,
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from "recharts";
import api from "../../services/api";

export default function DashboardPage() {
  const [summary, setSummary] = useState({
    revenue_today: 0,
    total_transactions: 0,
    average_transaction: 0,
    items_sold: 0,
  });

  const [salesData, setSalesData] = useState([]);
  const [productsData, setProductsData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        // Fetch 3 API sekaligus secara paralel agar lebih cepat
        const [summaryRes, salesRes, productsRes] = await Promise.all([
          api.get("/dashboard/summary"),
          api.get("/dashboard/chart/sales"),
          api.get("/dashboard/chart/products"),
        ]);

        setSummary(summaryRes.data);
        setSalesData(salesRes.data);
        setProductsData(productsRes.data);
      } catch (error) {
        console.error("Gagal mengambil data dashboard", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  const formatRupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(number);
  };

  const statCards = [
    {
      name: "Pendapatan Hari Ini",
      value: formatRupiah(summary.revenue_today),
      icon: DollarSign,
      color: "text-green-600",
      bg: "bg-green-100",
    },
    {
      name: "Total Transaksi",
      value: summary.total_transactions,
      icon: Receipt,
      color: "text-blue-600",
      bg: "bg-blue-100",
    },
    {
      name: "Rata-rata Transaksi",
      value: formatRupiah(summary.average_transaction),
      icon: TrendingUp,
      color: "text-purple-600",
      bg: "bg-purple-100",
    },
    {
      name: "Produk Terjual",
      value: summary.items_sold,
      icon: ShoppingBag,
      color: "text-citric-600",
      bg: "bg-citric-100",
    },
  ];

  return (
    <div className="pb-10 space-y-6">
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold text-gray-900 font-playfair">
          Overview Dashboard
        </h2>
        <button
          onClick={() =>
            window.open("http://backend.test/api/reports/export", "_blank")
          }
          className="px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-900 rounded-lg hover:bg-gray-800"
        >
          Export Laporan Excel
        </button>
      </div>

      {/* Summary Cards - Diperbaiki class tailwind-nya agar teks tidak terpotong */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {statCards.map((item) => (
          <div
            key={item.name}
            className="p-5 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl"
          >
            <div className="flex items-center gap-4">
              <div className={`flex-shrink-0 rounded-lg p-3 ${item.bg}`}>
                <item.icon
                  className={`h-6 w-6 ${item.color}`}
                  aria-hidden="true"
                />
              </div>
              <div className="flex-1 min-w-0">
                <p className="mb-1 text-sm font-medium leading-tight text-gray-500">
                  {item.name}
                </p>
                <p className="text-xl font-bold text-gray-900">
                  {isLoading ? "..." : item.value}
                </p>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Area Grafik Interaktif */}
      <div className="grid grid-cols-1 gap-6 mt-8 lg:grid-cols-2">
        {/* Line Chart: Penjualan Harian */}
        <div className="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
          <h3 className="mb-4 text-base font-bold text-gray-800 font-playfair">
            Grafik Penjualan Harian
          </h3>
          <div className="h-72">
            {isLoading ? (
              <div className="flex items-center justify-center h-full text-gray-400">
                Loading chart...
              </div>
            ) : salesData.length > 0 ? (
              <ResponsiveContainer width="100%" height="100%">
                <LineChart
                  data={salesData}
                  margin={{ top: 5, right: 20, bottom: 5, left: 20 }}
                >
                  <CartesianGrid
                    strokeDasharray="3 3"
                    vertical={false}
                    stroke="#f3f4f6"
                  />
                  <XAxis
                    dataKey="date"
                    tick={{ fontSize: 12, fill: "#6b7280" }}
                    tickFormatter={(val) => val.split("-")[2]}
                  />
                  <YAxis
                    tick={{ fontSize: 12, fill: "#6b7280" }}
                    tickFormatter={(val) => `Rp ${val / 1000}k`}
                    width={70}
                  />
                  <Tooltip
                    formatter={(value) => [formatRupiah(value), "Pendapatan"]}
                    labelFormatter={(label) => `Tanggal: ${label}`}
                  />
                  <Line
                    type="monotone"
                    dataKey="revenue"
                    stroke="#f59e0b"
                    strokeWidth={3}
                    dot={{ r: 4, fill: "#f59e0b" }}
                    activeDot={{ r: 6 }}
                  />
                </LineChart>
              </ResponsiveContainer>
            ) : (
              <div className="flex items-center justify-center h-full text-gray-400">
                Belum ada data penjualan bulan ini.
              </div>
            )}
          </div>
        </div>

        {/* Bar Chart: Produk Terlaris */}
        <div className="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
          <h3 className="mb-4 text-base font-bold text-gray-800 font-playfair">
            Top 10 Produk Terlaris
          </h3>
          <div className="h-72">
            {isLoading ? (
              <div className="flex items-center justify-center h-full text-gray-400">
                Loading chart...
              </div>
            ) : productsData.length > 0 ? (
              <ResponsiveContainer width="100%" height="100%">
                <BarChart
                  data={productsData}
                  layout="vertical"
                  margin={{ top: 5, right: 20, bottom: 5, left: 40 }}
                >
                  <CartesianGrid
                    strokeDasharray="3 3"
                    horizontal={true}
                    vertical={false}
                    stroke="#f3f4f6"
                  />
                  <XAxis
                    type="number"
                    tick={{ fontSize: 12, fill: "#6b7280" }}
                  />
                  <YAxis
                    type="category"
                    dataKey="product_name"
                    tick={{ fontSize: 12, fill: "#374151" }}
                    width={120}
                  />
                  <Tooltip
                    formatter={(value) => [`${value} Porsi`, "Terjual"]}
                  />
                  <Bar
                    dataKey="total_sold"
                    fill="#1e293b"
                    radius={[0, 4, 4, 0]}
                    barSize={24}
                  />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <div className="flex items-center justify-center h-full text-gray-400">
                Belum ada data produk terjual.
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
