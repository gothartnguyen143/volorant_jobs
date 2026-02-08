<div id="chasing-dot"></div>

<div id="app-loading" hidden class="flex-col items-center justify-center fixed top-0 left-0 right-0 bottom-0 bg-black/70 flex z-[999]">
  <div class="relative bottom-[20px]">
    <div class="STYLE-animation-loading-shapes"></div>
    <p class="QUERY-app-loading-message w-max text-base font-bold text-white mt-6 absolute top-[calc(50%+50px)] left-1/2 -translate-x-1/2 -translate-y-1/2"></p>
  </div>
</div>

<div id="app-tooltip" hidden class="!text-[0.9em]"></div>

<button id="scroll-to-top-btn" class="outline-2 outline outline-white fixed right-6 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl text-white p-2 rounded-full shadow-lg hover:scale-110 transition-[bottom,transform] duration-300 z-50">
  <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-arrow-big-up-icon lucide-arrow-big-up w-[2em] h-[2em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M9 18v-6H5l7-7 7 7h-4v6H9z" />
  </svg>
</button>

<div id="account-avatar-modal" hidden class="QUERY-modal fixed inset-0 z-[999] flex justify-center items-center">
  <div class="QUERY-modal-overlay absolute z-10 inset-0 bg-transparent"></div>

  <div class="aspect-video w-[80%] z-20 relative">
    <img src="" alt="" class="w-full h-full object-contain transition duration-200">
  </div>
</div>

<!-- Global Centered Hero (title + CTA) -->
<!-- <div id="global-hero" style="display:none" class="fixed left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-none flex flex-col items-center gap-4"> -->
  <!-- Nổi khối: VALORANTIME -->
  <!-- <div id="global-title" class="pointer-events-none bg-white/8 backdrop-blur-sm text-white font-extrabold italic text-3xl md:text-5xl uppercase tracking-wider px-6 py-4 rounded-lg shadow-2xl" style="text-shadow: 0 0 10px rgba(255,255,255,0.9), 0 0 24px rgba(249,115,22,0.55);"> -->
    <!-- VALORANTIME -->
  <!-- </div> -->

  <!-- CTA below title -->
  <!-- <div id="global-cta" class="pointer-events-none"> -->
    <!-- <a href="/thueacc" aria-label="Thuê ngay" class="pointer-events-auto inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold rounded-full px-6 py-3 text-lg shadow-lg transition transform hover:scale-105"> -->
      <!-- THUÊ NGAY -->
    <!-- </a> -->
  <!-- </div> -->
<!-- </div> -->

<!-- <script>
  (function(){
    function updateHeroVisibility(){
      var hero = document.getElementById('global-hero');
      if(!hero) return;
      var intro = document.getElementById('page-intro');
      // show only when #page-intro exists on the page
      if(intro) hero.style.display = 'flex';
      else hero.style.display = 'none';
    }
    // run on DOM ready and immediately
    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', updateHeroVisibility);
    } else {
      updateHeroVisibility();
    }
  })();
</script> -->