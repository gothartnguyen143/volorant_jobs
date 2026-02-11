<?php
// Rotation page view - Gaming/Valorant Style
require_once __DIR__ . '/../../utils/Asset.php';
// Debug settings
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

?>

<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Teko:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= queryAssetWithVersion('/pages/rotation/rotation.css') ?>">

<div class="gaming-wrapper">
  <div class="bg-layer">
    <video autoplay muted loop playsinline class="bg-video">
      <source src="https://assets.contentstack.io/v3/assets/bltb6530b271fddd0b1/blt7c224254bc81f868/6530a614d3a246944b0292f7/VAL_Ep7_Act3_Cinematic_30s_1920x1080.mp4" type="video/mp4">
    </video>
    <div class="bg-overlay"></div>
    <div class="bg-grid"></div>
  </div>

  <div class="main-container">
    <div class="header-branding">
      <h1 class="brand-name" data-text="DƯƠNG ANH TUẤN">DƯƠNG ANH TUẤN</h1>
      <div class="brand-sub">DỊCH VỤ CHO THUÊ ACC VALORANT</div>
    </div>

    <div class="content-layout">
      <div class="col-wheel">
        <div class="wheel-outer-ring">
            <div class="wheel-pointer-container">
                <div class="pointer-triangle"></div>
            </div>
            
            <div class="wheel-rotator" id="wheel">
              <svg viewBox="0 0 200 200" class="wheel-svg">
                <?php
                $baseRotation = -90; 
                foreach ($segments as $index => $segment):
                  $startAngle = $baseRotation + ($index * $segmentAngle);
                  $endAngle = $baseRotation + (($index + 1) * $segmentAngle);
                  $startRad = deg2rad($startAngle);
                  $endRad = deg2rad($endAngle);
                  $radius = 100;
                  // Tính toán path
                  $x1 = 100 + $radius * cos($startRad);
                  $y1 = 100 + $radius * sin($startRad);
                  $x2 = 100 + $radius * cos($endRad);
                  $y2 = 100 + $radius * sin($endRad);
                  $pathData = sprintf('M 100 100 L %.3f %.3f A 100 100 0 0 1 %.3f %.3f Z', $x1, $y1, $x2, $y2);
                  
                  // Tính vị trí text
                  $textRadius = 60; // Đẩy chữ gần tâm hơn (giảm từ 68 xuống 50)
                  $textAngle = $startAngle + ($segmentAngle / 2);
                  $textRad = deg2rad($textAngle);  
                  $textX = 100 + $textRadius * cos($textRad);
                  $textY = 100 + $textRadius * sin($textRad);
                  $rotateText = $textAngle; // Xoay chữ theo hướng segment
                  $label = htmlspecialchars($segment['label'], ENT_QUOTES, 'UTF-8');
                  
                  // Logic màu xen kẽ: Xanh đậm và Đen xanh
                  $isEven = $index % 2 == 0;
                  // Chúng ta sẽ dùng CSS class để quản lý màu thay vì inline style để dễ chỉnh theme
                  $segClass = $isEven ? 'seg-even' : 'seg-odd';
                ?>
                  <path
                    class="wheel-segment <?= $segClass ?>"
                    data-index="<?= $index + 1 ?>"
                    data-label="<?= $label ?>"
                    d="<?= $pathData ?>"
                  ></path>
                  <text
                    class="wheel-label"
                    x="<?= $textX ?>"
                    y="<?= $textY ?>"
                    transform="rotate(<?= $rotateText ?>, <?= $textX ?>, <?= $textY ?>)"
                    text-anchor="middle"
                    dominant-baseline="middle"
                  ><?= $label ?></text>
                <?php endforeach; ?>
                <circle cx="100" cy="100" r="14" class="center-cap-outer" />
                <circle cx="100" cy="100" r="6" class="center-cap-inner" />
              </svg>
            </div>
        </div>
      </div>

      <div class="col-info">
        
        <div class="status-panel">
            <div class="panel-decor-tl"></div>
            <div class="panel-decor-br"></div>
            <div class="status-label">STATUS: READY TO SPIN</div>
            <div class="status-value status-main"><?= htmlspecialchars($defaultPrize, ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <button id="spin-btn" class="btn-valorant">
            <span class="btn-text">QUAY NGAY</span>
            <div class="btn-glare"></div>
        </button>

        <div class="rules-box">
            <div class="rules-header">
                <span class="line-decor"></span>
                <h3>LƯU Ý DỊCH VỤ</h3>
            </div>
            <ul class="rules-list">
                <li>
                    <span class="num">01</span>
                    <span class="desc">Thuê qua app: <b>AweSun, UltraView...</b></span>
                </li>
                <li>
                    <span class="num">02</span>
                    <span class="desc">Khuyến mãi cực lớn cho <b>Khách Quen</b></span>
                </li>
                <li>
                    <span class="num">03</span>
                    <span class="desc">Giá thuê <b>Cạnh Tranh</b> nhất thị trường</span>
                </li>
                <li>
                    <span class="num">04</span>
                    <span class="desc">Cập nhật <b>Acc Skin mới</b> liên tục</span>
                </li>
            </ul>
        </div>

        <div class="support-area">
            <div class="supp-left">
                <div class="supp-label">SUPPORT LINE</div>
                <div class="supp-phone">0779791102</div>
            </div>
            <div class="supp-right">
                <div class="supp-time">VALORANT TIME</div>
                <div class="supp-author">BY DUONG ANH TUAN</div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="<?= queryAssetWithVersion('/pages/rotation/rotation.js') ?>" defer></script>