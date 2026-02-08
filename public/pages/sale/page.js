import { URLHelper } from "../../utils/scripts/helpers.js"
import { initUtils } from "../../utils/scripts/init-utils.js"

const accounts = window.APP_DATA.saleAccounts

class SalePageManager {
  constructor() {
    this.slider = document.getElementById("slider")
    this.slidesContainer = document.getElementById("slides-container")
    this.prevButton = document.getElementById("prev-button")
    this.nextButton = document.getElementById("next-button")
    this.pages = document.querySelectorAll("#pages button")
    this.counter = document.getElementById("counter")

    this.currentIndex = 0
    this.isDragging = false
    this.startX = 0
    this.translateX = 0
    this.totalSlides = window.APP_DATA.slidesCount
    this.threshold = 100

    this.initEvents()
    this.updateSlider()
    this.initPagination()

    initUtils.initTooltip()

    this.initCountdown()
  }

  formatTimeFromSeconds(totalSeconds) {
    const days = Math.floor(totalSeconds / (60 * 60 * 24))
    const hours = Math.floor((totalSeconds % (60 * 60 * 24)) / (60 * 60))
    const minutes = Math.floor((totalSeconds % (60 * 60)) / 60)
    const seconds = totalSeconds % 60

    return `${days} ngày ${hours} giờ ${minutes} phút ${seconds} giây`
  }

  initCountdown() {
    document.querySelectorAll("#slides-container .QUERY-count-down-text").forEach((el) => {
      const futureTimeStr = el.dataset.sellToTime
      if (!futureTimeStr || futureTimeStr.length === 0) return

      const future = new Date(futureTimeStr.replace(" ", "T"))

      if (isNaN(future.getTime())) {
        el.textContent = "Thời gian không hợp lệ"
        return
      }

      let interval = null

      const updateCountdown = () => {
        const now = new Date()
        let diffInSeconds = Math.floor((future - now) / 1000)

        if (diffInSeconds <= 0) {
          el.textContent = "0 ngày 0 giờ 0 phút 0 giây"
          clearInterval(interval)
          return
        }

        el.textContent = this.formatTimeFromSeconds(diffInSeconds)
      }

      updateCountdown() // Cập nhật ngay lập tức lần đầu
      interval = setInterval(updateCountdown, 1000)
    })
  }

  updateSlider() {
    this.slidesContainer.style.transform = `translateX(-${this.currentIndex * 100}%)`
    this.updateCounter()
  }

  updateCounter() {
    const currentPage = this.counter.querySelector(".QUERY-current-page")
    currentPage.textContent = (this.currentIndex + 1).toString()
  }

  nextSlide() {
    this.currentIndex = (this.currentIndex + 1) % this.totalSlides
    this.updateSlider()
  }

  prevSlide() {
    this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides
    this.updateSlider()
  }

  goToSlide(index) {
    this.currentIndex = index
    this.updateSlider()
  }

  handleStart(clientX) {
    this.isDragging = true
    this.startX = clientX
    this.translateX = 0
    this.slidesContainer.style.transition = "none"
  }

  handleMove(clientX) {
    if (!this.isDragging) return
    this.translateX = clientX - this.startX
    this.slidesContainer.style.transform = `translateX(calc(-${this.currentIndex * 100}% + ${
      this.translateX
    }px))`
  }

  handleEnd() {
    if (!this.isDragging) return
    this.isDragging = false
    this.slidesContainer.style.transition = "transform 0.5s ease-out"
    if (this.translateX > this.threshold) {
      this.prevSlide()
    } else if (this.translateX < -this.threshold) {
      this.nextSlide()
    } else {
      this.updateSlider()
    }
    this.translateX = 0
  }

  initEvents() {
    // Mouse events
    this.slider.addEventListener("mousedown", (e) => {
      e.preventDefault()
      this.handleStart(e.clientX)
    })

    this.slider.addEventListener("mousemove", (e) => {
      this.handleMove(e.clientX)
    })

    this.slider.addEventListener("mouseup", () => this.handleEnd())
    this.slider.addEventListener("mouseleave", () => {
      if (this.isDragging) this.handleEnd()
    })

    // Touch events
    this.slider.addEventListener("touchstart", (e) => {
      this.handleStart(e.touches[0].clientX)
    })

    this.slider.addEventListener("touchmove", (e) => {
      this.handleMove(e.touches[0].clientX)
    })

    this.slider.addEventListener("touchend", () => this.handleEnd())

    // Prevent text selection while dragging
    document.addEventListener("selectstart", (e) => {
      if (this.isDragging) e.preventDefault()
    })

    // Navigation buttons
    this.prevButton.addEventListener("click", () => this.prevSlide())
    this.nextButton.addEventListener("click", () => this.nextSlide())

    // Dots navigation
    this.pages.forEach((dot, index) => {
      dot.addEventListener("click", () => this.goToSlide(index))
    })

    // Prevent button drag interference
    document.querySelectorAll("button").forEach((button) => {
      button.addEventListener("mousedown", (e) => e.stopPropagation())
      button.addEventListener("touchstart", (e) => e.stopPropagation())
    })

    // Copy button
    document.querySelectorAll(".QUERY-copy-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const accountId = e.target.closest(".QUERY-account-container").dataset.accountId * 1
        const account = accounts.find((account) => account.id === accountId)
        navigator.clipboard.writeText(account.description).then(() => {
          btn.classList.add("QUERY-is-copied")
          setTimeout(() => {
            btn.classList.remove("QUERY-is-copied")
          }, 1000)
        })
      })
    })
  }

  goToPage(page, limit) {
    window.location.href = `/sale?page=${page}&limit=${limit}`
  }

  initPagination() {
    const currentPage = parseInt(URLHelper.getUrlQueryParam("page")) || 1
    const limit = parseInt(URLHelper.getUrlQueryParam("limit")) || window.APP_DATA.limit
    const totalPages = window.APP_DATA.totalPages

    // Các nút số
    document.querySelectorAll("#pagination button").forEach((btn) => {
      const val = btn.dataset.pageNum
      if (!isNaN(val)) {
        btn.addEventListener("click", () => {
          this.goToPage(parseInt(val), limit)
        })
      }
    })

    // Nút prev
    document.querySelector("#pagination .QUERY-prev-btn").addEventListener("click", () => {
      if (currentPage > 1) {
        this.goToPage(currentPage - 1, limit)
      }
    })

    // Nút next
    document.querySelector("#pagination .QUERY-next-btn").addEventListener("click", () => {
      if (currentPage < totalPages) {
        this.goToPage(currentPage + 1, limit)
      }
    })
  }
}

class CommitmentModalManager {
  constructor() {
    this.modal = document.getElementById("commitment-modal")
    this.closeButton = document.getElementById("close-commitment-modal-btn")
    this.commitmentBtns = document.querySelectorAll(
      "#slides-container .QUERY-account-container .QUERY-commitment-btn"
    )

    this.initEvents()
  }

  initEvents() {
    this.closeButton.addEventListener("click", () => this.closeModal())
    this.commitmentBtns.forEach((btn) => {
      btn.addEventListener("click", () => this.openModal())
    })
    this.modal
      .querySelector(".QUERY-modal-overlay")
      .addEventListener("click", () => this.closeModal())
  }

  closeModal() {
    this.modal.hidden = true
  }

  openModal() {
    this.modal.hidden = false
  }
}

class BuyNowModalManager {
  constructor() {
    this.modal = document.getElementById("buy-now-modal")
    this.closeButton = document.getElementById("close-buy-now-modal-btn")
    this.byNowBtns = document.querySelectorAll(
      "#slides-container .QUERY-account-container .QUERY-buy-now-btn"
    )

    this.initEvents()
  }

  initEvents() {
    this.closeButton.addEventListener("click", () => this.closeModal())
    this.byNowBtns.forEach((btn) => {
      btn.addEventListener("click", () => this.openModal())
    })
    this.modal
      .querySelector(".QUERY-modal-overlay")
      .addEventListener("click", () => this.closeModal())
  }

  openModal() {
    this.modal.hidden = false
  }

  closeModal() {
    this.modal.hidden = true
  }
}

new CommitmentModalManager()
new BuyNowModalManager()
new SalePageManager()
