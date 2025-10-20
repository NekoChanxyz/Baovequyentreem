<?php
// admin_tuvan.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Quản lý tư vấn - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <style>
    :root {
      --main-blue: #2563eb;
      --blue-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
      --white: #ffffff;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --radius: 14px;
    }
    body { background: #f8f9fa; margin:0; padding:0; font-family: 'Segoe UI', sans-serif; }

    /* ===== HEADER ĐỒNG BỘ DASHBOARD ===== */
    header {
      background: var(--blue-gradient);
      color: white;
      padding: 18px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow);
      border-bottom-left-radius: var(--radius);
      border-bottom-right-radius: var(--radius);
    }
    header h1 {
      font-size: 1.6rem;
      font-weight: 600;
      margin: 0;
    }
    .header-right {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 500;
    }
    .back-btn {
      background: var(--white);
      color: var(--main-blue);
      border: none;
      padding: 8px 16px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      transition: all 0.25s ease;
    }
    .back-btn:hover { background: #eff6ff; transform: translateY(-2px); }

    /* ===== NỘI DUNG GỐC ===== */
    .table-wrapper { overflow-x: auto; }
    table th, table td { vertical-align: middle !important; }
    .btn { min-width: 90px; }
    .modal-body img { max-width: 100%; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    .filter-bar {
      display:flex; flex-wrap:wrap; gap:10px;
      justify-content:space-between; align-items:center;
      margin-bottom:15px;
    }
    .filter-bar select, .filter-bar input { min-width:200px; }
  </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<header>
  <h1>📋 Quản lý tư vấn</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.location.href='../../admin.php'">⬅️ Quay lại</button>
    <span>Xin chào, <b>Admin</b> 👋</span>
  </div>
</header>

<div class="container mt-4">

  <!-- Bộ lọc + tìm kiếm -->
  <div class="filter-bar">
    <div class="input-group" style="max-width: 320px;">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Tìm theo người dùng hoặc câu hỏi...">
      <button class="btn btn-outline-primary" onclick="filterQuestions()">Tìm</button>
    </div>
    <div class="d-flex gap-2">
      <select id="statusFilter" class="form-select" onchange="filterQuestions()">
        <option value="">-- Trạng thái --</option>
        <option value="dang_cho_tra_loi">Đang Chờ trả lời</option>
        <option value="da_tra_loi">Đã trả lời</option>
      </select>

      <select id="chuyenmonFilter" class="form-select" onchange="filterQuestions()">
        <option value="">-- Chuyên môn --</option>
      </select>
    </div>
  </div>

  <!-- Bảng câu hỏi -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">Câu hỏi người dùng</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover" id="tblQuestions">
        <thead class="table-secondary">
          <tr>
            <th>ID</th>
            <th>Người dùng</th>
            <th>Câu hỏi</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
            <th>Chuyên môn</th>
            <th>Chuyên gia</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody><tr><td colspan="8" class="text-center">Đang tải dữ liệu...</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Bảng thống kê -->
  <div class="card shadow-sm mb-5">
    <div class="card-header bg-success text-white fw-bold">Thống kê theo chuyên môn</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover" id="tblSessions">
        <thead class="table-secondary">
          <tr>
            <th>Chuyên môn</th>
            <th>Số câu hỏi</th>
          </tr>
        </thead>
        <tbody><tr><td colspan="2" class="text-center">Đang tải dữ liệu...</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Chi tiết câu hỏi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detailContent">
        <p>Đang tải dữ liệu...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php // ✅ Giữ nguyên toàn bộ JS logic của bạn ?>
const BASE_URL = '../BE/';
let allQuestions = [];

function safeJSON(raw) {
  try { return typeof raw === 'object' ? raw : JSON.parse(raw); }
  catch { return { success: false, message: 'Phản hồi không hợp lệ từ server' }; }
}

function loadQuestions() {
  $.get(BASE_URL + 'cauhoi_ds.php', function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success')) {
      return $('#tblQuestions tbody').html(`<tr><td colspan="8" class="text-danger text-center">${res.message || 'Không có dữ liệu'}</td></tr>`);
    }
    allQuestions = res.data;
    renderQuestions(allQuestions);
  }).fail(() => {
    $('#tblQuestions tbody').html(`<tr><td colspan="8" class="text-danger text-center">Không thể kết nối máy chủ</td></tr>`);
  });
}

function renderQuestions(data) {
  const rows = data.map(r => `
    <tr>
      <td>${r.id}</td>
      <td>${r.ten_nguoi_dung || ''}</td>
      <td>${r.cau_hoi || ''}</td>
      <td>${r.ngay_gui || ''}</td>
      <td>${r.trang_thai || ''}</td>
      <td>${r.ten_chuyen_mon || ''}</td>
      <td>${r.ten_chuyen_gia || 'Chưa có'}</td>
      <td>
        <button class='btn btn-outline-info btn-sm' onclick='viewDetail(${r.id})'>Xem</button>
        <button class='btn btn-outline-danger btn-sm' onclick='deleteQuestion(${r.id})'>Xóa</button>
      </td>
    </tr>
  `).join('');
  $('#tblQuestions tbody').html(rows || `<tr><td colspan="8" class="text-center text-muted">Không có câu hỏi nào</td></tr>`);
}

function filterQuestions() {
  const text = $('#searchInput').val().toLowerCase();
  const status = $('#statusFilter').val();
  const cm = $('#chuyenmonFilter').val();
  const filtered = allQuestions.filter(q => {
    const matchText = q.cau_hoi?.toLowerCase().includes(text) || q.ten_nguoi_dung?.toLowerCase().includes(text);
    const matchStatus = status ? q.trang_thai === status : true;
    const matchCM = cm ? (q.ten_chuyen_mon === cm) : true;
    return matchText && matchStatus && matchCM;
  });
  renderQuestions(filtered);
}

function loadChuyenMon() {
  $.get(BASE_URL + 'chuyenmon_list.php', function(raw) {
    const res = safeJSON(raw);
    if (!res.success || !res.data) return;
    const options = res.data.map(c => `<option value="${c.ten_chuyen_mon}">${c.ten_chuyen_mon}</option>`);
    $('#chuyenmonFilter').append(options.join(''));
  });
}

function viewDetail(id) {
  $.get(BASE_URL + 'cauhoi_chitiet.php', { id }, function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success') || !res.data)
      return alert('❌ Không tải được chi tiết câu hỏi.');
    const d = res.data;
    let html = `
      <div class="row">
        <div class="col-md-7">
          <p><strong>📌 Câu hỏi:</strong> ${d.cau_hoi || '(Không có nội dung)'}</p>
          <p><strong>👤 Người hỏi:</strong> ${d.nguoi_dung || 'Ẩn danh'}</p>
          <p><strong>📚 Chuyên môn:</strong> ${d.ten_chuyen_mon || 'Không xác định'}</p>
          <p><strong>⚙️ Trạng thái:</strong> ${d.trang_thai || 'Không rõ'}</p>
          <p><strong>👨‍⚕️ Chuyên gia:</strong> ${d.chuyen_gia || 'Chưa có'}</p>
          <p><strong>💬 Phản hồi:</strong><br>
            ${d.tra_loi ? `<div class="p-2 bg-light border rounded">${d.tra_loi}</div>` : 'Chưa có phản hồi'}
          </p>
          <p><strong>🕒 Gửi lúc:</strong> ${d.ngay_gui || ''}</p>
          ${d.ngay_tra_loi ? `<p><strong>📅 Trả lời:</strong> ${d.ngay_tra_loi}</p>` : ''}
        </div>
        <div class="col-md-5 text-center">
          ${d.anh_minh_hoa 
            ? `<img src="../../${d.anh_minh_hoa}" alt="Ảnh minh họa" class="img-fluid rounded shadow-sm border" style="max-height:300px; object-fit:cover;">`
            : '<em>Không có ảnh minh họa</em>'}
        </div>
      </div>
    `;
    $('#detailContent').html(html);
    new bootstrap.Modal(document.getElementById('detailModal')).show();
  });
}

function deleteQuestion(id) {
  if (!confirm('Bạn có chắc muốn xóa VĨNH VIỄN câu hỏi này không?')) return;
  $.post(BASE_URL + 'cauhoi_xoa.php', { id }, function(res) {
    const data = safeJSON(res);
    alert(data.message || 'Đã xử lý');
    if (data.success) loadQuestions();
  }, 'json');
}

function loadSessions() {
  $.get(BASE_URL + 'thong_ke_tu_van.php', function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success')) {
      return $('#tblSessions tbody').html(`<tr><td colspan="2" class="text-danger text-center">${res.message || 'Không có dữ liệu thống kê'}</td></tr>`);
    }
    const stats = res.data?.theo_chuyen_mon || res.theo_chuyen_mon || [];
    const rows = stats.map(r => `<tr><td>${r.ten_chuyen_mon}</td><td>${r.so_cau_hoi}</td></tr>`).join('');
    $('#tblSessions tbody').html(rows || `<tr><td colspan="2" class="text-center text-muted">Không có dữ liệu</td></tr>`);
  });
}

$(document).ready(function() {
  loadQuestions();
  loadChuyenMon();
  loadSessions();
});
</script>
</body>
</html>
