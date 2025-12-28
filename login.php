<?php
header('Content-Type: application/json');
require 'koneksi.php';

 $data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Data login tidak lengkap"]);
    exit;
}

 $username = $data['username'];
 $password = $data['password'];

// 1. Gunakan Prepared Statement (KEAMANAN)
 $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ss", $username, $password);
 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // 2. Pindah ke Database 2 (biodata_akademik)
    if (!$conn->select_db('biodata_akademik')) {
        echo json_encode(["status" => "error", "message" => "Gagal mengakses database biodata"]);
        exit;
    }

    $sql2 = "SELECT * FROM profile WHERE user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $user['id']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    // 3. Cek Profil & Berikan Default jika kosong
    if ($res2 && $res2->num_rows > 0) {
        $profile = $res2->fetch_assoc();
    } else {
        // Data Default
        $profile = [
            'avatar_url' => 'https://picsum.photos/seed/user/200/200.jpg',
            'nama_institusi' => 'Universitas Teknologi Digital',
            'sesi_belajar' => 'Ganjil 2023/2024',
            'pembimbing_akademik' => '-'
        ];
        
        // Insert otomatis agar next login aman
        $sqlInsert = "INSERT INTO profile (user_id, avatar_url, nama_institusi, sesi_belajar, pembimbing_akademik) VALUES (?, ?, ?, ?, ?)";
        $stmtIns = $conn->prepare($sqlInsert);
        $stmtIns->bind_param("issss", $user['id'], $profile['avatar_url'], $profile['nama_institusi'], $profile['sesi_belajar'], $profile['pembimbing_akademik']);
        $stmtIns->execute();
    }

    // 4. Gabungkan Data
    $user['avatar'] = $profile['avatar_url'];
    $user['institusi'] = $profile['nama_institusi'];
    $user['sesi'] = $profile['sesi_belajar'];
    $user['pembimbing'] = $profile['pembimbing_akademik'];

    echo json_encode(["status" => "success", "data" => $user]);

} else {
    echo json_encode(["status" => "error", "message" => "Username atau Password salah"]);
}
?>