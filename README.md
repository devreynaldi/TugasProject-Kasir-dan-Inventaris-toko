# TokoKu POS — Aplikasi Kasir & Inventaris (Laravel)

Aplikasi web full-stack Point of Sale untuk toko kecil: kasir, manajemen produk & stok, riwayat transaksi, dan laporan penjualan.

**Stack:** Laravel 11/12 · Blade + Alpine.js + Tailwind (Laravel Breeze) · MySQL (produksi) / SQLite (lokal & test) · Docker + Nginx · GitHub Actions

## Cara menjalankan (lokal)

Prasyarat: PHP ≥ 8.2, Composer, Node.js ≥ 20.

```bash
bash setup.sh tokoku-app      # membuat proyek Laravel + menerapkan kode TokoKu
cd tokoku-app
php artisan serve             # http://127.0.0.1:8000
php artisan test              # menjalankan 8 feature test
```

Akun demo (password `password`): `admin@tokoku.test` (admin), `kasir@tokoku.test` (kasir).

> Repositori ini berisi **kode inti proyek** (migrasi, model, service, controller, view, test, Docker, CI) yang
> ditumpuk di atas skeleton Laravel + Breeze oleh `setup.sh`, sehingga versi Laravel selalu terbaru.

## Struktur kode inti

| Lokasi | Isi |
|---|---|
| `database/migrations` | users.role, categories, products, sales, sale_items, stock_movements |
| `app/Services/CheckoutService.php` | Logika transaksi: validasi stok, `lockForUpdate`, kurangi stok, catat mutasi |
| `app/Http/Middleware/RoleMiddleware.php` | Pembatasan akses admin / kasir |
| `app/Http/Controllers` | Dashboard, Pos, Product, Category, Stock, Sale, Report |
| `resources/views` | UI Blade (kasir memakai Alpine.js) |
| `tests/Feature` | CheckoutTest, RoleAccessTest |
| `public/css/neon.css`, `public/js/fx.js` | Tema futuristik "Command Deck" (aurora, kaca, HUD, efek interaktif) |
| `docs/SDLC.md` | Dokumentasi seluruh fase SDLC |

## Deployment (Docker)

```bash
cp .env.example .env     # isi APP_KEY, APP_ENV=production, APP_DEBUG=false,
                         # DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_ROOT_PASSWORD
docker compose up -d --build
docker compose exec app php artisan db:seed --force   # sekali saja, lalu GANTI password akun demo
```

Detail lengkap (VPS, HTTPS, backup) ada di `docs/SDLC.md` bagian 5.
