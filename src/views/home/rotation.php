<?php
// Rotation page view - follows the same structure/pattern as hero_section.php
require_once __DIR__ . '/../../utils/Asset.php';
// Temporary debug: enable error display for this page only
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Debug marker to confirm this file is included in rendered HTML
echo "\n";

// Danh sách phần thưởng và màu sắc tương ứng (chiều kim đồng hồ, xuất phát từ đỉnh)
// Được load từ database qua RotationService
// $segments đã được extract từ controller

// $segmentCount, $segmentAngle, $defaultPrize đã được extract từ controller
?>

<link rel="stylesheet" href="<?= queryAssetWithVersion('/pages/rotation/rotation.css') ?>">

<div class="relative min-h-screen overflow-hidden z-80 bg-rotation-video">
  <div class="absolute inset-0 z-0 rotation-video-wrap">
    <video autoplay muted loop playsinline class="w-full h-full object-cover opacity-60 rotation-bg-video">
      </video>
    <div class="absolute inset-0 rotation-overlay bg-gradient-to-r from-black/60 via-transparent to-black/60"></div>
  </div>

  <div class="relative z-10 rotation-container">
    <div class="rotation-layout">
      <div class="wheel-column">
        <div class="wheel-shell" data-segment-count="<?= $segmentCount ?>">
          <div class="wheel-svg-wrapper">
            <div id="wheel" class="wheel-rotator">
              <svg viewBox="0 0 200 200" class="wheel-svg" role="img" aria-label="Vòng quay may mắn">
                <?php
                $baseRotation = -90; // start the first slice at the top (north)
                foreach ($segments as $index => $segment):
                  $startAngle = $baseRotation + ($index * $segmentAngle);
                  $endAngle = $baseRotation + (($index + 1) * $segmentAngle);
                  $startRad = deg2rad($startAngle);
                  $endRad = deg2rad($endAngle);
                  $radius = 100;
                  $textRadius = 65;
                  $x1 = 100 + $radius * cos($startRad);
                  $y1 = 100 + $radius * sin($startRad);
                  $x2 = 100 + $radius * cos($endRad);
                  $y2 = 100 + $radius * sin($endRad);
                  $pathData = sprintf('M 100 100 L %.3f %.3f A 100 100 0 0 1 %.3f %.3f Z', $x1, $y1, $x2, $y2);
                  $textAngle = $startAngle + ($segmentAngle / 2);
                  $textRad = deg2rad($textAngle);
                  $textX = 100 + $textRadius * cos($textRad);
                  $textY = 100 + $textRadius * sin($textRad);
                  $rotateText = $textAngle;
                  $label = htmlspecialchars($segment['label'], ENT_QUOTES, 'UTF-8');
                ?>
                  <path
                    class="wheel-segment"
                    data-index="<?= $index + 1 ?>"
                    data-label="<?= $label ?>"
                    d="<?= $pathData ?>"
                    fill="<?= $segment['color'] ?>"
                    stroke="rgba(255,255,255,0.25)"
                    stroke-width="1.5"
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
                <circle cx="100" cy="100" r="12" fill="#fff" opacity="0.8"></circle>
              </svg>
              <div class="wheel-center-cap">
                <button id="spin-btn" class="wheel-center-btn">Quay</button>
              </div>
            </div>
          </div>
          <div class="wheel-pointer-top" aria-hidden="true"></div>
        </div>
      </div>

      <div class="rotation-info">
        <div class="status-area">
          <div class="status-top">STATUS: READY TO SPIN</div>
          <div class="status-main"><?= htmlspecialchars($defaultPrize, ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div class="input-area mb-4">
          <input type="text" id="identifier-input" placeholder="Nhập email hoặc số điện thoại" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button class="spin-btn-large">QUAY NGAY</button>

        <div class="rules-panel">
          <div class="rules-title">LƯU Ý DỊCH VỤ</div>
          <ol class="rules-list">
            <li><span>01</span> Thuê qua app: AweSun, UltraView...</li>
            <li><span>02</span> Khuyến mãi cực lớn cho Khách Quen</li>
            <li><span>03</span> Giá thuê cạnh tranh nhất thị trường</li>
            <li><span>04</span> Cập nhật Acc Skin mới liên tục</li>
          </ol>
        </div>

        <div class="support-bar">SUPPORT LINE <span class="phone">0779791102</span></div>
      </div>
    </div>
  </div>
</div>

<script src="<?= queryAssetWithVersion('/pages/rotation/rotation.js') ?>" defer></script>