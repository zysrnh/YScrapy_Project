# YScrapy — Project Scope & Specification Document

> **Nama Sistem:** YScrapy  
> **Tipe Aplikasi:** Multi-Platform Web Scraper & Public Sentiment Analysis Platform  
> **Tech Stack Utama:** Laravel (PHP), Blade, Tailwind CSS, Chart.js, SweetAlert2, SQLite/MySQL  
> **Design Theme:** Glassmorphism UI (Frosted Glass Effect)  
> **Color Palette:** Sesuai `CollorPallet.jpg`

---

## 1. Definisi & Tujuan Proyek (Core Concept)

**YScrapy** adalah aplikasi web untuk mengikis (scrape) data opini, komentar, ulasan, dan percakapan publik dari berbagai platform digital, kemudian menganalisis sentimen publik tersebut secara otomatis ke dalam kategori:
- **Positif** (Apresiasi, kepuasan, dukungan, sentimen baik)
- **Netral** (Pertanyaan, informasi umum, pernyataan objektif)
- **Negatif** (Keluhan, kritik, kemarahan, kekecewaan)

Sistem ini membantu memonitor **Social Listening & Market Sentiment** mengenai suatu topik, brand, produk, institusi, atau isu dari apa yang dirasakan dan diutarakan publik.

---

## 2. Design System: Glassmorphism & Palet Warna

Berdasarkan referensi palet resmi di [`CollorPallet.jpg`](./CollorPallet.jpg):

| Hex Code | Peran / Variabel CSS | Penggunaan UI |
| :--- | :--- | :--- |
| **`#06141B`** | `--ys-bg-darkest` | Background kanvas utama (deep void / canvas base) |
| **`#11212D`** | `--ys-surface-dark` | Background layer sekunder & gradient mesh |
| **`#253745`** | `--ys-glass-card` | Base layer frosted glass card (`rgba(37, 55, 69, 0.45)`) |
| **`#4A5C6A`** | `--ys-glass-border` | Border tipis glass, separator, interactive element state |
| **`#9BA8AB`** | `--ys-text-muted` | Text sekunder, label info, icon inactive, badge netral |
| **`#CCD0CF`** | `--ys-text-main` | Text judul, teks utama kontras tinggi, highlight active |

### Karakteristik Glassmorphism:
- **Backdrop Blur:** `backdrop-filter: blur(14px) saturate(160%)`
- **Border:** `1px solid rgba(204, 208, 207, 0.12)`
- **Box Shadow:** Subtle ambient glow `0 8px 32px 0 rgba(6, 20, 27, 0.45)`
- **Glass Accents untuk Sentimen:**
  - *Positif:* Glass emerald glow (`rgba(16, 185, 129, 0.15)` border `rgba(16, 185, 129, 0.4)`)
  - *Netral:* Glass slate glow (`rgba(155, 168, 171, 0.15)` border `rgba(155, 168, 171, 0.4)`)
  - *Negatif:* Glass rose/red glow (`rgba(239, 68, 68, 0.15)` border `rgba(239, 68, 68, 0.4)`)

---

## 3. Scope Fitur & Modul

### A. Modul Scraper (Multi-Platform Engine)
1. **News Portal Scraper:** Mengambil berita & opini publik dari portal berita (Kompas, Detik, Tempo) berdasarkan kata kunci isu.
2. **YouTube Comments Scraper:** Mengambil komentar netizen dari video YouTube berdasarkan URL video atau ID topik.
3. **Twitter / X Discussion Scraper:** Ekstraksi tweet dan reply netizen mengenai trending topic atau keyword.
4. **Google Reviews & E-commerce Scraper:** Ekstraksi ulasan bintang & komentar konsumen dari produk atau lokasi.
5. **Direct Custom URL Scraper:** Input URL spesifik dengan selector dinamis untuk scraping cepat.

### B. Modul Sentiment Engine (Indonesian NLP & Lexicon)
1. **Preprocessing Pipeline:**
   - Case folding, sanitasi teks (pembersihan mention, emoji, link URL).
   - Normalisasi slang kata Indonesia (misal: "bgt" -> "banget", "gak/ga" -> "tidak").
   - Deteksi negasi (misal: "tidak ramah", "kurang bagus").
2. **Sentiment Scoring:**
   - Skoring matematis berbasis InSet Indonesian Lexicon (-1.0 s/d +1.0).
   - Penentuan label: **Positif** (score > 0.05), **Netral** (-0.05 <= score <= 0.05), **Negatif** (score < -0.05).
   - Ekstraksi keyword pemicu sentimen (kata kunci penentu).

### C. Modul Dashboard & Visualisasi (UI YScrapy)
1. **Glassmorphism Metrics Bar:**
   - Total data dikumpulkan, rasio sentimen orang (positif/netral/negatif), rasio polaritas.
2. **Chart.js Glass Visualization:**
   - Donut Chart: Distribusi sentimen audiens.
   - Bar Chart: Perbandingan sentimen per platform.
3. **Data Explorer Table:**
   - Kartu / baris data komentar dengan badge glassmorphism.
   - Filter instan berdasarkan Platform, Sentimen, dan pencarian kata.
4. **Export Data:**
   - Download hasil scraping & analisis ke format CSV dan JSON.

---

## 4. Arsitektur Database (SQLite)

- **`scrape_jobs`**: Riwayat pencarian/ekstraksi (platform, query, total data, statistik sentimen).
- **`scraped_feedbacks`**: Data komentar/opini publik (author, konten raw & clean, skor sentimen, label sentimen, token sentimen, URL sumber).

---

## 5. Batasan & Scope Guard (Apa yang TIDAK Masuk Scope)

1. **Bukan Bot Spammer / Auto-Posting:** YScrapy murni read-only data scraper & sentiment analyzer.
2. **Tidak Menyentuh Database Sensitif / Production:** Menjalankan SQLite secara lokal untuk isolasi penuh.
3. **Autentikasi Kompleks yang Memerlukan Kredensial Pribadi:** Scraping fokus ke public endpoints, open feeds, dan public web views tanpa meminta login credential rahasia akun pengguna.
