<?php
header('Content-Type: application/json');
include '../includes/config.php';

try {
    $bulan = $_GET['bulan'] ?? date('m');
    $tahun = $_GET['tahun'] ?? date('Y');
    
    // Validasi input
    if (!preg_match('/^(0[1-9]|1[0-2])$/', $bulan) || !preg_match('/^\d{4}$/', $tahun)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format bulan atau tahun tidak valid'
        ]);
        exit;
    }
    
    // Ambil semua guru
    $query_guru = $conn->query("SELECT id, fullname FROM guru ORDER BY fullname ASC");
    $guru_list = $query_guru->fetch_all(MYSQLI_ASSOC);
    
    if (empty($guru_list)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data guru'
        ]);
        exit;
    }
    
    // Hitung jumlah hari dalam bulan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
    // Ambil semua data absensi untuk bulan dan tahun yang dipilih
    $start_date = "$tahun-$bulan-01";
    $end_date = "$tahun-$bulan-$jumlah_hari";
    
    $query_absen = $conn->query("
        SELECT guru_id, tanggal, status, waktu_absen, catatan 
        FROM absensi_guru 
        WHERE tanggal BETWEEN '$start_date' AND '$end_date'
        ORDER BY tanggal ASC
    ");
    
    // Organize absensi data by guru_id and tanggal
    $absensi_by_guru = [];
    while ($row = $query_absen->fetch_assoc()) {
        $guru_id = $row['guru_id'];
        $tanggal = $row['tanggal'];
        
        if (!isset($absensi_by_guru[$guru_id])) {
            $absensi_by_guru[$guru_id] = [];
        }
        
        $absensi_by_guru[$guru_id][$tanggal] = [
            'status' => $row['status'],
            'waktu_absen' => $row['waktu_absen'],
            'catatan' => $row['catatan']
        ];
    }
    
    // Build response data
    $data = [];
    $stats = [
        'hadir' => 0,
        'terlambat' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alfa' => 0
    ];
    
    foreach ($guru_list as $guru) {
        $guru_id = $guru['id'];
        $guru_data = [
            'id' => $guru_id,
            'fullname' => $guru['fullname'],
            'absensi' => [],
            'summary' => [
                'hadir' => 0,
                'terlambat' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0
            ]
        ];
        
        // Loop through all days in the month
        for ($day = 1; $day <= $jumlah_hari; $day++) {
            $tanggal = sprintf("%s-%s-%02d", $tahun, $bulan, $day);
            
            if (isset($absensi_by_guru[$guru_id][$tanggal])) {
                $absen = $absensi_by_guru[$guru_id][$tanggal];
                $guru_data['absensi'][$tanggal] = $absen;
                
                // Count summary
                $status = strtolower($absen['status']);
                if (isset($guru_data['summary'][$status])) {
                    $guru_data['summary'][$status]++;
                    $stats[$status]++;
                }
            }
        }
        
        $data[] = $guru_data;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'stats' => $stats,
        'bulan' => $bulan,
        'tahun' => $tahun
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>