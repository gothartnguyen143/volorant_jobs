import { GameAccountService } from "../../services/game-account-services.js?v=1.0.0"
import { AccountCard } from "../../utils/scripts/components.js?v=1.0.0"
import {
  AxiosErrorHandler,
  LitHTMLHelper,
  ThemeHelper,
  Toaster,
} from "../../utils/scripts/helpers.js?v=1.0.0"
import { initUtils } from "../../utils/scripts/init-utils.js?v=1.0.0"

class HomePageManager {
  #SCROLLING_THRESHOLD = 300
  #zoomHolder = {}
  #currentAvatar = null
  #defaultAccountStateColors = ["#38bdf8", "#00bfff", "#3dc2ef", "#87ceeb"]

  constructor() {
    this.loadMoreContainer = document.getElementById("load-more-container")
    this.accountsList = document.getElementById("accounts-list")
    this.accountRankTypes = document.getElementById("account-rank-types")
    this.accountStatuses = document.getElementById("account-statuses")
    this.accountDeviceTypes = document.getElementById("account-device-types")
    this.rentNowModal = document.getElementById("rent-now-modal")
    this.rentNowModalOverlay = this.rentNowModal.querySelector(".QUERY-modal-overlay")
    this.closeRentNowModalBtn = document.getElementById("close-rent-now-modal-btn")
    this.accNameRentNowModal = document.getElementById("acc-name--rent-now-modal")
    this.scrollToTopBtn = document.getElementById("scroll-to-top-btn")
    this.cancelAllFiltersBtn = document.getElementById("cancel-all-filters-btn")
    this.acceptRulesCheckbox = document.getElementById("accept-rules-checkbox")
    this.accountRankTypesSelect = document.getElementById("account-rank-types-select")
    this.accountStatusesSelect = document.getElementById("account-statuses-select")
    this.accountDeviceTypesSelect = document.getElementById("account-device-types-select")
    this.accountAvatarModal = document.getElementById("account-avatar-modal")
    this.accountTypesSelect = document.getElementById("account-types-select")

    this.isFetchingItems = false
    this.isMoreItems = true
    this.gameAccounts = []
    this.selectedAccount = null
    this.filterHolder = {
      rank: "",
      status: "",
      device_type: "",
      account_type: "",
    }
    this.rankColors = {
      ALL: "bg-gray-200",
      Iron: "bg-[#4B4B4B] text-white", // Xám đậm – giống màu kim loại thô trong ảnh
      Bronze: "bg-[#B67B4B] text-white", // Nâu đồng – gần đúng với màu đồng trong hình
      Silver: "bg-[#C0C0C0]", // Bạc sáng
      Gold: "bg-yellow-400", // Vàng đậm hơi cam – đúng với icon GOLD
      Platinum: "bg-[#32BDB2]", // Xanh ngọc – đặc trưng Platinum
      Diamond: "bg-[#AE78D6]", // Tím ngọc – nổi bật trong Diamond
      Immortal: "bg-[#BD2B63] text-white", // Đỏ tím – Immortal mang tone rực, đậm ánh đỏ tím
      Ascendant: "bg-gray-200", // Không có trong hình – giữ nguyên
      Radiant: "bg-[#F9D65D]", // Vàng kim – icon Radiant nổi bật bằng màu vàng chói
    }

    this.initCloseModalListener()
    this.initFilterByRankListener()
    this.initFilterByStatusListener()
    this.initFilterByDeviceTypeListener()
    this.initFilterByAccountTypeListener()
    this.initAccountsListListener()
    this.initCloseRentNowModalListener()
    this.initScrollToTopBtnListener()
    this.initCancelAllFiltersListener()
    this.initModalOverlayListener()
    this.initAcceptRulesCheckboxListener()
    this.initModals()

    this.watchScrolling()

    this.fetchAccounts()
    this.fetchAccountRankTypes()
    this.initRentAccountNowBtnListener()
  }

  initRentAccountNowBtnListener() {
    document.getElementById("rent-account-now-btn").addEventListener("click", () => {
      document.getElementById("accounts-list-container").scrollIntoView({ behavior: "smooth" })
    })
  }

  applyRankColors() {
    const options = this.accountRankTypesSelect.querySelectorAll("option")
    for (const option of options) {
      option.classList.add(...this.rankColors[option.value].split(" "))
    }
  }

  activateFilterItems() {
    const { rank, status, device_type, account_type } = this.filterHolder
    if (rank) {
      this.accountRankTypesSelect.value = rank
    }
    if (status) {
      this.accountStatusesSelect.value = status
    }
    if (device_type) {
      this.accountDeviceTypesSelect.value = device_type
    }
    if (account_type) {
      this.accountTypesSelect.value = account_type
    }
  }

  getLastAccountInfoForFetching() {
    let lastAccount = null
    const accounts = this.gameAccounts
    if (accounts.length > 0) {
      lastAccount = accounts[0]
      for(const acc of accounts){
          if((acc.id * 1) < (lastAccount.id * 1)){
              lastAccount = acc
          }
      }
    }
    let last_id = lastAccount ? lastAccount.id : null
    return { last_id }
  }

  fetchAccounts() {
    const { last_id } = this.getLastAccountInfoForFetching()
    const { rank, status, device_type, account_type } = this.filterHolder
    if (this.isFetchingItems) return
    this.isFetchingItems = true

    GameAccountService.fetchAccounts(last_id, undefined, rank, status, device_type, account_type)
      .then((accounts) => {
        if (accounts && accounts.length > 0) {
          this.isFetchingItems = false
          const newList = [...this.gameAccounts, ...accounts]
          newList.sort((a, b) => (b.acc_code*1) - (a.acc_code*1))
          this.gameAccounts = newList
          this.accountsList.innerHTML = ''
          this.renderNewAccounts(this.gameAccounts)
          initUtils.initTooltip()
          this.activateFilterItems()
          this.fetchAccounts()
        } else {
          this.isFetchingItems = false
          this.isMoreItems = false
          this.hideShowLoadMore(false)
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
  }

  renderNewAccounts(accounts) {
    for (const account of accounts) {
      const fragment = LitHTMLHelper.getFragment(AccountCard, account)
      this.accountsList.appendChild(fragment)
    }
  }

  moveActiveItemsToTop(arr, conditionChecker) {
    const result = []

    for (const item of arr) {
      if (conditionChecker(item)) {
        result.unshift(item)
      } else {
        result.push(item)
      }
    }

    return result
  }

  fetchAccountRankTypes() {
    GameAccountService.fetchAccountRankTypes().then((rankTypes) => {
      if (rankTypes && rankTypes.length > 0) {
        const rankFilter = this.filterHolder.rank
        const orderedRankTypes = this.moveActiveItemsToTop(
          rankTypes,
          (rankType) => rankType.type === rankFilter
        )
        for (const { type } of orderedRankTypes) {
          const isActive = rankFilter === type
          const option = document.createElement("option")
          option.classList.add("text-black")
          option.value = type
          option.textContent = type
          option.selected = isActive
          this.accountRankTypesSelect.appendChild(option)
          if (isActive) {
            document
              .querySelector("#account-rank-types-container .QUERY-active-icon")
              .classList.remove("hidden")
          }
        }
        this.applyRankColors()
      }
    })
  }

  hideShowLoadMore(show) {
    if (show) {
      this.loadMoreContainer.classList.remove("QUERY-no-more")
      this.loadMoreContainer.classList.add("QUERY-is-more")
    } else {
      this.loadMoreContainer.classList.remove("QUERY-is-more")
      this.loadMoreContainer.classList.add("QUERY-no-more")
    }
  }

  initCloseModalListener() {
    const closeModalBtns = document.querySelectorAll(".QUERY-close-modal-btn")
    for (const closeModalBtn of closeModalBtns) {
      closeModalBtn.addEventListener("click", () => {
        closeModalBtn.closest(".QUERY-modal").hidden = true
      })
    }
  }

  initFilterByRankListener() {
    this.accountRankTypesSelect.addEventListener("change", (e) => {
      const rankType = e.target.value
      if (rankType === "ALL") {
        this.submitFilter("rank=")
      } else {
        this.submitFilter(`rank=${rankType}`)
      }
    })
  }

  initFilterByStatusListener() {
    this.accountStatusesSelect.addEventListener("change", (e) => {
      const status = e.target.value
      if (status === "ALL") {
        this.submitFilter("status=")
      } else {
        this.submitFilter(`status=${status}`)
      }
    })
  }

  initFilterByDeviceTypeListener() {
    this.accountDeviceTypesSelect.addEventListener("change", (e) => {
      const deviceType = e.target.value
      if (deviceType === "ALL") {
        this.submitFilter("device_type=")
      } else {
        this.submitFilter(`device_type=${deviceType}`)
        // if (deviceType === "Only máy nhà") {
        //   ThemeHelper.updateAccountStateColor("#facc15", "#facc15", "#fed73a", "#fee580") // màu vàng
        // } else {
        //   ThemeHelper.updateAccountStateColor(...this.#defaultAccountStateColors) // màu xanh lam
        // }
      }
    })
  }

  initFilterByAccountTypeListener() {
    this.accountTypesSelect.addEventListener("change", (e) => {
      const accountType = e.target.value
      if (accountType === "ALL") {
        this.submitFilter("account_type=")
      } else {
        this.submitFilter(`account_type=${accountType}`)
      }
    })
  }

  resetAccountsList() {
    this.accountsList.innerHTML = ""
    this.gameAccounts = []
    this.hideShowLoadMore(true)
    this.isMoreItems = true
    ThemeHelper.updateAccountStateColor(...this.#defaultAccountStateColors) // màu xanh lam
  }

  submitFilter(keyValuePair = "rank=&status=&device_type=&account_type=") {
    this.resetAccountsList()
    const conditions = keyValuePair.split("&")
    for (const condition of conditions) {
      const [key, value] = condition.split("=")
      this.filterHolder[key] = value || ""
    }
    this.fetchAccounts()
    // NavigationHelper.pureNavigateTo(currentUrl.toString())
  }

  initAccountsListListener() {
    this.accountsList.addEventListener("click", (e) => {
      let target = e.target
      while (
        target &&
        !target.classList.contains("QUERY-rent-now-btn") &&
        !target.classList.contains("QUERY-account-avatar-1") &&
        !target.classList.contains("QUERY-account-avatar-2")
      ) {
        target = target.parentElement
        if (target && (target.id === "accounts-list" || target.tagName === "BODY")) {
          break
        }
      }
      // Rent Now Modal
      if (target && target.classList.contains("QUERY-rent-now-btn")) {
        let accountId = target.dataset.accountId
        if (accountId) {
          accountId = accountId * 1
          const account = this.gameAccounts.find((account) => account.id === accountId)
          if (account) {
            this.selectedAccount = account
            this.showRentNowModal()
          }
        }
      }
      // Image Modal
      if (
        !this.#zoomHolder.isOnModal &&
        target &&
        (target.classList.contains("QUERY-account-avatar-1") ||
          target.classList.contains("QUERY-account-avatar-2"))
      ) {
        this.showAccountAvatarModal(target)
      }
    })
  }

  initModalOverlayListener() {
    this.rentNowModalOverlay.addEventListener("click", () => {
      this.hideRentNowModal()
    })
  }

  resetFilterSection() {
    this.accountRankTypesSelect.value = "ALL"
    this.accountStatusesSelect.value = "ALL"
    this.accountDeviceTypesSelect.value = "ALL"
    this.accountTypesSelect.value = "ALL"
  }

  initCancelAllFiltersListener() {
    this.cancelAllFiltersBtn.addEventListener("click", () => {
      this.resetAccountsList()
      this.resetFilterSection()
      this.submitFilter()
    })
  }

  showRentNowModal() {
    this.accNameRentNowModal.textContent = this.selectedAccount.acc_name
    this.acceptRulesCheckbox.checked = false
    this.rentNowModal.hidden = false
  }

  hideRentNowModal() {
    this.rentNowModal.hidden = true
  }

  initCloseRentNowModalListener() {
    this.closeRentNowModalBtn.addEventListener("click", () => {
      this.hideRentNowModal()
    })
  }

  watchScrolling() {
    window.addEventListener("scroll", (e) => {
      if (window.scrollY > this.#SCROLLING_THRESHOLD) {
        this.scrollToTopBtn.classList.remove("bottom-[-4.26em]")
        this.scrollToTopBtn.classList.add("bottom-6")
      } else {
        this.scrollToTopBtn.classList.remove("bottom-6")
        this.scrollToTopBtn.classList.add("bottom-[-4.26em]")
      }
    })
  }

  initScrollToTopBtnListener() {
    this.scrollToTopBtn.addEventListener("click", () => {
      window.scrollTo({ top: 100, behavior: "instant" })
      window.scrollTo({ top: 0, behavior: "smooth" })
    })
  }

  initAcceptRulesCheckboxListener() {
    this.acceptRulesCheckbox.addEventListener("change", () => {
      const rentNowModalContactLinks = document.getElementById("rent-now-modal-contact-links")
      if (this.acceptRulesCheckbox.checked) {
        rentNowModalContactLinks.classList.remove("opacity-50", "pointer-events-none")
      } else {
        rentNowModalContactLinks.classList.add("opacity-50", "pointer-events-none")
      }
    })
  }

  closeAccountAvatarModal(imgWrapper, imgElement, modalOverlay, closeModalBtn, imgWrapperParent) {
    imgWrapper.style.cssText = ""
    imgWrapper.classList.remove("STATE-account-avatar-wrapper-on-responsive")
    imgElement.style.cssText = ""
    imgElement.classList.remove("STATE-account-avatar-on-responsive")
    modalOverlay.remove()
    closeModalBtn.remove()
    imgElement.removeEventListener("wheel", this.#zoomHolder.wheelHandler)
    imgElement.removeEventListener("mousedown", this.#zoomHolder.mouseDownHandler)
    this.#zoomHolder = {}
    this.#currentAvatar = null
    document.body.style.overflow = "auto"
    imgWrapperParent.style.cssText = ""
  }

  showAccountAvatarModal(imgElement) {
    this.#zoomHolder = {
      isOnModal: true,
    }
    this.#currentAvatar = imgElement
    const imgWrapper = imgElement.parentElement
    const imgWrapperParent = imgWrapper.parentElement
    imgWrapperParent.style.cssText = `height: ${imgWrapperParent.clientHeight}px;`
    const wrapperRect = imgWrapper.getBoundingClientRect()
    const transitionDuration = 300
    imgWrapper.style.cssText = `
      z-index: 1001;
      transition: height ${transitionDuration}ms ease, width ${transitionDuration}ms ease, left ${transitionDuration}ms ease, top ${transitionDuration}ms ease;
      position: fixed;
      top: ${wrapperRect.top}px; 
      left: ${wrapperRect.left}px; 
      height: ${imgWrapper.clientHeight}px; 
      width: ${imgWrapper.clientWidth}px;
    `
    requestAnimationFrame(() => {
      // đặt width và height
      imgWrapper.style.cssText += `
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
        height: 100vh;
        left: 0;
        top: 0;
      `
      imgWrapper.classList.add("STATE-account-avatar-wrapper-on-responsive")
      imgElement.style.cssText = `
        max-height: 100%;
        position: relative;
        z-index: 20;
      `
      imgElement.classList.add("STATE-account-avatar-on-responsive")
    })
    setTimeout(() => {
      document.body.style.overflow = "hidden"

      const modalOverlay = document.createElement("div")
      modalOverlay.classList.add(
        "QUERY-modal-overlay",
        "absolute",
        "inset-0",
        "bg-black/80",
        "z-10"
      )
      imgWrapper.prepend(modalOverlay)
      const closeModalBtn = document.createElement("button")
      closeModalBtn.innerHTML = `
        <svg viewBox="0 0 24 24" height="28" width="28" xmlns="http://www.w3.org/2000/svg">
          <g>
            <path
              d="M8.00386 9.41816C7.61333 9.02763 7.61334 8.39447 8.00386 8.00395C8.39438 7.61342 9.02755 7.61342 9.41807 8.00395L12.0057 10.5916L14.5907 8.00657C14.9813 7.61605 15.6144 7.61605 16.0049 8.00657C16.3955 8.3971 16.3955 9.03026 16.0049 9.42079L13.4199 12.0058L16.0039 14.5897C16.3944 14.9803 16.3944 15.6134 16.0039 16.0039C15.6133 16.3945 14.9802 16.3945 14.5896 16.0039L12.0057 13.42L9.42097 16.0048C9.03045 16.3953 8.39728 16.3953 8.00676 16.0048C7.61624 15.6142 7.61624 14.9811 8.00676 14.5905L10.5915 12.0058L8.00386 9.41816Z"
              fill="#fff"
            ></path>
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12ZM3.00683 12C3.00683 16.9668 7.03321 20.9932 12 20.9932C16.9668 20.9932 20.9932 16.9668 20.9932 12C20.9932 7.03321 16.9668 3.00683 12 3.00683C7.03321 3.00683 3.00683 7.03321 3.00683 12Z"
              fill="#fff"
            ></path>
          </g>
        </svg>
      `
      closeModalBtn.classList.add(
        "QUERY-close-modal-btn",
        "absolute",
        "top-6",
        "right-6",
        "hover:scale-110",
        "transition",
        "duration-200",
        "z-20"
      )
      imgWrapper.prepend(closeModalBtn)

      closeModalBtn.addEventListener("click", () => {
        this.closeAccountAvatarModal(
          imgWrapper,
          imgElement,
          modalOverlay,
          closeModalBtn,
          imgWrapperParent
        )
      })
      modalOverlay.addEventListener("click", () => {
        this.closeAccountAvatarModal(
          imgWrapper,
          imgElement,
          modalOverlay,
          closeModalBtn,
          imgWrapperParent
        )
      })
    }, transitionDuration)

    this.bindZoomAvatarListener(imgElement)
  }

  zoomAvatarHandler(e) {
    e.preventDefault() // ngăn cuộn màn hình

    if (e.deltaY < 0) {
      // cuộn lên -> zoom in
      this.#zoomHolder.scale += 0.1
    } else {
      // cuộn xuống -> zoom out
      this.#zoomHolder.scale -= 0.1
    }

    // không cho scale quá nhỏ
    this.#zoomHolder.scale = Math.max(0.1, this.#zoomHolder.scale)

    this.#currentAvatar.style.transform = `scale(${this.#zoomHolder.scale})`
  }

  bindZoomAvatarListener(imgElement) {
    // cleanup listener cũ
    if (this.#zoomHolder?.wheelHandler) {
      imgElement.removeEventListener("wheel", this.#zoomHolder.wheelHandler)
      imgElement.removeEventListener("mousedown", this.#zoomHolder.mouseDownHandler)
    }

    this.#zoomHolder = {
      ...this.#zoomHolder,
      scale: 1,
      translateX: 0,
      translateY: 0,
      isDragging: false,
      startX: 0,
      startY: 0,
    }

    // zoom
    this.#zoomHolder.wheelHandler = this.zoomAvatarHandler.bind(this)
    imgElement.addEventListener("wheel", this.#zoomHolder.wheelHandler)

    // drag
    this.#zoomHolder.mouseDownHandler = (e) => this.startDrag(e, imgElement)
    imgElement.addEventListener("mousedown", this.#zoomHolder.mouseDownHandler)
  }

  startDrag(e) {
    e.preventDefault()
    this.#zoomHolder.isDragging = true
    this.#zoomHolder.startX = e.clientX - this.#zoomHolder.translateX
    this.#zoomHolder.startY = e.clientY - this.#zoomHolder.translateY

    const onMouseMove = (ev) => {
      if (!this.#zoomHolder.isDragging) return
      this.#zoomHolder.translateX = ev.clientX - this.#zoomHolder.startX
      this.#zoomHolder.translateY = ev.clientY - this.#zoomHolder.startY
      this.applyTransform()
    }

    const onMouseUp = () => {
      this.#zoomHolder.isDragging = false
      window.removeEventListener("mousemove", onMouseMove)
      window.removeEventListener("mouseup", onMouseUp)
    }

    window.addEventListener("mousemove", onMouseMove)
    window.addEventListener("mouseup", onMouseUp)
  }

  applyTransform() {
    this.#currentAvatar.style.transform = `
      translate(${this.#zoomHolder.translateX}px, ${this.#zoomHolder.translateY}px)
      scale(${this.#zoomHolder.scale})
    `
  }

  initModals() {
    const modals = document.querySelectorAll(".QUERY-modal")
    for (const modal of modals) {
      modal.querySelector(".QUERY-modal-overlay").addEventListener("click", () => {
        modal.hidden = true
      })
    }
  }
}

new HomePageManager()