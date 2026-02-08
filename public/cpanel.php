<?php
/****************************************************
 * index.php - Log rút tiền (1 file duy nhất, full, có lỗi/thành công)
 * Yêu cầu PHP >= 7.4, SQLite PDO bật sẵn.
 * DB: data.db, bảng logs như đề bài + isSuccess (int, default 1), reason (text).
 ****************************************************/

ini_set('display_errors', 1);
error_reporting(E_ALL);

/** ------------------ Cấu hình ------------------ **/
$dbFile = __DIR__ . '/data.db';
$SESSION_GAP_MINUTES = 150;  // 4.4: cùng 1 uid, cách nhau <= 60 phút => 1 đợt rút tiền
$MERGE_GAP_MINUTES   = 150;  // Yêu cầu: cùng uuid+serial, cách nhau <= 90 phút => gộp 1, lấy amount cao nhất

function dt_parse($ts) {
    try { return new DateTime($ts); } catch (Exception $e) { return null; }
}
function minutes_diff(DateTime $a, DateTime $b) {
    return abs(($a->getTimestamp() - $b->getTimestamp()) / 60.0);
}
function fetch_raw_rows($dbFile) {
    if (!file_exists($dbFile)) return [];
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // CHANGED: lấy thêm isSuccess, reason
    $stmt = $pdo->query("SELECT id, uuid, email, spaces, amount, time, serial, 
                                COALESCE(isSuccess,1) AS isSuccess, reason
                         FROM logs 
                         ORDER BY uuid ASC, serial ASC, time ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows ?: [];
}
function preprocess_merge_rows($rows, $MERGE_GAP_MINUTES) {
    // Gộp các row có cùng uuid+serial trong 90 phút -> giữ row có amount cao nhất
    $merged = [];
    $currentGroup = null;
    foreach ($rows as $r) {
        $uuid   = $r['uuid'];
        $serial = $r['serial'];
        $dt     = dt_parse($r['time']);
        if (!$dt) continue;

        if ($currentGroup === null) {
            $currentGroup = [
                'uuid'=>$uuid,'serial'=>$serial,'start_ts'=>$dt,'last_ts'=>$dt,'best_row'=>$r
            ];
            continue;
        }
        if ($uuid !== $currentGroup['uuid'] || $serial !== $currentGroup['serial']) {
            $merged[] = $currentGroup['best_row'];
            $currentGroup = ['uuid'=>$uuid,'serial'=>$serial,'start_ts'=>$dt,'last_ts'=>$dt,'best_row'=>$r];
            continue;
        }
        $gapMin = minutes_diff($dt, $currentGroup['last_ts']);
        if ($gapMin <= $MERGE_GAP_MINUTES) {
            if (floatval($r['amount']) > floatval($currentGroup['best_row']['amount'])) {
                $currentGroup['best_row'] = $r;
            }
            $currentGroup['last_ts'] = $dt;
        } else {
            $merged[] = $currentGroup['best_row'];
            $currentGroup = ['uuid'=>$uuid,'serial'=>$serial,'start_ts'=>$dt,'last_ts'=>$dt,'best_row'=>$r];
        }
    }
    if ($currentGroup !== null) $merged[] = $currentGroup['best_row'];

    // Sort theo thời gian giảm dần để hiển thị
    usort($merged, fn($a,$b)=> strcmp($b['time'],$a['time']));
    return $merged;
}

$rawRows    = fetch_raw_rows($dbFile);
$mergedRows = preprocess_merge_rows($rawRows, $MERGE_GAP_MINUTES);

// Chuẩn hóa dữ liệu xuất ra JS
$rowsForJs = [];
$uids = [];
foreach ($mergedRows as $idx => $r) {
    if (floatval($r['amount']) < 0.25) {
        continue;
    }
    $rowsForJs[] = [
        // NEW: đưa id, isSuccess, reason ra JS để thao tác UI
        'id'=>intval($r['id']),
        'stt'=>$idx+1,
        'uuid'=>$r['uuid'],
        'serial'=>$r['serial'],
        'amount'=>floatval($r['amount']),
        'email'=>$r['email'],
        'spaces'=>intval($r['spaces']),
        'time'=>$r['time'],
        'isSuccess'=>intval($r['isSuccess'] ?? 1),
        'reason'=>$r['reason'] ?? null,
    ];
    $uids[$r['uuid']] = true;
}
$uids = array_keys($uids);
sort($uids);
?>
<!doctype html>
<html lang="vi" data-bs-theme="light">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hệ thống log rút tiền</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables + Bootstrap 5 -->
<link href="https://cdn.datatables.net/v/bs5/dt-2.0.7/r-3.0.3/datatables.min.css" rel="stylesheet" />
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- flatpickr -->
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<!-- dayjs & plugins -->
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.13/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.13/plugin/customParseFormat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.13/plugin/utc.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.13/plugin/timezone.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.13/plugin/weekOfYear.js"></script>
<script>
  dayjs.extend(dayjs_plugin_customParseFormat);
  dayjs.extend(dayjs_plugin_utc);
  dayjs.extend(dayjs_plugin_timezone);
  dayjs.extend(dayjs_plugin_weekOfYear);
</script>

<!-- jQuery + Select2 -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/v/bs5/dt-2.0.7/r-3.0.3/datatables.min.js"></script>
<!-- flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
:root{ --brand:#0d6efd; }
body{ background:#f7fbff; }
.navbar{ background:linear-gradient(135deg,var(--brand),#3aa0ff); }
.navbar .navbar-brand,.navbar .nav-link,.navbar .navbar-text{ color:#fff!important; }
.card{ border:none; box-shadow:0 6px 20px rgba(13,110,253,.12); border-radius:16px; }
.card-header{ background:linear-gradient(135deg,#e8f2ff,#f6fbff); border-bottom:none; border-top-left-radius:16px!important; border-top-right-radius:16px!important; }
.btn-brand{ background:var(--brand); color:#fff; border-radius:12px; }
.btn-outline-brand{ border-color:var(--brand); color:var(--brand); border-radius:12px; }
.form-control,.select2-container--default .select2-selection--single{ border-radius:12px; }
.small-muted{ color:#6c757d;font-size:.9rem; }
.table thead th{ background:#eef6ff; }
.chart-wrap{ background:#fff;border-radius:16px;padding:16px;border:1px solid #e8f1ff; }
.kpi{ display:flex;gap:12px;align-items:center;background:linear-gradient(135deg,#f0f8ff,#ffffff);border:1px solid #e5f0ff;border-radius:16px;padding:16px; }
.kpi .num{ font-size:1.4rem;font-weight:700;color:#0a3d8f }
.kpi .lbl{ color:#4977c1 }
footer{ color:#5b7bbb; }
/* NEW: màu dòng lỗi */
/*tr.row-failed { background-color:#ffe7e7 !important; }*/

tr.row-failed { background-color:#ffefef !important; }
/* hover vẫn thấy rõ */
.table-hover tbody tr.row-failed:hover { background-color:#ffe3e3 !important; }

/* mới trong 24h (xanh nhạt) */
tr.row-new { background-color:#e9f9ef !important; }
.table-hover tbody tr.row-new:hover { background-color:#def5e7 !important; }

.badge-success-soft{background:#e7f8ef;color:#0a8f49;border:1px solid #bcead1}
.badge-failed-soft{background:#ffefef;color:#bf0b0b;border:1px solid #f7c2c2}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">💸 Báo cáo rút tiền</a>
    <span class="navbar-text">UI xanh đẹp & sống động</span>
  </div>
</nav>

<div class="container my-4">

  <!-- KPIs -->
  
<div class="row g-3 mb-3">
    
    <div class="col-md-3">
  <div class="kpi">
    <div>💼</div>
    <div>
      <div class="lbl">Số dư khả dụng</div>
      <div class="num" id="kpiAvailableBalance">$0</div>
    </div>
  </div>
</div>


  <div class="col-md-3">
    <div class="kpi">
      <div>✅</div>
      <div>
        <div class="lbl">Số tiền thu được (OK)</div>
        <div class="num" id="kpiSuccessAmount">$0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="kpi">
      <div>⚠️</div>
      <div>
        <div class="lbl">Số tiền lỗi</div>
        <div class="num" id="kpiFailedAmount">$0</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
  <div class="kpi">
    <div>🏧</div>
    <div>
      <div class="lbl">Số tiền đã rút</div>
      <div class="num" id="kpiWithdrawn">$0</div>
    </div>
  </div>
</div>

<div class="col-md-3">
  <div class="kpi">
    <div>📈</div>
    <div>
      <div class="lbl">Lợi nhuận (ước tính)</div>
      <div class="num" id="kpiProfitEstimated">$0</div>
    </div>
  </div>
</div>

<div class="col-md-3">
  <div class="kpi">
    <div>💰</div>
    <div>
      <div class="lbl">Tiền vốn</div>
      <div class="num" id="tienvon">$0</div>
    </div>
  </div>
</div>
  
  <div class="col-md-3">
    <div class="kpi">
      <div>🧑‍💻</div>
      <div>
        <div class="lbl">Số UID</div>
        <div class="num" id="kpiUidCount">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="kpi">
      <div>🔌</div>
      <div>
        <div class="lbl">Số Serial</div>
        <div class="num" id="kpiSerialCount">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="kpi">
      <div>🧾</div>
      <div>
        <div class="lbl">Số Giao dịch</div>
        <div class="num" id="kpiTxCount">0</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="kpi">
      <div>🚨</div>
      <div>
        <div class="lbl">Số giao dịch lỗi</div>
        <div class="num" id="kpiFailCount">0</div>
      </div>
    </div>
  </div>
</div>


  <!-- Bộ lọc -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Bộ lọc dữ liệu</div>
      <div class="small-muted">Gộp trùng theo <span class="badge text-bg-info">uuid+serial trong 90 phút</span> đã áp dụng trước khi lọc</div>
    </div>
    <div class="card-body">
      <form id="filterForm" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">UID</label>
          <select id="uidSelect" class="form-select">
            <option value="">— Tất cả —</option>
            <?php foreach($uids as $u): ?>
              <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Serial</label>
          <input type="text" id="serialInput" class="form-control" placeholder="Nhập serial...">
        </div>
        <div class="col-md-3">
          <label class="form-label">Email</label>
          <input type="text" id="emailInput" class="form-control" placeholder="Nhập email...">
        </div>
        <div class="col-md-3">
          <label class="form-label">Khoảng thời gian</label>
          <div class="d-flex gap-2">
            <input type="text" id="timeFrom" class="form-control" placeholder="Từ">
            <input type="text" id="timeTo" class="form-control" placeholder="Đến">
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="todayOnly">
            <label class="form-check-label" for="todayOnly">Chỉ hôm nay</label>
          </div>
        </div>
        <!-- NEW: Lọc trạng thái -->
        <div class="col-md-3">
          <label class="form-label">Trạng thái</label>
          <select id="statusSelect" class="form-select">
            <option value="">— Tất cả —</option>
            <option value="success">Thành công</option>
            <option value="failed">Lỗi</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="button" id="applyFilter" class="btn btn-brand">Áp dụng</button>
          <button type="button" id="resetFilter" class="btn btn-outline-brand">Xóa lọc</button>
        </div>
      </form>
    </div>
  </div>

<!--Bảng định danh-->

<!-- Identification CRUD -->
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="fw-semibold">Định danh (Identification)</div>
    <div class="d-flex gap-2">
      <button id="btnRefreshIdentification" class="btn btn-outline-secondary btn-sm">Tải lại</button>
      <button id="btnAddIdentification" class="btn btn-primary btn-sm">
        + Thêm định danh
      </button>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="identificationTable" class="table table-hover table-bordered w-100">
        <thead>
          <tr>
            <th style="width:10px;">ID</th>
            <th>UUID</th>
            <th>Label</th>
            <th>Note</th>
            <th style="width:50px;">Hành động</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <small class="text-muted">Gợi ý: ID là khóa ngoại trỏ tới <code>logs.id</code>. Khi xóa <code>logs</code> có thể xóa kèm (CASCADE) nếu bạn bật trong schema.</small>
  </div>
</div>

<!-- Modal Create/Update -->
<div class="modal fade" id="identModal" tabindex="-1" aria-labelledby="identModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="identForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="identModalLabel">Thêm định danh</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="identMode" value="create">
        <div class="mb-3">
          <label class="form-label">ID (FK logs.id) <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="identId" placeholder="Ví dụ: 123" required>
          <div class="form-text">Phải tồn tại trong bảng <code>logs</code>.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">UUID <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="identUuid" placeholder="uuid..." required>
        </div>
        <div class="mb-3">
          <label class="form-label">Label <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="identLabel" placeholder="Nhập nhãn bất kỳ..." required>
        </div>
        <div class="mb-3">
          <label class="form-label">Note</label>
          <textarea class="form-control" id="identNote" rows="3" placeholder="Ghi chú..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
        <button class="btn btn-primary" type="submit">Lưu</button>
      </div>
    </form>
  </div>
</div>


<!--Bảng định danh-->

  <!-- Bảng dữ liệu -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Dữ liệu đã xử lý</div>
      <span class="small-muted">Click header để sort ↑↓</span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="logsTable" class="table table-hover table-bordered w-100">
          <thead>
            <tr>
              <!-- CHANGED: thêm Trạng thái, Lý do, Hành động -->
              <th>STT</th><th>UID</th><th>Serial</th><th>Amount ($)</th><th>Email</th><th>Spaces</th><th>Time</th><th>Trạng thái</th><th>Lý do</th><th>Hành động</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

<!-- Ghi rút tiền theo Email -->
<div class="card mb-4">
  <div class="card-header fw-semibold">Ghi rút tiền theo Email</div>
  <div class="card-body row g-3">
    <div class="col-md-3">
      <label class="form-label">Email</label>
      <input type="email" id="wdEmail" class="form-control" placeholder="example@email.com">
    </div>
    <div class="col-md-3">
      <label class="form-label">Số tiền (USD)</label>
      <input type="number" id="wdAmount" class="form-control" min="0" step="0.01" placeholder="0.00">
    </div>
    <div class="col-md-4">
      <label class="form-label">Ghi chú</label>
      <input type="text" id="wdNote" class="form-control" placeholder="Tuỳ chọn...">
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <button id="btnSaveWithdrawal" class="btn btn-brand w-100">Lưu rút tiền</button>
    </div>
  </div>
</div>

<!-- Ghi vốn theo UID -->
<div class="card mb-4">
  <div class="card-header fw-semibold">Ghi vốn theo UID</div>
  <div class="card-body row g-3">
    <div class="col-md-3">
      <label class="form-label">UID</label>
      <input type="text" id="cpUid" class="form-control" placeholder="Nhập UID...">
    </div>
    <div class="col-md-3">
      <label class="form-label">Số tiền (VND)</label>
      <input type="number" id="cpVnd" class="form-control" min="0" step="1" placeholder="0">
    </div>
    <div class="col-md-2">
      <label class="form-label">Tỷ giá</label>
      <input type="number" id="cpRate" class="form-control" min="1" step="1" value="25000">
      <div class="small-muted mt-1">25,000 VND = $1</div>
    </div>
    <div class="col-md-2">
      <label class="form-label">Nguồn</label>
      <input type="text" id="cpSource" class="form-control" placeholder="Tiền mặt/NĐT A...">
    </div>
    <div class="col-md-2">
      <label class="form-label">Ghi chú</label>
      <input type="text" id="cpNote" class="form-control" placeholder="Tuỳ chọn...">
    </div>
    <div class="col-12 d-flex gap-2">
      <button id="btnSaveCapital" class="btn btn-brand">Lưu vốn</button>
    </div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header fw-semibold">Thống kê rút tiền theo Email</div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblWithdrawByEmail" class="table table-bordered table-hover w-100">
        <thead>
          <tr>
            <th>Email</th>
            <th>Số lần rút</th>
            <th>Tổng rút (USD)</th>
            <th>Đầu tiên</th>
            <th>Gần nhất</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header fw-semibold">Lịch sử rút tiền gần đây</div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblWithdrawList" class="table table-bordered table-hover w-100">
        <thead>
          <tr>
            <th>ID</th><th>Email</th><th>Amount (USD)</th><th>Time</th><th>Note</th><th>Hành động</th>
          </tr>
        </thead><tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header fw-semibold">Lịch sử vốn gần đây</div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblCapitalList" class="table table-bordered table-hover w-100">
        <thead>
          <tr>
            <th>ID</th><th>UID</th><th>VND</th><th>USD</th><th>Rate</th><th>Source</th><th>Time</th><th>Note</th><th>Hành động</th>
          </tr>
        </thead><tbody></tbody>
      </table>
    </div>
  </div>
</div>











  <!-- Điều khiển Top (cũ) -->
  <div class="card mb-4">
    <div class="card-header fw-semibold">Tùy chọn hiển thị biểu đồ Top</div>
    <div class="card-body row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Sắp xếp</label>
        <select id="chartSort" class="form-select">
          <option value="desc" selected>Giảm dần theo tổng $</option>
          <option value="asc">Tăng dần theo tổng $</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Số lượng Top</label>
        <input type="number" id="chartTopN" class="form-control" min="3" max="50" value="10">
      </div>
      <div class="col-md-3">
        <button id="refreshCharts" class="btn btn-brand">Cập nhật biểu đồ</button>
      </div>
    </div>
  </div>

  <!-- Điều khiển nâng cao -->
  <div class="card mb-4">
    <div class="card-header fw-semibold">Biểu đồ nâng cao (xu hướng & phân bổ)</div>
    <div class="card-body row g-3">
      <div class="col-md-3">
        <label class="form-label">Kỳ tổng hợp thời gian</label>
        <select id="aggPeriod" class="form-select">
          <option value="week" selected>Tuần</option>
          <option value="month">Tháng</option>
        </select>
        <div class="small-muted mt-1">Dùng cho Line chart tổng $ theo tuần/tháng</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Moving Average (cửa sổ)</label>
        <input type="number" id="maWindow" class="form-control" min="2" max="60" value="4">
        <div class="small-muted mt-1">Áp dụng cho đường xu hướng (tuần/tháng)</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Đa đường (Top theo)</label>
        <select id="multiSeriesDim" class="form-select">
          <option value="uuid" selected>UID</option>
          <option value="serial">Serial</option>
          <option value="email">Email</option>
        </select>
        <div class="small-muted mt-1">Vẽ nhiều đường cho top N (dựa “Số lượng Top”)</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Phân bổ (Pie / Ranking theo)</label>
        <select id="pieRankDim" class="form-select">
          <option value="uuid" selected>UID</option>
          <option value="serial">Serial</option>
          <option value="email">Email</option>
        </select>
        <div class="small-muted mt-1">Áp dụng cho Pie/Donut & Ranking</div>
      </div>
      <div class="col-md-12 d-flex gap-2">
        <button id="refreshAdvanced" class="btn btn-brand">Cập nhật biểu đồ nâng cao</button>
      </div>
    </div>
  </div>

  <!-- Biểu đồ CƠ BẢN -->
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.1 Tổng $ theo Serial</h5>
        <canvas id="chartBySerial" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.2 Tổng $ theo UID</h5>
        <canvas id="chartByUid" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.3 Tổng $ theo Email</h5>
        <canvas id="chartByEmail" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.4 Biểu đồ "Đợt rút tiền" (<= 60 phút/UID)</h5>
        <canvas id="chartSessions" height="220"></canvas>
        <div class="small-muted mt-2">Mỗi cột = 1 đợt (X: thời điểm bắt đầu, Y: tổng $)</div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.5(a) Tổng $ theo Ngày</h5>
        <canvas id="chartByDay" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">4.5(b) Phân bố $ theo Giờ trong ngày</h5>
        <canvas id="chartByHour" height="220"></canvas>
      </div>
    </div>
    <!-- NEW: Biểu đồ lỗi -->
    <div class="col-lg-12">
      <div class="chart-wrap">
        <h5 class="mb-3">4.6 Thiết bị lỗi: Số lần lỗi theo Serial</h5>
        <canvas id="chartFailBySerial" height="140"></canvas>
        <div class="small-muted mt-2">Dựa trên các giao dịch được đánh dấu lỗi (isSuccess=0) sau khi gộp.</div>
      </div>
    </div>
  </div>

  <!-- Biểu đồ NÂNG CAO -->
  <div class="row g-4 mt-1">
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Xu hướng tổng $ theo tuần/tháng + MA</h5>
        <canvas id="chartTrendAgg" height="220"></canvas>
        <div class="small-muted mt-2">Đường 1: Tổng $ theo kỳ. Đường 2: MA làm mượt.</div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Tích lũy tổng amount theo thời gian</h5>
        <canvas id="chartCumulative" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="chart-wrap">
        <h5 class="mb-3">Đa đường: Top N theo (UID/Serial/Email)</h5>
        <canvas id="chartMultiSeries" height="260"></canvas>
        <div class="small-muted mt-2">Top N lấy theo “Số lượng Top”. Mỗi đường là một thực thể.</div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Tỉ trọng % (Pie/Donut) theo (UID/Serial/Email)</h5>
        <div class="d-flex gap-3">
          <canvas id="chartPie" height="220" style="flex:1"></canvas>
          <canvas id="chartDonut" height="220" style="flex:1"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Stacked bar theo ngày (màu = UID)</h5>
        <canvas id="chartStackedByDayUid" height="220"></canvas>
        <div class="small-muted mt-2">X = ngày (YYYY-MM-DD), Y = tổng $, mỗi màu = 1 UID.</div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="chart-wrap">
        <h5 class="mb-3">Ranking (Horizontal Bar): Top N theo (UID/Serial/Email)</h5>
        <canvas id="chartRanking" height="260"></canvas>
      </div>
    </div>
    
    
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Vốn theo UID (USD)</h5>
        <canvas id="chartCapitalByUid" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-wrap">
        <h5 class="mb-3">Lời / Lỗ theo UID (USD)</h5>
        <canvas id="chartProfitByUid" height="220"></canvas>
        <div class="small-muted mt-2">Lời xanh, lỗ đỏ (profit = doanh thu - vốn)</div>
      </div>
    </div>

  </div>

  <footer class="mt-5 text-center">
    <div class="small-muted">Đã áp dụng gộp trùng: <strong>uuid + serial</strong> trong <strong>90 phút</strong> (lấy amount cao nhất). Biểu đồ đợt rút tiền: gap ≤ <strong>60 phút</strong>/UID.</div>
  </footer>
</div>

<script>
async function sha256Hex(message) {
  const enc = new TextEncoder();                // UTF-8
  const data = enc.encode(message);
  const digest = await crypto.subtle.digest('SHA-256', data);
  // ArrayBuffer -> hex
  const bytes = new Uint8Array(digest);
  return [...bytes].map(b => b.toString(16).padStart(2, '0')).join('');
}


function isNew24h(isoStr){
  const t = dayjs(isoStr, "YYYY-MM-DD HH:mm:ss", true);
  if (!t.isValid()) return false;
  return dayjs().diff(t, 'hour') < 24;
}

// ---------- DỮ LIỆU PHP -> JS ----------
const MASTER_ROWS = <?php echo json_encode($rowsForJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

// ---------- TIỆN ÍCH ----------
const PARSE_FMT = "YYYY-MM-DD HH:mm:ss";
function parseTs(s){ return dayjs(s, PARSE_FMT, true); }
function formatMoney(x){ return '$'+(Number(x).toLocaleString(undefined,{maximumFractionDigits:2})); }

// ---------- BIẾN CHART ----------
let chartBySerial=null, chartByUid=null, chartByEmail=null, chartSessions=null, chartByDay=null, chartByHour=null;
let chartTrendAgg=null, chartCumulative=null, chartMultiSeries=null, chartPie=null, chartDonut=null, chartStackedByDayUid=null, chartRanking=null;
// NEW:
let chartFailBySerial=null;

// ---------- KHỞI TẠO UI ----------
let chartCapitalByUid=null, chartProfitByUid=null;
let _lastCapitalsByUid = [];
let _lastWithdrawFiltered = 0;


async function loadFinanceStats(uidForWithdraw='',selectedEmail='', fromStr='', toStr=''){
  const url = new URL('api_finance.php', location.href);
  url.searchParams.set('action','stats');
  if (selectedEmail) url.searchParams.set('email', selectedEmail);
  if (uidForWithdraw) url.searchParams.set('uid', uidForWithdraw);
  if (fromStr) url.searchParams.set('from', fromStr);
  if (toStr)   url.searchParams.set('to', toStr);

  const res = await fetch(url.toString());
  const j = await res.json();
  if (!res.ok || !j.ok){ throw new Error(j.error || res.statusText); }

  // KPI "Số tiền đã rút" (đã lọc theo email/time)
  document.getElementById('kpiWithdrawn').textContent = formatMoney(j.total_withdraw_usd || 0);
  _lastWithdrawFiltered = Number(j.total_withdraw_usd || 0);

  // cache vốn theo UID (để client tự tính profit theo filtered)
  _lastCapitalsByUid = j.capitals_by_uid || [];

  // bảng thống kê rút theo email (đời)
  if (!$.fn.dataTable.isDataTable('#tblWithdrawByEmail')) {
    $('#tblWithdrawByEmail').DataTable({
      data: j.withdrawals_by_email || [],
      columns: [
        {data:'email'},
        {data:'withdraw_count'},
        {data:'total_withdraw_usd', render:(d)=> Number(d).toFixed(2)},
        {data:'first_withdraw_at'},
        {data:'last_withdraw_at'}
      ],
      order: [[2,'desc']],
      pageLength: 10,
      responsive: true
    });
  } else {
    const dt = $('#tblWithdrawByEmail').DataTable();
    dt.clear().rows.add(j.withdrawals_by_email || []).draw();
  }

  // chart vốn theo UID (không lọc thời gian)
  destroyChart(chartCapitalByUid);
  const cap = (_lastCapitalsByUid || []).map(x=>({k:x.uid, total:Number(x.total_capital_usd||0)}));
  chartCapitalByUid = buildBarChart('chartCapitalByUid', cap, 'Vốn (USD)');
}

$(function(){
  // Select2 cho UID
  $('#uidSelect').select2({width:'100%', placeholder:'— Tất cả —', allowClear:true});

  // flatpickr
  const fpFrom = flatpickr("#timeFrom", {enableTime:true, dateFormat:"Y-m-d H:i:S"});
  const fpTo   = flatpickr("#timeTo",   {enableTime:true, dateFormat:"Y-m-d H:i:S"});

  $('#todayOnly').on('change', function(){
    const checked = $(this).is(':checked');
    $('#timeFrom, #timeTo').prop('disabled', checked);
    if (checked){ fpFrom.clear(); fpTo.clear(); }
  });
  $('#timeFrom, #timeTo').on('input change', function(){
    if ($('#timeFrom').val() || $('#timeTo').val()){
      $('#todayOnly').prop('checked', false);
      $('#timeFrom, #timeTo').prop('disabled', false);
    }
  });

  // DataTable
  const dt = $('#logsTable').DataTable({
    data: [],
    columns: [
      {data:'stt'},
      {data:'uuid'},
      {data:'serial'},
      {data:'amount', render:(d)=> Number(d).toFixed(2)},
      {data:'email'},
      {data:'spaces'},
      {data:'time'},
      { // NEW: trạng thái
        data:'isSuccess',
        render:(v)=> v? '<span class="badge badge-success-soft">Thành công</span>' :
                        '<span class="badge badge-failed-soft">Lỗi</span>'
      },
      { // NEW: lý do
        data:'reason',
        render:(v)=> v? ('<span class="text-danger">'+escapeHtml(v)+'</span>') : ''
      },
      { // NEW: hành động
        data:null,
        orderable: false,
        searchable: false,
        render:(row)=> {
          const btnFail = `<button class="btn btn-sm btn-outline-danger btn-mark-fail" data-id="${row.id}" title="Báo lỗi">Báo lỗi</button>`;
          const btnUndo = `<button class="btn btn-sm btn-outline-secondary btn-undo-fail ms-1" data-id="${row.id}" title="Hủy lỗi">Hủy lỗi</button>`;
          return (row.isSuccess? btnFail : btnUndo + btnFail);
        }
      }
    ],
    order: [[6,'desc']],
    responsive: true,
    pageLength: 25,
    // NEW: highlight dòng lỗi
    createdRow: function(row, data) {
      if (!data.isSuccess) $(row).addClass('row-failed');
    }
  });

  // Hàm áp dụng filter
 async function applyFilter(){
    const uid    = ($('#uidSelect').val()||'').trim();
    const serial = ($('#serialInput').val()||'').trim().toLowerCase();
    const email  = ($('#emailInput').val()||'').trim().toLowerCase();
    const today  = $('#todayOnly').is(':checked');
    const fromV  = ($('#timeFrom').val()||'').trim();
    const toV    = ($('#timeTo').val()||'').trim();
    const status = ($('#statusSelect').val()||'').trim(); // NEW
    
  await loadFinanceStats(uid,email, fromV, toV);
  

    let fromTs = null, toTs = null;
    if (today){
      fromTs = dayjs().startOf('day');
      toTs   = dayjs().endOf('day');
    } else {
      if (fromV) fromTs = parseTs(fromV);
      if (toV)   toTs   = parseTs(toV);
    }

    let filtered = MASTER_ROWS.filter(r=>{
      if (uid && r.uuid !== uid) return false;
      if (serial && !String(r.serial).toLowerCase().includes(serial)) return false;
      if (email && !String(r.email).toLowerCase().includes(email)) return false;
      const t = parseTs(r.time); if (!t.isValid()) return false;
      if (fromTs && t.isBefore(fromTs)) return false;
      if (toTs && t.isAfter(toTs)) return false;
      if (status === 'success' && !r.isSuccess) return false;
      if (status === 'failed' && r.isSuccess) return false;
      return true;
    });

    filtered = filtered.map((x,i)=> ({...x, stt: i+1}));

    dt.clear().rows.add(filtered).draw();

    // KPIs
    const successAmount = filtered.filter(x=> x.isSuccess).reduce((s,x)=> s + Number(x.amount), 0);
    
    const available = successAmount - _lastWithdrawFiltered;
const elBal = document.getElementById('kpiAvailableBalance');
if (elBal){
    console.log(available)
  elBal.textContent = formatMoney(available);
  elBal.style.color = available >= 0 ? '#0a8f49' : '#bf0b0b';
}

const failedAmount  = filtered.filter(x=> !x.isSuccess).reduce((s,x)=> s + Number(x.amount), 0);
const uidSet    = new Set(filtered.map(x=>x.uuid));
const serialSet = new Set(filtered.map(x=>x.serial));
const failCount = filtered.filter(x=>!x.isSuccess).length;

$('#kpiSuccessAmount').text(formatMoney(successAmount));
$('#kpiFailedAmount').text(formatMoney(failedAmount));
$('#kpiUidCount').text(uidSet.size);
$('#kpiSerialCount').text(serialSet.size);
$('#kpiTxCount').text(filtered.length);
$('#kpiFailCount').text(failCount);

    // Charts
    rebuildAllCharts(filtered);
// === Profit từ filtered: chỉ tính success ===
const revByUidMap = new Map();
for (const r of filtered){
  if (!r.isSuccess) continue; // bỏ lỗi
  const uid = String(r.uuid||'');
  revByUidMap.set(uid, (revByUidMap.get(uid)||0) + Number(r.amount));
}

// vốn theo UID từ cache API
const capMap = new Map();
for (const c of _lastCapitalsByUid){
  capMap.set(String(c.uid||''), Number(c.total_capital_usd||0));
}

// mảng profit theo UID
const profitArr = [];
const tienvonArr = [];
for (const [uid, rev] of revByUidMap.entries()){
  const capUsd = capMap.get(uid) || 0;
  tienvonArr.push({ uid, tienvon: Number(capUsd.toFixed(2)) });
  profitArr.push({ uid, profit: Number((rev - capUsd).toFixed(2)) });
}
// nếu có UID có vốn nhưng không có revenue trong filtered -> có thể hiển thị lỗ = -vốn (tuỳ chọn)
for (const [uid, capUsd] of capMap.entries()){
  if (!revByUidMap.has(uid)){
    const filterUid = ($('#uidSelect').val()||'').trim();
    if (!filterUid || filterUid===uid){
      profitArr.push({ uid, profit: Number((-capUsd).toFixed(2)) });
    }
  }
}

// KPI tổng lợi nhuận
const totalProfit = profitArr.reduce((s,x)=> s + x.profit, 0);
const profitEl = document.getElementById('kpiProfitEstimated');
const tienvonEl = document.getElementById('tienvon');
if (profitEl){
  profitEl.textContent = formatMoney(totalProfit);
  profitEl.style.color = totalProfit>=0 ? '#0a8f49' : '#bf0b0b';
}
const totalTienvon = tienvonArr.reduce((s,x)=> s + x.tienvon, 0);
if (tienvonEl){
  tienvonEl.textContent = formatMoney(totalTienvon);
  tienvonEl.style.color = totalProfit <= 0 ? '#0a8f49' : '#bf0b0b';
}

// Vẽ chart Profit (xanh lời, đỏ lỗ)
destroyChart(chartProfitByUid);
const ctxP = document.getElementById('chartProfitByUid');
chartProfitByUid = new Chart(ctxP, {
  type:'bar',
  data:{ labels: profitArr.map(x=>x.uid),
         datasets:[{label:'Profit (USD)', data: profitArr.map(x=>x.profit)}] },
  options:{
    responsive:true,
    plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}} },
    scales:{ y:{beginAtZero:true} }
  }
});
const dsP = chartProfitByUid.data.datasets[0];
dsP.backgroundColor = profitArr.map(x=> x.profit>0 ? 'rgba(0,160,80,0.6)' : (x.profit<0 ? 'rgba(220,30,30,0.6)' : undefined));
dsP.borderColor     = profitArr.map(x=> x.profit>0 ? 'rgba(0,160,80,1)'   : (x.profit<0 ? 'rgba(220,30,30,1)'   : undefined));
dsP.borderWidth     = 1;
chartProfitByUid.update();
const emailForWithdraw = ($('#emailInput').val()||'').trim();
const uidForWithdraw  = ($('#uidSelect').val()||'').trim();

loadFinanceStats(uidForWithdraw,emailForWithdraw, fromV, toV).catch(console.error);



refreshWithdrawList().catch(console.error);
refreshCapitalList().catch(console.error);
  }

  // Nút
  $('#applyFilter').on('click', applyFilter);
  $('#resetFilter').on('click', function(){
    $('#uidSelect').val('').trigger('change');
    $('#serialInput, #emailInput').val('');
    $('#statusSelect').val('');
    $('#todayOnly').prop('checked', false);
    $('#timeFrom, #timeTo').prop('disabled', false).val('');
    applyFilter();
  });
  $('#refreshCharts').on('click', function(){ applyFilter(); });
  $('#refreshAdvanced').on('click', function(){ $('#applyFilter').trigger('click'); });

  // Lắng nghe click hành động (ủy quyền)
  $('#logsTable tbody').on('click', '.btn-mark-fail', async function(){
    const id = Number($(this).data('id'));
    const password = prompt('Nhập mật khẩu để báo lỗi:');
    if (password === null) return;


  const pass = await sha256Hex(password);

    if (pass !== '72d3c92ecf78e2a0603801461c4fc1f5101f8da6aee5d252a4744edc24f7c3a1'){ alert('Sai mật khẩu.'); return; }
    let reason = prompt('Nhập lý do lỗi:');
    if (reason === null) return;
    reason = reason.trim();
    if (!reason){ alert('Lý do không được trống.'); return; }
    await callUpdateFail(id, password, reason, false);
  });
  $('#logsTable tbody').on('click', '.btn-undo-fail', async function(){
    const id = Number($(this).data('id'));
    const password = prompt('Nhập mật khẩu để HỦY lỗi:');
    if (password === null) return;
     const pass = await sha256Hex(password);
    if (pass !== '72d3c92ecf78e2a0603801461c4fc1f5101f8da6aee5d252a4744edc24f7c3a1'){ alert('Sai mật khẩu.'); return; }
    if (!confirm('Xác nhận HỦY lỗi cho nhóm giao dịch này?')) return;
    await callUpdateFail(id, password, '', true);
  });

// mới
(async () => {
  const emailForWithdraw = ($('#emailInput').val()||'').trim();
  const uidForWithdraw  = ($('#uidSelect').val()||'').trim();
  const fromV = ($('#timeFrom').val()||'').trim();
  const toV   = ($('#timeTo').val()||'').trim();

  await loadFinanceStats(uidForWithdraw,emailForWithdraw, fromV, toV); // nạp vốn & KPI rút
  await applyFilter();                                   // giờ tính profit sẽ đúng ngay lần đầu
  await refreshWithdrawList();
  await refreshCapitalList();
})();



  // --- helpers gọi API ---
  async function callUpdateFail(id, password, reason, undo){
    try{
      const fd = new FormData();
      fd.append('id', id);
      fd.append('password', password);
      if (undo) fd.append('undo', '1'); else fd.append('reason', reason);

      const res = await fetch('api_update_failure.php', { method:'POST', body: fd });
      const j = await res.json();
      if (!res.ok || !j.ok){
        alert('Cập nhật thất bại: ' + (j.error || res.status));
        return;
      }
      // Cập nhật MASTER_ROWS theo updated_ids
      const ids = new Set(j.updated_ids || [id]);
      for (const r of MASTER_ROWS){
        if (ids.has(Number(r.id))){
          if (undo){ r.isSuccess = 1; r.reason = null; }
          else { r.isSuccess = 0; r.reason = reason; }
        }
      }
      // render lại
      $('#applyFilter').trigger('click');
      alert((undo? 'Đã hủy lỗi': 'Đã báo lỗi') + ` cho ${ids.size} giao dịch (nhóm ${j.group_info?.from_time || ''} → ${j.group_info?.to_time || ''}).`);
    }catch(e){
      console.error(e);
      alert('Lỗi kết nối API.');
    }
  }
  
  async function refreshWithdrawList(){
  const email = ($('#emailInput').val()||'').trim();
  const fromV = ($('#timeFrom').val()||'').trim();
  const toV   = ($('#timeTo').val()||'').trim();

  const url = new URL('api_finance.php', location.href);
  url.searchParams.set('action','withdrawals.list');
  if (email) url.searchParams.set('email', email);
  if (fromV) url.searchParams.set('from', fromV);
  if (toV)   url.searchParams.set('to', toV);
  url.searchParams.set('limit','100');

  const res = await fetch(url.toString());
  const j = await res.json();
  if (!res.ok || !j.ok){ console.error(j.error||res.statusText); return; }

  const rows = (j.rows||[]).map(r=>({
    ...r,
    _actions: `<button class="btn btn-sm btn-outline-danger btn-del-wd" data-id="${r.id}">Xóa</button>`
  }));

  if (!$.fn.dataTable.isDataTable('#tblWithdrawList')){
    $('#tblWithdrawList').DataTable({
      data: rows,
      columns: [
        {data:'id'},{data:'email'},
        {data:'amount_usd', render:(d)=>Number(d).toFixed(2)},
        {data:'created_at'},{data:'note'},
        {data:'_actions', orderable:false, searchable:false}
      ],
      order:[[0,'desc']], pageLength:10, responsive:true
    });
  } else {
    const dt = $('#tblWithdrawList').DataTable();
    dt.clear().rows.add(rows).draw();
  }
}

async function refreshCapitalList(){
  const uid  = ($('#uidSelect').val()||'').trim();
  const fromV = ($('#timeFrom').val()||'').trim();
  const toV   = ($('#timeTo').val()||'').trim();

  const url = new URL('api_finance.php', location.href);
  url.searchParams.set('action','capitals.list');
  if (uid)  url.searchParams.set('uid', uid);
  if (fromV) url.searchParams.set('from', fromV);
  if (toV)   url.searchParams.set('to', toV);
  url.searchParams.set('limit','100');

  const res = await fetch(url.toString());
  const j = await res.json();
  if (!res.ok || !j.ok){ console.error(j.error||res.statusText); return; }

  const rows = (j.rows||[]).map(r=>({
    ...r,
    _actions: `<button class="btn btn-sm btn-outline-danger btn-del-cap" data-id="${r.id}">Xóa</button>`
  }));

  if (!$.fn.dataTable.isDataTable('#tblCapitalList')){
    $('#tblCapitalList').DataTable({
      data: rows,
      columns: [
        {data:'id'},{data:'uid'},
        {data:'vnd_amount'},
        {data:'usd_amount', render:(d)=>Number(d).toFixed(2)},
        {data:'usd_rate'},{data:'source'},{data:'created_at'},{data:'note'},
        {data:'_actions', orderable:false, searchable:false}
      ],
      order:[[0,'desc']], pageLength:10, responsive:true
    });
  } else {
    const dt = $('#tblCapitalList').DataTable();
    dt.clear().rows.add(rows).draw();
  }
}


$('#tblWithdrawList').on('click', '.btn-del-wd', async function(){
  const id = Number($(this).data('id'));
  const pass = prompt('Nhập mật khẩu để xóa rút tiền:');
  if (pass===null) return;
  const fd = new FormData();
  fd.append('password', pass);
  fd.append('id', String(id));
  const res = await fetch('api_finance.php?action=withdrawal.delete', { method:'POST', body:fd });
  const j = await res.json();
  if (!res.ok || !j.ok){ alert(j.error||'Lỗi API'); return; }
  alert('Đã xóa #' + id);
  await refreshWithdrawList();
  // cập nhật KPI và profit từ filtered
  $('#applyFilter').trigger('click');
});


$('#tblCapitalList').on('click', '.btn-del-cap', async function(){
  const id = Number($(this).data('id'));
  const pass = prompt('Nhập mật khẩu để xóa vốn:');
  if (pass===null) return;
  const fd = new FormData();
  fd.append('password', pass);
  fd.append('id', String(id));
  const res = await fetch('api_finance.php?action=capital.delete', { method:'POST', body:fd });
  const j = await res.json();
  if (!res.ok || !j.ok){ alert(j.error||'Lỗi API'); return; }
  alert('Đã xóa #' + id);
  await refreshCapitalList();
  // nạp lại vốn -> tính lại profit theo filtered
  const emailForWithdraw = ($('#emailInput').val()||'').trim();
  const uidForWithdraw  = ($('#uidSelect').val()||'').trim();
  const fromV = ($('#timeFrom').val()||'').trim();
  const toV   = ($('#timeTo').val()||'').trim();
  await loadFinanceStats(uidForWithdraw,emailForWithdraw, fromV, toV).catch(console.error);
  $('#applyFilter').trigger('click');
});
$('#btnSaveWithdrawal').on('click', async function(){
  const email = ($('#wdEmail').val()||'').trim();
  const amount = Number($('#wdAmount').val()||0);
  const note = ($('#wdNote').val()||'').trim();
  if (!email) return alert('Email không được trống');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return alert('Email không hợp lệ');
  if (!(amount>=0)) return alert('Số tiền không hợp lệ');

  const password = prompt('Nhập mật khẩu để lưu rút tiền:');
  if (password===null) return;

  try{
    const fd = new FormData();
    fd.append('password', password);
    fd.append('email', email);
    fd.append('amount_usd', String(amount));
    fd.append('note', note);

    const res = await fetch('api_finance.php?action=withdrawal.create', { method:'POST', body: fd });
    const j = await res.json();
    if (!res.ok || !j.ok) { alert(j.error || 'Lỗi API'); return; }

    alert('Đã lưu rút tiền (#'+j.id+')');
    // refresh KPI đã rút theo filter hiện tại + bảng danh sách
    const emailForWithdraw = ($('#emailInput').val()||'').trim();
    const uidForWithdraw  = ($('#uidSelect').val()||'').trim();
    const fromV = ($('#timeFrom').val()||'').trim();
    const toV   = ($('#timeTo').val()||'').trim();
    await loadFinanceStats(uidForWithdraw,emailForWithdraw, fromV, toV).catch(console.error);
    await refreshWithdrawList();
    // clear
    $('#wdAmount').val(''); $('#wdNote').val('');
  }catch(e){ console.error(e); alert('Lỗi kết nối'); }
});

$('#btnSaveCapital').on('click', async function(){
  const uid  = ($('#cpUid').val()||'').trim();
  const vnd  = parseInt($('#cpVnd').val()||'-1',10);
  const rate = Number($('#cpRate').val()||'25000');
  const source = ($('#cpSource').val()||'').trim();
  const note   = ($('#cpNote').val()||'').trim();

  if (!uid) return alert('UID không được trống');
  if (!(Number.isInteger(vnd))) return alert('Số tiền VND không hợp lệ');
  if (!(rate>0)) return alert('Tỷ giá không hợp lệ');

  const password = prompt('Nhập mật khẩu để lưu vốn:');
  if (password===null) return;

  try{
    const fd = new FormData();
    fd.append('password', password);
    fd.append('uid', uid);
    fd.append('vnd_amount', String(vnd));
    fd.append('usd_rate', String(rate));
    fd.append('source', source);
    fd.append('note', note);

    const res = await fetch('api_finance.php?action=capital.create', { method:'POST', body: fd });
    const j = await res.json();
    if (!res.ok || !j.ok) { alert(j.error || 'Lỗi API'); return; }

    alert('Đã lưu vốn (#'+j.id+', ~'+ formatMoney(j.usd_amount) +')');
    // reload vốn/Profit
    const emailForWithdraw = ($('#emailInput').val()||'').trim();
    const uidForWithdraw  = ($('#uidSelect').val()||'').trim();
    const fromV = ($('#timeFrom').val()||'').trim();
    const toV   = ($('#timeTo').val()||'').trim();
    await loadFinanceStats(uidForWithdraw,emailForWithdraw, fromV, toV).catch(console.error);
    await refreshCapitalList();
    $('#cpVnd').val(''); $('#cpSource').val(''); $('#cpNote').val('');
  }catch(e){ console.error(e); alert('Lỗi kết nối'); }
});

  

});

// ---------- HÀM CHUNG CHO CHART ----------
function destroyChart(ch){ if (ch) ch.destroy(); }
function aggregateSumByKey(rows, key){
  const map = new Map();
  for (const r of rows){
    const k = String(r[key] ?? '');
    map.set(k, (map.get(k)||0) + Number(r.amount));
  }
  return Array.from(map.entries()).map(([k,v])=>({k,total:v}));
}
function sortLimit(items, order='desc', topN=10){
  items.sort((a,b)=> order==='asc' ? a.total-b.total : b.total-a.total);
  return items.slice(0, Math.max(1, Math.min(50, Number(topN)||10)));
}
function buildBarChart(ctxId, dataItems, title){
  const ctx = document.getElementById(ctxId);
  return new Chart(ctx, {
    type:'bar',
    data:{ labels:dataItems.map(x=>x.k), datasets:[{label:title, data:dataItems.map(x=>x.total)}] },
    options:{
      responsive:true,
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}} },
      scales:{ x:{ticks:{autoSkip:false,maxRotation:45,minRotation:0}}, y:{beginAtZero:true} }
    }
  });
}
function computeSessions(rows, gapMinutes){
  const arr = [...rows].sort((a,b)=> a.uuid!==b.uuid ? (a.uuid<b.uuid?-1:1) : (a.time<b.time?-1:1));
  const sessions=[]; let cur=null;
  for (const r of arr){
    const t = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true);
    if (!cur){ cur={uid:r.uuid,start:t,end:t,total:Number(r.amount),count:1}; continue; }
    if (r.uuid!==cur.uid){ sessions.push(cur); cur={uid:r.uuid,start:t,end:t,total:Number(r.amount),count:1}; continue; }
    const gap = t.diff(cur.end,'minute');
    if (gap<=gapMinutes){ cur.end=t; cur.total+=Number(r.amount); cur.count+=1; }
    else { sessions.push(cur); cur={uid:r.uuid,start:t,end:t,total:Number(r.amount),count:1}; }
  }
  if (cur) sessions.push(cur);
  sessions.sort((a,b)=> a.start.valueOf()-b.start.valueOf());
  return sessions;
}

// ---- Helpers nâng cao ----
function sortByKeyAsc(arr){ return arr.sort((a,b)=> a.k<b.k?-1:(a.k>b.k?1:0)); }
function aggregateByPeriod(rows, period){ // 'day'|'week'|'month'
  const map = new Map();
  for (const r of rows){
    const t = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true);
    if (!t.isValid()) continue;
    let key;
    if (period==='day') key = t.format('YYYY-MM-DD');
    else if (period==='week') key = t.format('YYYY-[W]') + String(t.week()).padStart(2,'0');
    else key = t.format('YYYY-MM'); // month
    map.set(key, (map.get(key)||0) + Number(r.amount));
  }
  const out = Array.from(map.entries()).map(([k,v])=>({k,total:v}));
  return sortByKeyAsc(out);
}
function movingAverage(series, windowSize){
  const w = Math.max(2, Number(windowSize)||4);
  const ma=[]; let sum=0;
  for (let i=0;i<series.length;i++){
    sum += series[i].total;
    if (i>=w) sum -= series[i-w].total;
    ma.push({k: series[i].k, total: i>=w-1 ? (sum/w) : null});
  }
  return ma;
}
function cumulativeSeries(rows){
  const byTime = [...rows].sort((a,b)=> a.time<b.time?-1:1);
  const labels=[]; const values=[]; let sum=0;
  for (const r of byTime){
    sum += Number(r.amount);
    labels.push(dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true).format('YYYY-MM-DD HH:mm'));
    values.push(sum);
  }
  return {labels, values};
}
function topNByDimension(rows, dim, topN=10, order='desc'){
  const agg = aggregateSumByKey(rows, dim);
  agg.sort((a,b)=> order==='asc' ? a.total-b.total : b.total-a.total);
  return agg.slice(0, Math.max(1, Math.min(50, Number(topN)||10)));
}
function multiLineByDimPerDay(rows, dim, topN=5){
  const top = topNByDimension(rows, dim, topN, 'desc').map(x=>x.k);
  const daysSet = new Set();
  const perKey = new Map();
  for (const r of rows){
    const key = String(r[dim] ?? '');
    if (!top.includes(key)) continue;
    const d = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true).format('YYYY-MM-DD');
    daysSet.add(d);
    const mm = perKey.get(key) || new Map();
    mm.set(d, (mm.get(d)||0) + Number(r.amount));
    perKey.set(key, mm);
  }
  const labels = Array.from(daysSet).sort();
  const datasets = [];
  for (const key of top){
    const mm = perKey.get(key) || new Map();
    const data = labels.map(d=> mm.get(d)||0);
    datasets.push({ label:key, data });
  }
  return {labels, datasets};
}
function pieDataByDim(rows, dim, topN=10){
  const top = topNByDimension(rows, dim, topN, 'desc');
  return { labels: top.map(x=>x.k), data: top.map(x=>x.total) };
}
function stackedByDayUid(rows, topUidN=6){
  const topUids = topNByDimension(rows, 'uuid', topUidN, 'desc').map(x=>x.k);
  const daySet = new Set();
  const matrix = new Map();
  for (const r of rows){
    const uid = String(r.uuid||'');
    if (!topUids.includes(uid)) continue;
    const d = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true).format('YYYY-MM-DD');
    daySet.add(d);
    const mm = matrix.get(uid) || new Map();
    mm.set(d, (mm.get(d)||0) + Number(r.amount));
    matrix.set(uid, mm);
  }
  const labels = Array.from(daySet).sort();
  const datasets = topUids.map(uid=>{
    const mm = matrix.get(uid) || new Map();
    const data = labels.map(d=> mm.get(d)||0);
    return { label:uid, data, stack:'stack1' };
  });
  return {labels, datasets};
}
function rankingData(rows, dim, topN=10, order='desc'){
  const top = topNByDimension(rows, dim, topN, order);
  return { labels: top.map(x=>x.k), data: top.map(x=>x.total) };
}

// NEW: gom serial -> số lần lỗi (đếm bản ghi isSuccess=0)
function countFailuresBySerial(rows){
  const map = new Map();
  for (const r of rows){
    if (!r.isSuccess){
      const k = String(r.serial ?? '');
      map.set(k, (map.get(k)||0) + 1);
    }
  }
  return Array.from(map.entries()).map(([k,v])=>({k,total:v}));
}

// ---------- VẼ TOÀN BỘ CHART ----------
function rebuildAllCharts(filtered){
  const order = document.getElementById('chartSort').value || 'desc';
  const topN  = document.getElementById('chartTopN').value || 10;

  // 4.1/4.2/4.3
  const bySerialTop = sortLimit(aggregateSumByKey(filtered,'serial'), order, topN);
  const byUidTop    = sortLimit(aggregateSumByKey(filtered,'uuid'), order, topN);
  const byEmailTop  = sortLimit(aggregateSumByKey(filtered,'email'), order, topN);

  // 4.4 Sessions
  const sessions = computeSessions(filtered, <?php echo intval($SESSION_GAP_MINUTES); ?>);
  const sessLabels = sessions.map(s=> s.start.format('YYYY-MM-DD HH:mm'));
  const sessTotals = sessions.map(s=> s.total);

  // 4.5(a) byDay
  const byDayMap = new Map();
  for (const r of filtered){
    const d = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true).format('YYYY-MM-DD');
    byDayMap.set(d, (byDayMap.get(d)||0) + Number(r.amount));
  }
  const byDay = Array.from(byDayMap.entries()).map(([k,v])=>({k,total:v})).sort((a,b)=> a.k<b.k?-1:1);

  // 4.5(b) byHour
  const byHour = Array(24).fill(0);
  for (const r of filtered){
    const h = dayjs(r.time, "YYYY-MM-DD HH:mm:ss", true).hour();
    byHour[h] += Number(r.amount);
  }

  // --- Nâng cao ---
  const aggPeriod = (document.getElementById('aggPeriod')?.value || 'week');
  const maWindow  = Number(document.getElementById('maWindow')?.value || 4);
  const multiDim  = (document.getElementById('multiSeriesDim')?.value || 'uuid');
  const pieRankDim= (document.getElementById('pieRankDim')?.value || 'uuid');

  // A) Tổng $ theo tuần/tháng + MA
  const seriesAgg = aggregateByPeriod(filtered, aggPeriod==='month'?'month':'week');
  const labelsAgg = seriesAgg.map(x=>x.k);
  const dataAgg   = seriesAgg.map(x=>x.total);
  const maSeries  = movingAverage(seriesAgg, maWindow);
  const dataMA    = maSeries.map(x=> x.total);

  // B) Cumulative
  const cum = cumulativeSeries(filtered);

  // C) Multi-line Top N theo dim (per day)
  const multi = multiLineByDimPerDay(filtered, multiDim, topN);

  // D) Pie/Donut
  const pie = pieDataByDim(filtered, pieRankDim, topN);

  // E) Stacked day / UID
  const stacked = stackedByDayUid(filtered, Math.min(8, Number(topN)||8));

  // F) Ranking
  const rank = rankingData(filtered, pieRankDim, topN, order);

  // G) NEW: lỗi theo Serial
  const failSerial = sortLimit(countFailuresBySerial(filtered), 'desc', topN);

  // Destroy trước khi vẽ lại
  destroyChart(chartBySerial); destroyChart(chartByUid); destroyChart(chartByEmail);
  destroyChart(chartSessions); destroyChart(chartByDay); destroyChart(chartByHour);
  destroyChart(chartTrendAgg); destroyChart(chartCumulative); destroyChart(chartMultiSeries);
  destroyChart(chartPie); destroyChart(chartDonut); destroyChart(chartStackedByDayUid); destroyChart(chartRanking);
  destroyChart(chartFailBySerial);

  // --- Charts CƠ BẢN ---
  chartBySerial  = buildBarChart('chartBySerial', bySerialTop, 'Tổng $ / Serial');
  chartByUid     = buildBarChart('chartByUid', byUidTop, 'Tổng $ / UID');
  chartByEmail   = buildBarChart('chartByEmail', byEmailTop, 'Tổng $ / Email');

  chartSessions = new Chart(document.getElementById('chartSessions'), {
    type:'bar',
    data:{ labels:sessLabels, datasets:[{label:'Tổng $ / đợt', data:sessTotals}]},
    options:{ responsive:true, plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ x:{ticks:{autoSkip:true}}, y:{beginAtZero:true} } }
  });
  chartByDay = new Chart(document.getElementById('chartByDay'), {
    type:'line',
    data:{ labels:byDay.map(x=>x.k), datasets:[{label:'Tổng $ theo ngày', data:byDay.map(x=>x.total), fill:false, tension:0.3}]},
    options:{ responsive:true, plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ y:{beginAtZero:true} } }
  });
  chartByHour = new Chart(document.getElementById('chartByHour'), {
    type:'line',
    data:{ labels:Array.from({length:24},(_,i)=>i.toString().padStart(2,'0')+':00'), datasets:[{label:'Tổng $ theo giờ', data:byHour, fill:false, tension:0.3}]},
    options:{ responsive:true, plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ y:{beginAtZero:true} } }
  });

  // --- Charts NÂNG CAO ---
  chartTrendAgg = new Chart(document.getElementById('chartTrendAgg'), {
    type:'line',
    data:{ labels:labelsAgg, datasets:[
      { label:'Tổng $ theo kỳ', data:dataAgg, fill:false, tension:0.3 },
      { label:`MA(${maWindow})`, data:dataMA, fill:false, tension:0.2 }
    ]},
    options:{ responsive:true, plugins:{ legend:{display:true}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ y:{beginAtZero:true} } }
  });
  chartCumulative = new Chart(document.getElementById('chartCumulative'), {
    type:'line',
    data:{ labels:cum.labels, datasets:[{ label:'Tích lũy tổng $', data:cum.values, fill:false, tension:0.25 }]},
    options:{ responsive:true, plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ y:{beginAtZero:true} } }
  });
  chartMultiSeries = new Chart(document.getElementById('chartMultiSeries'), {
    type:'line',
    data:{ labels:multi.labels, datasets: multi.datasets.map(ds=>({...ds, fill:false, tension:0.25})) },
    options:{ responsive:true, plugins:{ legend:{display:true}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}}}, scales:{ y:{beginAtZero:true} } }
  });
  chartPie = new Chart(document.getElementById('chartPie'), {
    type:'pie',
    data:{ labels:pie.labels, datasets:[{ data:pie.data }]},
    options:{ responsive:true, plugins:{ legend:{position:'bottom'}, tooltip:{callbacks:{label:(tt)=>` ${tt.label}: ${formatMoney(tt.parsed)}`}} } }
  });
  chartDonut = new Chart(document.getElementById('chartDonut'), {
    type:'doughnut',
    data:{ labels:pie.labels, datasets:[{ data:pie.data }]},
    options:{ responsive:true, plugins:{ legend:{position:'bottom'}, tooltip:{callbacks:{label:(tt)=>` ${tt.label}: ${formatMoney(tt.parsed)}`}} } }
  });
  chartStackedByDayUid = new Chart(document.getElementById('chartStackedByDayUid'), {
    type:'bar',
    data:{ labels:stacked.labels, datasets:stacked.datasets },
    options:{
      responsive:true,
      plugins:{ legend:{position:'bottom'}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.y)}} },
      scales:{ x:{stacked:true}, y:{stacked:true, beginAtZero:true} }
    }
  });
  chartRanking = new Chart(document.getElementById('chartRanking'), {
    type:'bar',
    data:{ labels:rank.labels, datasets:[{ label:`Top N (${(document.getElementById('pieRankDim')?.value||'uuid').toUpperCase()})`, data:rank.data }] },
    options:{
      indexAxis:'y', responsive:true,
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(tt)=>' '+formatMoney(tt.parsed.x)}} },
      scales:{ x:{beginAtZero:true} }
    }
  });

  // NEW: Chart lỗi theo Serial (đếm)
  chartFailBySerial = new Chart(document.getElementById('chartFailBySerial'), {
    type:'bar',
    data:{ labels:failSerial.map(x=>x.k), datasets:[{ label:'Số lần lỗi', data:failSerial.map(x=>x.total) }] },
    options:{
      responsive:true,
      plugins:{ legend:{display:false} },
      scales:{ y:{beginAtZero:true, ticks:{precision:0}} }
    }
  });
  
  
 
  
}

// --- nhỏ: escape html cho cột lý do ---
function escapeHtml(s){
  return String(s).replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]));
}
</script>

<script>
$(function () {
  // ====== Config ======
  const API_IDENT = 'api_identification.php';

  // ====== Select2 cho Label (cho phép gõ tự do) ======
  

  // ====== DataTable ======
  const identTable = $('#identificationTable').DataTable({
    responsive: true,
    searching: true,
    paging: true,
    info: true,
    lengthChange: true,
    order: [[0, 'desc']], // id desc
    columns: [
      { data: 'id' },
      { data: 'uuid' },
      { data: 'label' },
      { data: 'note',
        render: function(data) {
          if (!data) return '';
          // rút gọn cho gọn bảng
          return data.length > 120 ? data.substring(0,117) + '...' : data;
        }
      },
      {
        data: null,
        orderable: false,
        render: function (row) {
          return `
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-primary btn-edit" data-id="${row.id}">Sửa</button>
              <button class="btn btn-outline-danger btn-del" data-id="${row.id}">Xóa</button>
            </div>`;
        }
      }
    ]
  });

  function loadIdentification(params = {}) {
    // Gọi list
    $.ajax({
      url: API_IDENT,
      method: 'GET',
      data: Object.assign({ action: 'list', limit: 1000, offset: 0 }, params),
      dataType: 'json'
    }).done(function (res) {
      if (!res.ok) return alert(res.error || 'Lỗi tải danh sách');
      identTable.clear().rows.add(res.data.items || []).draw();
    }).fail(function (xhr) {
      alert('Không tải được dữ liệu Identification');
    });
  }

  // Lần đầu tải
  loadIdentification();

  // Nút tải lại
  $('#btnRefreshIdentification').on('click', function() {
    loadIdentification();
  });

  // ====== Modal Create ======
  const identModalEl = document.getElementById('identModal');
  const identModal = new bootstrap.Modal(identModalEl);

  $('#btnAddIdentification').on('click', function() {
  $('#identModalLabel').text('Thêm định danh');
  $('#identMode').val('create');
  $('#identId').prop('disabled', false).val('');
  $('#identUuid').val('');
  $('#identLabel').val('');   // <-- input thường
  $('#identNote').val('');
  identModal.show();
});

  // ====== Edit: mở modal và nạp dữ liệu ======
  $('#identificationTable').on('click', '.btn-edit', function() {
  const id = $(this).data('id');
  $.ajax({
    url: API_IDENT,
    method: 'GET',
    data: { action: 'get', id },
    dataType: 'json'
  }).done(function(res) {
    if (!res.ok) return alert(res.error || 'Không lấy được bản ghi');
    const r = res.data;
    $('#identModalLabel').text('Sửa định danh');
    $('#identMode').val('update');
    $('#identId').val(r.id).prop('disabled', true);
    $('#identUuid').val(r.uuid);
    $('#identLabel').val(r.label); // <-- input thường
    $('#identNote').val(r.note || '');
    identModal.show();
  }).fail(function() {
    alert('Lỗi tải chi tiết Identification');
  });
});

  // ====== Delete ======
  $('#identificationTable').on('click', '.btn-del', function() {
    const id = $(this).data('id');
    if (!confirm(`Xóa định danh id = ${id}?`)) return;
    $.ajax({
      url: API_IDENT,
      method: 'POST',
      data: { action: 'delete', id },
      dataType: 'json'
    }).done(function(res) {
      if (!res.ok) return alert(res.error || 'Xóa thất bại');
      loadIdentification();
    }).fail(function() {
      alert('Không xóa được bản ghi');
    });
  });

  // ====== Submit (Create/Update) ======
  $('#identForm').on('submit', function(e) {
  e.preventDefault();
  const mode  = $('#identMode').val();
  const id    = Number($('#identId').val());
  const uuid  = $('#identUuid').val().trim();
  const label = $('#identLabel').val().trim();  // <-- input thường
  const note  = $('#identNote').val();

  if (!id || !uuid || !label) {
    alert('Vui lòng nhập đủ: ID, UUID, Label');
    return;
  }

  const payload = { id, uuid, label, note };
  const action  = (mode === 'update') ? 'update' : 'create';

  $.ajax({
    url: API_IDENT,
    method: 'POST',
    data: Object.assign({ action }, payload),
    dataType: 'json'
  }).done(function(res) {
    if (!res.ok) return alert(res.error || 'Lưu thất bại');
    identModal.hide();
    loadIdentification();
  }).fail(function(xhr) {
    let msg = 'Không thể lưu';
    if (xhr?.responseJSON?.error) msg = xhr.responseJSON.error;
    alert(msg);
  });
});

});
</script>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
