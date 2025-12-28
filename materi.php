<?php
header('Content-Type: application/json');
require 'koneksi.php';

 $method = $_SERVER['REQUEST_METHOD'];
 $action = $_GET['action'] ?? '';

// AMBIL MATERI
if ($method === 'GET' && $action === 'get') {
    $prodi = $_GET['prodi'] ?? 'all';
    $semester = $_GET['semester'] ?? 'all';
    $bab = $_GET['bab'] ?? 'all';
    $sub = $_GET['sub'] ?? '';

    $sql = "SELECT * FROM materials WHERE 1=1";
    if ($prodi != 'all') $sql .= " AND prodi = '$prodi'";
    if ($semester != 'all') $sql .= " AND semester = '$semester'";
    if ($bab != 'all') $sql .= " AND bab = '$bab'";
    if ($sub != '') $sql .= " AND subbab LIKE '%$sub%'";

    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

// TAMBAH MATERI
elseif ($method === 'POST' && $action === 'add') {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "INSERT INTO materials (prodi, semester, bab, subbab, file_url, author_name) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissss", $data['prodi'], $data['semester'], $data['bab'], $data['subbab'], $data['file'], $data['author']);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

// HAPUS MATERI
elseif ($method === 'POST' && $action === 'delete') {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "DELETE FROM materials WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
}

// EDIT MATERI
elseif ($method === 'POST' && $action === 'edit') {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "UPDATE materials SET prodi=?, semester=?, bab=?, subbab=?, file_url=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssi", $data['prodi'], $data['semester'], $data['bab'], $data['subbab'], $data['file'], $data['id']);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
}
?>