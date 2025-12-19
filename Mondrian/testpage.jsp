<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %>
<%@ taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %>
<%@ taglib uri="http://www.tonbeller.com/wcf" prefix="wcf" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jstl/core" %>
<!DOCTYPE html>
<html>
<head>
  <title>Adventure Works - OLAP Analysis</title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<!-- TAMBAHKAN BAGIAN INI - SANGAT PENTING -->
  <link rel="stylesheet" type="text/css" href="jpivot/table/mdxtable.css">
  <link rel="stylesheet" type="text/css" href="jpivot/navi/mdxnavi.css">
  <link rel="stylesheet" type="text/css" href="wcf/form/xform.css">
  <link rel="stylesheet" type="text/css" href="wcf/table/xtable.css">
  <link rel="stylesheet" type="text/css" href="wcf/tree/xtree.css">
  
  <style>
    body { 
      font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; 
      background:#f5f5f5; 
      margin:0; 
      padding:20px; 
    }
    .header { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
      color:white; 
      padding:30px; 
      border-radius:10px; 
      margin-bottom:20px; 
      box-shadow:0 4px 6px rgba(0,0,0,0.1);
    }
    .header h2 { 
      margin:0 0 10px 0; 
      font-size:2em; 
    }
    .header .description { 
      margin:0; 
      font-size:1.1em; 
      opacity:0.95; 
    }
    .back-link { 
      display:inline-block; 
      margin:20px 0; 
      padding:12px 25px; 
      background:#667eea; 
      color:white; 
      text-decoration:none; 
      border-radius:5px; 
      font-weight:bold; 
      transition:all 0.3s ease; 
    }
    .back-link:hover { 
      background:#5568d3; 
      transform:translateY(-2px); 
      box-shadow:0 4px 8px rgba(102,126,234,0.3);
    }
    .info-box { 
      margin:20px 0; 
      padding:15px 20px; 
      background:#fff3cd; 
      border-left:4px solid #ffc107; 
      border-radius:5px;
    }
    .info-box strong { 
      color:#856404; 
    }
    .content-wrapper { 
      background:white; 
      padding:25px; 
      border-radius:10px; 
      box-shadow:0 2px 4px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>


<%
  String queryParam = request.getParameter("query");
  if(queryParam == null || queryParam.isEmpty()) queryParam = "demo1";
  session.setAttribute("currentQuery", queryParam);

  String pageTitle = "";
  String pageDescription = "";
  String infoMessage = "";

  switch(queryParam){
    case "bq1":
      pageTitle = "BQ1: Credit Card Usage Analysis";
      pageDescription = "Jenis kartu kredit yang paling banyak digunakan dalam transaksi penjualan";
      infoMessage = "Tabel menampilkan jumlah order dan total sales per jenis kartu kredit, diurutkan dari yang paling banyak digunakan.";
      break;
    case "bq2":
      pageTitle = "BQ2: Product Color Popularity";
      pageDescription = "Produk dengan warna yang paling diminati konsumen berdasarkan total quantity terjual";
      infoMessage = "Tabel menampilkan total quantity terjual per warna produk, diurutkan dari yang paling banyak terjual.";
      break;
    case "bq3":
      pageTitle = "BQ3: Order Status Distribution";
      pageDescription = "Distribusi status order dalam 1 tahun terakhir";
      infoMessage = "Tabel menampilkan total order dan sales untuk tahun 2004. Klik [+] untuk drill-down ke Quarter dan Month.";
      break;
    case "bq4":
      pageTitle = "BQ4: Average Order Value Analysis";
      pageDescription = "Rata-rata nilai transaksi per customer type (Individual vs Store) per kuartal";
      infoMessage = "Tabel menampilkan cross-tabulation antara Customer Type dan Quarter. Calculated Member 'Avg Order Value' dihitung otomatis.";
      break;
    case "bq5":
      pageTitle = "BQ5: Salesperson Performance";
      pageDescription = "Perbandingan performa penjualan antar salesperson di setiap territory (2 tahun terakhir)";
      infoMessage = "Tabel menampilkan cross-tabulation antara Territory dan Salesperson untuk tahun 2003-2004.";
      break;
    case "demo1":
      pageTitle = "OLAP Demo 1: Drill-Down & Drill-Through";
      pageDescription = "Demonstrasi operasi Drill-Down dari Year → Quarter → Month dan Drill-Through untuk detail transaksi";
      infoMessage = "Klik icon [+] di sebelah Year untuk drill-down ke Quarter, lalu klik [+] lagi untuk drill-down ke Month. Klik angka di cell untuk drill-through melihat detail transaksi.";
      break;
      
    case "demo2":
      pageTitle = "OLAP Demo 2: Slice & Dice Operations";
      pageDescription = "Demonstrasi operasi Slice (filter 1 dimensi) dan Dice (filter multiple dimensi)";
      infoMessage = "Slice: Data di-filter hanya untuk Year 2003. Gunakan Cube Navigator untuk menambah filter dimensi lain (Dice operation).";
      break;
      
    case "demo3":
      pageTitle = "OLAP Demo 3: Pivot & Roll-Up";
      pageDescription = "Demonstrasi operasi Pivot (rotasi axis) dan Roll-Up (agregasi ke level lebih tinggi)";
      infoMessage = "Klik icon 'Swap Axes' untuk melakukan Pivot. Klik icon [-] untuk Roll-Up (collapse hierarchy).";
      break;
      
    default:
      pageTitle = "Sales Analysis Overview";
      pageDescription = "Analisis penjualan umum berdasarkan periode waktu";
      infoMessage = "Tabel menampilkan total sales dan order count per tahun. Klik [+] untuk drill-down.";

  }
%>


<form action="testpage.jsp?query=<%= queryParam %>" method="post" id="jpivotForm">
<!-- Tidak perlu hidden input lagi karena sudah di URL -->

<!-- Header -->
<div class="header">
  <h2><%= pageTitle %></h2>
  <p class="description"><%= pageDescription %></p>
</div>

<a href="index.html" class="back-link">⬅ Kembali ke Menu Utama</a>

<div class="content-wrapper">



  <!-- ============================================ -->
  <!-- BQ1: Credit Card Usage Analysis -->
  <!-- ============================================ -->
  <%
    if("bq1".equals(queryParam)) {
  %>
      <%@ include file="/WEB-INF/queries/bq1.jsp" %>
      
      <jp:table id="table_bq1" query="#{query_bq1}"/>
      <jp:navigator id="navi_bq1" query="#{query_bq1}" visible="false"/>
      <wcf:form id="mdxedit01" xmlUri="/WEB-INF/jpivot/table/mdxedit.xml" model="#{query_bq1}" visible="false"/>
      <wcf:form id="sortform01" xmlUri="/WEB-INF/jpivot/table/sortform.xml" model="#{table_bq1}" visible="false"/>
      <jp:print id="print01"/>
      <wcf:form id="printform01" xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml" model="#{print01}" visible="false"/>
      <jp:chart id="chart01" query="#{query_bq1}" visible="false"/>
      <wcf:form id="chartform01" xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml" model="#{chart01}" visible="false"/>
      <wcf:table id="query_bq1.drillthroughtable" visible="false" selmode="none" editable="true"/>
      
      <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
        <wcf:scriptbutton id="cubeNaviButton" tooltip="toolb.cube" img="cube" model="#{navi_bq1.visible}"/>
        <wcf:scriptbutton id="mdxEditButton" tooltip="toolb.mdx.edit" img="mdx-edit" model="#{mdxedit01.visible}"/>
        <wcf:scriptbutton id="sortConfigButton" tooltip="toolb.table.config" img="sort-asc" model="#{sortform01.visible}"/>
        <wcf:separator/>
        <wcf:scriptbutton id="levelStyle" tooltip="toolb.level.style" img="level-style" model="#{table_bq1.extensions.axisStyle.levelStyle}"/>
        <wcf:scriptbutton id="hideSpans" tooltip="toolb.hide.spans" img="hide-spans" model="#{table_bq1.extensions.axisStyle.hideSpans}"/>
        <wcf:scriptbutton id="nonEmpty" tooltip="toolb.non.empty" img="non-empty" model="#{table_bq1.extensions.nonEmpty.buttonPressed}"/>
        <wcf:scriptbutton id="swapAxes" tooltip="toolb.swap.axes" img="swap-axes" model="#{table_bq1.extensions.swapAxes.buttonPressed}"/>
        <wcf:separator/>
        <wcf:scriptbutton model="#{table_bq1.extensions.drillMember.enabled}" tooltip="toolb.navi.member" radioGroup="navi" id="drillMember" img="navi-member"/>
        <wcf:scriptbutton model="#{table_bq1.extensions.drillPosition.enabled}" tooltip="toolb.navi.position" radioGroup="navi" id="drillPosition" img="navi-position"/>
        <wcf:scriptbutton model="#{table_bq1.extensions.drillReplace.enabled}" tooltip="toolb.navi.replace" radioGroup="navi" id="drillReplace" img="navi-replace"/>
        <wcf:scriptbutton model="#{table_bq1.extensions.drillThrough.enabled}" tooltip="toolb.navi.drillthru" id="drillThrough01" img="navi-through"/>
        <wcf:separator/>
        <wcf:scriptbutton id="chartButton01" tooltip="toolb.chart" img="chart" model="#{chart01.visible}"/>
        <wcf:scriptbutton id="chartPropertiesButton01" tooltip="toolb.chart.config" img="chart-config" model="#{chartform01.visible}"/>
      </wcf:toolbar>
      
      <wcf:render ref="toolbar01" xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" xslCache="true"/>
      
      <div class="info-box">
        <strong>💡 Info:</strong> <%= infoMessage %>
      </div>
      
      <wcf:render ref="navi_bq1" xslUri="/WEB-INF/jpivot/navi/navigator.xsl" xslCache="true"/>
      
      <c:if test="${mdxedit01.visible}">
        <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
        <wcf:render ref="mdxedit01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      </c:if>
      
      <wcf:render ref="sortform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chartform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="printform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      
      <div style="margin: 20px 0;">
        <wcf:render ref="table_bq1" xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" xslCache="true"/>
      </div>
      
      <div style="margin: 15px 0;">
        <strong>Slicer:</strong>
        <wcf:render ref="table_bq1" xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" xslCache="true"/>
      </div>
      
      <wcf:render ref="query_bq1.drillthroughtable" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chart01" xslUri="/WEB-INF/jpivot/chart/chart.xsl" xslCache="true"/>
  <%
    } 
    // ============================================
    // BQ2: Product Color Popularity
    // ============================================
    else if("bq2".equals(queryParam)) {
  %>
      <%@ include file="/WEB-INF/queries/bq2.jsp" %>
      
      <jp:table id="table_bq2" query="#{query_bq2}" />
      <jp:navigator id="navi_bq2" query="#{query_bq2}" visible="false"/>
      <wcf:form id="mdxedit01" xmlUri="/WEB-INF/jpivot/table/mdxedit.xml" model="#{query_bq2}" visible="false"/>
      <wcf:form id="sortform01" xmlUri="/WEB-INF/jpivot/table/sortform.xml" model="#{table_bq2}" visible="false"/>
      <jp:print id="print01"/>
      <wcf:form id="printform01" xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml" model="#{print01}" visible="false"/>
      <jp:chart id="chart01" query="#{query_bq2}" visible="false"/>
      <wcf:form id="chartform01" xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml" model="#{chart01}" visible="false"/>
      <wcf:table id="query_bq2.drillthroughtable" visible="false" selmode="none" editable="true"/>
      
      <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
        <wcf:scriptbutton id="cubeNaviButton" tooltip="toolb.cube" img="cube" model="#{navi_bq2.visible}"/>
        <wcf:scriptbutton id="mdxEditButton" tooltip="toolb.mdx.edit" img="mdx-edit" model="#{mdxedit01.visible}"/>
        <wcf:scriptbutton id="sortConfigButton" tooltip="toolb.table.config" img="sort-asc" model="#{sortform01.visible}"/>
        <wcf:separator/>
        <wcf:scriptbutton id="levelStyle" tooltip="toolb.level.style" img="level-style" model="#{table_bq2.extensions.axisStyle.levelStyle}"/>
        <wcf:scriptbutton id="hideSpans" tooltip="toolb.hide.spans" img="hide-spans" model="#{table_bq2.extensions.axisStyle.hideSpans}"/>
        <wcf:scriptbutton id="nonEmpty" tooltip="toolb.non.empty" img="non-empty" model="#{table_bq2.extensions.nonEmpty.buttonPressed}"/>
        <wcf:scriptbutton id="swapAxes" tooltip="toolb.swap.axes" img="swap-axes" model="#{table_bq2.extensions.swapAxes.buttonPressed}"/>
        <wcf:separator/>
        <wcf:scriptbutton model="#{table_bq2.extensions.drillMember.enabled}" tooltip="toolb.navi.member" radioGroup="navi" id="drillMember" img="navi-member"/>
        <wcf:scriptbutton model="#{table_bq2.extensions.drillPosition.enabled}" tooltip="toolb.navi.position" radioGroup="navi" id="drillPosition" img="navi-position"/>
        <wcf:scriptbutton model="#{table_bq2.extensions.drillReplace.enabled}" tooltip="toolb.navi.replace" radioGroup="navi" id="drillReplace" img="navi-replace"/>
        <wcf:scriptbutton model="#{table_bq2.extensions.drillThrough.enabled}" tooltip="toolb.navi.drillthru" id="drillThrough01" img="navi-through"/>
        <wcf:separator/>
        <wcf:scriptbutton id="chartButton01" tooltip="toolb.chart" img="chart" model="#{chart01.visible}"/>
        <wcf:scriptbutton id="chartPropertiesButton01" tooltip="toolb.chart.config" img="chart-config" model="#{chartform01.visible}"/>
      </wcf:toolbar>
      
      <wcf:render ref="toolbar01" xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" xslCache="true"/>
      
      <div class="info-box">
        <strong>💡 Info:</strong> <%= infoMessage %>
      </div>
      
      <wcf:render ref="navi_bq2" xslUri="/WEB-INF/jpivot/navi/navigator.xsl" xslCache="true"/>
      
      <c:if test="${mdxedit01.visible}">
        <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
        <wcf:render ref="mdxedit01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      </c:if>
      
      <wcf:render ref="sortform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chartform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="printform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      
      <div style="margin: 20px 0;">
        <wcf:render ref="table_bq2" xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" xslCache="true"/>
      </div>
      
      <div style="margin: 15px 0;">
        <strong>Slicer:</strong>
        <wcf:render ref="table_bq2" xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" xslCache="true"/>
      </div>
      
      <wcf:render ref="query_bq2.drillthroughtable" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chart01" xslUri="/WEB-INF/jpivot/chart/chart.xsl" xslCache="true"/>
  <%
    } 
    // ============================================
    // BQ3: Order Status Distribution
    // ============================================
    else if("bq3".equals(queryParam)) {
  %>
      <%@ include file="/WEB-INF/queries/bq3.jsp" %>
      
      <jp:table id="table_bq3" query="#{query_bq3}"/>
      <jp:navigator id="navi_bq3" query="#{query_bq3}" visible="false"/>
      <wcf:form id="mdxedit01" xmlUri="/WEB-INF/jpivot/table/mdxedit.xml" model="#{query_bq3}" visible="false"/>
      <wcf:form id="sortform01" xmlUri="/WEB-INF/jpivot/table/sortform.xml" model="#{table_bq3}" visible="false"/>
      <jp:print id="print01"/>
      <wcf:form id="printform01" xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml" model="#{print01}" visible="false"/>
      <jp:chart id="chart01" query="#{query_bq3}" visible="false"/>
      <wcf:form id="chartform01" xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml" model="#{chart01}" visible="false"/>
      <wcf:table id="query_bq3.drillthroughtable" visible="false" selmode="none" editable="true"/>
      
      <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
        <wcf:scriptbutton id="cubeNaviButton" tooltip="toolb.cube" img="cube" model="#{navi_bq3.visible}"/>
        <wcf:scriptbutton id="mdxEditButton" tooltip="toolb.mdx.edit" img="mdx-edit" model="#{mdxedit01.visible}"/>
        <wcf:scriptbutton id="sortConfigButton" tooltip="toolb.table.config" img="sort-asc" model="#{sortform01.visible}"/>
        <wcf:separator/>
        <wcf:scriptbutton id="levelStyle" tooltip="toolb.level.style" img="level-style" model="#{table_bq3.extensions.axisStyle.levelStyle}"/>
        <wcf:scriptbutton id="hideSpans" tooltip="toolb.hide.spans" img="hide-spans" model="#{table_bq3.extensions.axisStyle.hideSpans}"/>
        <wcf:scriptbutton id="nonEmpty" tooltip="toolb.non.empty" img="non-empty" model="#{table_bq3.extensions.nonEmpty.buttonPressed}"/>
        <wcf:scriptbutton id="swapAxes" tooltip="toolb.swap.axes" img="swap-axes" model="#{table_bq3.extensions.swapAxes.buttonPressed}"/>
        <wcf:separator/>
        <wcf:scriptbutton model="#{table_bq3.extensions.drillMember.enabled}" tooltip="toolb.navi.member" radioGroup="navi" id="drillMember" img="navi-member"/>
        <wcf:scriptbutton model="#{table_bq3.extensions.drillPosition.enabled}" tooltip="toolb.navi.position" radioGroup="navi" id="drillPosition" img="navi-position"/>
        <wcf:scriptbutton model="#{table_bq3.extensions.drillReplace.enabled}" tooltip="toolb.navi.replace" radioGroup="navi" id="drillReplace" img="navi-replace"/>
        <wcf:scriptbutton model="#{table_bq3.extensions.drillThrough.enabled}" tooltip="toolb.navi.drillthru" id="drillThrough01" img="navi-through"/>
        <wcf:separator/>
        <wcf:scriptbutton id="chartButton01" tooltip="toolb.chart" img="chart" model="#{chart01.visible}"/>
        <wcf:scriptbutton id="chartPropertiesButton01" tooltip="toolb.chart.config" img="chart-config" model="#{chartform01.visible}"/>
      </wcf:toolbar>
      
      <wcf:render ref="toolbar01" xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" xslCache="true"/>
      
      <div class="info-box">
        <strong>💡 Info:</strong> <%= infoMessage %>
      </div>
      
      <wcf:render ref="navi_bq3" xslUri="/WEB-INF/jpivot/navi/navigator.xsl" xslCache="true"/>
      
      <c:if test="${mdxedit01.visible}">
        <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
        <wcf:render ref="mdxedit01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      </c:if>
      
      <wcf:render ref="sortform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chartform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="printform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      
      <div style="margin: 20px 0;">
        <wcf:render ref="table_bq3" xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" xslCache="true"/>
      </div>
      
      <div style="margin: 15px 0;">
        <strong>Slicer:</strong>
        <wcf:render ref="table_bq3" xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" xslCache="true"/>
      </div>
      
      <wcf:render ref="query_bq3.drillthroughtable" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chart01" xslUri="/WEB-INF/jpivot/chart/chart.xsl" xslCache="true"/>
  <%
    } 
    // ============================================
    // BQ4: Average Order Value Analysis
    // ============================================
    else if("bq4".equals(queryParam)) {
  %>
      <%@ include file="/WEB-INF/queries/bq4.jsp" %>
      
      <jp:table id="table_bq4" query="#{query_bq4}"/>
      <jp:navigator id="navi_bq4" query="#{query_bq4}" visible="false"/>
      <wcf:form id="mdxedit01" xmlUri="/WEB-INF/jpivot/table/mdxedit.xml" model="#{query_bq4}" visible="false"/>
      <wcf:form id="sortform01" xmlUri="/WEB-INF/jpivot/table/sortform.xml" model="#{table_bq4}" visible="false"/>
      <jp:print id="print01"/>
      <wcf:form id="printform01" xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml" model="#{print01}" visible="false"/>
      <jp:chart id="chart01" query="#{query_bq4}" visible="false"/>
      <wcf:form id="chartform01" xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml" model="#{chart01}" visible="false"/>
      <wcf:table id="query_bq4.drillthroughtable" visible="false" selmode="none" editable="true"/>
      
      <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
        <wcf:scriptbutton id="cubeNaviButton" tooltip="toolb.cube" img="cube" model="#{navi_bq4.visible}"/>
        <wcf:scriptbutton id="mdxEditButton" tooltip="toolb.mdx.edit" img="mdx-edit" model="#{mdxedit01.visible}"/>
        <wcf:scriptbutton id="sortConfigButton" tooltip="toolb.table.config" img="sort-asc" model="#{sortform01.visible}"/>
        <wcf:separator/>
        <wcf:scriptbutton id="levelStyle" tooltip="toolb.level.style" img="level-style" model="#{table_bq4.extensions.axisStyle.levelStyle}"/>
        <wcf:scriptbutton id="hideSpans" tooltip="toolb.hide.spans" img="hide-spans" model="#{table_bq4.extensions.axisStyle.hideSpans}"/>
        <wcf:scriptbutton id="nonEmpty" tooltip="toolb.non.empty" img="non-empty" model="#{table_bq4.extensions.nonEmpty.buttonPressed}"/>
        <wcf:scriptbutton id="swapAxes" tooltip="toolb.swap.axes" img="swap-axes" model="#{table_bq4.extensions.swapAxes.buttonPressed}"/>
        <wcf:separator/>
        <wcf:scriptbutton model="#{table_bq4.extensions.drillMember.enabled}" tooltip="toolb.navi.member" radioGroup="navi" id="drillMember" img="navi-member"/>
        <wcf:scriptbutton model="#{table_bq4.extensions.drillPosition.enabled}" tooltip="toolb.navi.position" radioGroup="navi" id="drillPosition" img="navi-position"/>
        <wcf:scriptbutton model="#{table_bq4.extensions.drillReplace.enabled}" tooltip="toolb.navi.replace" radioGroup="navi" id="drillReplace" img="navi-replace"/>
        <wcf:scriptbutton model="#{table_bq4.extensions.drillThrough.enabled}" tooltip="toolb.navi.drillthru" id="drillThrough01" img="navi-through"/>
        <wcf:separator/>
        <wcf:scriptbutton id="chartButton01" tooltip="toolb.chart" img="chart" model="#{chart01.visible}"/>
        <wcf:scriptbutton id="chartPropertiesButton01" tooltip="toolb.chart.config" img="chart-config" model="#{chartform01.visible}"/>
      </wcf:toolbar>
      
      <wcf:render ref="toolbar01" xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" xslCache="true"/>
      
      <div class="info-box">
        <strong>💡 Info:</strong> <%= infoMessage %>
      </div>
      
      <wcf:render ref="navi_bq4" xslUri="/WEB-INF/jpivot/navi/navigator.xsl" xslCache="true"/>
      
      <c:if test="${mdxedit01.visible}">
        <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
        <wcf:render ref="mdxedit01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      </c:if>
      
      <wcf:render ref="sortform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chartform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="printform01" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      
      <div style="margin: 20px 0;">
        <wcf:render ref="table_bq4" xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" xslCache="true"/>
      </div>
      
      <div style="margin: 15px 0;">
        <strong>Slicer:</strong>
        <wcf:render ref="table_bq4" xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" xslCache="true"/>
      </div>
      
      <wcf:render ref="query_bq4.drillthroughtable" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
      <wcf:render ref="chart01" xslUri="/WEB-INF/jpivot/chart/chart.xsl" xslCache="true"/>
  <%
    } 
    // ============================================
    // BQ5: Salesperson Performance
    // ============================================
    else if("bq5".equals(queryParam)) {
%>
<%@ include file="/WEB-INF/queries/bq5.jsp" %>
<jp:table id="table_bq5" query="#{query_bq5}"/>
<jp:navigator id="navi_bq5" query="#{query_bq5}" visible="false"/>

<wcf:form id="mdxedit05" xmlUri="/WEB-INF/jpivot/table/mdxedit.xml"
          model="#{query_bq5}" visible="false"/>
<wcf:form id="sortform05" xmlUri="/WEB-INF/jpivot/table/sortform.xml"
          model="#{table_bq5}" visible="false"/>

<jp:print id="print05"/>
<wcf:form id="printform05"
          xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml"
          model="#{print05}" visible="false"/>

<jp:chart id="chart05" query="#{query_bq5}" visible="false"/>
<wcf:form id="chartform05"
          xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml"
          model="#{chart05}" visible="false"/>

<wcf:table id="query_bq5.drillthroughtable"
           visible="false" selmode="none" editable="true"/>

<wcf:toolbar id="toolbar05" bundle="com.tonbeller.jpivot.toolbar.resources">
  <wcf:scriptbutton id="cubeNaviButton05"
      tooltip="toolb.cube" img="cube"
      model="#{navi_bq5.visible}"/>

  <wcf:scriptbutton id="mdxEditButton05"
      tooltip="toolb.mdx.edit" img="mdx-edit"
      model="#{mdxedit05.visible}"/>

  <wcf:scriptbutton id="sortConfigButton05"
      tooltip="toolb.table.config" img="sort-asc"
      model="#{sortform05.visible}"/>

  <wcf:separator/>

  <wcf:scriptbutton id="levelStyle05"
      tooltip="toolb.level.style" img="level-style"
      model="#{table_bq5.extensions.axisStyle.levelStyle}"/>

  <wcf:scriptbutton id="hideSpans05"
      tooltip="toolb.hide.spans" img="hide-spans"
      model="#{table_bq5.extensions.axisStyle.hideSpans}"/>

  <wcf:scriptbutton id="nonEmpty05"
      tooltip="toolb.non.empty" img="non-empty"
      model="#{table_bq5.extensions.nonEmpty.buttonPressed}"/>

  <wcf:scriptbutton id="swapAxes05"
      tooltip="toolb.swap.axes" img="swap-axes"
      model="#{table_bq5.extensions.swapAxes.buttonPressed}"/>

  <wcf:separator/>

  <wcf:scriptbutton id="drillMember05"
      tooltip="toolb.navi.member" img="navi-member"
      radioGroup="navi"
      model="#{table_bq5.extensions.drillMember.enabled}"/>

  <wcf:scriptbutton id="drillPosition05"
      tooltip="toolb.navi.position" img="navi-position"
      radioGroup="navi"
      model="#{table_bq5.extensions.drillPosition.enabled}"/>

  <wcf:scriptbutton id="drillReplace05"
      tooltip="toolb.navi.replace" img="navi-replace"
      radioGroup="navi"
      model="#{table_bq5.extensions.drillReplace.enabled}"/>

  <wcf:scriptbutton id="drillThrough05"
      tooltip="toolb.navi.drillthru" img="navi-through"
      model="#{table_bq5.extensions.drillThrough.enabled}"/>

  <wcf:separator/>

  <wcf:scriptbutton id="chartButton05"
      tooltip="toolb.chart" img="chart"
      model="#{chart05.visible}"/>

  <wcf:scriptbutton id="chartPropertiesButton05"
      tooltip="toolb.chart.config" img="chart-config"
      model="#{chartform05.visible}"/>
</wcf:toolbar>

<wcf:render ref="toolbar05"
            xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl"
            xslCache="true"/>

<wcf:render ref="navi_bq5"
            xslUri="/WEB-INF/jpivot/navi/navigator.xsl"
            xslCache="true"/>

<wcf:render ref="sortform05" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
<wcf:render ref="chartform05" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
<wcf:render ref="printform05" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>

<wcf:render ref="table_bq5"
            xslUri="/WEB-INF/jpivot/table/mdxtable.xsl"
            xslCache="true"/>

<wcf:render ref="table_bq5"
            xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl"
            xslCache="true"/>

<wcf:render ref="query_bq5.drillthroughtable"
            xslUri="/WEB-INF/wcf/wcf.xsl"
            xslCache="true"/>

<wcf:render ref="chart05"
            xslUri="/WEB-INF/jpivot/chart/chart.xsl"
            xslCache="true"/>
<%
}
  // ============================================
  // DEMO 1: Drill-Down & Drill-Through
  // ============================================
  if("demo1".equals(queryParam)) {
%>
    <%@ include file="/WEB-INF/queries/demo_drilldown.jsp" %>
    
    <jp:table id="table01" query="#{querydemo1}"/>
    <jp:navigator id="navi01" query="#{querydemo1}" visible="false"/>
    
    <wcf:form id="mdxedit01" 
              xmlUri="/WEB-INF/jpivot/table/mdxedit.xml" 
              model="#{querydemo1}" 
              visible="false"/>
    
    <wcf:form id="sortform01" 
              xmlUri="/WEB-INF/jpivot/table/sortform.xml" 
              model="#{table01}" 
              visible="false"/>
    
    <jp:print id="print01"/>
    
    <wcf:form id="printform01" 
              xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml" 
              model="#{print01}" 
              visible="false"/>
    
    <jp:chart id="chart01" query="#{querydemo1}" visible="false"/>
    
    <wcf:form id="chartform01" 
              xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml" 
              model="#{chart01}" 
              visible="false"/>
    
    <wcf:table id="querydemo1.drillthroughtable" 
               visible="false" 
               selmode="none" 
               editable="true"/>
    
    <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
      <wcf:scriptbutton id="cubeNaviButton" 
                        tooltip="toolb.cube" 
                        img="cube" 
                        model="#{navi01.visible}"/>
      
      <wcf:scriptbutton id="mdxEditButton" 
                        tooltip="toolb.mdx.edit" 
                        img="mdx-edit" 
                        model="#{mdxedit01.visible}"/>
      
      <wcf:scriptbutton id="sortConfigButton" 
                        tooltip="toolb.table.config" 
                        img="sort-asc" 
                        model="#{sortform01.visible}"/>
      
      <wcf:separator/>
      
      <wcf:scriptbutton id="levelStyle" 
                        tooltip="toolb.level.style" 
                        img="level-style" 
                        model="#{table01.extensions.axisStyle.levelStyle}"/>
      
      <wcf:scriptbutton id="hideSpans" 
                        tooltip="toolb.hide.spans" 
                        img="hide-spans" 
                        model="#{table01.extensions.axisStyle.hideSpans}"/>
      
      <wcf:scriptbutton id="nonEmpty" 
                        tooltip="toolb.non.empty" 
                        img="non-empty" 
                        model="#{table01.extensions.nonEmpty.buttonPressed}"/>
      
      <wcf:scriptbutton id="swapAxes" 
                        tooltip="toolb.swap.axes" 
                        img="swap-axes" 
                        model="#{table01.extensions.swapAxes.buttonPressed}"/>
      
      <wcf:separator/>
      
      <wcf:scriptbutton model="#{table01.extensions.drillMember.enabled}" 
                        tooltip="toolb.navi.member" 
                        radioGroup="navi" 
                        id="drillMember" 
                        img="navi-member"/>
      
      <wcf:scriptbutton model="#{table01.extensions.drillPosition.enabled}" 
                        tooltip="toolb.navi.position" 
                        radioGroup="navi" 
                        id="drillPosition" 
                        img="navi-position"/>
      
      <wcf:scriptbutton model="#{table01.extensions.drillReplace.enabled}" 
                        tooltip="toolb.navi.replace" 
                        radioGroup="navi" 
                        id="drillReplace" 
                        img="navi-replace"/>
      
      <wcf:scriptbutton model="#{table01.extensions.drillThrough.enabled}" 
                        tooltip="toolb.navi.drillthru" 
                        id="drillThrough01" 
                        img="navi-through"/>
      
      <wcf:separator/>
      
      <wcf:scriptbutton id="chartButton01" 
                        tooltip="toolb.chart" 
                        img="chart" 
                        model="#{chart01.visible}"/>
      
      <wcf:scriptbutton id="chartPropertiesButton01" 
                        tooltip="toolb.chart.config" 
                        img="chart-config" 
                        model="#{chartform01.visible}"/>
    </wcf:toolbar>
    
    <wcf:render ref="toolbar01" 
                xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" 
                xslCache="true"/>
    
    <!-- TUTORIAL BOX -->
    <div class="tutorial-box">
      <strong style="color: #c62828; font-size: 1.2em;">⚠️ CARA DRILL-DOWN:</strong>
      <ol style="margin: 10px 0; padding-left: 25px; color: #d32f2f;">
        <li><strong>KLIK salah satu tombol drill:</strong> "Member", "Position", atau "Replace" (di toolbar atas)</li>
        <li>Tombol yang diklik akan berubah warna (aktif/pressed)</li>
        <li>Setelah itu, klik icon <strong>[+]</strong> di sebelah Year (2001, 2002, 2003, 2004)</li>
        <li>Quarter akan muncul (Q1, Q2, Q3, Q4)</li>
        <li>Klik <strong>[+]</strong> lagi untuk melihat Month</li>
        <li>Klik <strong>[-]</strong> untuk collapse/roll-up</li>
      </ol>
    </div>
    
    <div class="info-box">
      <strong>💡 Info:</strong> <%= infoMessage %>
    </div>
    
    <wcf:render ref="navi01" 
                xslUri="/WEB-INF/jpivot/navi/navigator.xsl" 
                xslCache="true"/>
    
    <c:if test="${mdxedit01.visible}">
      <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
      <wcf:render ref="mdxedit01" 
                  xslUri="/WEB-INF/wcf/wcf.xsl" 
                  xslCache="true"/>
    </c:if>
    
    <wcf:render ref="sortform01" 
                xslUri="/WEB-INF/wcf/wcf.xsl" 
                xslCache="true"/>
    
    <wcf:render ref="chartform01" 
                xslUri="/WEB-INF/wcf/wcf.xsl" 
                xslCache="true"/>
    
    <wcf:render ref="printform01" 
                xslUri="/WEB-INF/wcf/wcf.xsl" 
                xslCache="true"/>
    
    <div style="margin: 20px 0;">
      <h3>📊 Tabel OLAP - Klik [+] Setelah Aktifkan Mode Drill</h3>
      <wcf:render ref="table01" 
                  xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" 
                  xslCache="true"/>
    </div>
    
    <div style="margin: 15px 0;">
      <strong>Slicer:</strong>
      <wcf:render ref="table01" 
                  xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" 
                  xslCache="true"/>
    </div>
    
    <wcf:render ref="querydemo1.drillthroughtable" 
                xslUri="/WEB-INF/wcf/wcf.xsl" 
                xslCache="true"/>
    
    <wcf:render ref="chart01" 
                xslUri="/WEB-INF/jpivot/chart/chart.xsl" 
                xslCache="true"/>
  <%
  } 
  
  // ============================================
  // DEMO 2: Slice & Dice
  // ============================================
  else if ("demo2".equals(queryParam)) {
%>
    <%@ include file="/WEB-INF/queries/demo_slice.jsp" %>

    <!-- MODEL & VIEW -->
    <jp:table     id="table_demo2"  query="#{querydemo2}"/>
    <jp:navigator id="navi_demo2"   query="#{querydemo2}" visible="false"/>

    <wcf:form id="mdxedit01"
              xmlUri="/WEB-INF/jpivot/table/mdxedit.xml"
              model="#{querydemo2}" visible="false"/>

    <wcf:form id="sortform01"
              xmlUri="/WEB-INF/jpivot/table/sortform.xml"
              model="#{table_demo2}" visible="false"/>

    <jp:print id="print01"/>

    <wcf:form id="printform01"
              xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml"
              model="#{print01}" visible="false"/>

    <jp:chart id="chart01" query="#{querydemo2}" visible="false"/>

    <wcf:form id="chartform01"
              xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml"
              model="#{chart01}" visible="false"/>

    <wcf:table id="querydemo2.drillthroughtable"
               visible="false" selmode="none" editable="true"/>

    <!-- TOOLBAR -->
    <wcf:toolbar id="toolbar01" bundle="com.tonbeller.jpivot.toolbar.resources">
      <wcf:scriptbutton id="cubeNaviButton"
                        tooltip="toolb.cube" img="cube"
                        model="#{navi_demo2.visible}"/>

      <wcf:scriptbutton id="mdxEditButton"
                        tooltip="toolb.mdx.edit" img="mdx-edit"
                        model="#{mdxedit01.visible}"/>

      <wcf:scriptbutton id="sortConfigButton"
                        tooltip="toolb.table.config" img="sort-asc"
                        model="#{sortform01.visible}"/>

      <wcf:separator/>

      <wcf:scriptbutton id="levelStyle"
                        tooltip="toolb.level.style" img="level-style"
                        model="#{table_demo2.extensions.axisStyle.levelStyle}"/>

      <wcf:scriptbutton id="hideSpans"
                        tooltip="toolb.hide.spans" img="hide-spans"
                        model="#{table_demo2.extensions.axisStyle.hideSpans}"/>

      <wcf:scriptbutton id="nonEmpty"
                        tooltip="toolb.non.empty" img="non-empty"
                        model="#{table_demo2.extensions.nonEmpty.buttonPressed}"/>

      <wcf:scriptbutton id="swapAxes"
                        tooltip="toolb.swap.axes" img="swap-axes"
                        model="#{table_demo2.extensions.swapAxes.buttonPressed}"/>

      <wcf:separator/>

      <wcf:scriptbutton id="drillMember"
                        tooltip="toolb.navi.member" img="navi-member"
                        radioGroup="navi"
                        model="#{table_demo2.extensions.drillMember.enabled}"/>

      <wcf:scriptbutton id="drillPosition"
                        tooltip="toolb.navi.position" img="navi-position"
                        radioGroup="navi"
                        model="#{table_demo2.extensions.drillPosition.enabled}"/>

      <wcf:scriptbutton id="drillReplace"
                        tooltip="toolb.navi.replace" img="navi-replace"
                        radioGroup="navi"
                        model="#{table_demo2.extensions.drillReplace.enabled}"/>

      <wcf:scriptbutton id="drillThrough01"
                        tooltip="toolb.navi.drillthru" img="navi-through"
                        model="#{table_demo2.extensions.drillThrough.enabled}"/>

      <wcf:separator/>

      <wcf:scriptbutton id="chartButton01"
                        tooltip="toolb.chart" img="chart"
                        model="#{chart01.visible}"/>

      <wcf:scriptbutton id="chartPropertiesButton01"
                        tooltip="toolb.chart.config" img="chart-config"
                        model="#{chartform01.visible}"/>
    </wcf:toolbar>

    <!-- RENDER TOOLBAR -->
    <wcf:render ref="toolbar01"
                xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl"
                xslCache="true"/>

    <!-- PANEL TUTORIAL -->
    <div style="background:#fff3e0;padding:20px;border-radius:10px;
                margin:20px 0;border-left:5px solid #ff9800;">
      <h3 style="color:#e65100;margin-top:0;">📚 Tutorial: Slice & Dice Operations</h3>

      <div style="margin:15px 0;">
        <strong style="color:#f57c00;">1️⃣ SLICE (Filter 1 Dimensi):</strong>
        <ul style="margin:10px 0;padding-left:25px;">
          <li><strong>Saat ini aktif:</strong> Data di-filter hanya untuk <strong>Year 2003</strong></li>
          <li>Perhatikan bagian "Slicer" di bawah tabel yang menunjukkan filter aktif</li>
          <li>Ini adalah contoh Slice operation - memotong data berdasarkan satu dimensi</li>
        </ul>
      </div>

      <div style="margin:15px 0;">
        <strong style="color:#f57c00;">2️⃣ DICE (Filter Multiple Dimensi):</strong>
        <ul style="margin:10px 0;padding-left:25px;">
          <li>Klik icon <strong>"Cube Navigator"</strong> di toolbar (icon kubus)</li>
          <li>Pilih dimensi lain untuk menambah filter (contoh: Territory, Customer Type)</li>
          <li>Drag & drop dimensi yang diinginkan ke area Slicer</li>
          <li>Ini adalah Dice operation - memotong data berdasarkan kombinasi multiple dimensi</li>
        </ul>
      </div>

      <div style="margin:15px 0;">
        <strong style="color:#f57c00;">3️⃣ Contoh Dice:</strong>
        <ul style="margin:10px 0;padding-left:25px;">
          <li>Filter: Year = 2003 AND Territory = Northwest</li>
          <li>Hasil: Hanya data penjualan tahun 2003 di wilayah Northwest yang ditampilkan</li>
        </ul>
      </div>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
      <strong>💡 Info:</strong> <%= infoMessage %>
    </div>

    <!-- RENDER CUBE NAVIGATOR -->
    <wcf:render ref="navi_demo2"
                xslUri="/WEB-INF/jpivot/navi/navigator.xsl"
                xslCache="true"/>

    <!-- MDX EDITOR -->
    <c:if test="${mdxedit01.visible}">
      <h3 style="color:#495057;margin-top:20px;">MDX Query Editor</h3>
      <wcf:render ref="mdxedit01"
                  xslUri="/WEB-INF/wcf/wcf.xsl"
                  xslCache="true"/>
    </c:if>

    <!-- FORM LAIN -->
    <wcf:render ref="sortform01"
                xslUri="/WEB-INF/wcf/wcf.xsl"
                xslCache="true"/>
    <wcf:render ref="chartform01"
                xslUri="/WEB-INF/wcf/wcf.xsl"
                xslCache="true"/>
    <wcf:render ref="printform01"
                xslUri="/WEB-INF/wcf/wcf.xsl"
                xslCache="true"/>

    <!-- TABEL PIVOT -->
    <div style="margin:20px 0;">
      <wcf:render ref="table_demo2"
                  xslUri="/WEB-INF/jpivot/table/mdxtable.xsl"
                  xslCache="true"/>
    </div>

    <!-- SLICER -->
    <div style="margin:15px 0;">
      <strong>Slicer (Filter Aktif):</strong>
      <wcf:render ref="table_demo2"
                  xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl"
                  xslCache="true"/>
    </div>

    <!-- DRILLTHROUGH & CHART -->
    <wcf:render ref="querydemo2.drillthroughtable"
                xslUri="/WEB-INF/wcf/wcf.xsl"
                xslCache="true"/>
    <wcf:render ref="chart01"
                xslUri="/WEB-INF/jpivot/chart/chart.xsl"
                xslCache="true"/>
  

<%
}
  // ============================================
  // DEMO 3: Pivot & Roll-Up
  // ============================================
  else if("demo3".equals(queryParam)) {
%>
    <%@ include file="/WEB-INF/queries/demo_pivot.jsp" %>

    <jp:table    id="table_demo3"   query="#{querydemo3}"/>
    <jp:navigator id="navi_demo3"   query="#{querydemo3}" visible="false"/>

    <wcf:form id="mdxedit03"  xmlUri="/WEB-INF/jpivot/table/mdxedit.xml"
              model="#{querydemo3}" visible="false"/>
    <wcf:form id="sortform03" xmlUri="/WEB-INF/jpivot/table/sortform.xml"
              model="#{table_demo3}" visible="false"/>

    <jp:print id="print03"/>
    <wcf:form id="printform03"
              xmlUri="/WEB-INF/jpivot/print/printpropertiesform.xml"
              model="#{print03}" visible="false"/>

    <jp:chart id="chart03" query="#{querydemo3}" visible="false"/>
    <wcf:form id="chartform03"
              xmlUri="/WEB-INF/jpivot/chart/chartpropertiesform.xml"
              model="#{chart03}" visible="false"/>

    <wcf:table id="querydemo3.drillthroughtable"
               visible="false" selmode="none" editable="true"/>

    <wcf:toolbar id="toolbar03" bundle="com.tonbeller.jpivot.toolbar.resources">
      <wcf:scriptbutton id="cubeNaviButton03" tooltip="toolb.cube" img="cube"
                        model="#{navi_demo3.visible}"/>
      <wcf:scriptbutton id="mdxEditButton03" tooltip="toolb.mdx.edit" img="mdx-edit"
                        model="#{mdxedit03.visible}"/>
      <wcf:scriptbutton id="sortConfigButton03" tooltip="toolb.table.config" img="sort-asc"
                        model="#{sortform03.visible}"/>
      <wcf:separator/>
      <wcf:scriptbutton id="levelStyle03" tooltip="toolb.level.style" img="level-style"
                        model="#{table_demo3.extensions.axisStyle.levelStyle}"/>
      <wcf:scriptbutton id="hideSpans03" tooltip="toolb.hide.spans" img="hide-spans"
                        model="#{table_demo3.extensions.axisStyle.hideSpans}"/>
      <wcf:scriptbutton id="nonEmpty03" tooltip="toolb.non.empty" img="non-empty"
                        model="#{table_demo3.extensions.nonEmpty.buttonPressed}"/>
      <wcf:scriptbutton id="swapAxes03" tooltip="toolb.swap.axes" img="swap-axes"
                        model="#{table_demo3.extensions.swapAxes.buttonPressed}"/>
      <wcf:separator/>
      <wcf:scriptbutton model="#{table_demo3.extensions.drillMember.enabled}"
                        tooltip="toolb.navi.member" radioGroup="navi"
                        id="drillMember03" img="navi-member"/>
      <wcf:scriptbutton model="#{table_demo3.extensions.drillPosition.enabled}"
                        tooltip="toolb.navi.position" radioGroup="navi"
                        id="drillPosition03" img="navi-position"/>
      <wcf:scriptbutton model="#{table_demo3.extensions.drillReplace.enabled}"
                        tooltip="toolb.navi.replace" radioGroup="navi"
                        id="drillReplace03" img="navi-replace"/>
      <wcf:scriptbutton model="#{table_demo3.extensions.drillThrough.enabled}"
                        tooltip="toolb.navi.drillthru" id="drillThrough03"
                        img="navi-through"/>
      <wcf:separator/>
      <wcf:scriptbutton id="chartButton03" tooltip="toolb.chart" img="chart"
                        model="#{chart03.visible}"/>
      <wcf:scriptbutton id="chartPropertiesButton03" tooltip="toolb.chart.config"
                        img="chart-config" model="#{chartform03.visible}"/>
    </wcf:toolbar>

    <wcf:render ref="toolbar03"
                xslUri="/WEB-INF/jpivot/toolbar/htoolbar.xsl" xslCache="true"/>

    <div class="info-box">
      <strong>💡 Info:</strong> <%= infoMessage %>
    </div>

    <wcf:render ref="navi_demo3"
                xslUri="/WEB-INF/jpivot/navi/navigator.xsl" xslCache="true"/>

    <c:if test="${mdxedit03.visible}">
      <h3 style="color: #495057; margin-top: 20px;">MDX Query Editor</h3>
      <wcf:render ref="mdxedit03"
                  xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
    </c:if>

    <wcf:render ref="sortform03"  xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
    <wcf:render ref="chartform03" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
    <wcf:render ref="printform03" xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>

    <div style="margin: 20px 0;">
      <wcf:render ref="table_demo3"
                  xslUri="/WEB-INF/jpivot/table/mdxtable.xsl" xslCache="true"/>
    </div>

    <div style="margin: 15px 0;">
      <strong>Slicer:</strong>
      <wcf:render ref="table_demo3"
                  xslUri="/WEB-INF/jpivot/table/mdxslicer.xsl" xslCache="true"/>
    </div>

    <wcf:render ref="querydemo3.drillthroughtable"
                xslUri="/WEB-INF/wcf/wcf.xsl" xslCache="true"/>
    <wcf:render ref="chart03"
                xslUri="/WEB-INF/jpivot/chart/chart.xsl" xslCache="true"/>
<%
  }
%>



</div>
<div style="margin:30px 0;">
  <a href="index.html" class="back-link">⬅ Kembali ke Menu Utama</a>
</div>
</form>

<!-- Di akhir file, sebelum </body> -->
<script type="text/javascript">
  // Initialize WCF after page load
  if (typeof WCF !== 'undefined' && WCF.init) {
    WCF.init();
  }
  
  // Debug: Log toolbar buttons
  console.log('WCF initialized');
  
  // Make sure form submission works
  var form = document.getElementById('testpageForm');
  if (form) {
    console.log('Form found:', form);
  }
</script>
</body>
</html>