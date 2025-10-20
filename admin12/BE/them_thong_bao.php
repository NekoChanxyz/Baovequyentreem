<?php
// 📂 experts/BE/them_thong_bao.php
require_once __DIR__ . '/db.php';  // giữ nguyên kết nối PDO

// ❌ Không cần header JSON vì file này không phải API độc lập
// header('Content-Type: application/json; charset=UTF-8');

function guiThongBao($tai_khoan_id, $tieu_de, $noi_dung) {
    global $conn; // $conn là đối tượng PDO
    if (!$tai_khoan_id) return false;

    try {
        $sql = "INSERT INTO thong_bao 
                (tai_khoan_id, loai_thong_bao, tieu_de, noi_dung, da_xem, ngay_gui)
                VALUES (?, 'lich_hen', ?, ?, 0, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tai_khoan_id, $tieu_de, $noi_dung]);
        return true;
    } catch (PDOException $e) {
        error_log('Lỗi khi gửi thông báo: ' . $e->getMessage());
        return false;
    }
}
?>
