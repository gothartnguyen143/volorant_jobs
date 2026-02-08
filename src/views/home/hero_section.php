<div class="relative min-h-screen overflow-hidden z-80">
  <div class="absolute inset-0 z-0">
    <video autoplay muted loop playsinline class="w-full h-full object-cover">
      <source src="/videos/UI/intro-bg.mp4" type="video/mp4" />
    </video>
    <div class="absolute inset-0 bg-black/40"></div>
  </div>

  <!-- Intro Header Nav (full-width bar, centered content) -->
  <div class="absolute inset-x-0 top-0 z-30">
    <nav class="w-full bg-black/60 backdrop-blur-sm px-2 py-2 shadow-lg">
      <div class="max-w-[1200px] mx-auto w-full flex items-center justify-center gap-6 text-white font-medium">
        <a href="/thong-tin" class="hover:underline">Thông tin trò chơi</a>
        <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" class="hover:underline" aria-label="Mở Facebook">Facebook</a>
        <a href="https://zalo.me" target="_blank" rel="noopener noreferrer" class="hover:underline" aria-label="Mở Zalo">Zalo</a>
        <a href="https://discord.com" target="_blank" rel="noopener noreferrer" class="hover:underline" aria-label="Mở Discord">Discord</a>
        <a href="/sale" target="_blank" rel="noopener noreferrer" class="hover:underline" aria-label="Mở Sale">Sale</a>
      </div>
    </nav>
  </div>

  <div class="relative z-10 min-h-screen flex items-center font-bold">
    <div class="w-full px-2 pb-[60px] pt-[30px] min-[1900px]:px-[350px] min-[1400px]:px-[240px] min-[1200px]:px-[200px]">
      <div class="w-full relative z-[90]">
        <div class="text-center mb-4 relative mt-8">
          <div class="absolute inset-0 pointer-events-none">
            <?php
            $particles = [];
            for ($i = 0; $i < 50; $i++) {
                $topPercent = rand(5, 95);
                $delay = ($topPercent / 95) * 5; // delay từ 0.26s đến 5s, hạt trên xuất hiện sớm hơn
                $particles[] = ['top' => $topPercent . '%', 'delay' => number_format($delay, 2) . 's'];
            }
            foreach ($particles as $particle) {
                echo '<div class="particle" style="top: ' . $particle['top'] . '; left: 0; animation-delay: ' . $particle['delay'] . ';"></div>';
            }
            ?>
          </div>
          <h1 class="text-[3em] leading-tight flex flex-row items-center justify-center text-center uppercase tracking-wider whitespace-nowrap italic" style="font-family: 'Orbitron', monospace; filter: brightness(1.3) contrast(1.2);">
            <span class="font-medium text-gray-100" style="text-shadow: 0 0 8px rgba(0, 240, 255, 1), 0 0 16px rgba(0, 240, 255, 0.9), 0 0 24px rgba(255, 0, 255, 0.7); -webkit-text-stroke: 1px rgba(255, 255, 255, 0.5);">
              DUONG ANH TUAN
            </span>
            <span class="text-[0.8em] text-red-500 font-bold mx-2" style="text-shadow: 0 0 8px rgba(255, 59, 59, 1), 0 0 16px rgba(255, 59, 59, 0.9); -webkit-text-stroke: 1px rgba(255, 0, 0, 0.5);">
              X
            </span>
            <span class="font-bold text-cyan-400" style="text-shadow: 0 0 8px rgba(0, 240, 255, 1), 0 0 16px rgba(0, 240, 255, 0.9), 0 0 24px rgba(255, 0, 255, 0.7); -webkit-text-stroke: 1px rgba(0, 255, 255, 0.5);">
              VALORANTIME
            </span>
          </h1>
          <div class="w-80 h-1 bg-gradient-to-r from-yellow-400 via-yellow-300 to-yellow-500 mx-auto rounded-full lightning-bar" style="box-shadow: 0 0 20px rgba(255, 215, 0, 0.8), 0 0 40px rgba(255, 255, 0, 0.6), 0 0 60px rgba(255, 215, 0, 0.4);"></div>
        </div>

        <div class="bg-slate-950 p-2 rounded-xl relative overflow-hidden font-sans">
  <div class="grid min-[1170px]:grid-cols-3 min-[600px]:grid-cols-2 grid-cols-1 gap-y-2 gap-x-2 items-start text-[1.22em]">

    <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl px-2 py-2 border border-sky-500/30 shadow-[0_0_10px_rgba(14,165,233,0.15)] flex-1 h-full flex flex-col justify-between group hover:border-sky-400 transition-colors duration-300">
      
      <h3 class="text-[1.15em] font-bold text-sky-400 mb-2 flex items-center justify-center gap-2 text-center uppercase border-b border-sky-500/20 pb-2" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 8px rgba(56, 189, 248, 0.6);">
        Giá Máy Nét <span class="text-[0.7em] normal-case opacity-80">(Tất cả)</span>
      </h3>

      <div class="space-y-2 flex-1" style="font-size: 0.8em;">
        <div class="flex items-center justify-between px-3 py-1 bg-sky-950/30 rounded-lg border border-sky-500/10 hover:bg-sky-500/10 transition-all duration-300">
          <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">20K</span>
          <span class="text-sky-200">- 2 Giờ</span>
        </div>
        
        <div class="flex items-center justify-between px-3 py-1 bg-sky-950/30 rounded-lg border border-sky-500/10 hover:bg-sky-500/10 transition-all duration-300 relative overflow-hidden">
          <div class="absolute right-0 top-0 h-full w-[2px] bg-yellow-400 shadow-[0_0_5px_rgba(250,204,21,0.8)]"></div>
          <div class="flex items-center gap-2">
            <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">30K</span>
            <span class="text-sky-200">- 3 Giờ</span>
          </div>
          <span class="text-yellow-400 font-bold text-[0.9em] animate-pulse">+ 1 Giờ</span>
        </div>

        <div class="h-[1px] bg-sky-500/20 my-2"></div>

        <div class="flex items-center justify-between px-2 py-1 text-sky-400 w-full">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span class="font-bold text-sky-400" style="font-family: 'Orbitron', monospace;">Combo đêm</span>
            </div>
            <div class="text-right">
                <span class="font-bold text-white text-[1.1em] block leading-none" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 5px rgba(56, 189, 248, 0.8);">50k</span>
                <span class="text-[0.7em] text-sky-500/80 block leading-none">(22h-7h)</span>
            </div>
        </div>

        <div class="flex items-center justify-between px-2 py-1 text-cyan-400 w-full">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                <span class="font-bold text-cyan-400" style="font-family: 'Orbitron', monospace;">Combo ngày</span>
            </div>
            <span class="font-bold text-white text-[1.1em]" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 5px rgba(34, 211, 238, 0.8);">120k</span>
        </div>
      </div>
    </div>

    <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl px-2 py-2 border border-yellow-500/30 shadow-[0_0_10px_rgba(234,179,8,0.15)] flex-1 h-full flex flex-col justify-between group hover:border-yellow-400 transition-colors duration-300">
      
      <h3 class="text-[1.15em] font-bold text-yellow-400 mb-2 flex flex-col items-center justify-center text-center uppercase border-b border-yellow-500/20 pb-2" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 8px rgba(234, 179, 8, 0.6);">
        <span>Giá Máy Nhà</span>
        <span class="text-[0.6em] normal-case opacity-80 text-yellow-200/70 tracking-wide">(Tất cả + Only Máy nhà)</span>
      </h3>

      <div class="space-y-2 mb-2 flex-1" style="font-size: 0.8em;">
        <div class="flex items-center justify-between px-3 py-1 bg-yellow-950/30 rounded-lg border border-yellow-500/10 hover:bg-yellow-500/10 transition-all duration-300">
            <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">20K</span>
            <span class="text-yellow-100">- 2 Giờ</span>
        </div>
        <div class="flex items-center justify-between px-3 py-1 bg-yellow-950/30 rounded-lg border border-yellow-500/10 hover:bg-yellow-500/10 transition-all duration-300">
            <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">30K</span>
            <span class="text-yellow-100">- 3 Giờ</span>
        </div>

        <div class="h-[1px] bg-yellow-500/20 my-2"></div>

        <div class="flex items-center justify-between px-2 py-1 text-sky-400 w-full">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span class="font-bold text-sky-400" style="font-family: 'Orbitron', monospace;">Combo đêm</span>
            </div>
            <div class="text-right">
                <span class="font-bold text-white text-[1.1em] block leading-none" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 5px rgba(56, 189, 248, 0.8);">40k</span>
                <span class="text-[0.7em] text-sky-500/80 block leading-none">(22h-7h)</span>
            </div>
        </div>

        <div class="flex items-center justify-between px-2 py-1 text-cyan-400 w-full">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                <span class="font-bold text-cyan-400" style="font-family: 'Orbitron', monospace;">Combo ngày</span>
            </div>
            <span class="font-bold text-white text-[1.1em]" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 5px rgba(34, 211, 238, 0.8);">120k</span>
        </div>
      </div>

      <div class="mt-auto pt-2 border-t border-white/5">
        <div class="text-[0.75em] text-red-300 leading-tight italic opacity-90 text-center" style="font-family: 'Orbitron', monospace;">
             ⚠ Lưu ý: Chơi Account Only máy nhà có cơ hội + thêm 1 giờ!
        </div>
      </div>
    </div>

    <div class="min-[600px]:col-span-2 min-[1170px]:col-span-1 bg-slate-900/90 backdrop-blur-md rounded-2xl px-2 py-2 border border-red-500/30 shadow-[0_0_10px_rgba(239,68,68,0.15)] flex-1 h-full w-full flex flex-col justify-between group hover:border-red-400 transition-colors duration-300">
      
      <h3 class="text-[1.15em] font-bold text-red-400 mb-2 flex items-center justify-center gap-2 text-center uppercase border-b border-red-500/20 pb-2" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 8px rgba(239, 68, 68, 0.6);">
        Giá Acc Đặc Biệt
      </h3>

      <div class="space-y-2 mb-2 flex-1" style="font-size: 0.8em;">
        <div class="flex items-center justify-between px-3 py-1 bg-red-950/30 rounded-lg border border-red-500/10 hover:bg-red-500/10 transition-all duration-300 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-[2px] bg-yellow-400 shadow-[0_0_5px_rgba(250,204,21,0.8)]"></div>
            <div class="flex items-center gap-2">
              <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">30K</span>
              <span class="text-red-200">- 2 Giờ</span>
            </div>
            <span class="text-yellow-400 font-bold text-[0.9em] animate-pulse">+ 1 Giờ</span>
        </div>

        <div class="flex items-center justify-between px-3 py-1 bg-red-950/30 rounded-lg border border-red-500/10 hover:bg-red-500/10 transition-all duration-300">
            <span class="text-white font-bold tracking-wide" style="font-family: 'Orbitron', monospace;">40K</span>
            <span class="text-red-200">- 4 Giờ</span>
        </div>

        <div class="h-[1px] bg-red-500/20 my-2"></div>

        <div class="flex items-start justify-between px-2 py-1 text-white w-full">
            <div class="flex items-center gap-2 mt-1">
                <span class="font-bold text-red-400 uppercase" style="font-family: 'Orbitron', monospace;">Combo đêm</span>
            </div>
            <div class="text-right flex flex-col items-end">
                <div class="flex items-center gap-2">
                    <span class="text-[0.9em] text-red-300">Nét:</span>
                    <span class="font-bold text-white text-[1.1em]" style="font-family: 'Orbitron', monospace;">50k</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[0.9em] text-yellow-300">Nhà:</span>
                    <span class="font-bold text-white text-[1.1em]" style="font-family: 'Orbitron', monospace;">60k</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between px-2 py-1 text-white w-full">
            <div class="flex items-center gap-2">
                <span class="font-bold text-red-400 uppercase" style="font-family: 'Orbitron', monospace;">Combo ngày</span>
            </div>
            <span class="font-bold text-white text-[1.1em]" style="font-family: 'Orbitron', monospace; text-shadow: 0 0 5px rgba(239, 68, 68, 0.8);">140k</span>
        </div>
      </div>

      <div class="mt-auto pt-2 border-t border-white/5">
        <div class="text-[0.75em] text-red-300 leading-tight italic opacity-90 text-center" style="font-family: 'Orbitron', monospace;">
            ⚠ Áp dụng cho Account có bundle mới trong 3 ngày đầu
        </div>
      </div>
    </div>
  </div>
</div>

        <!-- Buttons to toggle notes (same row) -->
        <style>
          /* Pulsing animation for note buttons */
          @keyframes note-pulse {
            0% { transform: scale(1); filter: brightness(1); box-shadow: none; }
            50% { transform: scale(1.10); filter: brightness(1.15); box-shadow: 0 12px 30px rgba(255,99,132,0.18), 0 0 24px rgba(255,105,180,0.18); }
            100% { transform: scale(1); filter: brightness(1); box-shadow: none; }
          }
          .note-pulse {
            animation: note-pulse 2000ms ease-in-out infinite;
            transition: box-shadow 180ms ease, transform 180ms ease;
            transform-origin: center center;
          }
          .note-pulse:hover, .note-pulse:focus {
            animation-play-state: paused;
            transform: scale(1.10);
            box-shadow: 0 16px 40px rgba(255,105,180,0.22), 0 0 36px rgba(255,105,180,0.22);
          }
          .note-pulse .btn-label { display: inline-block; text-shadow: 0 0 8px rgba(255,105,180,0.95), 0 0 18px rgba(255,99,132,0.55); }
        </style>
        <div class="w-full flex items-center justify-center gap-4 mt-2 mb-2">
          <button id="btn-deduct" class="note-pulse px-4 py-2 bg-pink-500/20 hover:bg-pink-500/30 text-pink-300 font-bold rounded-xl backdrop-blur-sm border border-pink-400/30 shadow-md transition"><span class="btn-label">Lưu ý trừ cọc</span></button>
          <button id="btn-rent" class="note-pulse px-4 py-2 bg-pink-500/20 hover:bg-pink-500/30 text-pink-300 font-bold rounded-xl backdrop-blur-sm border border-pink-400/30 shadow-md transition"><span class="btn-label">Lưu ý khi thuê Account</span></button>
        </div>

        <!-- Display area for notes (buttons will inject content here). Details below kept hidden. -->
        <div id="notes-display" class="mx-auto mt-6 max-w-[900px] w-[90%] bg-white/5 backdrop-blur-md rounded-2xl border border-white/20 p-4 text-white text-[1em] hidden"></div>

        <div class="flex flex-col gap-2 mt-2">
          <details id="detail-deduct" style="display:none" class="bg-red-500/10 backdrop-blur-md rounded-2xl px-3 py-2 border border-white/20 flex-1">
            <summary class="text-pink-400 text-[1.51em] font-bold leading-none cursor-pointer hover:text-pink-300 transition-colors duration-300 list-none mb-2 px-2 rounded-lg flex items-center justify-between">
              <span>Lưu ý trừ cọc</span>
              <div class="flex items-center gap-2">
                <span class="text-sm text-pink-300">(Click để xem chi tiết)</span>
                <svg class="w-5 h-5 text-pink-400 transition-transform duration-300 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </summary>
            <div class="text-[1.14em]">
              <div class="break-normal text-white">
                <span>Tiền chơi + 20k cọc</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span><span class="text-red-400 font-bold">AFK</span> bị ban hàng chờ 1 ngày</span>
                <span class="text-red-400 font-bold">- 20k</span>
                <span>cọc / Bị ban hàng chờ hơn 1 ngày bồi thường 1 ngày</span>
                <span class="text-red-400 font-bold">20k</span>
                <span class="text-yellow-400">( khi không thể chơi được tiếp hoặc gặp sự cố vui lòng báo</span>
                <span class="text-sky-400 font-bold">Admin</span>
                <span>ngay để được giảm bớt bị trừ cọc )</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Chơi quá giờ</span>
                <span class="text-red-400 font-bold">- 20k</span>
                <span>( Khi hết giờ còn trong trận Game thì tính là quá giờ, Vui lòng báo</span>
                <span class="text-sky-400 font-bold">Admin</span>
                <span> thêm giờ tránh bị trừ cọc. )</span>
              </div>

              <div class="break-normal text-white">
                <span class="text-red-400 font-bold">Lưu ý : Tiền cọc sẽ được hoàn lại sau khi chơi xong nếu Account không có vấn đề gì !</span>
              </div>

              <div class="break-normal text-white">
                <span class="text-white">-</span>
                <span>Nghiêm cấm mọi loại Tool Hack, nếu phát hiện có Tool Hack sẽ bị cấm thuê và mất số tiền đã thuê,</span>
                <span>Không Smuff, Không Toxic vì có thể dẫn đến khoá Account vĩnh viễn !</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Nếu Account bị Ban vĩnh viễn bạn sẽ phải bồi thường</span>
                <span class="text-red-400 font-bold">500k</span>
                <span class="text-yellow-400">( bất kể lý do gì )</span>
              </div>
            </div>
          </details>
        </div>

        <div class="flex flex-col gap-2 mt-2">
          <details id="detail-rent" style="display:none" class="bg-red-500/10 backdrop-blur-md rounded-2xl px-3 py-2 border border-white/20 flex-1">
            <summary class="text-pink-400 text-[1.51em] font-bold leading-none cursor-pointer hover:text-pink-300 transition-colors duration-300 list-none mb-2 px-2 rounded-lg flex items-center justify-between">
              <span>Lưu ý khi thuê Account</span>
              <div class="flex items-center gap-2">
                <span class="text-sm text-pink-300">(Click để xem chi tiết)</span>
                <svg class="w-5 h-5 text-pink-400 transition-transform duration-300 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </summary>
            <div class="text-[1.14em]">
              <div class="break-normal text-white">
                <span>-</span>
                <span>Không bảo lưu giờ nên căn thời gian thuê cho hợp lý</span>
                <span class="text-yellow-400">( bất kể lý do gì )</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Bị văng game vui lòng chụp lỗi và liên hệ</span>
                <span class="text-sky-400 font-bold">Admin</span>
                <span>để được hỗ trợ</span>
                <span class="text-yellow-400">( Nếu bạn không liên hệ để Account bị vấn đề gì thì bạn sẽ chịu trách nhiệm bất kể lý do gì ! )</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Sau khi thuê trong vòng</span>
                <span class="text-sky-400 font-bold">10 phút</span>
                <span>đầu nếu lỗi do Account bên mình bạn sẽ hỗ trợ được đổi Account và không tính giờ / Nếu đang chơi bình thường đổi Account</span>
                <span class="text-red-400 font-bold">20k</span>
                <span>cho 1 lượt</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Thời gian làm việc từ</span>
                <span class="text-sky-400 font-bold">7AM - 00PM</span>
                <span>nên khi chơi Combo đêm nếu còn thức mình sẽ hỗ trợ / còn nếu có vấn đề gì bạn sẽ chịu trách nhiệm !</span>
              </div>

              <div class="break-normal text-white">
                <span>-</span>
                <span>Sẽ có 1 vài hạn chế trong lúc chơi nếu đồng ý thuê vui lòng chấp nhận / đã chấp nhận thuê không hoàn trả dưới mọi hình thức</span>
              </div>
            </div>
          </details>
        </div>
      </div>
        <script>
          // Disabled automatic scroll on <details> open: user requested no automatic focusing.
          document.addEventListener('DOMContentLoaded', function() {
            // Intentionally left blank to avoid auto-scrolling when a <details> is toggled.
            // If needed later, we can re-enable a milder behavior here.
          });
        </script>
        <script>
          // Buttons to open/close the details and scroll into view
          document.addEventListener('DOMContentLoaded', function(){
            const btnDeduct = document.getElementById('btn-deduct');
            const btnRent = document.getElementById('btn-rent');
            const detDeduct = document.getElementById('detail-deduct');
            const detRent = document.getElementById('detail-rent');

            function closeOthers(openOne){
              [detDeduct, detRent].forEach(d => {
                if(!d) return;
                if(d !== openOne) d.open = false;
              });
            }

            function openAndScroll(detail, id){
              // Copy the <details> content (excluding the <summary>) into #notes-display.
              const display = document.getElementById('notes-display');
              if(!display) return;
              if(!detail) return;

              // Build HTML from detail children, skipping the <summary>
              let contentHTML = '';
              const children = Array.from(detail.children || []);
              children.forEach(child => {
                if(!child) return;
                if(child.tagName && child.tagName.toLowerCase() === 'summary') return;
                contentHTML += child.outerHTML || child.innerHTML || '';
              });

              // Fallback: clone and remove summary then use remaining innerHTML
              if(!contentHTML.trim()){
                try{
                  const clone = detail.cloneNode(true);
                  const summary = clone.querySelector('summary');
                  if(summary) summary.remove();
                  contentHTML = clone.innerHTML || '';
                }catch(e){
                  contentHTML = detail.innerHTML || '';
                }
              }

              display.innerHTML = contentHTML;
              display.classList.remove('hidden');
              // subtle entry animation
              display.style.opacity = 0;
              display.style.transform = 'translateY(6px)';
              requestAnimationFrame(()=>{
                display.style.transition = 'opacity 220ms ease, transform 220ms ease';
                display.style.opacity = 1;
                display.style.transform = 'translateY(0)';
              });
                // Do not auto-scroll to the injected content — user requested no automatic focus.

              closeOthers(detail);
              try{ console.log('openAndScroll: injected content length', contentHTML.length); }catch(e){}
            }

            if(btnDeduct) btnDeduct.addEventListener('click', function(e){ openAndScroll(detDeduct, 'deduct'); });
            if(btnRent) btnRent.addEventListener('click', function(e){ openAndScroll(detRent, 'rent'); });
          });
        </script>
      <div class="flex-col items-center gap-3 min-[1170px]:flex hidden pt-2 absolute bottom-[500px] left-[40px] z-[80]">
        <span id="rent-account-now-btn" class="CSS-hero-section-text-animation cursor-pointer hover:scale-110 transition duration-200 text-transparent font-bold bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-400 text-[1.43em]">THUÊ ACC NGAY</span>
        <div id="arrows-animation">
          <div class="arrow-sliding">
            <div class="arrow"></div>
          </div>
          <div class="arrow-sliding delay1">
            <div class="arrow"></div>
          </div>
          <div class="arrow-sliding delay2">
            <div class="arrow"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="ghost-logo" class="absolute bottom-2 min-[1170px]:left-[-70px] min-[1170px]:rotate-[40deg] right-[-70px] rotate-[-40deg] h-[180px] w-[180px] overflow-hidden">
    <img src="/images/UI/ghost.webp" alt="Logo" class="absolute top-[-50px] left-[-60px] h-[280px] min-w-[280px]">
  </div>
</div>