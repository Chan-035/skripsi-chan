<?php
require_once '../../includes/config.php';

if (isset($_GET['supplierID'])) {
    $supplierID = $_GET['supplierID'];

    // Query untuk mengambil barang berdasarkan supplier
    $query = "
        SELECT st.idBarang, st.namaBarang, k.namaKategori, j.namaJenis
        FROM ListSupplier ls
        LEFT JOIN Stok st ON ls.idBarang = st.idBarang
        LEFT JOIN Kategori k ON st.kategoriID = k.kategoriID
        LEFT JOIN Jenis j ON st.jenisID = j.jenisID
        WHERE ls.supplierID = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $supplierID);
    $stmt->execute();
    $result = $stmt->get_result();

    $barang = [];
    while ($row = $result->fetch_assoc()) {
        $barang[] = $row;
    }

    echo json_encode($barang);
}
?>
