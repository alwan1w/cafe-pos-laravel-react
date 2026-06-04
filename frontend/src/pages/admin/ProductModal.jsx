import { useState, useEffect, useRef } from "react";
import { X, Upload, Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "../../services/api";

export default function ProductModal({ isOpen, onClose, onSuccess, editData }) {
  const [categories, setCategories] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  // Form State
  const [formData, setFormData] = useState({
    name: "",
    category_id: "",
    price: "",
    type: "food",
    is_active: true,
  });
  const [imageFile, setImageFile] = useState(null);
  const [imagePreview, setImagePreview] = useState(null);
  const fileInputRef = useRef(null);

  const isEditMode = !!editData;

  // Fetch data kategori untuk dropdown
  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const res = await api.get("/categories");
        setCategories(res.data.data);
      } catch {
        toast.error("Gagal memuat kategori");
      }
    };
    if (isOpen) fetchCategories();
  }, [isOpen]);

  // Isi form jika dalam mode Edit
  useEffect(() => {
    if (isEditMode && isOpen) {
      setFormData({
        name: editData.name,
        category_id: editData.category_id || "",
        price: editData.price,
        type: editData.type,
        is_active: editData.is_active,
      });
      setImagePreview(editData.image);
      setImageFile(null);
    } else if (isOpen) {
      // Reset form jika mode Tambah
      setFormData({
        name: "",
        category_id: "",
        price: "",
        type: "food",
        is_active: true,
      });
      setImagePreview(null);
      setImageFile(null);
    }
  }, [editData, isOpen]);

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setImageFile(file);
      setImagePreview(URL.createObjectURL(file)); // Buat preview lokal
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    // Gunakan FormData karena ada upload file
    const submitData = new FormData();
    submitData.append("name", formData.name);
    submitData.append("category_id", formData.category_id);
    submitData.append("price", formData.price);
    submitData.append("type", formData.type);
    submitData.append("is_active", formData.is_active ? 1 : 0);

    if (imageFile) {
      submitData.append("image", imageFile);
    }

    try {
      if (isEditMode) {
        // Trik Laravel: Gunakan POST dengan _method=PUT untuk upload file multipart/form-data
        submitData.append("_method", "PUT");
        await api.post(`/products/${editData.id}`, submitData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Produk berhasil diperbarui");
      } else {
        await api.post("/products", submitData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Produk berhasil ditambahkan");
      }
      onSuccess(); // Refresh tabel di halaman induk
      onClose(); // Tutup modal
    } catch (error) {
      toast.error(
        error.response?.data?.message ||
          "Terjadi kesalahan saat menyimpan data",
      );
    } finally {
      setIsLoading(false);
    }
  };

  if (!isOpen) return null;

  return (
    <div
      className="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      {/* Background overlay */}
      <div className="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div
          className="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
          onClick={onClose}
          aria-hidden="true"
        ></div>
        <span
          className="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
        >
          &#8203;
        </span>

        {/* Modal Panel */}
        <div className="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div className="flex items-center justify-between px-4 pt-5 pb-4 bg-white border-b border-gray-100 sm:p-6 sm:pb-4">
            <h3
              className="text-xl font-bold leading-6 text-gray-900 font-playfair"
              id="modal-title"
            >
              {isEditMode ? "Edit Produk" : "Tambah Produk Baru"}
            </h3>
            <button
              onClick={onClose}
              className="text-gray-400 transition-colors hover:text-gray-500"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          <form onSubmit={handleSubmit}>
            <div className="px-4 py-5 space-y-4 sm:p-6">
              {/* Upload Foto */}
              <div className="flex flex-col items-center justify-center">
                <div
                  className="flex items-center justify-center w-32 h-32 overflow-hidden transition-colors border-2 border-gray-300 border-dashed cursor-pointer rounded-xl bg-gray-50 hover:bg-gray-100"
                  onClick={() => fileInputRef.current?.click()}
                >
                  {imagePreview ? (
                    <img
                      src={imagePreview}
                      alt="Preview"
                      className="object-cover w-full h-full"
                    />
                  ) : (
                    <div className="text-center text-gray-400">
                      <Upload className="w-8 h-8 mx-auto mb-1" />
                      <span className="text-xs">Upload Foto</span>
                    </div>
                  )}
                </div>
                <input
                  type="file"
                  ref={fileInputRef}
                  onChange={handleImageChange}
                  accept="image/*"
                  className="hidden"
                />
              </div>

              {/* Input Nama & Kategori */}
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Nama Produk *
                  </label>
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) =>
                      setFormData({ ...formData, name: e.target.value })
                    }
                    className="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Kategori *
                  </label>
                  <select
                    required
                    value={formData.category_id}
                    onChange={(e) =>
                      setFormData({ ...formData, category_id: e.target.value })
                    }
                    className="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm"
                  >
                    <option value="" disabled>
                      Pilih Kategori
                    </option>
                    {categories.map((cat) => (
                      <option key={cat.id} value={cat.id}>
                        {cat.name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {/* Input Harga & Tipe */}
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Harga (Rp) *
                  </label>
                  <input
                    type="number"
                    min="0"
                    required
                    value={formData.price}
                    onChange={(e) =>
                      setFormData({ ...formData, price: e.target.value })
                    }
                    className="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Tipe Menu *
                  </label>
                  <select
                    required
                    value={formData.type}
                    onChange={(e) =>
                      setFormData({ ...formData, type: e.target.value })
                    }
                    className="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-citric-500 focus:border-citric-500 sm:text-sm"
                  >
                    <option value="food">Makanan (Food)</option>
                    <option value="drink">Minuman (Drink)</option>
                  </select>
                </div>
              </div>

              {/* Status Aktif */}
              <div className="flex items-center mt-4">
                <input
                  id="is_active"
                  type="checkbox"
                  checked={formData.is_active}
                  onChange={(e) =>
                    setFormData({ ...formData, is_active: e.target.checked })
                  }
                  className="w-4 h-4 border-gray-300 rounded cursor-pointer text-citric-600 focus:ring-citric-500"
                />
                <label
                  htmlFor="is_active"
                  className="block ml-2 text-sm text-gray-900 cursor-pointer"
                >
                  Produk Aktif (Tersedia untuk dijual)
                </label>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="px-4 py-3 border-t border-gray-100 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl">
              <button
                type="submit"
                disabled={isLoading}
                className="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors border border-transparent rounded-lg shadow-sm bg-citric-600 hover:bg-citric-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-citric-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-70"
              >
                {isLoading ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  "Simpan Data"
                )}
              </button>
              <button
                type="button"
                onClick={onClose}
                disabled={isLoading}
                className="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
