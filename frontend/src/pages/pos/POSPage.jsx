import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
  Search,
  ShoppingCart,
  Trash2,
  Plus,
  Minus,
  Tag,
  CreditCard,
  Banknote,
  LogOut,
} from "lucide-react";
import { toast, Toaster } from "sonner";
import api from "../../services/api";
import useAuthStore from "../../stores/useAuthStore";

export default function POSPage() {
  const navigate = useNavigate();
  const { user, logout } = useAuthStore();

  // Data State
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [activeCategory, setActiveCategory] = useState("all");
  const [searchTerm, setSearchTerm] = useState("");
  const [isLoading, setIsLoading] = useState(true);

  // Cart State
  const [cart, setCart] = useState([]);
  const [voucherCode, setVoucherCode] = useState("");
  const [activeVoucher, setActiveVoucher] = useState(null);
  const [customerName, setCustomerName] = useState("Pelanggan Walk-in");

  // Checkout Modal State
  const [isCheckoutOpen, setIsCheckoutOpen] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState("cash");
  const [paymentAmount, setPaymentAmount] = useState("");
  const [isProcessing, setIsProcessing] = useState(false);

  // Fetch Data Awal
  useEffect(() => {
    const fetchData = async () => {
      try {
        const [prodRes, catRes] = await Promise.all([
          api.get("/products"),
          api.get("/categories"),
        ]);
        // Hanya tampilkan produk & kategori yang aktif
        setProducts(prodRes.data.data.filter((p) => p.is_active));
        setCategories(catRes.data.data.filter((c) => c.is_active));
      } catch (error) {
        toast.error("Gagal memuat data menu");
      } finally {
        setIsLoading(false);
      }
    };
    fetchData();
  }, []);

  // Format Mata Uang
  const formatRupiah = (number) =>
    new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(number);

  // --- LOGIKA KERANJANG ---
  const addToCart = (product) => {
    setCart((prevCart) => {
      const existingItem = prevCart.find(
        (item) => item.product_id === product.id,
      );
      if (existingItem) {
        return prevCart.map((item) =>
          item.product_id === product.id
            ? { ...item, qty: item.qty + 1 }
            : item,
        );
      }
      return [
        ...prevCart,
        {
          product_id: product.id,
          name: product.name,
          price: product.price,
          qty: 1,
          notes: "",
        },
      ];
    });
  };

  const updateQty = (id, change) => {
    setCart((prevCart) =>
      prevCart.map((item) => {
        if (item.product_id === id) {
          const newQty = item.qty + change;
          return newQty > 0 ? { ...item, qty: newQty } : item;
        }
        return item;
      }),
    );
  };

  const removeItem = (id) =>
    setCart((prevCart) => prevCart.filter((item) => item.product_id !== id));

  // --- KALKULASI HARGA ---
  const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

  let discountAmount = 0;
  if (activeVoucher && subtotal >= activeVoucher.min_purchase) {
    if (activeVoucher.type === "fixed") {
      discountAmount = activeVoucher.discount_value;
    } else {
      let calc = (subtotal * activeVoucher.discount_value) / 100;
      discountAmount =
        activeVoucher.max_discount && calc > activeVoucher.max_discount
          ? activeVoucher.max_discount
          : calc;
    }
    if (discountAmount > subtotal) discountAmount = subtotal;
  }

  const amountAfterDiscount = subtotal - discountAmount;
  const taxAmount = Math.round(amountAfterDiscount * 0.11); // Pajak 11%
  const grandTotal = amountAfterDiscount + taxAmount;

  // --- LOGIKA VOUCHER ---
  const handleCheckVoucher = async () => {
    if (!voucherCode) return;
    try {
      const res = await api.post("/pos/vouchers/check", { code: voucherCode });
      setActiveVoucher(res.data.data);
      toast.success("Voucher berhasil dipasang!");
    } catch (error) {
      setActiveVoucher(null);
      toast.error(error.response?.data?.message || "Voucher tidak valid");
    }
  };

  // --- LOGIKA CHECKOUT ---
  const handleCheckout = async () => {
    if (paymentMethod === "cash" && parseInt(paymentAmount || 0) < grandTotal) {
      return toast.error("Uang tunai kurang dari total tagihan!");
    }

    setIsProcessing(true);
    try {
      const payload = {
        customer_name: customerName,
        voucher_code: activeVoucher ? activeVoucher.code : null,
        items: cart.map((item) => ({
          product_id: item.product_id,
          qty: item.qty,
          notes: item.notes,
        })),
        payment_method: paymentMethod,
        payment_amount:
          paymentMethod === "qris" ? grandTotal : parseInt(paymentAmount),
      };

      await api.post("/pos/transactions", payload);

      toast.success("Transaksi Berhasil!");
      setCart([]);
      setActiveVoucher(null);
      setVoucherCode("");
      setIsCheckoutOpen(false);
      setPaymentAmount("");
    } catch (error) {
      toast.error(error.response?.data?.message || "Transaksi gagal diproses");
    } finally {
      setIsProcessing(false);
    }
  };

  // --- FILTER PRODUK ---
  const filteredProducts = products.filter((p) => {
    const matchCategory =
      activeCategory === "all" || p.category_id === activeCategory;
    const matchSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase());
    return matchCategory && matchSearch;
  });

  const handleLogout = () => {
    logout();
    navigate("/login");
  };

  return (
    <div className="flex h-screen overflow-hidden font-sans bg-gray-100">
      <Toaster position="top-center" richColors />

      {/* KIRI: Area Produk */}
      <div className="relative flex flex-col flex-1 h-full">
        {/* Header POS */}
        <header className="z-10 flex items-center justify-between px-6 py-4 bg-white shadow-sm">
          <div className="flex items-center gap-4">
            <h1 className="text-2xl font-bold font-playfair text-citric-600">
              POS Kasir
            </h1>
            <div className="w-px h-6 bg-gray-300"></div>
            <p className="text-sm text-gray-500">
              Kasir:{" "}
              <span className="font-semibold text-gray-800">{user?.name}</span>
            </p>
          </div>
          <div className="flex items-center gap-4">
            <div className="relative">
              <Search className="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" />
              <input
                type="text"
                placeholder="Cari menu..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-64 py-2 pr-4 text-sm transition-all bg-gray-100 border-transparent rounded-lg pl-9 focus:bg-white focus:ring-2 focus:ring-citric-500 focus:border-transparent"
              />
            </div>
            {user?.role === "admin" && (
              <button
                onClick={() => navigate("/admin/dashboard")}
                className="text-sm font-medium text-gray-600 hover:text-citric-600"
              >
                Kembali ke Admin
              </button>
            )}
            <button
              onClick={handleLogout}
              className="p-2 text-red-500 transition-colors rounded-lg hover:bg-red-50"
              title="Logout"
            >
              <LogOut className="w-5 h-5" />
            </button>
          </div>
        </header>

        {/* Filter Kategori */}
        <div className="z-10 flex gap-2 px-6 py-4 overflow-x-auto bg-white border-t border-gray-100 shadow-sm whitespace-nowrap hide-scrollbar">
          <button
            onClick={() => setActiveCategory("all")}
            className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${activeCategory === "all" ? "bg-citric-600 text-white" : "bg-gray-100 text-gray-700 hover:bg-gray-200"}`}
          >
            Semua Menu
          </button>
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveCategory(cat.id)}
              className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${activeCategory === cat.id ? "bg-citric-600 text-white" : "bg-gray-100 text-gray-700 hover:bg-gray-200"}`}
            >
              {cat.name}
            </button>
          ))}
        </div>

        {/* Grid Produk */}
        <div className="flex-1 p-6 overflow-y-auto bg-gray-50">
          {isLoading ? (
            <div className="flex items-center justify-center h-full text-gray-400">
              Memuat daftar menu...
            </div>
          ) : (
            <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
              {filteredProducts.map((product) => (
                <div
                  key={product.id}
                  onClick={() => addToCart(product)}
                  className="flex flex-col h-48 overflow-hidden transition-all transform bg-white border border-gray-100 shadow-sm cursor-pointer rounded-xl hover:shadow-md hover:-translate-y-1"
                >
                  <div className="flex-shrink-0 bg-gray-200 h-28">
                    {product.image ? (
                      <img
                        src={product.image}
                        alt={product.name}
                        className="object-cover w-full h-full"
                      />
                    ) : (
                      <div className="flex items-center justify-center w-full h-full text-xs text-gray-400">
                        No Image
                      </div>
                    )}
                  </div>
                  <div className="flex flex-col justify-between flex-1 p-3">
                    <h3 className="text-sm font-bold leading-tight text-gray-800 line-clamp-2">
                      {product.name}
                    </h3>
                    <p className="mt-1 text-sm font-semibold text-citric-600">
                      {formatRupiah(product.price)}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* KANAN: Panel Keranjang */}
      <div className="w-[400px] bg-white shadow-[-4px_0_15px_rgba(0,0,0,0.05)] flex flex-col z-20">
        {/* Cart Header */}
        <div className="flex items-center gap-3 p-4 bg-white border-b border-gray-100">
          <ShoppingCart className="w-5 h-5 text-gray-500" />
          <h2 className="flex-1 text-lg font-bold text-gray-800">
            Pesanan Saat Ini
          </h2>
          <span className="bg-citric-100 text-citric-700 text-xs font-bold px-2.5 py-1 rounded-full">
            {cart.reduce((a, b) => a + b.qty, 0)} Item
          </span>
        </div>

        {/* Input Nama Pelanggan */}
        <div className="p-4 border-b border-gray-100 bg-gray-50">
          <input
            type="text"
            value={customerName}
            onChange={(e) => setCustomerName(e.target.value)}
            placeholder="Nama Pelanggan"
            className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-citric-500 focus:border-citric-500"
          />
        </div>

        {/* Cart Items List */}
        <div className="flex-1 p-4 space-y-4 overflow-y-auto">
          {cart.length === 0 ? (
            <div className="flex flex-col items-center justify-center h-full space-y-3 text-gray-400">
              <ShoppingCart className="w-12 h-12 opacity-20" />
              <p className="text-sm">Keranjang masih kosong</p>
            </div>
          ) : (
            cart.map((item) => (
              <div
                key={item.product_id}
                className="flex flex-col gap-2 p-3 bg-white border border-gray-100 rounded-lg shadow-sm"
              >
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <h4 className="text-sm font-bold text-gray-800">
                      {item.name}
                    </h4>
                    <p className="text-xs font-medium text-citric-600">
                      {formatRupiah(item.price)}
                    </p>
                  </div>
                  <button
                    onClick={() => removeItem(item.product_id)}
                    className="p-1 text-red-400 hover:text-red-600"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>

                {/* Input Catatan Per Item */}
                <input
                  type="text"
                  placeholder="Catatan (opsional)..."
                  value={item.notes}
                  onChange={(e) =>
                    setCart(
                      cart.map((c) =>
                        c.product_id === item.product_id
                          ? { ...c, notes: e.target.value }
                          : c,
                      ),
                    )
                  }
                  className="w-full px-2 py-1 text-xs border border-gray-200 rounded bg-gray-50 focus:outline-none focus:border-citric-500"
                />

                <div className="flex items-center justify-between mt-1">
                  <p className="text-sm font-bold text-gray-900">
                    {formatRupiah(item.price * item.qty)}
                  </p>
                  <div className="flex items-center gap-3 p-1 border border-gray-200 rounded-lg bg-gray-50">
                    <button
                      onClick={() => updateQty(item.product_id, -1)}
                      className="p-1 text-gray-600 rounded shadow-sm hover:bg-white"
                    >
                      <Minus className="w-3 h-3" />
                    </button>
                    <span className="w-4 text-sm font-bold text-center">
                      {item.qty}
                    </span>
                    <button
                      onClick={() => updateQty(item.product_id, 1)}
                      className="p-1 text-gray-600 rounded shadow-sm hover:bg-white"
                    >
                      <Plus className="w-3 h-3" />
                    </button>
                  </div>
                </div>
              </div>
            ))
          )}
        </div>

        {/* Kalkulasi Total */}
        <div className="p-4 space-y-3 border-t border-gray-200 bg-gray-50">
          {/* Input Voucher */}
          <div className="flex gap-2">
            <div className="relative flex-1">
              <Tag className="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" />
              <input
                type="text"
                placeholder="Kode Promo"
                value={voucherCode}
                onChange={(e) => setVoucherCode(e.target.value)}
                className="w-full py-2 pr-3 text-sm uppercase border border-gray-300 rounded-lg pl-9 focus:ring-citric-500 focus:border-citric-500"
              />
            </div>
            <button
              onClick={handleCheckVoucher}
              className="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900"
            >
              Terapkan
            </button>
          </div>

          <div className="space-y-1.5 pt-2 text-sm">
            <div className="flex justify-between text-gray-500">
              <span>Subtotal</span>
              <span>{formatRupiah(subtotal)}</span>
            </div>
            {activeVoucher && (
              <div className="flex justify-between font-medium text-green-600">
                <span>Diskon ({activeVoucher.code})</span>
                <span>-{formatRupiah(discountAmount)}</span>
              </div>
            )}
            <div className="flex justify-between text-gray-500">
              <span>Pajak (11%)</span>
              <span>{formatRupiah(taxAmount)}</span>
            </div>
            <div className="flex justify-between pt-3 mt-2 text-xl font-bold text-gray-900 border-t border-gray-200">
              <span>Total</span>
              <span>{formatRupiah(grandTotal)}</span>
            </div>
          </div>

          <button
            onClick={() => setIsCheckoutOpen(true)}
            disabled={cart.length === 0}
            className="w-full py-3.5 bg-citric-600 text-white rounded-xl font-bold text-lg hover:bg-citric-700 transition-colors shadow-lg shadow-citric-200 disabled:opacity-50 disabled:shadow-none flex justify-center items-center gap-2 mt-2"
          >
            Bayar Sekarang
          </button>
        </div>
      </div>

      {/* MODAL CHECKOUT */}
      {isCheckoutOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
          <div className="bg-white rounded-2xl shadow-2xl p-6 w-[400px]">
            <h3 className="pb-2 mb-4 text-xl font-bold text-gray-900 border-b font-playfair">
              Penyelesaian Pembayaran
            </h3>

            <div className="mb-4">
              <label className="block mb-2 text-sm font-medium text-gray-700">
                Metode Pembayaran
              </label>
              <div className="grid grid-cols-2 gap-3">
                <button
                  onClick={() => setPaymentMethod("cash")}
                  className={`flex items-center justify-center gap-2 py-3 border-2 rounded-xl font-medium transition-all ${paymentMethod === "cash" ? "border-citric-500 bg-citric-50 text-citric-700" : "border-gray-200 text-gray-500 hover:border-gray-300"}`}
                >
                  <Banknote className="w-5 h-5" /> Tunai
                </button>
                <button
                  onClick={() => setPaymentMethod("qris")}
                  className={`flex items-center justify-center gap-2 py-3 border-2 rounded-xl font-medium transition-all ${paymentMethod === "qris" ? "border-citric-500 bg-citric-50 text-citric-700" : "border-gray-200 text-gray-500 hover:border-gray-300"}`}
                >
                  <CreditCard className="w-5 h-5" /> QRIS
                </button>
              </div>
            </div>

            <div className="p-4 mb-4 text-center border border-gray-100 bg-gray-50 rounded-xl">
              <p className="mb-1 text-sm text-gray-500">Total Tagihan</p>
              <p className="text-3xl font-bold text-gray-900">
                {formatRupiah(grandTotal)}
              </p>
            </div>

            {paymentMethod === "cash" && (
              <div className="mb-6">
                <label className="block mb-1 text-sm font-medium text-gray-700">
                  Uang Tunai Diterima (Rp)
                </label>
                <input
                  type="number"
                  value={paymentAmount}
                  onChange={(e) => setPaymentAmount(e.target.value)}
                  placeholder="Misal: 100000"
                  className="w-full px-4 py-3 text-xl font-bold text-gray-900 border border-gray-300 rounded-xl focus:ring-citric-500 focus:border-citric-500"
                />
                {paymentAmount && parseInt(paymentAmount) >= grandTotal && (
                  <p className="mt-2 text-sm font-medium text-green-600">
                    Kembalian:{" "}
                    {formatRupiah(parseInt(paymentAmount) - grandTotal)}
                  </p>
                )}
              </div>
            )}

            <div className="flex gap-3">
              <button
                onClick={() => setIsCheckoutOpen(false)}
                className="flex-1 py-3 font-bold text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200"
              >
                Batal
              </button>
              <button
                onClick={handleCheckout}
                disabled={
                  isProcessing ||
                  (paymentMethod === "cash" &&
                    parseInt(paymentAmount || 0) < grandTotal)
                }
                className="flex-1 py-3 font-bold text-white transition-colors bg-citric-600 rounded-xl hover:bg-citric-700 disabled:opacity-50"
              >
                {isProcessing ? "Memproses..." : "Proses Transaksi"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
