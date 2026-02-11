// Frontend admin script for rotation page
// - Fetch list of prizes from /api/v1/admin/rotation/prizes
// - Create / Update / Delete via corresponding endpoints

const apiBase = '/api/v1/admin/rotation';

async function fetchPrizes() {
  const res = await fetch(`${apiBase}/prizes`, { credentials: 'same-origin' });
  return res.json();
}

function renderPrizes(list) {
  const tbody = document.querySelector('#prizes-table-body');
  if (!tbody) return;
  tbody.innerHTML = '';
  list.forEach(p => {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50';
    tr.innerHTML = `
      <td class="px-3 py-3 text-sm text-gray-900">${p.id}</td>
      <td class="px-3 py-3 text-sm text-gray-900">${p.name}</td>
      <td class="px-3 py-3 text-sm text-gray-900">${p.type}</td>
      <td class="px-3 py-3 text-sm text-gray-900">${p.value ?? ''}</td>
      <td class="px-3 py-3 text-sm text-gray-900">${p.probability}</td>
      <td class="px-3 py-3 text-sm text-gray-900">${p.quantity}</td>
      <td class="px-3 py-3 text-sm text-gray-900">
        <label class="inline-flex items-center gap-2 toggle-switch">
          <input type="checkbox" data-id="${p.id}" class="toggle-active" ${p.is_active ? 'checked' : ''} />
          <span class="switch-track" aria-hidden="true"></span>
        </label>
      </td>
      <td class="px-3 py-3 text-sm text-gray-900">
        <div class="flex gap-2 items-center">
          <button data-id="${p.id}" class="btn-edit icon-btn" title="Sửa">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
          </button>
          <button data-id="${p.id}" class="btn-del icon-btn" title="Xóa">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

async function loadAndRender() {
  try {
    const data = await fetchPrizes();
    renderPrizes(data.prizes || []);
  } catch (e) {
    console.error(e);
    alert('Lỗi khi tải danh sách phần thưởng');
  }
}

// Modal helpers
function openModal(mode='create', prize={}){
  const modal = document.getElementById('prize-modal');
  const form = document.getElementById('prize-form');
  modal.hidden = false;
  form.dataset.mode = mode;
  form.dataset.id = prize.id || '';
  form.querySelector('[name=name]').value = prize.name || '';
  form.querySelector('[name=type]').value = prize.type || 'TEXT';
  form.querySelector('[name=value]').value = prize.value || '';
  form.querySelector('[name=probability]').value = prize.probability || '';
  form.querySelector('[name=quantity]').value = prize.quantity ?? -1;
  form.querySelector('[name=image]').value = prize.image || '';
  form.querySelector('[name=is_active]').checked = prize.is_active == 1 || prize.is_active === true;
}
function closeModal(){ document.getElementById('prize-modal').hidden = true; }

// Event bindings
window.addEventListener('load', () => {
  loadAndRender();
  document.getElementById('btn-refresh').addEventListener('click', loadAndRender);
  document.getElementById('btn-add').addEventListener('click', () => openModal('create'));
  document.getElementById('btn-cancel').addEventListener('click', closeModal);

  document.getElementById('prize-form').addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const form = ev.target;
    const mode = form.dataset.mode;
    const id = form.dataset.id;
    const payload = {
      name: form.name.value,
      type: form.type.value,
      value: form.value.value,
      probability: parseFloat(form.probability.value) || 0,
      quantity: parseInt(form.quantity.value) || -1,
      image: form.image.value,
      is_active: form.is_active.checked ? 1 : 0
    };

    try {
      const url = mode === 'create' ? `${apiBase}/prizes` : `${apiBase}/prizes/${id}`;
      const method = mode === 'create' ? 'POST' : 'PUT';
      const res = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error');
      closeModal();
      loadAndRender();
    } catch (e) {
      alert('Lỗi: ' + (e.message || e));
    }
  });

  document.querySelector('#prizes-table').addEventListener('click', async (ev) => {
    const t = ev.target;
    const btn = t.closest('button');
    if (!btn) return;
    if (btn.classList.contains('btn-edit')){
      const id = btn.dataset.id;
      // fetch single prize
      const res = await fetch(`${apiBase}/prizes/${id}`, { credentials: 'same-origin' });
      const data = await res.json();
      if (!res.ok) { alert(data.message || 'Không tìm thấy'); return; }
      openModal('edit', data.prize);
    }
    if (t.classList.contains('btn-del')){
      if (!confirm('Xóa phần thưởng này?')) return;
      const id = btn.dataset.id;
      try {
        const res = await fetch(`${apiBase}/prizes/${id}`, { method: 'DELETE', credentials: 'same-origin' });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error');
        loadAndRender();
      } catch (e) { alert('Lỗi xóa: ' + e.message); }
    }
  });

  // Handle toggle change via delegation
  document.querySelector('#prizes-table-body').addEventListener('change', async (ev) => {
    const el = ev.target;
    if (!el.classList.contains('toggle-active')) return;
    const id = el.dataset.id;
    const is_active = el.checked ? 1 : 0;
    try {
      const res = await fetch(`${apiBase}/prizes/${id}`, {
        method: 'PUT',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ is_active })
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error');
    } catch (e) {
      alert('Lỗi cập nhật trạng thái: ' + e.message);
      // revert UI
      el.checked = !el.checked;
    }
  });

  // Handle update all player turns
  document.getElementById('btn-update-all-turns').addEventListener('click', async () => {
    const totalTurns = parseInt(document.getElementById('all-turns-input').value) || 0;
    if (totalTurns < 0) {
      alert('Số lượt quay phải >= 0');
      return;
    }

    if (!confirm(`Cập nhật lượt quay cho tất cả người chơi thành ${totalTurns}?`)) return;

    try {
      const res = await fetch(`${apiBase}/update-all-player-turns`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ total_turns: totalTurns })
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error');
      alert('Cập nhật lượt quay cho tất cả người chơi thành công!');
    } catch (e) {
      alert('Lỗi: ' + (e.message || e));
    }
  });
});
