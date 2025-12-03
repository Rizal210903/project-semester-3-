<?php
header('Content-Type: application/json');
include '../includes/config.php';

try {
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    
    // Ambil semua guru
    $query_guru = $conn->query("SELECT id, fullname FROM guru ORDER BY fullname ASC");
    $all_guru = [];
    while ($row = $query_guru->fetch_assoc()) {
        $all_guru[$row['id']] = $row['fullname'];
    }
    
    // Ambil data absensi hari ini
    $query_absen = $conn->query("
        SELECT ag.*, g.fullname 
        FROM absensi_guru ag
        JOIN guru g ON ag.guru_id = g.id
        WHERE ag.tanggal = '$tanggal'
        ORDER BY g.fullname ASC
    ");
    
    $hadir = [];
    $terlambat = [];
    $izin = [];
    $sakit = [];
    $alfa = [];
    $guru_sudah_absen = [];
    
    while ($row = $query_absen->fetch_assoc()) {
        $guru_sudah_absen[] = $row['guru_id'];
        
        $guru_data = [
            'id' => $row['guru_id'],
            'fullname' => $row['fullname'],
            'waktu_absen' => $row['waktu_absen'] ? substr($row['waktu_absen'], 0, 5) : null,
            'catatan' => $row['catatan']
        ];
        
        switch ($row['status']) {
            case 'Hadir':
                $hadir[] = $guru_data;
                break;
            case 'Terlambat':
                $terlambat[] = $guru_data;
                break;
            case 'Izin':
                $izin[] = $guru_data;
                break;
            case 'Sakit':
                $sakit[] = $guru_data;
                break;
            case 'Alfa':
                $alfa[] = $guru_data;
                break;
        }
    }
    
    // Guru yang belum absen
    $belum = [];
    foreach ($all_guru as $id => $name) {
        if (!in_array($id, $guru_sudah_absen)) {
            $belum[] = [
                'id' => $id,
                'fullname' => $name
            ];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
            'alfa' => $alfa,
            'belum' => $belum
        ],
        'tanggal' => $tanggal
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>