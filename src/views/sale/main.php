<div class="relative w-full mx-auto py-12 min-[1200px]:px-[100px] min-[1028px]:px-10 px-3">
  <div class="text-center mb-10">
    <h2 class="text-[2.57em] md:text-[3.42em] font-black text-gray-800 mb-4">
      DANH SÁCH
      <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">
        SALE
      </span>
    </h2>
    <div class="w-24 h-1 min-[1440px]:h-3 bg-gradient-to-r from-sky-400 to-cyan-400 mx-auto rounded-full mb-6"></div>
    <p class="text-gray-600 text-[1.14em] mx-auto">
      Cam kết cung cấp tài khoản Valorant uy tín, chất lượng cao.
    </p>
  </div>

  <?php
  function displayRemainingTime(string $futureTime): string
  {
    try {
      $now = new DateTime();
      $future = DateTime::createFromFormat('Y-m-d H:i:s', $futureTime);

      if (!$future || $future < $now) {
        return "00:00:00";
      }

      $interval = $now->diff($future);

      return sprintf(
        "Còn %d ngày, %d giờ, %d phút, %d giây",
        $interval->days,
        $interval->h,
        $interval->i,
        $interval->s
      );
    } catch (\Throwable $th) {
      return "Không xác định";
    }
  }
  ?>

  <!-- Main Slider Container -->
  <div
    id="slider"
    class="relative overflow-hidden cursor-grab active:cursor-grabbing w-full">
    <div
      id="slides-container"
      class="flex transition-transform duration-500 ease-out w-full">
      <?php foreach ($sale_accounts as $sale_account) : ?>
        <div data-account-id="<?= $sale_account['id'] ?>" class="QUERY-account-container w-full flex-shrink-0">
          <div class="text-[1em] font-medium bg-sky-100 rounded-md px-4 py-1 border-l-4 border-solid border-sky-300 border">
            Sale | <span data-sell-to-time="<?= $sale_account['sell_to_time'] ?? '' ?>" class="QUERY-count-down-text font-bold"><?= $sale_account['sell_to_time'] ? displayRemainingTime($sale_account['sell_to_time']) : 'Chưa có' ?></span>
          </div>

          <div class="mt-4 rounded-lg w-full">
            <div class="flex min-[860px]:flex-row flex-col items-stretch gap-2 min-[860px]:gap-4 w-full">
              <div class="min-[860px]:min-w-[70%] min-[860px]:max-w-[70%] aspect-[747/397] w-full min-w-[100%] max-w-[100%]">
                <img class="w-full h-full <?= $sale_account['avatar'] ? 'object-cover' : 'object-contain' ?>" src="/images/account/<?= $sale_account['avatar'] ?? 'default-account-avatar.png' ?>" alt="Account Avatar">
              </div>
              <div class="flex flex-col min-[860px]:w-[30%] min-[860px]:min-h-[unset] min-h-[260px] max-h-full w-full">
                <div class="flex items-center justify-between w-full gap-4 pt-3 pb-2 px-4 text-[1.43em] font-bold bg-white/80 backdrop-blur-md border border-solid border-b-0 border-gray-300 rounded-t-md">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-text-icon lucide-text w-[1.2em] h-[1.2em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M15 18H3" />
                      <path d="M17 6H3" />
                      <path d="M21 12H3" />
                    </svg>
                    <span class="w-max">Mô tả</span>
                  </div>
                  <button data-vcn-tooltip-content="Copy mô tả" class="QUERY-tooltip-trigger QUERY-copy-btn QUERY-not-copied p-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="QUERY-copy-icon lucide lucide-copy-icon lucide-copy w-[1em] h-[1em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                      <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="QUERY-copied-icon lucide lucide-check-check-icon lucide-check-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M18 6 7 17l-5-5" />
                      <path d="m22 10-7.5 7.5L13 16" />
                    </svg>
                  </button>
                </div>
                <div class="grow w-full pb-4 px-4 text-[1em] bg-white border border-solid border-t-0 border-gray-300 rounded-b-md whitespace-pre-line overflow-y-auto CSS-styled-scrollbar"><?= $sale_account['description'] ?></div>
                <div class="flex items-center gap-2 mt-2 min-[860px]:mt-4 bg-green-50 border border-solid border-green-300 py-2 px-4 w-full rounded-md">
                  <div class="rounded-full p-0.5 border-[1.5px] border-solid border-black">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000" class="w-[1.43em] h-[1.43em]" viewBox="0 0 32 32">
                      <g>
                        <path d="M19.8,26.1h-0.2c-2.4,0-4.8,0-7.2,0c-0.3,0-0.5-0.1-0.6-0.3c-2.5-3.2-5.1-6.3-7.6-9.5C4.1,16.1,4,16,4,15.8   c0-3.1,0-6.1,0-9.2c0-0.1,0-0.2,0.1-0.2h0.1c5.2,6.5,10.4,13,15.5,19.5c0,0,0,0.1,0.1,0.1L19.8,26.1L19.8,26.1z" />
                        <path d="M27.8,16.3c-0.7,0.9-1.5,1.8-2.2,2.8c-0.2,0.2-0.4,0.3-0.6,0.3c-2.4,0-4.8,0-7.1,0c0,0-0.1,0-0.1,0c-0.1,0-0.2-0.1-0.1-0.2   c0,0,0-0.1,0.1-0.1c2.4-3,4.7-5.9,7.1-8.9c1-1.2,2-2.5,2.9-3.7c0-0.1,0.1-0.1,0.2-0.1c0,0,0.1,0,0.1,0c0,0.1,0,0.1,0,0.2   c0,3,0,6.1,0,9.1C28,16,27.9,16.2,27.8,16.3L27.8,16.3z" />
                      </g>
                    </svg>
                  </div>
                  <span data-vcn-tooltip-content="Giá bán" class="QUERY-tooltip-trigger text-[1.14em]"><?= $sale_account['price'] ?></span>
                </div>
                <button id="by-now-btn" class="QUERY-buy-now-btn CSS-button-shadow-decoration bg-regular-blue-cl rounded-lg text-white font-bold min-h-[42px] mt-2 min-[860px]:mt-4">
                  MUA NGAY
                </button>
              </div>
            </div>

            <!-- Account Details -->
            <div class="flex flex-wrap gap-2 mt-2 min-[860px]:mt-4 text-[1.14em]">
              <div class="flex flex-col min-w-max items-center justify-center flex-1 bg-sky-100 border border-solid border-sky-300 border-t-4 rounded-md py-1 px-4">
                <h4 class="font-bold">Gmail</h4>
                <div><?= $sale_account['gmail'] ?></div>
              </div>
              <div class="flex flex-col min-w-max items-center justify-center flex-1 bg-sky-100 border border-solid border-sky-300 border-t-4 rounded-md py-1 px-4">
                <h4 class="font-bold">Thư</h4>
                <div><?= $sale_account['letter'] ?></div>
              </div>
              <div class="flex flex-col min-w-max items-center justify-center flex-1 bg-sky-100 border border-solid border-sky-300 border-t-4 rounded-md py-1 px-4">
                <h4 class="font-bold">Tình trạng</h4>
                <div><?= $sale_account['status'] ?></div>
              </div>
              <div class="flex-1">
                <button class="QUERY-commitment-btn CSS-button-shadow-decoration bg-regular-blue-cl w-full h-[calc(100%-4px)] text-white rounded-lg text-[1.14em] font-bold py-2 px-4">
                  CAM KẾT
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Navigation Buttons -->
  <button
    id="prev-button"
    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 active:scale-90 z-10">
    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-chevron-left-icon lucide-chevron-left h-[2em] w-[2em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
      <path d="m15 18-6-6 6-6" />
    </svg>
  </button>
  <button
    id="next-button"
    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 active:scale-90 z-10">
    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-chevron-right-icon lucide-chevron-right h-[2em] w-[2em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
      <path d="m9 18 6-6-6-6" />
    </svg>
  </button>

  <div class="w-full relative mt-8">
    <div id="pages" class="flex !justify-start min-[690px]:!justify-center gap-2 bg-white rounded-md outline-1 outline outline-gray-300 w-full py-2 px-4 relative overflow-x-auto CSS-styled-scrollbar">
      <?php foreach ($sale_accounts as $sale_account) : ?>
        <button data-vcn-tooltip-content="Nhấn để xem chi tiết" class="QUERY-tooltip-trigger outline-1 outline outline-gray-200 h-[4.1vw] min-w-[7.1vw] border-1 box-content border-solid border-white hover:scale-110 active:scale-90 transition duration-200">
          <img class="h-full min-w-[7.1vw] <?= $sale_account['avatar'] ? 'object-cover' : 'object-contain' ?> box-content" src="/images/account/<?= $sale_account['avatar'] ?? 'default-account-avatar.png' ?>" alt="Account Avatar">
        </button>
      <?php endforeach; ?>
    </div>

    <div id="counter" class="absolute top-2 right-2 rounded-md bg-white/40 text-center text-white w-fit mx-auto py-0.5 px-2 font-bold">
      <span class="QUERY-current-page">1</span> / <span><?= $slides_count ?></span>
    </div>
  </div>

  <section id="pagination" class="flex justify-center gap-1 items-center mt-6 w-full">
    <!-- Nút Previous -->
    <button
      class="<?= $current_page <= 1 ? 'opacity-60 cursor-not-allowed pointer-events-none' : '' ?> QUERY-prev-btn flex items-center justify-center rounded-md bg-blue-100 border border-solid border-blue-300 h-[2.26em] w-[2.26em] hover:scale-110 active:scale-90 transition duration-200">
      <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-600 lucide lucide-chevron-left-icon lucide-chevron-left h-[1.43em] w-[1.43em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
      </svg>
    </button>

    <?php
    function renderPageButton($pageNum, $currentPage)
    {
      $isActive = $pageNum == $currentPage;
      $class = $isActive
        ? 'rounded-md bg-sky-300 text-black font-bold border border-solid border-sky-300 h-[2.26em] w-[2.26em] hover:scale-110 active:scale-90 transition duration-200'
        : 'rounded-md bg-sky-100 border border-solid border-sky-300 h-[2.26em] w-[2.26em] hover:scale-110 active:scale-90 transition duration-200';
      echo "<button data-page-num=\"$pageNum\" class=\"$class\">$pageNum</button>";
    }

    $DOT = '<span class="px-2 font-bold">...</span>';

    if ($total_pages <= 7) {
      for ($i = 1; $i <= $total_pages; $i++) {
        renderPageButton($i, $current_page);
      }
    } else {
      renderPageButton(1, $current_page);

      if ($current_page > 3) echo $DOT;

      $start = max(2, $current_page - 1);
      $end = min($total_pages - 1, $current_page + 1);

      for ($i = $start; $i <= $end; $i++) {
        renderPageButton($i, $current_page);
      }

      if ($current_page < $total_pages - 2) echo $DOT;

      renderPageButton($total_pages, $current_page);
    }
    ?>

    <!-- Nút Next -->
    <button
      class="<?= $current_page >= $total_pages ? 'opacity-60 cursor-not-allowed pointer-events-none' : '' ?> QUERY-next-btn flex items-center justify-center rounded-md bg-blue-100 border border-solid border-blue-300 h-[2.26em] w-[2.26em] hover:scale-110 active:scale-90 transition duration-200">
      <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-600 lucide lucide-chevron-right-icon lucide-chevron-right h-[1.43em] w-[1.43em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m9 18 6-6-6-6" />
      </svg>
    </button>
  </section>
</div>

<script>

</script>

<script>
  window.APP_DATA = {
    saleAccounts: <?= json_encode($sale_accounts) ?>,
    slidesCount: <?= $slides_count ?>,
    totalPages: <?= $total_pages ?>,
    currentPage: <?= $current_page ?>,
    limit: <?= $limit ?>
  }
</script>

<?php require_once __DIR__ . '/commitment_modal.php'; ?>
<?php require_once __DIR__ . '/buy_now_modal.php'; ?>

<?php require_once __DIR__ . '/../templates/layout.php'; ?>