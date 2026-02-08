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