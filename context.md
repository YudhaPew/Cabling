# Arsitektur Dashboard Mobile FTTH - ISP Inventory

## 1. Konteks Proyek

Kami sedang membangun aplikasi manajemen inventori kabel fiber optik yang berfokus pada penggunaan Mobile (HP) untuk teknisi lapangan.

## 2. Struktur Database Saat Ini

- `nodes`: id, name, type (ODF, ODC, ODP), latitude, longitude, description.
- `cables`: id, name, source_node_id, destination_node_id, total_tube, cores_per_tube, description.
- `splicing_connections`: id, cable_id, tube_number, core_number, status (Kosong, Terpakai, Rusak), connected_to.
- `splitters`: id, node_id, capacity (1:4, 1:8, 1:16).
- `splitter_ports`: id, splitter_id, port_number, status (Kosong, Terpakai, Rusak), customer_name, customer_id, description.

## 3. Aturan UI/UX Mobile Baru (welcome.blade.php)

- **Tema:** Dark mode (bg-slate-900), kontras tinggi, tombol besar minimal h-12 (ramah jempol teknisi).
- **Alur Halaman Utama:** Saat dibuka di HP, JANGAN langsung menampilkan peta. Tampilkan data teks terlebih dahulu agar ringan. Peta hanya menjadi opsi di Tab kedua.
- **Komponen Atas (Sticky):** 1. Input Search bar untuk memfilter data ODP/ODC secara real-time. 2. Tab Switcher (Navigasi): [Tab 1: Daftar Data] (Aktif secara default) dan [Tab 2: Peta Jaringan].
- **Komponen Tengah (Stat Cards):** 3 kotak rangkuman vertikal berjejer (gapan gaya Portainer):
    - Kotak 1 (Biru): Total ODF
    - Kotak 2 (Gelap): Total ODC
    - Kotak 3 (Gelap): Total ODP
- **Komponen Bawah (Daftar Card):** Menampilkan daftar ODP/ODC hasil filter search.
    - Jika ODP: Tampilkan nama, hulu ODC-nya, indikator visual mini port splitter (contoh: 🟢🟢🟡🔴), dan tombol besar "Kelola Port Pelanggan".
    - Jika ODC: Tampilkan nama, kapasitas core, dan tombol "Buka Splicing Matrix".
