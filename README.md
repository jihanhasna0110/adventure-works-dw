# Adventure Works Data Warehouse Dashboard

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4.6-purple.svg)](https://getbootstrap.com/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://www.mysql.com/)

## 📊 Deskripsi Project
Sistem Business Intelligence berbasis Data Warehouse untuk analisis penjualan Adventure Works 2008. Project ini mengimplementasikan Star Schema, proses ETL menggunakan Pentaho Data Integration, dan dashboard analitik interaktif berbasis web dengan integrasi Mondrian OLAP.

> **Catatan**: Dashboard ini dibangun menggunakan [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2/) template dari Start Bootstrap sebagai base framework untuk antarmuka pengguna.

---

## 👥 Tim Pengembang
- **Anadya Tafdhila** (22082010142)
- **Jihan Hasna Iftinan** (22082010148)
- **Azzahra Rahmadani** (22082010155)

**Program Studi Sistem Informasi**  
**Fakultas Ilmu Komputer**  
**UPN "Veteran" Jawa Timur**  
**2025/2026**

---

## 🎯 Business Questions
Project ini dirancang untuk menjawab 5 pertanyaan bisnis utama:

1. **BQ1**: Jenis kartu kredit (CardType) apa yang paling banyak digunakan dalam transaksi penjualan?
2. **BQ2**: Produk dengan warna apa yang paling diminati konsumen berdasarkan total quantity terjual?
3. **BQ3**: Bagaimana distribusi status order dalam 1 tahun terakhir? Berapa persen order yang cancelled atau back order?
4. **BQ4**: Berapa rata-rata nilai transaksi (AOV) per customer type (Individual vs Store) dan bagaimana trennya per kuartal?
5. **BQ5**: Bagaimana perbandingan performa penjualan antar salesperson di setiap territory dalam 2 tahun terakhir?

---

## 🏗️ Arsitektur Sistem

### Data Warehouse Schema
Project menggunakan **Star Schema** dengan struktur:
- **1 Fact Table**: `fact_sales`
- **6 Dimension Tables**:
  - `dim_time` - Dimensi waktu (tahun, kuartal, bulan, tanggal)
  - `dim_product` - Dimensi produk (kategori, subkategori, warna)
  - `dim_customer` - Dimensi pelanggan (tipe customer, territory)
  - `dim_territory` - Dimensi wilayah penjualan
  - `dim_salesperson` - Dimensi tenaga penjual
  - `dim_creditcard` - Dimensi metode pembayaran

### Tech Stack
- **Database**: MySQL 8.0+
- **ETL Tool**: Pentaho Data Integration (PDI)
- **OLAP Server**: Mondrian OLAP
- **Backend**: PHP 7.4+
- **Frontend**: 
  - HTML5, CSS3 (Bootstrap 4.6)
  - JavaScript (jQuery 3.6)
  - Chart.js 4.4.4
  - DataTables
- **Template**: SB Admin 2

---

## 📂 Struktur Folder

```
project-dwh/
├── api/
│   └── check_session.php       # Check user session
├── assets/
│   ├── css/                    # Custom stylesheets
│   ├── img/                    # Images & icons
│   └── js/
│       └── demo/
│           ├── dashboard.js
│           ├── product_analysis.js
│           ├── customer_geo.js
│           └── business_analytics.js
├── auth/
│   ├── check_session.php       # Session validation
│   ├── login.php              # Login page
│   ├── logout.php             # Logout handler
│   └── process_login.php      # Login authentication
├── config/
│   └── config.php             # Database configuration
├── includes/
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
├── pages/
│   ├── dashboard.php          # Sales overview
│   ├── product_analysis.php   # Product analysis
│   ├── customer_geo.php       # Customer geography
│   ├── business_analytics.php # Business analytics
│   └── olap_mondrian.php      # Mondrian OLAP
├── ETL/
│   └── (Pentaho transformation files)
├── SQL/
│   ├── create_schema.sql      # DW schema creation
│   ├── business_questions.sql # BQ queries
│   └── sample_queries.sql     # Sample analytical queries
└── index.php                  # Main entry point
```

---

## ⚙️ Instalasi dan Setup

### Prerequisites
- XAMPP/WAMP (Apache + MySQL) atau stack PHP lainnya
- MySQL Server 8.0+
- Pentaho Data Integration (untuk ETL)
- Java JDK 8+ (untuk Mondrian)
- Browser modern (Chrome, Firefox, Edge)

### Langkah 1: Clone Repository
```bash
git clone https://github.com/[username]/adventure-works-dw.git
cd adventure-works-dw
```

### Langkah 2: Setup Database

#### 2.1 Import Database Sumber
```bash
# Import database AdventureWorks 2008 (OLTP)
mysql -u root -p < adventureworks_2008.sql
```

#### 2.2 Buat Data Warehouse
```bash
# Buat database DW dan schema
mysql -u root -p < SQL/create_schema.sql
```

#### 2.3 Konfigurasi Koneksi Database
Edit file `config/config.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'adventureworks_dw');
?>
```

### Langkah 3: Proses ETL

#### 3.1 Buka Pentaho Data Integration (Spoon)
```bash
# Windows
cd pentaho-di/
spoon.bat

# Linux/Mac
cd pentaho-di/
./spoon.sh
```

#### 3.2 Jalankan Transformasi ETL
Urutan eksekusi transformasi:
1. `ETL/dim_time.ktr` - Load dimensi waktu
2. `ETL/dim_product.ktr` - Load dimensi produk
3. `ETL/dim_customer.ktr` - Load dimensi customer
4. `ETL/dim_territory.ktr` - Load dimensi territory
5. `ETL/dim_salesperson.ktr` - Load dimensi salesperson
6. `ETL/dim_creditcard.ktr` - Load dimensi credit card
7. `ETL/fact_sales.ktr` - Load fact table

**Catatan**: Jalankan dimensi terlebih dahulu sebelum fact table!

### Langkah 4: Setup Web Server

#### 4.1 Copy Project ke htdocs
```bash
# Windows XAMPP
copy project-dwh C:/xampp/htdocs/

# Linux
sudo cp -r project-dwh /var/www/html/
```

#### 4.2 Start Apache & MySQL
- Buka XAMPP Control Panel
- Start Apache
- Start MySQL

### Langkah 5: Setup Mondrian OLAP (Opsional)

#### 5.1 Install Mondrian Server
- Download Mondrian OLAP Server
- Configure schema XML sesuai struktur DW
- Deploy ke Tomcat server

#### 5.2 Konfigurasi Connection
Edit file `mondrian/schema.xml` untuk menyesuaikan:
- Database connection
- Cube definitions
- Dimension hierarchies

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/project-dwh
```

### 2. Login
**Default Credentials**:
- Username: `admin`
- Password: `admin123`

### 3. Navigasi Menu
Setelah login, Anda dapat mengakses:
- **Sales Overview**: Dashboard utama dengan KPI dan tren penjualan
- **Product Analysis**: Analisis performa produk per kategori
- **Customer Geography**: Distribusi pelanggan berdasarkan wilayah
- **Business Analytics**: Analisis mendalam (CardType, Color, AOV, Status Order)
- **OLAP Mondrian**: Analisis multidimensional interaktif

### 4. Filter Data
- Gunakan **Year Filter** di header untuk filter berdasarkan tahun
- Pilih tahun spesifik (2001-2004) atau "All Years"
- Semua visualisasi akan update otomatis

---

## 📊 Fitur Dashboard

### Sales Overview
- **KPI Cards**: Total Revenue, Total Orders, Quantity Sold, AOV
- **Sales Trend Chart**: Tren penjualan bulanan (line chart)
- **Sales by Category**: Perbandingan penjualan per kategori (bar chart)

### Product Analysis
- **Product Trend**: Tren penjualan produk dari waktu ke waktu
- **Top 5 Best Selling**: Produk terlaris dengan ranking
- **Category Comparison**: Perbandingan performa antar kategori
- **Product Mix**: Komposisi penjualan (polar area chart)
- **Product Table**: Detail semua produk dengan growth percentage

### Customer Geography
- **Territory Distribution**: Penjualan per wilayah (horizontal bar chart)
- **Customer Detail Table**: Daftar customer dengan total purchases

### Business Analytics
- **Card Type Distribution**: Analisis metode pembayaran (doughnut chart)
- **Color Preferences**: Preferensi warna produk (pie chart)
- **Order Status**: Distribusi status pesanan
- **AOV by Customer Type**: Perbandingan Individual vs Store
- **Salesperson Performance**: Performa sales per territory

### OLAP Mondrian
- **Slice & Dice**: Analisis subset data multidimensi
- **Drill-down**: Dari agregat ke detail
- **Roll-up**: Agregasi ke level lebih tinggi
- **Pivot**: Ubah orientasi dimensi

---

## 🔍 Query Business Questions

### BQ1: Card Type Usage
```sql
SELECT 
    cc.CardType,
    COUNT(DISTINCT fs.SalesOrderID) AS OrderCount,
    CONCAT('$', FORMAT(SUM(fs.TotalDue), 2)) AS TotalSales
FROM fact_sales fs
JOIN dim_creditcard cc ON fs.CreditCardKey = cc.CreditCardKey
GROUP BY cc.CardType
ORDER BY OrderCount DESC;
```

### BQ2: Product Color Preference
```sql
SELECT 
    p.Color,
    SUM(fs.OrderQuantity) AS TotalQuantity,
    COUNT(DISTINCT fs.SalesOrderID) AS OrderCount
FROM fact_sales fs
JOIN dim_product p ON fs.ProductKey = p.ProductKey
WHERE p.Color != 'Not Specified'
GROUP BY p.Color
ORDER BY TotalQuantity DESC;
```

### BQ3: Order Status Distribution
```sql
SELECT 
    CASE fs.OrderStatus
        WHEN 1 THEN 'In Process'
        WHEN 2 THEN 'Approved'
        WHEN 3 THEN 'Backordered'
        WHEN 4 THEN 'Rejected'
        WHEN 5 THEN 'Shipped'
        WHEN 6 THEN 'Cancelled'
    END AS StatusName,
    COUNT(DISTINCT fs.SalesOrderID) AS OrderCount
FROM fact_sales fs
JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
WHERE dt.Year = (SELECT MAX(Year) FROM dim_time)
GROUP BY fs.OrderStatus;
```

### BQ4: AOV by Customer Type
```sql
SELECT 
    c.CustomerType,
    dt.Year,
    dt.Quarter,
    COUNT(DISTINCT fs.SalesOrderID) AS OrderCount,
    SUM(fs.TotalDue) / COUNT(DISTINCT fs.SalesOrderID) AS AvgOrderValue
FROM fact_sales fs
JOIN dim_customer c ON fs.CustomerKey = c.CustomerKey
JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
GROUP BY c.CustomerType, dt.Year, dt.Quarter
ORDER BY dt.Year DESC, dt.Quarter;
```

### BQ5: Salesperson Performance
```sql
SELECT 
    sp.FullName AS SalespersonName,
    t.TerritoryName,
    dt.Year,
    COUNT(DISTINCT fs.SalesOrderID) AS OrderCount,
    SUM(fs.TotalDue) AS TotalSales
FROM fact_sales fs
JOIN dim_salesperson sp ON fs.SalespersonKey = sp.SalespersonKey
JOIN dim_territory t ON fs.TerritoryKey = t.TerritoryKey
JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
WHERE dt.Year >= (SELECT MAX(Year) - 1 FROM dim_time)
GROUP BY sp.FullName, t.TerritoryName, dt.Year
ORDER BY TotalSales DESC;
```

---

## 🧪 Testing

### Test Business Questions
Jalankan query di `SQL/business_questions.sql` untuk verifikasi:
```bash
mysql -u root -p adventureworks_dw < SQL/business_questions.sql
```

### Test Dashboard
1. Login ke aplikasi
2. Test filter tahun pada setiap halaman
3. Verifikasi data sesuai dengan hasil query manual
4. Test interaktivitas chart (click events)
5. Test drill-down features

---

## 📝 Catatan Penting

### Performa
- Data warehouse ini mengandung 31,465 transaksi penjualan
- Loading dashboard pertama kali mungkin memerlukan 2-3 detik
- Cache browser direkomendasikan untuk performa optimal

### Maintenance
- Backup database secara berkala
- Update ETL schedule sesuai kebutuhan bisnis
- Monitor ukuran fact table untuk indexing

### Security
- Ganti default password setelah instalasi
- Gunakan prepared statements untuk semua query
- Implementasi SSL untuk production environment

---

## 🎨 Template Credit

Dashboard ini menggunakan **[SB Admin 2](https://startbootstrap.com/theme/sb-admin-2/)** sebagai base template, sebuah open source admin dashboard theme untuk Bootstrap yang dibuat oleh [Start Bootstrap](https://startbootstrap.com/).

### SB Admin 2 Features Used:
- Responsive sidebar navigation
- Card components untuk statistics
- Color utilities dan typography
- Table styling dengan DataTables integration
- Chart.js integration untuk visualisasi data

### About Start Bootstrap
Start Bootstrap adalah open source library dari free Bootstrap templates dan themes. Template ini dirilis under MIT license.

- Website: <https://startbootstrap.com>
- Twitter: <https://twitter.com/SBootstrap>
- Created by: **[David Miller](https://davidmiller.io/)**

---

## 👨‍💻 Authors & Contributors

### Tim Pengembang
- **Anadya Tafdhila** (22082010142) - ETL Development, Data Modeling
- **Jihan Hasna Iftinan** (22082010148) - Frontend Development, Dashboard Design  
- **Azzahra Rahmadani** (22082010155) - Backend Development, OLAP Integration

### Dosen Pengampu
**Abdul Rezha Efrat Najaf, S.Kom, M.Kom.**  
Mata Kuliah: Data Warehouse & OLAP

**Program Studi Sistem Informasi**  
**Fakultas Ilmu Komputer**  
**UPN "Veteran" Jawa Timur**  
**2025/2026**

---

## 📄 License

Project ini menggunakan MIT License untuk compatibility dengan SB Admin 2 template.

### MIT License
```
Copyright (c) 2025 Anadya Tafdhila, Jihan Hasna Iftinan, Azzahra Rahmadani

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

**SB Admin 2 License**: Copyright 2013-2021 Start Bootstrap LLC - [View License](https://github.com/StartBootstrap/startbootstrap-sb-admin-2/blob/master/LICENSE)

---

## 📞 Contact & Support

Untuk pertanyaan atau dukungan terkait project ini:

- **Email**: [Email tim atau individu]
- **GitHub Issues**: [Link to issues page]
- **Documentation**: Lihat file `FP DWO.docx` untuk dokumentasi lengkap

---

## 🙏 Acknowledgments

- **Adventure Works Database**: Microsoft sample database
- **SB Admin 2**: Start Bootstrap template
- **Chart.js**: Data visualization library
- **Mondrian OLAP**: Pentaho OLAP Server
- **Pentaho Data Integration**: ETL tool
- **UPN "Veteran" Jawa Timur**: Academic support

---

## 📚 References

1. Adventure Works Database Documentation - Microsoft
2. Pentaho Data Integration Documentation
3. Mondrian OLAP Server Documentation

---

**⭐ Jika project ini bermanfaat, jangan lupa berikan star di GitHub!**