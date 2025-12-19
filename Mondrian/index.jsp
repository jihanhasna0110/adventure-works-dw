<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>

<html>
<head>
    <title>Adventure Works - Mondrian OLAP Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #333; }
        .menu { list-style: none; padding: 0; }
        .menu li { margin: 10px 0; }
        .menu a { 
            display: block; 
            padding: 15px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            width: 500px;
        }
        .menu a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>📊 Adventure Works Data Warehouse</h1>
    <h2>Mondrian OLAP Analysis - Business Questions</h2>
    
    <ul class="menu">
        <li><a href="bq1_creditcard.jsp">BQ1: Jenis Kartu Kredit yang Paling Banyak Digunakan</a></li>
        <li><a href="bq2_productcolor.jsp">BQ2: Produk dengan Warna yang Paling Diminati</a></li>
        <li><a href="bq3_orderstatus.jsp">BQ3: Distribusi Status Order (1 Tahun Terakhir)</a></li>
        <li><a href="bq4_aov.jsp">BQ4: Average Order Value per Customer Type</a></li>
        <li><a href="bq5_salesperson.jsp">BQ5: Performa Salesperson per Territory</a></li>
    </ul>
    
    <hr>
    
    <h3>OLAP Operations Demo</h3>
    <ul class="menu">
        <li><a href="drill_down_demo.jsp">🔍 Drill-Down: Year → Quarter → Month</a></li>
        <li><a href="adventureworks.jsp">📊 Original Test Page</a></li>
    </ul>
</body>
</html>