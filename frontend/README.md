# Dicoding Jobs Frontend

Frontend aplikasi lowongan pekerjaan yang dibangun dengan Next.js, TanStack
Query, Tailwind CSS, dan Playwright.

## Menjalankan frontend

Salin konfigurasi environment:

```bash
cp .env.example .env.local
```

Pasang dependency dan jalankan development server:

```bash
npm install
npm run dev
```

Frontend dapat dibuka melalui `http://localhost:3000`.

## Pemeriksaan kode

```bash
npm run lint
npm run typecheck
npm run build
```

## End-to-end test

Pasang browser Playwright untuk penggunaan pertama:

```bash
npx playwright install chromium
```

Jalankan seluruh E2E test:

```bash
npm run test:e2e
```

Atau gunakan mode UI:

```bash
npm run test:e2e:ui
```

E2E test menggunakan API interception dengan data deterministik, sehingga
alur daftar, pencarian, empty state, detail, pembuatan, pengeditan, dan
penghapusan lowongan dapat diuji tanpa bergantung pada isi database lokal.
