<?php
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id'])) {
  header('Location: ../../pages/dang_nhap.php');
  exit;
}

$db = new Database();
$conn = $db->connect();

$chuyen_gia_id = $_SESSION['expert_id'];
$ho_ten = $_SESSION['expert_name'] ?? 'Chuyên gia';

// ✅ Lấy danh sách câu hỏi được giao
$sql = "SELECT t.id, t.cau_hoi, t.ngay_gui, u.ho_ten AS ten_nguoi_dung, 
               cm.ten_chuyen_mon, t.tra_loi, t.trang_thai
        FROM tu_van t
        JOIN tai_khoan u ON t.nguoi_dung_id = u.id
        JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        WHERE t.chuyen_gia_id = ?
        ORDER BY t.ngay_gui DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id]);
$cau_hoi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>💬 Danh sách câu hỏi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/danhsachcauhoi.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="question-container">
      <h2>💬 Câu hỏi được giao cho <?= htmlspecialchars($ho_ten) ?></h2>

      <?php if ($cau_hoi): ?>
        <table class="question-table">
          <thead>
            <tr>
              <th>Người hỏi</th>
              <th>Câu hỏi</th>
              <th>Chuyên môn</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($cau_hoi as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['ten_nguoi_dung']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['cau_hoi'])) ?></td>
              <td><?= htmlspecialchars($row['ten_chuyen_mon']) ?></td>
              <td><?= htmlspecialchars($row['ngay_gui']) ?></td>
              <td>
                <?php if (!empty($row['tra_loi'])): ?>
                  <span class="status success">Đã trả lời</span>
                <?php else: ?>
                  <span class="status pending">Chưa trả lời</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (empty($row['tra_loi'])): ?>
                  <a href="traloi.php?id=<?= $row['id'] ?>" class="btn btn-primary">Trả lời</a>
                <?php else: ?>
                  <em class="done">Đã phản hồi</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">Chưa có câu hỏi nào được giao.</p>
      <?php endif; ?>
    </div>
  </main>
    <!-- JS dropdown sidebar -->
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
