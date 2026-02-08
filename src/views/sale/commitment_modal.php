<!-- Commitment Modal -->
<div id="commitment-modal" hidden class="flex justify-center p-6 items-center overflow-hidden fixed inset-0 z-[990]">
  <div class="QUERY-modal-overlay absolute z-10 inset-0 bg-black/80"></div>

  <div
    class="inset-0 z-20 min-w-[300px] max-h-[90vh] overflow-y-auto CSS-styled-scrollbar p-6 relative w-fit h-fit bg-white/10 backdrop-blur-md text-gray-700 border border-solid border-white/20 rounded-lg shadow-md">
    <!-- Close button -->
    <button id="close-commitment-modal-btn" class="absolute top-6 right-6 text-white hover:scale-125 transition duration-200 text-[1.71em] font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-x-icon lucide-x h-[1.43em] w-[1.43em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 6 6 18" />
        <path d="m6 6 12 12" />
      </svg>
    </button>

    <h3 class="text-[1.43em] font-bold text-sky-300 mb-4">Cam kết</h3>

    <div class="mb-2">
      <h3 class="block text-[1em] font-semibold italic text-white mb-2">Cam kết từ chủ sở hữu acc:</h3>
      <div
        class="bg-transparent border border-white/20 border-solid w-full max-h-full overflow-y-auto CSS-styled-scrollbar px-3 py-2 rounded-md text-[1em] text-white focus:outline-none focus:ring-2 focus:ring-regular-light-blue-cl">
        <p class="whitespace-pre-line"><?= htmlspecialchars($rules['commitment']) ?></p>
      </div>
    </div>
  </div>
</div>