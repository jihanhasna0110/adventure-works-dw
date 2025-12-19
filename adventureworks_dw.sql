-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adventureworks_dw`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_creditcard`
--

CREATE TABLE `dim_creditcard` (
  `CreditCardKey` int(11) NOT NULL,
  `CreditCardID` int(11) NOT NULL,
  `CardType` varchar(50) DEFAULT NULL,
  `CardNumber` varchar(25) DEFAULT NULL,
  `ExpMonth` int(11) DEFAULT NULL,
  `ExpYear` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_customer`
--

CREATE TABLE `dim_customer` (
  `CustomerKey` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `PersonID` int(11) DEFAULT NULL,
  `StoreID` int(11) DEFAULT NULL,
  `CustomerType` varchar(20) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `TerritoryID` int(11) DEFAULT NULL,
  `AccountNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_product`
--

CREATE TABLE `dim_product` (
  `ProductKey` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(100) DEFAULT NULL,
  `ProductNumber` varchar(25) DEFAULT NULL,
  `Color` varchar(20) DEFAULT NULL,
  `StandardCost` decimal(10,2) DEFAULT NULL,
  `ListPrice` decimal(10,2) DEFAULT NULL,
  `Size` varchar(10) DEFAULT NULL,
  `Weight` decimal(10,2) DEFAULT NULL,
  `ProductSubcategoryID` int(11) DEFAULT NULL,
  `SubcategoryName` varchar(50) DEFAULT NULL,
  `ProductCategoryID` int(11) DEFAULT NULL,
  `CategoryName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_salesperson`
--

CREATE TABLE `dim_salesperson` (
  `SalespersonKey` int(11) NOT NULL,
  `BusinessEntityID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `JobTitle` varchar(50) DEFAULT NULL,
  `TerritoryID` int(11) DEFAULT NULL,
  `SalesQuota` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_territory`
--

CREATE TABLE `dim_territory` (
  `TerritoryKey` int(11) NOT NULL,
  `TerritoryID` int(11) NOT NULL,
  `TerritoryName` varchar(50) DEFAULT NULL,
  `CountryRegionCode` varchar(10) DEFAULT NULL,
  `TerritoryGroup` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dim_time`
--

CREATE TABLE `dim_time` (
  `TimeKey` int(11) NOT NULL,
  `FullDate` date NOT NULL,
  `DayOfWeek` int(11) DEFAULT NULL,
  `DayName` varchar(10) DEFAULT NULL,
  `DayOfMonth` int(11) DEFAULT NULL,
  `DayOfYear` int(11) DEFAULT NULL,
  `WeekOfYear` int(11) DEFAULT NULL,
  `Month` int(11) DEFAULT NULL,
  `MonthName` varchar(15) DEFAULT NULL,
  `Quarter` int(11) DEFAULT NULL,
  `QuarterName` varchar(5) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fact_sales`
--

CREATE TABLE `fact_sales` (
  `SalesKey` int(11) NOT NULL,
  `TimeKey` int(11) NOT NULL,
  `ProductKey` int(11) NOT NULL,
  `CustomerKey` int(11) NOT NULL,
  `TerritoryKey` int(11) DEFAULT NULL,
  `SalespersonKey` int(11) DEFAULT NULL,
  `CreditCardKey` int(11) DEFAULT NULL,
  `OrderQuantity` int(11) DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL,
  `UnitPriceDiscount` decimal(10,2) DEFAULT NULL,
  `LineTotal` decimal(10,2) DEFAULT NULL,
  `SubTotal` decimal(10,2) DEFAULT NULL,
  `TaxAmt` decimal(10,2) DEFAULT NULL,
  `Freight` decimal(10,2) DEFAULT NULL,
  `TotalDue` decimal(10,2) DEFAULT NULL,
  `OrderStatus` tinyint(4) DEFAULT NULL,
  `OnlineOrderFlag` tinyint(1) DEFAULT NULL,
  `SalesOrderID` int(11) DEFAULT NULL,
  `SalesOrderDetailID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `Role` varchar(20) DEFAULT 'executive',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dim_creditcard`
--
ALTER TABLE `dim_creditcard`
  ADD PRIMARY KEY (`CreditCardKey`),
  ADD KEY `idx_creditcard_id` (`CreditCardID`),
  ADD KEY `idx_cardtype` (`CardType`);

--
-- Indeks untuk tabel `dim_customer`
--
ALTER TABLE `dim_customer`
  ADD PRIMARY KEY (`CustomerKey`),
  ADD UNIQUE KEY `CustomerID` (`CustomerID`),
  ADD KEY `idx_customer_id` (`CustomerID`),
  ADD KEY `idx_customer_type` (`CustomerType`);

--
-- Indeks untuk tabel `dim_product`
--
ALTER TABLE `dim_product`
  ADD PRIMARY KEY (`ProductKey`),
  ADD UNIQUE KEY `ProductID` (`ProductID`),
  ADD KEY `idx_product_id` (`ProductID`),
  ADD KEY `idx_category` (`CategoryName`),
  ADD KEY `idx_subcategory` (`SubcategoryName`),
  ADD KEY `idx_color` (`Color`);

--
-- Indeks untuk tabel `dim_salesperson`
--
ALTER TABLE `dim_salesperson`
  ADD PRIMARY KEY (`SalespersonKey`),
  ADD UNIQUE KEY `BusinessEntityID` (`BusinessEntityID`) USING BTREE,
  ADD KEY `idx_BusinessEntityID_id` (`BusinessEntityID`) USING BTREE;

--
-- Indeks untuk tabel `dim_territory`
--
ALTER TABLE `dim_territory`
  ADD PRIMARY KEY (`TerritoryKey`),
  ADD UNIQUE KEY `TerritoryID` (`TerritoryID`),
  ADD KEY `idx_territory_id` (`TerritoryID`);

--
-- Indeks untuk tabel `dim_time`
--
ALTER TABLE `dim_time`
  ADD PRIMARY KEY (`TimeKey`),
  ADD UNIQUE KEY `FullDate` (`FullDate`),
  ADD KEY `idx_date` (`FullDate`),
  ADD KEY `idx_year_quarter` (`Year`,`Quarter`),
  ADD KEY `idx_year_month` (`Year`,`Month`);

--
-- Indeks untuk tabel `fact_sales`
--
ALTER TABLE `fact_sales`
  ADD PRIMARY KEY (`SalesKey`),
  ADD KEY `CreditCardKey` (`CreditCardKey`),
  ADD KEY `idx_time` (`TimeKey`),
  ADD KEY `idx_product` (`ProductKey`),
  ADD KEY `idx_customer` (`CustomerKey`),
  ADD KEY `idx_territory` (`TerritoryKey`),
  ADD KEY `idx_salesperson` (`SalespersonKey`),
  ADD KEY `idx_status` (`OrderStatus`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dim_creditcard`
--
ALTER TABLE `dim_creditcard`
  MODIFY `CreditCardKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dim_customer`
--
ALTER TABLE `dim_customer`
  MODIFY `CustomerKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dim_product`
--
ALTER TABLE `dim_product`
  MODIFY `ProductKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dim_salesperson`
--
ALTER TABLE `dim_salesperson`
  MODIFY `SalespersonKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dim_territory`
--
ALTER TABLE `dim_territory`
  MODIFY `TerritoryKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dim_time`
--
ALTER TABLE `dim_time`
  MODIFY `TimeKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fact_sales`
--
ALTER TABLE `fact_sales`
  MODIFY `SalesKey` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `fact_sales`
--
ALTER TABLE `fact_sales`
  ADD CONSTRAINT `fact_sales_ibfk_1` FOREIGN KEY (`TimeKey`) REFERENCES `dim_time` (`TimeKey`),
  ADD CONSTRAINT `fact_sales_ibfk_2` FOREIGN KEY (`ProductKey`) REFERENCES `dim_product` (`ProductKey`),
  ADD CONSTRAINT `fact_sales_ibfk_3` FOREIGN KEY (`CustomerKey`) REFERENCES `dim_customer` (`CustomerKey`),
  ADD CONSTRAINT `fact_sales_ibfk_4` FOREIGN KEY (`TerritoryKey`) REFERENCES `dim_territory` (`TerritoryKey`),
  ADD CONSTRAINT `fact_sales_ibfk_5` FOREIGN KEY (`SalespersonKey`) REFERENCES `dim_salesperson` (`SalespersonKey`),
  ADD CONSTRAINT `fact_sales_ibfk_6` FOREIGN KEY (`CreditCardKey`) REFERENCES `dim_creditcard` (`CreditCardKey`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
