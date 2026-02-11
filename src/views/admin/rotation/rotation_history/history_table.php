<?php
// Spin history table partial for dedicated history page
?>

<div class="mt-6 overflow-x-auto p-4">
  <h3 class="text-lg font-semibold mb-2">Lịch sử vòng quay</h3>
  <table id="spin-history-table" class="w-full border-collapse">
    <thead class="bg-gray-50">
      <tr class="border-b">
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">STT</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Identifier</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Prize</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Thời gian</th>
      </tr>
    </thead>

    <tbody id="spin-history-body" class="bg-white divide-y">
      <!-- rows injected by JS -->
    </tbody>
  </table>
</div>

<script>
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
      tr.innerHTML = `
        <td class="px-3 py-3 text-sm text-gray-900">${idx+1}</td>
        <td class="px-3 py-3 text-sm text-gray-900">${escapeHtml(identifier)}</td>
        <td class="px-3 py-3 text-sm text-gray-900">${escapeHtml(prize)}</td>
        <td class="px-3 py-3 text-sm text-gray-900">${escapeHtml(created)}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function escapeHtml(s){
    if(s === null || s === undefined) return '';
    return String(s).replace(/[&<>\"']/g, function(c){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c];
    });
  }

  async function loadSpinHistory(){
    const list = await fetchSpinHistory();
    renderSpinHistory(list);
  }

  document.addEventListener('DOMContentLoaded', function(){
    loadSpinHistory();
    const btn = document.getElementById('btn-refresh');
    if(btn) btn.addEventListener('click', loadSpinHistory);
  });
</script>
