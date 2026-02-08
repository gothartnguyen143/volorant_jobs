<div class="relative min-h-screen overflow-hidden z-80">
  <div class="absolute inset-0 z-0">
    <video autoplay muted loop playsinline class="w-full h-full object-cover">
      <source src="/videos/UI/intro-bg.mp4" type="video/mp4" />
    </video>
    <div class="absolute inset-0 bg-black/40"></div>
  </div>

  <div class="relative z-10 min-h-screen flex items-center font-bold">
    <div class="w-full px-2 pb-[60px] pt-[30px] min-[1900px]:px-[350px] min-[1400px]:px-[240px] min-[1200px]:px-[200px]">
      <div class="w-full relative z-[90]">
        <div class="text-center mb-4 relative">
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
          <h1 class="text-[2.5em] leading-tight flex flex-row items-center justify-center text-center uppercase tracking-wider whitespace-nowrap italic" style="font-family: 'Orbitron', monospace; filter: brightness(1.3) contrast(1.2);">
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

        <div class="grid min-[1170px]:grid-cols-3 min-[600px]:grid-cols-2 grid-cols-1 gap-y-2 gap-x-2 items-start text-[1.29em]">
          <div class="bg-white/10 backdrop-blur-md rounded-2xl px-2 py-1 border border-white/20 flex-1 h-full">
            <h3 class="text-[1.21em] font-bold text-sky-300 mb-2 flex items-center gap-3">
              Giá Máy Nét (Tất cả)
            </h3>
            <div class="space-y-2">
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">20K - 2 Giờ</span>
                </div>
              </div>
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">30K - 3 Giờ</span>
                  <span class="text-yellow-400 font-bold">+ 1 Giờ</span>
                </div>
              </div>
              <!--<div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">-->
              <!--  <div class="flex items-center gap-3">-->
              <!--    <span class="text-white font-bold">40K - 4 GIỜ</span>-->
              <!--    <span class="text-yellow-400 font-bold">+ 2 GIỜ</span>-->
              <!--  </div>-->
              <!--</div>-->
            </div>
            <div class="space-y-2 mt-4">
              <div class="flex items-center gap-1 text-sky-400 max-w-full w-fit">
                <svg class="w-6 h-6 flex-grow-[1]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span class="text-white flex items-start gap-2 w-max flex-grow-[3]">
                  <span class="font-bold text-sky-400 min-w-max h-fit">Combo đêm: 40k</span>
                  <span class="text-[0.7em]">(22PM - 7AM)</span>
                </span>
              </div>
              <div class="flex items-center gap-1 text-cyan-400 max-w-full w-fit">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
                <span class="text-white">
                  <span class="font-bold text-cyan-400">Combo ngày: 120k</span>
                </span>
              </div>
            </div>
          </div>

          <div class="bg-yellow-500/10 backdrop-blur-md rounded-2xl px-2 py-1 border border-white/20 flex-1 h-full">
            <h3 class="text-[1.21em] font-bold text-yellow-400 mb-2">Giá Máy Nhà (Tất cả + Only Máy nhà)</h3>
            <div class="space-y-2 mb-4">
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">20K - 2 Giờ</span>
                </div>
              </div>
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">30K - 3 Giờ</span>
                </div>
              </div>
              <!--<div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">-->
              <!--  <div class="flex items-center gap-3">-->
              <!--    <span class="text-white">40k - 4 Giờ <span class="text-yellow-400 font-bold">+ 1 Giờ</span></span>-->
              <!--  </div>-->
              <!--</div>-->
              <div class="space-y-2 mt-4">
                <div class="flex items-center gap-1 text-sky-400 max-w-full w-fit">
                  <svg class="w-6 h-6 flex-grow-[1]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <span class="text-white flex items-start gap-2 w-max flex-grow-[3]">
                    <span class="font-bold text-sky-400 min-w-max h-fit">Combo đêm: 50k</span>
                    <span class="text-[0.7em]">(22PM - 7AM)</span>
                  </span>
                </div>
                <div class="flex items-center gap-1 text-cyan-400 max-w-full w-fit">
                  <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                  </svg>
                  <span class="text-white">
                    <span class="font-bold text-cyan-400">Combo ngày: 120k</span>
                  </span>
                </div>
              </div>
            </div>
            <div class="text-[0.9em] text-red-300 leading-tight">
              Lưu ý : Nếu chơi Account Only máy nhà sẽ có cơ hội + thêm 1 giờ nữa !
            </div>
          </div>

          <div class="min-[600px]:col-span-2 min-[1170px]:col-span-1 bg-yellow-500/10 backdrop-blur-md rounded-2xl px-2 py-1 border border-white/20 flex-1 h-full w-full">
            <h3 class="text-[1.21em] font-bold text-red-400 mb-2">Giá Account Đặc Biệt </h3>
            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-center gap-3">
                      <span class="text-white font-bold">30k - 2 Giờ <span class="text-yellow-400 font-bold">+ 1 Giờ</span></span></span>
                    </div>
                </div>
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">40k - 4 Giờ</span>
                </div>
              </div>
              
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3 text-white">
                  <span class="font-bold min-w-max inline-block">Combo đêm :</span>
                  <span class="inline-block">
                    <span class="block">Nét: 50k</span>
                    <span class="block">Nhà: 60k</span>
                  </span>
                </div>
              </div>
              <div class="flex items-center justify-between px-2 py-0 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex items-center gap-3">
                  <span class="text-white font-bold">Combo ngày : 140k</span>
                </div>
              </div>
            </div>
            <div class="text-[0.9em] text-red-300 leading-tight">
              Lưu ý : Áp dụng cho Account có bundle mới trong vòng 3 ngày đầu ra mắt
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2 mt-2">
          <div class="bg-red-500/10 backdrop-blur-md rounded-2xl px-3 py-2 border border-white/20 flex-1">
            <div class="mb-2 px-2 rounded-lg">
              <h1 class="text-pink-400 text-[1.51em] font-bold leading-none">Lưu ý trừ cọc</h1>
            </div>

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
          </div>
        </div>

        <div class="flex flex-col gap-2 mt-2">
          <div class="bg-red-500/10 backdrop-blur-md rounded-2xl px-3 py-2 border border-white/20 flex-1">
            <div class="mb-2 px-2 rounded-lg">
              <h1 class="text-pink-400 text-[1.51em] font-bold leading-none">Lưu ý khi thuê Account :</h1>
            </div>

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
          </div>
        </div>
      </div>

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