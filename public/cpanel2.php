<?php
/****************************************************
 * index.php - Log rút tiền (1 file duy nhất, full)
 * Yêu cầu PHP >= 7.4, SQLite PDO bật sẵn.
 * DB: data.db, bảng logs như đề bài.
 ****************************************************/

ini_set('display_errors', 1);
error_reporting(E_ALL);

/** ------------------ Cấu hình ------------------ **/
$dbFile = __DIR__ . '/data.db';
$SESSION_GAP_MINUTES = 60;  // 4.4: cùng 1 uid, cách nhau <= 60 phút => 1 đợt rút tiền
$MERGE_GAP_MINUTES   = 90;  // Yêu cầu: cùng uuid+serial, cách nhau <= 90 phút => gộp 1, lấy amount cao nhất

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
    $stmt = $pdo->query("SELECT id, uuid, email, spaces, amount, time, serial FROM logs ORDER BY uuid ASC, serial ASC, time ASC");
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
    $rowsForJs[] = [
        'stt'=>$idx+1,
        'uuid'=>$r['uuid'],
        'serial'=>$r['serial'],
        'amount'=>floatval($r['amount']),
        'email'=>$r['email'],
        'spaces'=>intval($r['spaces']),
        'time'=>$r['time'],
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
    <div class="col-md-3"><div class="kpi"><div>💰</div><div><div class="lbl">Tổng Amount</div><div class="num" id="kpiTotalAmount">$0</div></div></div></div>
    <div class="col-md-3"><div class="kpi"><div>🧑‍💻</div><div><div class="lbl">Số UID</div><div class="num" id="kpiUidCount">0</div></div></div></div>
    <div class="col-md-3"><div class="kpi"><div>🔌</div><div><div class="lbl">Số Serial</div><div class="num" id="kpiSerialCount">0</div></div></div></div>
    <div class="col-md-3"><div class="kpi"><div>🧾</div><div><div class="lbl">Số Giao dịch</div><div class="num" id="kpiTxCount">0</div></div></div></div>
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
        <div class="col-12 d-flex gap-2">
          <button type="button" id="applyFilter" class="btn btn-brand">Áp dụng</button>
          <button type="button" id="resetFilter" class="btn btn-outline-brand">Xóa lọc</button>
        </div>
      </form>
    </div>
  </div>

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
              <th>STT</th><th>UID</th><th>Serial</th><th>Amount ($)</th><th>Email</th><th>Spaces</th><th>Time</th>
            </tr>
          </thead>
          <tbody></tbody>
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
  </div>

  <footer class="mt-5 text-center">
    <div class="small-muted">Đã áp dụng gộp trùng: <strong>uuid + serial</strong> trong <strong>90 phút</strong> (lấy amount cao nhất). Biểu đồ đợt rút tiền: gap ≤ <strong>60 phút</strong>/UID.</div>
  </footer>
</div>

<script>
// ---------- DỮ LIỆU PHP -> JS ----------
const MASTER_ROWS = <?php echo json_encode($rowsForJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

// ---------- TIỆN ÍCH ----------
const PARSE_FMT = "YYYY-MM-DD HH:mm:ss";
function parseTs(s){ return dayjs(s, PARSE_FMT, true); }
function formatMoney(x){ return '$'+(Number(x).toLocaleString(undefined,{maximumFractionDigits:2})); }

// ---------- BIẾN CHART ----------
let chartBySerial=null, chartByUid=null, chartByEmail=null, chartSessions=null, chartByDay=null, chartByHour=null;
// Biểu đồ mới
let chartTrendAgg=null, chartCumulative=null, chartMultiSeries=null, chartPie=null, chartDonut=null, chartStackedByDayUid=null, chartRanking=null;

// ---------- KHỞI TẠO UI ----------
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
      {data:'time'}
    ],
    order: [[6,'desc']],
    responsive: true,
    pageLength: 25
  });

  // Hàm áp dụng filter
  function applyFilter(){
    const uid    = ($('#uidSelect').val()||'').trim();
    const serial = ($('#serialInput').val()||'').trim().toLowerCase();
    const email  = ($('#emailInput').val()||'').trim().toLowerCase();
    const today  = $('#todayOnly').is(':checked');
    const fromV  = ($('#timeFrom').val()||'').trim();
    const toV    = ($('#timeTo').val()||'').trim();

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
      return true;
    });

    filtered = filtered.map((x,i)=> ({...x, stt: i+1}));

    dt.clear().rows.add(filtered).draw();

    // KPIs
    const totalAmount = filtered.reduce((s,x)=>s+Number(x.amount),0);
    const uidSet    = new Set(filtered.map(x=>x.uuid));
    const serialSet = new Set(filtered.map(x=>x.serial));
    $('#kpiTotalAmount').text(formatMoney(totalAmount));
    $('#kpiUidCount').text(uidSet.size);
    $('#kpiSerialCount').text(serialSet.size);
    $('#kpiTxCount').text(filtered.length);

    // Charts
    rebuildAllCharts(filtered);
  }

  // Nút
  $('#applyFilter').on('click', applyFilter);
  $('#resetFilter').on('click', function(){
    $('#uidSelect').val('').trigger('change');
    $('#serialInput, #emailInput').val('');
    $('#todayOnly').prop('checked', false);
    $('#timeFrom, #timeTo').prop('disabled', false).val('');
    applyFilter();
  });
  $('#refreshCharts').on('click', function(){ applyFilter(); });
  $('#refreshAdvanced').on('click', function(){ $('#applyFilter').trigger('click'); });

  // Lần đầu
  applyFilter();
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
    else if (period==='week') key = t.format('YYYY-[W]') + String(t.week()).padStart(2,'0'); // weekOfYear plugin
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

  // Destroy trước khi vẽ lại
  destroyChart(chartBySerial); destroyChart(chartByUid); destroyChart(chartByEmail);
  destroyChart(chartSessions); destroyChart(chartByDay); destroyChart(chartByHour);
  destroyChart(chartTrendAgg); destroyChart(chartCumulative); destroyChart(chartMultiSeries);
  destroyChart(chartPie); destroyChart(chartDonut); destroyChart(chartStackedByDayUid); destroyChart(chartRanking);

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
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
