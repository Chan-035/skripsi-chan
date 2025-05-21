-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 03:12 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skripsi`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `userID` varchar(4) NOT NULL,
  `namaDepan` varchar(10) NOT NULL,
  `namaBelakang` varchar(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `noHP` int(15) NOT NULL,
  `email` varchar(20) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`userID`, `namaDepan`, `namaBelakang`, `username`, `password`, `noHP`, `email`, `alamat`) VALUES
('U001', 'reanaa', 'schwarzer', 'cus', '123', 1423141312, 'rean@gmail.com', 'zemuria'),
('U002', 'chan22', 'adsa', 'chan2222aw', 'qwe', 1231432, 'ahndiw@gmail.com', 'jwijadmwkoqeq'),
('U003', 'aaaaaaaaa', 'aaaads', 'chan2222a', 'ads', 1234323, 'ajmdwakwo@gmail.com', 'kwoadkowmqoe'),
('U004', 'Renn', 'brights', 'RenneBrigh', 'renne#1!', 9124398, 'renne@gmail.co.id', 'jalan jalan jalan jalan jalan'),
('U005', 'chandrad', 'damar laha', 'cdl123', 'renne#1!', 821847123, 'lahandi58@gmail.com', ',kaofkow qa qdwqd'),
('U006', '132', '213412', 'chandradamar', '123', 867471, 'lahandi2u231@gmail.c', 'oqo ewqeq'),
('U007', 'Chqandra', 'Damar Laha', 'cdl22', 'renne#1!', 2147483647, 'lahandi99@gmail.com', 'makaliwe raya no 2913892 mwkqjmieq'),
('U008', 'chandra', 'lahandi da', 'chandra1', 'chandra1', 2147483647, 'lahandi38@gmail.com', 'jalan makaliwe'),
('U009', 'chandra', '0035', 'chan35', 'chan35', 825210035, 'chandra@gmail.com', 'ini chandra'),
('U010', 'chandra', '0035', 'chandra035', 'chandra035', 8621365, 'chandra035@gmail.com', 'chandra 825210035'),
('U011', 'damar', 'lahandi', 'damar35', 'damar35', 48192481, 'chanr@gmail.com', 'ini alamat testing');

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `jenisID` varchar(4) NOT NULL,
  `namaJenis` varchar(10) NOT NULL,
  `kategoriID` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis`
--

INSERT INTO `jenis` (`jenisID`, `namaJenis`, `kategoriID`) VALUES
('J001', 'Utama', 'K002'),
('J002', 'Pelengkap', 'K002'),
('J003', 'WIP', 'K002');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `kategoriID` varchar(4) NOT NULL,
  `namaKategori` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`kategoriID`, `namaKategori`) VALUES
('K001', 'Alat'),
('K002', 'Bahan');

-- --------------------------------------------------------

--
-- Table structure for table `listsupplier`
--

CREATE TABLE `listsupplier` (
  `supplierID` varchar(4) NOT NULL,
  `idBarang` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `listsupplier`
--

INSERT INTO `listsupplier` (`supplierID`, `idBarang`) VALUES
('S001', 'B001'),
('S001', 'B002'),
('S011', 'B002'),
('S011', 'B003'),
('S011', 'B004'),
('S011', 'B005'),
('S011', 'B009'),
('S011', 'B010'),
('S011', 'B011'),
('S012', 'B003'),
('S013', 'B001'),
('S013', 'B002'),
('S013', 'B003'),
('S013', 'B004'),
('S013', 'B005'),
('S014', 'B001'),
('S014', 'B003'),
('S014', 'B005'),
('S014', 'B023'),
('S014', 'B025'),
('S014', 'B027'),
('S014', 'B034'),
('S014', 'B036'),
('S015', 'B017'),
('S015', 'B019'),
('S015', 'B021');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menuID` varchar(4) NOT NULL,
  `namaMenu` varchar(35) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` int(10) NOT NULL,
  `gambar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menuID`, `namaMenu`, `deskripsi`, `harga`, `gambar`) VALUES
('M001', 'Nasi Ayam Goreng Mentega', 'Paket Nasi - Ayam Goreng Mentega - Cap Cay - Kerupuk', 20000, 'menu2.jpg'),
('M002', 'Nasi Ayam Teriyaki', 'Paket Nasi - Ayam Teriyaki - Salad (kol,wortel) - Kerupuk', 22000, 'menu3.png'),
('M003', 'Nasi Semur Daging', 'Nasi Semur Daging dengan cah Buncis', 18000, 'menu4.png'),
('M004', 'Nasi Ikan Pesmol', 'Paket Nasi - Ikan Pesmol (Kembung) - Acar Kuning - Kerupuk', 28000, 'menu5.jpg'),
('M005', 'Nasi Ayam wijen', 'Paket Nasi - Ayam wijen - Sapo Tahu - Bakwan Goreng', 23000, 'menu6.jpg\n');

-- --------------------------------------------------------

--
-- Table structure for table `orderpesanan`
--

CREATE TABLE `orderpesanan` (
  `orderID` varchar(6) NOT NULL,
  `pesananID` varchar(4) NOT NULL,
  `tanggalPesanan` date NOT NULL,
  `userID` varchar(4) NOT NULL,
  `tanggalPengambilan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orderpesanan`
--

INSERT INTO `orderpesanan` (`orderID`, `pesananID`, `tanggalPesanan`, `userID`, `tanggalPengambilan`) VALUES
('OP0012', 'O024', '2024-11-25', 'U003', '2024-11-25'),
('OP0013', 'O025', '2024-11-25', 'U001', '0000-00-00'),
('OP0013', 'O026', '2024-11-25', 'U001', '0000-00-00'),
('OP0014', 'O027', '2024-11-25', 'U001', '0000-00-00'),
('OP0014', 'O028', '2024-11-25', 'U001', '0000-00-00'),
('OP0015', 'O029', '2024-11-25', 'U001', '0000-00-00'),
('OP0015', 'O030', '2024-11-25', 'U001', '0000-00-00'),
('OP0016', 'O031', '2024-11-25', 'U001', '2024-11-28'),
('OP0016', 'O032', '2024-11-25', 'U001', '2024-11-28'),
('OP0017', 'O033', '2024-11-25', 'U001', '2024-12-01'),
('OP0018', 'O034', '2024-11-25', 'U001', '2024-11-29'),
('OP0019', 'O035', '2024-11-25', 'U002', '2024-11-29'),
('OP0019', 'O036', '2024-11-25', 'U002', '2024-11-29'),
('OP0020', 'O037', '2024-11-25', 'U002', '2024-11-29'),
('OP0020', 'O038', '2024-11-25', 'U002', '2024-11-29'),
('OP0021', 'O039', '2024-11-25', 'U002', '2024-11-29'),
('OP0021', 'O040', '2024-11-25', 'U002', '2024-11-29'),
('OP0022', 'O041', '2024-11-25', 'U002', '2024-11-29'),
('OP0022', 'O042', '2024-11-25', 'U002', '2024-11-29'),
('OP0023', 'O043', '2024-11-25', 'U001', '2024-11-30'),
('OP0024', 'O044', '2024-11-25', 'U002', '2024-11-30'),
('OP0025', 'O045', '2024-11-25', 'U001', '2024-11-29'),
('OP0026', 'O046', '2024-11-25', 'U001', '2024-11-29'),
('OP0026', 'O047', '2024-11-25', 'U001', '2024-11-29'),
('OP0027', 'O048', '2024-11-25', 'U001', '2024-12-01'),
('OP0028', 'O049', '2024-11-25', 'U001', '2024-11-29'),
('OP0029', 'O050', '2024-11-25', 'U001', '2024-11-30'),
('OP0030', 'O051', '2024-11-25', 'U001', '2024-11-30'),
('OP0031', 'O052', '2024-11-25', 'U001', '2024-11-30'),
('OP0031', 'O053', '2024-11-25', 'U001', '2024-11-30'),
('OP0032', 'O054', '2024-11-26', 'U003', '2024-12-06'),
('OP0032', 'O055', '2024-11-26', 'U003', '2024-12-06'),
('OP0033', 'O056', '2024-11-26', 'U003', '2024-12-05'),
('OP0034', 'O057', '2024-11-26', 'U003', '2024-12-04'),
('OP0035', 'O058', '2024-11-26', 'U003', '2024-12-03'),
('OP0036', 'O059', '2024-11-26', 'U003', '2024-12-07'),
('OP0037', 'O060', '2024-11-26', 'U003', '2024-12-07'),
('OP0038', 'O061', '2024-11-26', 'U003', '2024-12-05'),
('OP0039', 'O062', '2024-11-26', 'U003', '2024-12-20'),
('OP0040', 'O063', '2024-11-26', 'U003', '2024-11-30'),
('OP0041', 'O064', '2024-11-26', 'U001', '2024-12-19'),
('OP0041', 'O065', '2024-11-26', 'U001', '2024-12-19'),
('OP0042', 'O066', '2024-11-26', 'U001', '2024-11-30'),
('OP0043', 'O067', '2024-11-26', 'U007', '2024-11-30'),
('OP0044', 'O068', '2024-11-26', 'U007', '2024-11-30'),
('OP0045', 'O069', '2024-11-26', 'U007', '2024-11-30'),
('OP0046', 'O070', '2024-11-26', 'U008', '2024-11-30'),
('OP0046', 'O071', '2024-11-26', 'U008', '2024-11-30'),
('OP0047', 'O072', '2024-11-26', 'U009', '2024-12-01'),
('OP0047', 'O073', '2024-11-26', 'U009', '2024-12-01'),
('OP0048', 'O074', '2024-11-26', 'U010', '2024-11-30'),
('OP0048', 'O075', '2024-11-26', 'U010', '2024-11-30'),
('OP0049', 'O076', '2024-11-26', 'U011', '2024-11-29'),
('OP0049', 'O077', '2024-11-26', 'U011', '2024-11-29'),
('OP0050', 'O078', '2024-12-02', 'U004', '2024-12-07');

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `ownerID` int(5) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`ownerID`, `username`, `password`) VALUES
(1, 'chan', '123');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `pembayaranID` varchar(5) NOT NULL,
  `pesananID` varchar(4) NOT NULL,
  `metode` varchar(6) NOT NULL,
  `tanggalPembayaran` date NOT NULL,
  `jumlahPembayaran` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`pembayaranID`, `pesananID`, `metode`, `tanggalPembayaran`, `jumlahPembayaran`) VALUES
('Y001', 'O001', 'OVO', '2024-10-01', 200000);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `beliID` varchar(5) NOT NULL,
  `supplierID` varchar(4) NOT NULL,
  `idBarang` varchar(4) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int(3) NOT NULL,
  `harga` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`beliID`, `supplierID`, `idBarang`, `tanggal`, `jumlah`, `harga`) VALUES
('BE001', 'S001', 'B001', '2024-11-06', 2421, 213),
('BE002', 'S001', 'B001', '2024-11-11', 240, 100),
('BE003', 'S001', 'B002', '2024-11-17', 30, 300),
('BE004', 'S011', 'B009', '2024-11-26', 150, 500),
('BE005', 'S013', 'B001', '2024-11-26', 1500, 500),
('BE006', 'S014', 'B003', '2024-11-25', 500, 500),
('BE007', 'S013', 'B004', '2024-11-25', 1500, 500);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `pesananID` varchar(4) NOT NULL,
  `userID` varchar(4) NOT NULL,
  `menuID` varchar(4) NOT NULL,
  `tanggalPesanan` date NOT NULL,
  `kuantitas` int(3) NOT NULL,
  `totalHarga` int(15) NOT NULL,
  `statID` varchar(4) NOT NULL,
  `stokDikurangi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`pesananID`, `userID`, `menuID`, `tanggalPesanan`, `kuantitas`, `totalHarga`, `statID`, `stokDikurangi`) VALUES
('O000', 'U001', 'M004', '2024-11-20', 1, 28000, 'S001', 1),
('O001', 'U002', 'M003', '2024-10-30', 26, 18000, 'S001', 1),
('O002', 'U002', 'M005', '2024-11-16', 35, 805000, 'S001', 1),
('O003', 'U002', 'M001', '2024-11-17', 25, 1000000, 'S001', 1),
('O004', 'U002', 'M003', '2024-10-30', 26, 468000, 'S001', 1),
('O005', 'U002', 'M004', '2024-10-30', 30, 840000, 'S001', 1),
('O006', 'U002', 'M002', '2024-10-30', 26, 572000, 'S001', 1),
('O008', 'U002', 'M003', '2024-10-30', 26, 18000, 'S001', 1),
('O009', 'U001', 'M004', '2024-10-24', 28, 1568000, 'S001', 1),
('O010', 'U001', 'M002', '2024-11-19', 122, 2684000, 'S001', 1),
('O011', 'U001', 'M002', '2024-11-19', 121, 2662000, 'S001', 1),
('O013', 'U001', 'M002', '2024-11-19', 12, 264000, 'S001', 1),
('O014', 'U006', 'M002', '2024-11-19', 12, 264000, 'S001', 1),
('O015', 'U001', 'M005', '2024-11-19', 12, 276000, 'S001', 1),
('O016', 'U001', 'M003', '2024-11-20', 12, 216000, 'S001', 1),
('O017', 'U001', 'M001', '2024-11-20', 36, 720000, 'S001', 1),
('O022', 'U003', 'M001', '2024-11-25', 25, 500000, 'S001', 1),
('O023', 'U003', 'M001', '2024-11-25', 25, 500000, 'S004', 0),
('O024', 'U003', 'M001', '2024-11-25', 25, 500000, 'S001', 1),
('O025', 'U001', 'M002', '2024-11-25', 35, 770000, 'S001', 1),
('O026', 'U001', 'M003', '2024-11-25', 25, 450000, 'S001', 1),
('O027', 'U001', 'M002', '2024-11-25', 35, 770000, 'S001', 1),
('O028', 'U001', 'M003', '2024-11-25', 25, 450000, 'S001', 1),
('O029', 'U001', 'M002', '2024-11-25', 35, 770000, 'S001', 1),
('O030', 'U001', 'M003', '2024-11-25', 25, 450000, 'S001', 1),
('O031', 'U001', 'M002', '2024-11-25', 35, 770000, 'S004', 0),
('O032', 'U001', 'M003', '2024-11-25', 25, 450000, 'S001', 1),
('O033', 'U001', 'M002', '2024-11-25', 25, 550000, 'S001', 1),
('O034', 'U001', 'M003', '2024-11-25', 15, 270000, 'S004', 0),
('O035', 'U002', 'M005', '2024-11-25', 23, 529000, 'S004', 0),
('O036', 'U002', 'M004', '2024-11-25', 22, 616000, 'S004', 0),
('O037', 'U002', 'M005', '2024-11-25', 23, 529000, 'S004', 0),
('O038', 'U002', 'M004', '2024-11-25', 22, 616000, 'S004', 0),
('O039', 'U002', 'M005', '2024-11-25', 23, 529000, 'S004', 0),
('O040', 'U002', 'M004', '2024-11-25', 22, 616000, 'S004', 0),
('O041', 'U002', 'M005', '2024-11-25', 23, 529000, 'S002', 0),
('O042', 'U002', 'M004', '2024-11-25', 22, 616000, 'S002', 0),
('O043', 'U001', 'M002', '2024-11-25', 35, 770000, 'S002', 0),
('O044', 'U002', 'M003', '2024-11-25', 25, 450000, 'S002', 0),
('O045', 'U001', 'M001', '2024-11-25', 25, 500000, 'S004', 0),
('O046', 'U001', 'M001', '2024-11-25', 50, 1000000, 'S002', 0),
('O047', 'U001', 'M002', '2024-11-25', 25, 550000, 'S001', 0),
('O048', 'U001', 'M005', '2024-11-25', 70, 1610000, 'S001', 0),
('O049', 'U001', 'M005', '2024-11-25', 70, 1610000, 'S004', 0),
('O050', 'U001', 'M005', '2024-11-25', 70, 1610000, 'S004', 0),
('O051', 'U001', 'M001', '2024-11-25', 16, 320000, 'S004', 0),
('O052', 'U001', 'M003', '2024-11-25', 18, 324000, 'S002', 0),
('O053', 'U001', 'M001', '2024-11-25', 19, 380000, 'S001', 1),
('O054', 'U003', 'M001', '2024-11-26', 35, 700000, 'S001', 1),
('O055', 'U003', 'M002', '2024-11-26', 36, 792000, 'S001', 1),
('O056', 'U003', 'M001', '2024-11-26', 25, 500000, 'S001', 1),
('O057', 'U003', 'M004', '2024-11-26', 27, 756000, 'S001', 1),
('O058', 'U003', 'M004', '2024-11-26', 52, 1456000, 'S001', 1),
('O059', 'U003', 'M002', '2024-11-26', 25, 550000, 'S001', 1),
('O060', 'U003', 'M002', '2024-11-26', 25, 550000, 'S001', 1),
('O061', 'U003', 'M002', '2024-11-26', 25, 550000, 'S001', 1),
('O062', 'U003', 'M002', '2024-11-26', 25, 550000, 'S001', 1),
('O063', 'U003', 'M002', '2024-11-26', 25, 550000, 'S001', 0),
('O064', 'U001', 'M005', '2024-11-26', 27, 621000, 'S001', 0),
('O065', 'U001', 'M001', '2024-11-26', 20, 400000, 'S001', 1),
('O066', 'U001', 'M003', '2024-11-26', 12, 216000, 'S001', 1),
('O067', 'U007', 'M002', '2024-11-26', 27, 594000, 'S004', 0),
('O068', 'U007', 'M002', '2024-11-26', 27, 594000, 'S004', 0),
('O069', 'U007', 'M002', '2024-11-26', 17, 374000, 'S001', 1),
('O070', 'U008', 'M001', '2024-11-26', 15, 300000, 'S001', 0),
('O071', 'U008', 'M005', '2024-11-26', 25, 575000, 'S001', 1),
('O072', 'U009', 'M002', '2024-11-26', 25, 550000, 'S001', 0),
('O073', 'U009', 'M005', '2024-11-26', 35, 805000, 'S001', 1),
('O074', 'U010', 'M001', '2024-11-26', 25, 500000, 'S001', 0),
('O075', 'U010', 'M005', '2024-11-26', 15, 345000, 'S001', 1),
('O076', 'U011', 'M002', '2024-11-26', 25, 550000, 'S001', 0),
('O077', 'U011', 'M005', '2024-11-26', 35, 805000, 'S001', 0),
('O078', 'U004', 'M001', '2024-12-02', 15, 300000, 'S001', 1);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `resepID` varchar(4) NOT NULL,
  `idBarang` varchar(4) NOT NULL,
  `kuantitas` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`resepID`, `idBarang`, `kuantitas`) VALUES
('R001', 'B012', 1.50),
('R001', 'B017', 1.00),
('R001', 'B015', 0.25),
('R001', 'B019', 0.30),
('R001', 'B023', 0.15),
('R001', 'B007', 1.00),
('R001', 'B001', 1.00),
('R001', 'B002', 1.00),
('R001', 'B003', 1.00),
('R001', 'B004', 1.00),
('R001', 'B005', 1.00),
('R001', 'B006', 1.00),
('R001', 'B008', 1.00),
('R002', 'B012', 1.50),
('R002', 'B008', 0.50),
('R002', 'B014', 0.20),
('R002', 'B019', 0.20),
('R002', 'B023', 0.15),
('R002', 'B007', 1.00),
('R002', 'B001', 1.00),
('R002', 'B002', 1.00),
('R002', 'B003', 1.00),
('R002', 'B004', 1.00),
('R002', 'B005', 1.00),
('R002', 'B006', 1.00),
('R002', 'B008', 1.00),
('R003', 'B012', 1.50),
('R003', 'B022', 0.75),
('R003', 'B019', 0.25),
('R003', 'B023', 0.15),
('R003', 'B007', 1.00),
('R003', 'B001', 1.00),
('R003', 'B002', 1.00),
('R003', 'B003', 1.00),
('R003', 'B004', 1.00),
('R003', 'B005', 1.00),
('R003', 'B006', 1.00),
('R003', 'B008', 1.00),
('R004', 'B012', 1.50),
('R004', 'B029', 1.50),
('R004', 'B023', 0.15),
('R004', 'B007', 1.00),
('R004', 'B001', 1.00),
('R004', 'B002', 1.00),
('R004', 'B003', 1.00),
('R004', 'B004', 1.00),
('R004', 'B005', 1.00),
('R004', 'B006', 1.00),
('R005', 'B012', 1.50),
('R005', 'B008', 0.50),
('R005', 'B031', 0.05),
('R005', 'B032', 0.25),
('R005', 'B036', 0.20),
('R005', 'B035', 0.15),
('R005', 'B007', 1.00),
('R005', 'B001', 1.00),
('R005', 'B002', 1.00),
('R005', 'B003', 1.00),
('R005', 'B004', 1.00),
('R005', 'B005', 1.00),
('R005', 'B006', 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `resep`
--

CREATE TABLE `resep` (
  `resepID` varchar(4) NOT NULL,
  `menuID` varchar(4) NOT NULL,
  `resepNama` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `resep`
--

INSERT INTO `resep` (`resepID`, `menuID`, `resepNama`) VALUES
('R001', 'M001', 'Resep Nasi Ayam Goreng Mentega'),
('R002', 'M002', 'Resep Nasi Ayam Teriyaki'),
('R003', 'M003', 'Resep Nasi Semur Daging'),
('R004', 'M004', 'Resep Nasi Ikan Pesmol'),
('R005', 'M005', 'Resep Ayam Wijen');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(5) NOT NULL,
  `namaDepan` varchar(10) NOT NULL,
  `namaBelakang` varchar(10) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL,
  `alamat` varchar(25) NOT NULL,
  `noHP` int(15) NOT NULL,
  `email` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `namaDepan`, `namaBelakang`, `username`, `password`, `alamat`, `noHP`, `email`) VALUES
(1, 'renne', 'bright', 'chandra', '123', 'calvard', 231313141, 'renne@gmail.com'),
(2, 'renne', 'hayworth', 'chan321', '12321', 'calvard32', 2147483647, 'renneH@gmail.com'),
(3, 'chandra', 'damar laha', 'staff', '1234', 'akwodqmowjqd mwqoio', 2147483647, 'lahwqodwqem@gmail.com'),
(7, 'staff', 'testing', 'staff3', 'staff', 'ini alamat testing', 182938, 'testing@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `statID` varchar(4) NOT NULL,
  `statName` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`statID`, `statName`) VALUES
('S001', 'Done'),
('S002', 'In-Progress'),
('S003', 'Canceled'),
('S004', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `stok`
--

CREATE TABLE `stok` (
  `idBarang` varchar(4) NOT NULL,
  `namaBarang` varchar(30) NOT NULL,
  `kategoriID` varchar(4) NOT NULL,
  `jenisID` varchar(4) NOT NULL,
  `jumlahBarang` int(4) NOT NULL,
  `leadtime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stok`
--

INSERT INTO `stok` (`idBarang`, `namaBarang`, `kategoriID`, `jenisID`, `jumlahBarang`, `leadtime`) VALUES
('B001', 'Plastik Merah 40', 'K001', 'J001', 1240, 3),
('B002', 'Plastik Merah 50', 'K001', 'J001', 1069, 3),
('B003', 'Plastik 1/2 kg', 'K001', 'J001', 1, 5),
('B004', 'Plastik 1kg', 'K001', 'J001', 2649, 5),
('B005', 'Plastik 10*12', 'K001', 'J001', 389, 2),
('B006', 'Plastik 6*20', 'K001', 'J001', 389, 4),
('B007', 'Sendok (50)', 'K001', 'J001', 389, 7),
('B008', 'Tissue (50)', 'K001', 'J001', 446, 6),
('B009', 'Plastik Klip 8*5', 'K001', 'J001', 300, 5),
('B010', 'Plastik Klip 10*6', 'K001', 'J001', 150, 5),
('B011', 'Plastik Klip 10*7', 'K001', 'J001', 175, 5),
('B012', 'Beras', 'K002', 'J001', -18, 3),
('B013', 'Gula', 'K002', 'J001', 30, 3),
('B014', 'Garam', 'K002', 'J001', 10, 2),
('B015', 'Minyak', 'K002', 'J001', 984, 5),
('B016', 'Royco Ayam', 'K002', 'J002', 15, 7),
('B017', 'Royco Sapi', 'K002', 'J002', 929, 7),
('B018', 'Saus Tomat', 'K002', 'J002', 5, 4),
('B019', 'Kecap Manis', 'K002', 'J002', 926, 3),
('B020', 'Box Nasi(50)', 'K001', 'J001', 220, 3),
('B021', 'Sterofoam(25)', 'K001', 'J001', 210, 3),
('B022', 'Tinwall(20)', 'K001', 'J001', 6, 7),
('B023', 'Kerupuk Bawang', 'K002', 'J003', -1, 3),
('B024', 'Kerupuk Udang', 'K002', 'J003', 4, 3),
('B025', 'Gula Jawa(kg)', 'K002', 'J001', 10, 5),
('B026', 'Asam Jawa(kg)', 'K002', 'J001', 8, 5),
('B027', 'Lada (250g)', 'K002', 'J002', 5, 7),
('B028', 'Saus Tiram (270ml)', 'K002', 'J002', 5, 6),
('B029', 'Kecap Inggris(150ml)', 'K002', 'J002', 25, 4),
('B030', 'Minyak Wijen', 'K002', 'J001', 4, 4),
('B031', 'Wijen(250g)', 'K002', 'J001', 4, 7),
('B032', 'Tepung Terigu(1kg)', 'K002', 'J001', 40, 3),
('B033', 'Tepung Beras(1kg)', 'K002', 'J001', 5, 3),
('B034', 'Tepung Bumbu(1kg)', 'K002', 'J003', 5, 3),
('B035', 'Emping(200g)', 'K002', 'J002', 10, 4),
('B036', 'Saus Sambal', 'K002', 'J002', 25, 3),
('B037', 'Sambal Olahan', 'K002', 'J002', 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplierID` varchar(4) NOT NULL,
  `namaKontak` varchar(15) NOT NULL,
  `kontak` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplierID`, `namaKontak`, `kontak`) VALUES
('S001', 'Eka', '081287982959'),
('S002', 'Lenny', '02155771908'),
('S003', 'Aphin', '0215600121'),
('S004', 'Aliong', '081382461385'),
('S005', 'Ahmad Fauzi', '085719196543'),
('S006', 'Ahmad Tohsini', '081517132350'),
('S007', 'M. Nurohman', '0818786017'),
('S008', 'Parto', '083870024341'),
('S009', 'Yudhi', '081318654225'),
('S010', 'Alung', '085782272610'),
('S011', 'chnadra', '0218492183'),
('S012', 'chandwa', '021439813'),
('S013', 'chan35', '08221314135'),
('S014', 'chandra0035', '0812317481'),
('S015', 'damar', '0812481943');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`jenisID`),
  ADD KEY `kategoriID` (`kategoriID`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`kategoriID`);

--
-- Indexes for table `listsupplier`
--
ALTER TABLE `listsupplier`
  ADD KEY `supplierID` (`supplierID`,`idBarang`),
  ADD KEY `idBarang` (`idBarang`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menuID`);

--
-- Indexes for table `orderpesanan`
--
ALTER TABLE `orderpesanan`
  ADD KEY `pesananID` (`pesananID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`ownerID`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`pembayaranID`),
  ADD KEY `pesananID` (`pesananID`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`beliID`),
  ADD KEY `supplierID` (`supplierID`),
  ADD KEY `idBarang` (`idBarang`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`pesananID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `menuID` (`menuID`),
  ADD KEY `statID` (`statID`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD KEY `resepID` (`resepID`),
  ADD KEY `idBarang` (`idBarang`);

--
-- Indexes for table `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`resepID`),
  ADD KEY `menuID` (`menuID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`statID`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`idBarang`) USING BTREE,
  ADD KEY `kategoriID` (`kategoriID`),
  ADD KEY `jenisID` (`jenisID`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplierID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jenis`
--
ALTER TABLE `jenis`
  ADD CONSTRAINT `jenis_ibfk_1` FOREIGN KEY (`kategoriID`) REFERENCES `kategori` (`kategoriID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listsupplier`
--
ALTER TABLE `listsupplier`
  ADD CONSTRAINT `listsupplier_ibfk_1` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listsupplier_ibfk_2` FOREIGN KEY (`idBarang`) REFERENCES `stok` (`idBarang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderpesanan`
--
ALTER TABLE `orderpesanan`
  ADD CONSTRAINT `orderpesanan_ibfk_1` FOREIGN KEY (`pesananID`) REFERENCES `pesanan` (`pesananID`),
  ADD CONSTRAINT `orderpesanan_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `customer` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesananID`) REFERENCES `pesanan` (`pesananID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`idBarang`) REFERENCES `stok` (`idBarang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `customer` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`menuID`) REFERENCES `menu` (`menuID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`statID`) REFERENCES `status` (`statID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`resepID`) REFERENCES `resep` (`resepID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_ingredients_ibfk_2` FOREIGN KEY (`idBarang`) REFERENCES `stok` (`idBarang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resep`
--
ALTER TABLE `resep`
  ADD CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`menuID`) REFERENCES `menu` (`menuID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`kategoriID`) REFERENCES `kategori` (`kategoriID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stok_ibfk_2` FOREIGN KEY (`jenisID`) REFERENCES `jenis` (`jenisID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
