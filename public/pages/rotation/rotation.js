console.log("Script loaded");

document.addEventListener("DOMContentLoaded", function () {
  const wheel = document.querySelector(".wheel-rotator");
  // SỬA 1: Nên đặt class riêng cho nút quay, ví dụ .btn-spin
  // Nếu chưa có, hãy thêm class vào HTML hoặc dùng logic cũ nhưng cẩn thận
  const spinBtn = document.querySelector(".btn-spin") || document.querySelector("button"); 
  
  const statusTop = document.querySelector(".status-top");
  const statusMain = document.querySelector(".status-main");

  if (!wheel || !spinBtn) {
    console.warn("rotation.js: Thiếu wheel hoặc nút quay");
    return;
  }

  const segments = Array.from(document.querySelectorAll('.wheel-segment'));
  // Lấy danh sách quà
  const labels = segments.map(seg => seg.dataset.label?.trim() || `Phần ${seg.dataset.index}`);
  
  const SEG_COUNT = segments.length || 1;
  const SEG_ANGLE = 360 / SEG_COUNT;

  let currentRotation = 0; // Lưu tổng số độ đã quay để cộng dồn cho lần sau

  const showResult = (index) => {
    // Reset hiệu ứng winner cũ
    segments.forEach(seg => seg.classList.remove('winner'));
    
    // Highlight phần trúng
    if (segments[index]) segments[index].classList.add('winner');
    
    const prize = labels[index] || `Phần ${index + 1}`;
    console.log("Kết quả hiển thị:", prize);
    
    if (statusTop) statusTop.textContent = 'TRẠNG THÁI: ĐÃ DỪNG';
    if (statusMain) statusMain.textContent = prize;
  };

  const spin = async () => {
    if (spinBtn.disabled) return; // Chặn click kép

    // Lấy identifier từ input
    const identifierInput = document.getElementById('identifier-input');
    const identifier = identifierInput ? identifierInput.value.trim() : '';
    if (!identifier) {
      alert('Vui lòng nhập email hoặc số điện thoại!');
      return;
    }

    console.log('Bắt đầu quay...');
    spinBtn.disabled = true; // Khóa nút
    if (statusTop) statusTop.textContent = 'TRẠNG THÁI: ĐANG QUAY...';

    try {
      // Gọi API spin
      const response = await fetch('/api/v1/rotation/spin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier })
      });
      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || 'Lỗi không xác định');
      }

      // Lấy prize và index từ response
      const prize = data.prize;
      const prizeIndex = data.index !== null ? data.index : Math.floor(Math.random() * SEG_COUNT);
      let prizeName = 'Chúc may mắn lần sau';
      if (prize) {
        prizeName = prize.name;
      }

      // Sử dụng index từ server
      const targetIndex = prizeIndex;

      // Tính góc để dừng tại targetIndex
      const targetAngle = (360 / SEG_COUNT) * targetIndex;
      const spins = 5; // ít nhất 5 vòng
      const randomOffset = Math.floor(Math.random() * (360 / SEG_COUNT)); // offset trong segment
      const totalRotation = (spins * 360) + targetAngle + randomOffset;

      // Cộng dồn
      currentRotation += totalRotation;

      // CSS Transition
      wheel.style.transition = 'transform 4s cubic-bezier(0.25, 0.1, 0.25, 1)';
      wheel.style.transform = `rotate(${currentRotation}deg)`;

      // Đợi quay xong
      setTimeout(() => {
        spinBtn.disabled = false;
        showResult(targetIndex);
        alert(`Chúc mừng! Bạn trúng: ${prizeName}`);
      }, 4100);

    } catch (error) {
      console.error('Lỗi khi quay:', error);
      spinBtn.disabled = false;
      if (statusTop) statusTop.textContent = 'TRẠNG THÁI: LỖI';
      if (statusMain) statusMain.textContent = error.message;
      alert('Lỗi: ' + error.message);
    }
  };

  // Gắn sự kiện
  spinBtn.addEventListener('click', spin);
  
  // Debug: Click vào từng phần để kiểm tra xem label đúng chưa
  segments.forEach((seg, i) => {
    seg.addEventListener('click', () => {
        console.log(`Debug click segment ${i}`);
        showResult(i);
    });
  });
});