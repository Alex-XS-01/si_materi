<?php
 $host = "db.xfqtyqwfdmqkgevdssvv.supabase.co";
 $user = "postgres";
 $pass = "8ON9yJvlNF04paCL";
 $db1 = "si_materi_kuliah";
 $db2 = "biodata_akademik";

 $conn = new mysqli($host, $user, $pass, $db1);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>
