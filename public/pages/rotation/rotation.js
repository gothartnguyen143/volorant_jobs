console.log("Script loaded");

document.addEventListener("DOMContentLoaded", function () {
  const wheel = document.querySelector(".wheel-rotator");
  
  // SỬA 1: Bắt sự kiện cho CẢ 2 NÚT (nút tâm và nút to)
  const spinBtns = document.querySelectorAll("#spin-btn, .spin-btn-large");
  
  const statusTop = document.querySelector(".status-top");
  const statusMain = document.querySelector(".status-main");

  if (!wheel || spinBtns.length === 0) {
    console.warn("rotation.js: Thiếu wheel hoặc nút quay");
    return;
  }

  const segments = Array.from(document.querySelectorAll('.wheel-segment'));
  // Lấy danh sách quà từ data-label hoặc fallback
  const labels = segments.map(seg => seg.dataset.label?.trim() || `Phần ${seg.dataset.index}`);
  
  const SEG_COUNT = segments.length;
  // Góc của mỗi miếng bánh
  const SEG_ANGLE = 360 / SEG_COUNT;

  let currentRotation = 0; // Lưu tổng số độ đã quay
  let isSpinning = false;  // Cờ trạng thái để chặn click liên tục

  const showResult = (index) => {
    // Reset hiệu ứng winner cũ
    segments.forEach(seg => seg.classList.remove('winner'));
    
    // Highlight phần trúng (SVG path)
    if (segments[index]) {
        segments[index].classList.add('winner');
        // Nếu muốn đổi màu hoặc hiệu ứng CSS cho path trúng:
        // segments[index].style.fill = "#FFD700"; 
    }
    
    const prize = labels[index] || `Phần ${index + 1}`;
    console.log("Kết quả hiển thị:", prize);
    
    if (statusTop) statusTop.textContent = 'TRẠNG THÁI: ĐÃ DỪNG';
    if (statusMain) statusMain.textContent = prize;
  };

  const spin = async () => {
    if (isSpinning) return; // Chặn click khi đang quay

    // Khóa tất cả các nút quay
    isSpinning = true;
    spinBtns.forEach(btn => btn.disabled = true);
    
    if (statusTop) statusTop.textContent = 'TRẠNG THÁI: ĐANG QUAY...';

    // Dùng identifier mặc định hoặc lấy từ input ẩn (nếu có)
    const identifier = '0399793159'; // TODO: Nên lấy động từ user session

    try {
      console.log('Đang gọi API...');
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

      // Lấy index trúng thưởng từ server (0-based index)
      // Nếu server không trả về index, random tạm để test
      const targetIndex = data.index !== null && data.index !== undefined 
                          ? parseInt(data.index) 
                          : Math.floor(Math.random() * SEG_COUNT);

      const prizeName = data.prize ? data.prize.name : labels[targetIndex];
      console.log(`Server trả về index: ${targetIndex}, quà: ${prizeName}`);

      // --- LOGIC TÍNH TOÁN GÓC QUAY (ĐÃ SỬA) ---
      
      // 1. Tính góc cần dừng của ô trúng thưởng để nó nằm ngay đỉnh (12h)
      // Vì PHP vẽ index 0 ở đỉnh và chạy theo chiều kim đồng hồ,
      // nên muốn đưa index X về đỉnh, ta phải quay ngược lại một góc tương ứng.
      // Công thức: 360 - (Vị trí bắt đầu của ô) - (Một nửa độ rộng ô để vào tâm)
      const stopAngle = 360 - (targetIndex * SEG_ANGLE) - (SEG_ANGLE / 2);

      // 2. Tính số vòng quay tối thiểu (5 vòng = 1800 độ)
      const minSpins = 5 * 360; 

      // 3. Tính toán vị trí quay tiếp theo
      // Lấy vị trí hiện tại cộng thêm 5 vòng
      let nextRotation = currentRotation + minSpins;
      
      // 4. Cộng thêm phần dư để khớp với stopAngle
      // Logic: Tìm phần dư hiện tại so với 360, sau đó bù vào để đạt stopAngle
      const currentMod = nextRotation % 360;
      let distanceNeeded = stopAngle - currentMod;
      
      // Đảm bảo luôn quay tới (dương) chứ không quay lùi
      if (distanceNeeded < 0) {
        distanceNeeded += 360;
      }
      
      // Tổng độ quay cuối cùng
      currentRotation = nextRotation + distanceNeeded;

      // --- THỰC HIỆN QUAY ---
      
      // Dùng cubic-bezier để tạo hiệu ứng nhanh lúc đầu, chậm dần lúc cuối
      wheel.style.transition = 'transform 4s cubic-bezier(0.25, 0.1, 0.25, 1)';
      wheel.style.transform = `rotate(${currentRotation}deg)`;

      // Đợi quay xong (khớp với thời gian transition 4s)
      setTimeout(() => {
        isSpinning = false;
        spinBtns.forEach(btn => btn.disabled = false);
        showResult(targetIndex);
        alert(`Chúc mừng! Bạn trúng: ${prizeName}`);
      }, 4100); // Thêm 100ms buffer

    } catch (error) {
      console.error('Lỗi khi quay:', error);
      isSpinning = false;
      spinBtns.forEach(btn => btn.disabled = false);
      if (statusTop) statusTop.textContent = 'TRẠNG THÁI: LỖI';
      if (statusMain) statusMain.textContent = error.message;
      alert('Lỗi: ' + error.message);
    }
  };

  // Gắn sự kiện cho tất cả nút quay tìm thấy
  spinBtns.forEach(btn => {
      btn.addEventListener('click', spin);
  });
  
  // Debug: Click vào từng phần để kiểm tra xem label và vị trí
  // (Chỉ bật khi dev, production có thể comment lại)
  segments.forEach((seg, i) => {
    seg.addEventListener('click', () => {
        console.log(`Debug click segment ${i}: ${labels[i]}`);
    });
  });
});