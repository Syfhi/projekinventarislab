<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id <= 0){
    echo json_encode(['error'=>'invalid_id']);
    exit;
}

// check table exists
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'barang_unit'");
if(!$tableCheck || mysqli_num_rows($tableCheck) == 0){
    echo json_encode(['error'=>'no_table', 'message' => 'Tabel barang_unit tidak ditemukan']);
    exit;
}

$sql = sprintf("SELECT id_unit, kode_unit, kondisi, available FROM barang_unit WHERE id_barang='%d'", $id);
$res = mysqli_query($conn, $sql);
if(!$res){
    echo json_encode(['error'=>'sql_error', 'message' => mysqli_error($conn)]);
    exit;
}

$units = [];
while($r = mysqli_fetch_assoc($res)){
    $units[] = $r;
}

echo json_encode(['data' => $units]);

?>
