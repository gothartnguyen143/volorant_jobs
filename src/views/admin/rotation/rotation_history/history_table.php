<?php
// Spin history table partial for dedicated history page
?>

<div class="mt-6 p-4">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
      <h3 class="text-xl font-bold text-white mb-2">Lịch sử vòng quay</h3>
      <div class="flex flex-wrap gap-2 items-center">
        <label class="text-white text-base font-medium">Từ ngày:</label>
        <input id="start-date" type="date" class="px-2 py-1 rounded text-black">
        <label class="text-white text-base font-medium">Đến ngày:</label>
        <input id="end-date" type="date" class="px-2 py-1 rounded text-black">
        <button id="filter-btn" class="px-3 py-1 bg-white text-blue-600 rounded hover:bg-gray-100">Lọc</button>
        <button id="clear-filter-btn" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Xóa lọc</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table id="spin-history-table" class="w-full border-collapse">
        <thead class="bg-gray-100">
          <tr class="border-b border-gray-200">
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              <i class="fas fa-hashtag mr-2"></i>STT
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              <i class="fas fa-user mr-2"></i>Identifier
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              <i class="fas fa-gift mr-2"></i>Prize
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
              <i class="fas fa-clock mr-2"></i>Thời gian
            </th>
          </tr>
        </thead>

        <tbody id="spin-history-body" class="bg-white divide-y divide-gray-200">
          <!-- rows injected by JS -->
        </tbody>
      </table>
    </div>
    <div id="pagination-container" class="flex justify-center items-center p-4 bg-gray-50">
      <button id="prev-page" class="px-3 py-2 mx-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-gray-300" disabled>Trước</button>
      <span id="page-info" class="mx-4 text-sm text-gray-700">Trang 1 / 1</span>
      <button id="next-page" class="px-3 py-2 mx-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-gray-300" disabled>Sau</button>
    </div>
  </div>
</div>

<script>
  let currentPage = 1;
  const limit = 10;
  let totalPages = 1;
  let allHistoryList = [];
  let filteredList = [];

  function escapeHtml(s){
    if(s === null || s === undefined) return '';
    return String(s).replace(/[&<>\"']/g, function(c){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c];
    });
  }

  async function fetchSpinHistory(){
    try{
      const res = await fetch('/api/v1/admin/rotaions/spin-history', { credentials: 'same-origin' });
      if(!res.ok){
        const data = await res.json().catch(()=>({}));
        console.error('Failed to fetch history', data);
        return [];
      }
      const data = await res.json();
      return data.history || [];
    }catch(e){ console.error(e); return []; }
  }

  function renderSpinHistory(list){
    const tbody = document.getElementById('spin-history-body');
    if(!tbody) return;
    tbody.innerHTML = '';
      list.forEach((r, idx) => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-gray-50';
      const identifier = r.player_identifier || '';
      const prize = r.prize_name || '';
      const created = r.created_at || '';
      const rowNumber = (currentPage - 1) * limit + idx + 1;
      tr.innerHTML = `
        <td class="px-4 py-3 text-sm text-gray-900">${rowNumber}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(identifier)}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(prize)}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(created)}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function updatePagination(){
    document.getElementById('page-info').textContent = `Trang ${currentPage} / ${totalPages}`;
    document.getElementById('prev-page').disabled = currentPage <= 1;
    document.getElementById('next-page').disabled = currentPage >= totalPages;
  }

  function applyFilter(){
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    if(!startDate && !endDate){
      filteredList = [...allHistoryList];
    }else{
      filteredList = allHistoryList.filter(r => {
        const created = new Date(r.created_at);
        const start = startDate ? new Date(startDate) : new Date('1900-01-01');
        const end = endDate ? new Date(endDate + 'T23:59:59') : new Date('2100-01-01');
        return created >= start && created <= end;
      });
    }
    currentPage = 1;
    loadSpinHistory();
  }

  function clearFilter(){
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';
    filteredList = [...allHistoryList];
    currentPage = 1;
    loadSpinHistory();
  }

  async function loadSpinHistory(){
    if(allHistoryList.length === 0){
      allHistoryList = await fetchSpinHistory();
      filteredList = [...allHistoryList];
    }
    totalPages = Math.ceil(filteredList.length / limit) || 1;
    const start = (currentPage - 1) * limit;
    const end = start + limit;
    const pageList = filteredList.slice(start, end);
    renderSpinHistory(pageList);
    updatePagination();
  }

  function goToPrevPage(){
    if(currentPage > 1){
      currentPage--;
      loadSpinHistory();
    }
  }

  function goToNextPage(){
    if(currentPage < totalPages){
      currentPage++;
      loadSpinHistory();
    }
  }

  document.addEventListener('DOMContentLoaded', function(){
    loadSpinHistory();
    const refreshBtn = document.getElementById('btn-refresh');
    if(refreshBtn) refreshBtn.addEventListener('click', () => { currentPage = 1; allHistoryList = []; filteredList = []; loadSpinHistory(); });
    document.getElementById('prev-page').addEventListener('click', goToPrevPage);
    document.getElementById('next-page').addEventListener('click', goToNextPage);
    document.getElementById('filter-btn').addEventListener('click', applyFilter);
    document.getElementById('clear-filter-btn').addEventListener('click', clearFilter);
  });
</script>
