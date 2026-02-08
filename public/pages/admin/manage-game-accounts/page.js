import { GameAccountService } from "../../../services/game-account-services.js?v=1.0.0"
import { AccountRow } from "../../../utils/scripts/components.js?v=1.0.0"
import {
  LitHTMLHelper,
  AppLoadingHelper,
  AxiosErrorHandler,
  Toaster,
  URLHelper,
  NavigationHelper,
  LocalStorageHelper,
  ValidationHelper,
} from "../../../utils/scripts/helpers.js?v=1.0.0"
import { initUtils } from "../../../utils/scripts/init-utils.js?v=1.0.0"

const sharedData = {
  gameAccounts: [],
  rankTypes: window.APP_DATA.ranks,
}

class ManageGameAccountsPageManager {
  #SCROLLING_THRESHOLD = 300

  constructor() {
    this.accountsTableBody = document.getElementById("accounts-table-body")
    this.loadMoreContainer = document.getElementById("load-more-container")
    this.scrollToTopBtn = document.getElementById("scroll-to-top-btn")
    this.scrollToBottomBtn = document.getElementById("scroll-to-bottom-btn")

    this.isFetchingItems = false
    this.isMoreItems = true
    this.clickedAccountRowId = null
    this.RENT_TIME_INPUT_FORMAT = "YYYY-MM-DD HH:mm:ss"

    this.fetchAccounts()

    this.watchScrolling()

    this.initListeners()
  }

  getAccountsTableBodyEle() {
    return this.accountsTableBody
  }

  getLastAccounts() {
    const accounts = sharedData.gameAccounts
    if (!accounts || accounts.length === 0) return []
    let busyAcc = null
    let freeAcc = null
    let checkAcc = null
    for (const acc of accounts) {
      if (acc.status === "Bận") {
        if (busyAcc) {
          if (acc.acc_code > busyAcc.acc_code) {
            busyAcc = acc
          }
        } else {
          busyAcc = acc
        }
      } else if (acc.status === "Check") {
        if (checkAcc) {
          if (acc.acc_code > checkAcc.acc_code) {
            checkAcc = acc
          }
        } else {
          checkAcc = acc
        }
      } else {
        if (freeAcc) {
          if (acc.acc_code > freeAcc.acc_code) {
            freeAcc = acc
          }
        } else {
          freeAcc = acc
        }
      }
    }
    return [freeAcc, checkAcc, busyAcc]
  }

  fetchAccounts() {
    if (this.isFetchingItems || !this.isMoreItems) return
    this.isFetchingItems = true

    AppLoadingHelper.show("Đang tải dữ liệu...")
    const lastAccounts = this.getLastAccounts()
    const free_last_acc_code = lastAccounts[0]?.acc_code || null
    const check_last_acc_code = lastAccounts[1]?.acc_code || null
    const busy_last_acc_code = lastAccounts[2]?.acc_code || null
    const rank = URLHelper.getUrlQueryParam("rank")
    const status = URLHelper.getUrlQueryParam("status")
    const device_type = URLHelper.getUrlQueryParam("device_type")
    const search_term = URLHelper.getUrlQueryParam("search_term")

    GameAccountService.fetchAccountsForAdmin(
      free_last_acc_code,
      check_last_acc_code,
      busy_last_acc_code,
      rank,
      status,
      device_type,
      search_term,
      "updated_at"
    )
      .then((accounts) => {
        if (accounts && accounts.length > 0) {
          const startOrderNumber = sharedData.gameAccounts.length + 1
          sharedData.gameAccounts = [...sharedData.gameAccounts, ...accounts]
          this.renderNewAccounts(accounts, startOrderNumber)
          this.initInputListeners()
          this.initCatchDeleteAndUpdateAccountBtnClick()
          this.initCancelRentBtnListener()
          initUtils.initTooltip()
        } else {
          this.isMoreItems = false
          this.loadMoreContainer.classList.remove("QUERY-is-more")
          this.loadMoreContainer.classList.add("QUERY-no-more")
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        this.isFetchingItems = false
        AppLoadingHelper.hide()
      })
  }

  initCatchDeleteAndUpdateAccountBtnClick() {
    // khi click lên btn delete hoặc update
    this.accountsTableBody.addEventListener("click", (e) => {
      let target = e.target
      let isDeleteBtn = false,
        isUpdateBtn = false
      while (target) {
        if (target.classList.contains("QUERY-delete-account-btn")) {
          isDeleteBtn = true
          break
        }
        if (target.classList.contains("QUERY-update-account-btn")) {
          isUpdateBtn = true
          break
        }
        target = target.parentElement
        if (target.id === "accounts-table-body" || target.tagName === "BODY" || !target) {
          return
        }
      }
      if (isDeleteBtn) {
        deleteAccountManager.showModal(target.dataset.accountId * 1)
      }
      if (isUpdateBtn) {
        updateAccountManager.showModal(target.dataset.accountId * 1)
      }
    })
  }

  renderNewAccounts(newAccounts, startOrderNumber) {
    let order_number = startOrderNumber
    for (const account of newAccounts) {
      const accountRow = LitHTMLHelper.getFragment(
        AccountRow,
        account,
        order_number,
        UIEditor.convertRankTypesToRenderingRanks(sharedData.rankTypes)
      )
      this.accountsTableBody.appendChild(accountRow)
      order_number++
    }
  }

  scrollToTop() {
    window.scrollTo({
      top: 100,
      behavior: "instant",
    })
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    })
  }

  scrollToBottom() {
    const pageScrollHeight = document.body.scrollHeight
    window.scrollTo({
      top: pageScrollHeight - document.documentElement.clientHeight - 100,
      behavior: "instant",
    })
    window.scrollTo({
      top: pageScrollHeight,
      behavior: "smooth",
    })
  }

  watchScrolling() {
    window.addEventListener("scroll", (e) => {
      if (window.scrollY > this.#SCROLLING_THRESHOLD) {
        this.scrollToTopBtn.classList.remove("bottom-[-4.26em]")
        this.scrollToTopBtn.classList.add("bottom-[1.71em]")
      } else {
        this.scrollToTopBtn.classList.remove("bottom-[1.7em]")
        this.scrollToTopBtn.classList.add("bottom-[-4.26em]")
      }
      if (
        window.scrollY <
        document.body.scrollHeight - window.innerHeight - this.#SCROLLING_THRESHOLD
      ) {
        this.scrollToBottomBtn.classList.remove("bottom-[-4.26em]")
        this.scrollToBottomBtn.classList.add("bottom-[6.14em]")
      } else {
        this.scrollToBottomBtn.classList.remove("bottom-[6.14em]")
        this.scrollToBottomBtn.classList.add("bottom-[-4.26em]")
      }
    })
  }

  initListeners() {
    document.getElementById("load-more-btn").addEventListener("click", (e) => {
      this.fetchAccounts()
    })

    this.scrollToTopBtn.addEventListener("click", this.scrollToTop.bind(this))
    this.scrollToBottomBtn.addEventListener("click", this.scrollToBottom.bind(this))
  }

  submitRentTimeFromInput(input) {
    const value = input.value || ""
    if (value) {
      if (!this.clickedAccountRowId) return
      if (input.classList.contains("QUERY-rent-to-time-input--exact")) {
        // Kiểm tra định dạng "HH:mm DD/MM/YYYY" bằng dayjs
        const rentToTime = dayjs(value, "HH:mm DD/MM/YYYY", true)
        if (!rentToTime.isValid()) {
          Toaster.error(
            "Định dạng không phù hợp",
            "Thời gian cho thuê phải theo định dạng HH:mm DD/MM/YYYY (ví dụ: 13:00 21/07/2025)"
          )
          return
        } else if (rentToTime.isBefore(dayjs())) {
          Toaster.error(
            "Thời gian không hợp lệ",
            "Thời gian cho thuê phải lớn hơn thời gian hiện tại"
          )
          return
        }
        const rentToTimeValue = rentToTime.format(this.RENT_TIME_INPUT_FORMAT)
        updateAccountManager.updateRentTime(this.clickedAccountRowId, rentToTimeValue)
        return
      }
      if (!ValidationHelper.isPureInteger(value)) {
        Toaster.error("Thời gian cho thuê phải là một số nguyên")
        return
      }
      if (value * 1 <= 0) {
        Toaster.error("Thời gian cho thuê phải lớn hơn 0")
        return
      }
      let rentToTimeValue = input.dataset.rentToTimeValue
      rentToTimeValue = rentToTimeValue ? dayjs(rentToTimeValue) : dayjs() // nếu ko có thời gian cho thuê thì lấy thời gian hiện tại
      rentToTimeValue = rentToTimeValue.add(value, "hours").format(this.RENT_TIME_INPUT_FORMAT)
      updateAccountManager.updateRentTime(this.clickedAccountRowId, rentToTimeValue)
    } else {
      Toaster.error("Thời gian thuê không được để trống")
    }
  }

  submitAccUsernameFromInput(input) {
    const accUsername = input.value
    if (!accUsername) {
      Toaster.error("Cảnh báo", "Tên đăng nhập không được để trống")
      return
    }
    const accountId = input.closest(".QUERY-account-row-item").dataset.accountId * 1
    updateAccountManager.updateAccUsername(accountId, accUsername)
  }

  initInputListeners() {
    // hide actions section when click outside
    document.body.addEventListener("click", (e) => {
      const target = e.target
      if (target && !target.closest(".QUERY-input-container")) {
        const actions = this.accountsTableBody.querySelectorAll(
          `.QUERY-account-row-item-${this.clickedAccountRowId} .QUERY-input-actions`
        )
        for (const action of actions) {
          action.hidden = true
        }
        this.clickedAccountRowId = null
      }
    })
    // show actions section when focus in input
    this.accountsTableBody.addEventListener("focusin", (e) => {
      let target = e.target
      while (target && target.tagName !== "INPUT") {
        target = target.parentElement
        if (target && target.classList.contains("accounts-table-body")) {
          return
        }
      }
      if (!target) return
      this.clickedAccountRowId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
      const actionsSection = target.nextElementSibling
      if (actionsSection) {
        actionsSection.hidden = false
      }
    })
    // bắt sự kiện lưu thời gian cho thuê
    this.accountsTableBody.addEventListener("click", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-input-save-action")) {
        target = target.parentElement
        if (target && target.classList.contains("accounts-table-body")) {
          return
        }
      }
      if (!target || !target.classList.contains("QUERY-input-save-action")) return
      const input = target.closest(".QUERY-input-container").querySelector("input")
      if (
        input.classList.contains("QUERY-rent-to-time-input") ||
        input.classList.contains("QUERY-rent-to-time-input--exact") ||
        input.classList.contains("QUERY-rent-to-time-input--add")
      ) {
        this.submitRentTimeFromInput(input)
      }
      if (input.classList.contains("QUERY-acc-username-input")) {
        this.submitAccUsernameFromInput(input)
      }
    })
    // bắt sự kiện nhấn enter trong input
    this.accountsTableBody.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault()
        let target = e.target
        while (target && target.tagName !== "INPUT") {
          target = target.parentElement
          if (target && target.classList.contains("accounts-table-body")) {
            return
          }
        }
        if (!target) return
        if (
          target.classList.contains("QUERY-rent-to-time-input") ||
          target.classList.contains("QUERY-rent-to-time-input--exact") ||
          target.classList.contains("QUERY-rent-to-time-input--add")
        ) {
          this.submitRentTimeFromInput(target)
        }
        if (target.classList.contains("QUERY-acc-username-input")) {
          this.submitAccUsernameFromInput(target)
        }
      }
    })
  }

  cancelRent(accountId) {
    AppLoadingHelper.show()
    GameAccountService.cancelRent(accountId)
      .then((data) => {
        if (data && data.success) {
          Toaster.success("Thông báo", "Hủy cho thuê thành công")
          uiEditor.refreshAccountRowOnUI(accountId)
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initCancelRentBtnListener() {
    this.accountsTableBody.addEventListener("click", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-cancel-rent-btn")) {
        target = target.parentElement
        if (target && target.classList.contains("accounts-table-body")) {
          return
        }
      }
      if (!target) return
      const accountId = target.closest(".QUERY-account-row-item").dataset.accountId
      this.cancelRent(accountId)
    })
  }
}

class UIEditor {
  constructor() {
    this.accountsTableBody = document.getElementById("accounts-table-body")
  }

  setAccountRow(accountRow, accountData, orderNumber, ranksToRender) {
    const newAccountRow = LitHTMLHelper.getFragment(
      AccountRow,
      accountData,
      orderNumber,
      ranksToRender
    )
    accountRow.replaceWith(newAccountRow)
  }

  static convertRankTypesToRenderingRanks(rankTypes) {
    const ranksToRender = []
    for (const { type } of rankTypes) {
      if (type !== "Radiant") {
        ranksToRender.push(`${type} 1`, `${type} 2`, `${type} 3`)
      } else {
        ranksToRender.push(type)
      }
    }
    return ranksToRender
  }

  refreshAccountRowOnUI(accountId) {
    GameAccountService.fetchSingleAccount(accountId)
      .then((data) => {
        if (data && data.success) {
          const account = data.account
          if (account) {
            sharedData.gameAccounts = sharedData.gameAccounts.map((acc) =>
              acc.id === accountId ? account : acc
            )
            const accountRow = this.accountsTableBody.querySelector(
              `.QUERY-account-row-item-${accountId}`
            )
            if (accountRow) {
              this.setAccountRow(
                accountRow,
                account,
                accountRow.dataset.accountOrderNumber * 1,
                UIEditor.convertRankTypesToRenderingRanks(sharedData.rankTypes)
              )
              initUtils.initTooltip()
            }
          }
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
  }
}

class AddNewAccountManager {
  #MAX_AVATAR_COUNT = 2

  constructor() {
    this.addNewAccountModal = document.getElementById("add-new-account-modal")
    this.addNewAccountForm = document.getElementById("add-new-account-form")
    this.pickAvatarSection = document.getElementById("pick-avatar--add-section")
    this.avatarPreview = document.getElementById("avatar-preview-img--add-section")
    this.avatarPreview2 = document.getElementById("avatar-preview-img-2--add-section")
    this.avatarInput = document.getElementById("avatar-input--add-section")
    this.ranksSelect = document.getElementById("ranks-select--add-section")
    this.statusSelect = document.getElementById("status-select--add-section")
    this.deviceTypesSelect = document.getElementById("device-types-select--add-section")
    this.accTypeSelect = document.getElementById("acc-types-select--add-section")

    this.isSubmitting = false

    this.statusOptions = []
    this.deviceTypeOptions = []

    this.initUIData()

    this.initListeners()
  }

  initUIData() {
    this.statusOptions = ["Rảnh", "Bận", "Check"]
    this.deviceTypeOptions = ["Tất cả", "Only máy nhà"]
    this.accTypeOptions = ["Thường", "Đặc biệt"]
  }

  renderRanksSelect() {
    const rankTypes = UIEditor.convertRankTypesToRenderingRanks(sharedData.rankTypes)
    this.ranksSelect.innerHTML = ""
    for (const rank of rankTypes) {
      const option = document.createElement("option")
      option.value = rank
      option.textContent = rank
      this.ranksSelect.appendChild(option)
    }
  }

  renderAccTypeSelect() {
    const accTypes = this.accTypeOptions
    for (const accType of accTypes) {
      const option = document.createElement("option")
      option.value = accType
      option.textContent = accType
      this.accTypeSelect.appendChild(option)
    }
  }

  renderStatusSelect() {
    this.statusSelect.innerHTML = ""
    const statuses = this.statusOptions
    for (const status of statuses) {
      const option = document.createElement("option")
      option.value = status
      option.textContent = status
      this.statusSelect.appendChild(option)
    }
  }

  renderDeviceTypesSelect() {
    this.deviceTypesSelect.innerHTML = ""
    const deviceTypes = this.deviceTypeOptions
    for (const deviceType of deviceTypes) {
      const option = document.createElement("option")
      option.value = deviceType
      option.textContent = deviceType
      this.deviceTypesSelect.appendChild(option)
    }
  }

  initListeners() {
    document
      .getElementById("add-new-account-btn")
      .addEventListener("click", this.showModal.bind(this))

    document
      .getElementById("add-new-account-submit-btn")
      .addEventListener("click", this.submitAddAccount.bind(this))

    document
      .getElementById("add-new-account-cancel-btn")
      .addEventListener("click", this.hideModal.bind(this))

    this.addNewAccountModal.querySelector(".QUERY-modal-overlay").addEventListener("click", (e) => {
      this.hideModal()
    })

    this.avatarInput.addEventListener("change", this.handleAvatarInputChange.bind(this))
    document
      .getElementById("cancel-avatar-btn--add-section")
      .addEventListener("click", this.handleRemoveAvatar.bind(this))
  }

  switchToAvatarPreviewSection() {
    this.pickAvatarSection.classList.remove("QUERY-at-avatar-input-section")
    this.pickAvatarSection.classList.add("QUERY-at-avatar-preview-section")
  }

  switchToAvatarInputSection() {
    this.pickAvatarSection.classList.remove("QUERY-at-avatar-preview-section")
    this.pickAvatarSection.classList.add("QUERY-at-avatar-input-section")
  }

  handleAvatarInputChange(e) {
    const files = e.target.files
    if (files && files.length > 0) {
      let index = 0
      for (const file of files) {
        const reader = new FileReader()
        reader.onload = (e) => {
          if (index === 0) {
            this.avatarPreview.src = e.target.result
          } else {
            this.avatarPreview2.src = e.target.result
          }
          index++
          if (index === files.length) {
            this.switchToAvatarPreviewSection()
          }
        }
        reader.readAsDataURL(file)
      }
    }
  }

  handleRemoveAvatar() {
    this.avatarPreview.src = ""
    this.avatarPreview2.src = ""
    this.avatarPreview.style.maxHeight = "fit-content"
    this.avatarPreview2.style.maxHeight = "fit-content"
    this.avatarInput.value = null
    this.switchToAvatarInputSection()
  }

  showModal() {
    this.renderRanksSelect()
    this.renderStatusSelect()
    this.renderDeviceTypesSelect()
    this.renderAccTypeSelect()
    this.addNewAccountModal.hidden = false
  }

  hideModal() {
    this.addNewAccountModal.hidden = true
    this.addNewAccountForm.reset()
  }

  validateFormData({ rank, accCode, status, deviceType, accType, accUsername, avatars }) {
    if (!rank) {
      Toaster.error("Rank không được để trống")
      return false
    }
    if (!accCode) {
      Toaster.error("Mã account không được để trống")
      return false
    }
    if (!status) {
      Toaster.error("Trạng thái không được để trống")
      return false
    }
    if (!deviceType) {
      Toaster.error("Loại thiết bị không được để trống")
      return false
    }
    if (!accType) {
      Toaster.error("Loại acc không được để trống")
      return false
    }
    if (!accUsername) {
      Toaster.error("Tên đăng nhập không được để trống")
      return false
    }
    if (avatars && avatars.length > this.#MAX_AVATAR_COUNT) {
      Toaster.error("Chỉ được chọn tối đa 2 ảnh cho 1 tài khoản")
      return false
    }
    return true
  }

  submitAddAccount() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    const formData = new FormData(this.addNewAccountForm)
    const data = {
      rank: formData.get("rank"),
      accCode: formData.get("accCode"),
      status: formData.get("status"),
      deviceType: formData.get("deviceType"),
      accType: formData.get("accType"),
      accUsername: formData.get("accUsername"),
      avatars: this.avatarInput.files,
    }
    if (!this.validateFormData({ ...data })) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show()
    GameAccountService.addNewAccounts([data], data.avatars)
      .then((data) => {
        if (data && data.success) {
          Toaster.success("Thông báo", "Thêm tài khoản thành công", () => {
            NavigationHelper.reloadPage()
          })
        }
      })
      .catch((error) => {
        Toaster.error("Thêm tài khoản thất bại", AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        this.isSubmitting = false
        AppLoadingHelper.hide()
      })
  }
}

class DeleteAccountManager {
  constructor() {
    this.deleteAccountModal = document.getElementById("delete-account-modal")

    this.isDeleting = false
    this.accountId = null

    this.initListeners()
  }

  initListeners() {
    document
      .getElementById("delete-account-cancel-button")
      .addEventListener("click", this.hideModal.bind(this))

    document
      .getElementById("delete-account-confirm-button")
      .addEventListener("click", this.confirmDelete.bind(this))

    this.deleteAccountModal.querySelector(".QUERY-modal-overlay").addEventListener("click", (e) => {
      this.hideModal()
    })
  }

  showModal(accountId) {
    this.accountId = accountId
    const account = sharedData.gameAccounts.find((account) => account.id === accountId)
    document.getElementById("delete-account-name").textContent = account.acc_name
    this.deleteAccountModal.hidden = false
  }

  hideModal() {
    this.deleteAccountModal.hidden = true
  }

  confirmDelete() {
    if (this.isDeleting) return
    this.isDeleting = true

    AppLoadingHelper.show()
    GameAccountService.deleteAccount(this.accountId)
      .then((data) => {
        if (data && data.success) {
          Toaster.success("Thông báo", "Xóa tài khoản thành công", () => {
            NavigationHelper.reloadPage()
          })
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        this.isDeleting = false
        AppLoadingHelper.hide()
      })
  }
}

class UpdateAccountManager {
  #MAX_AVATAR_COUNT = 2

  constructor() {
    this.updateAccountModal = document.getElementById("update-account-modal")
    this.updateAccountForm = document.getElementById("update-account-form")
    this.pickAvatarSection = document.getElementById("pick-avatar--update-section")
    this.avatarPreview = document.getElementById("avatar-preview-img--update-section")
    this.avatarPreview2 = document.getElementById("avatar-preview-img-2--update-section")
    this.avatarInput = document.getElementById("avatar-input--update-section")
    this.accountsTableBody = document.getElementById("accounts-table-body")
    this.ranksSelect = document.getElementById("ranks-select--update-section")
    this.statusSelect = document.getElementById("status-select--update-section")
    this.deviceTypesSelect = document.getElementById("device-types-select--update-section")
    this.accTypeSelect = document.getElementById("acc-types-select--update-section")
    this.changeAvatar1Input = document.getElementById("change-avatar-1-input--update-section")
    this.changeAvatar2Input = document.getElementById("change-avatar-2-input--update-section")

    this.isSubmitting = false
    this.statusOptions = []
    this.deviceTypeOptions = []
    this.accTypeOptions = []
    this.pickedAvatars = {
      avatarFile1: null,
      avatarFile2: null,
    }
    this.cancelAllAvatars = false

    this.initUIData()

    this.initListeners()
    this.initSwitchStatusQuickly()
    this.initSwitchDeviceTypeQuickly()
    this.initSwitchRankQuickly()
    this.initSwitchAccTypeQuickly()
  }

  initUIData() {
    this.statusOptions = ["Rảnh", "Bận", "Check"]
    this.deviceTypeOptions = ["Tất cả", "Only máy nhà"]
    this.accTypeOptions = ["Thường", "Đặc biệt"]
  }

  renderRanksSelect(account) {
    const rankTypes = UIEditor.convertRankTypesToRenderingRanks(sharedData.rankTypes)
    this.ranksSelect.innerHTML = ""
    for (const rank of rankTypes) {
      const option = document.createElement("option")
      option.value = rank
      option.textContent = rank
      if (rank === account.rank) {
        option.selected = true
      }
      this.ranksSelect.appendChild(option)
    }
  }

  renderStatusSelect(account) {
    this.statusSelect.innerHTML = ""
    const statuses = this.statusOptions
    for (const status of statuses) {
      const option = document.createElement("option")
      option.value = status
      option.textContent = status
      if (status === account.status) {
        option.selected = true
      }
      this.statusSelect.appendChild(option)
    }
  }

  renderDeviceTypesSelect(account) {
    this.deviceTypesSelect.innerHTML = ""
    const deviceTypes = this.deviceTypeOptions
    for (const deviceType of deviceTypes) {
      const option = document.createElement("option")
      option.value = deviceType
      option.textContent = deviceType
      if (deviceType === account.device_type) {
        option.selected = true
      }
      this.deviceTypesSelect.appendChild(option)
    }
  }

  renderAccTypeSelect(account) {
    this.accTypeSelect.innerHTML = ""
    const accTypes = this.accTypeOptions
    for (const accType of accTypes) {
      const option = document.createElement("option")
      option.value = accType
      option.textContent = accType
      if (accType === account.acc_type) {
        option.selected = true
      }
      this.accTypeSelect.appendChild(option)
    }
  }

  updateAccUsername(accountId, accUsername) {
    AppLoadingHelper.show("Cập nhật tên đăng nhập...")
    GameAccountService.updateAccount(accountId, { accUsername })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
          Toaster.success("Thông báo", "Cập nhật tên đăng nhập thành công")
        }
      })
      .catch((error) => {
        Toaster.error(
          "Cập nhật tên đăng nhập thất bại",
          AxiosErrorHandler.handleHTTPError(error).message
        )
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  updateRentTime(accountId, toTime) {
    AppLoadingHelper.show("Cập nhật thời gian cho thuê...")
    GameAccountService.updateAccount(accountId, { rentToTime: toTime })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
          Toaster.success("Thông báo", "Cập nhật thời gian cho thuê thành công")
        }
      })
      .catch((error) => {
        Toaster.error(
          "Cập nhật thời gian cho thuê thất bại",
          AxiosErrorHandler.handleHTTPError(error).message
        )
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initListeners() {
    document
      .getElementById("update-account-submit-btn")
      .addEventListener("click", this.submitUpdateAccount.bind(this))

    document
      .getElementById("update-account-cancel-btn")
      .addEventListener("click", this.hideModal.bind(this))

    this.updateAccountModal.querySelector(".QUERY-modal-overlay").addEventListener("click", (e) => {
      this.hideModal()
    })

    this.avatarInput.addEventListener("change", this.handleAvatarInputChange.bind(this))
    document
      .getElementById("cancel-avatar-btn--update-section")
      .addEventListener("click", this.handleRemoveAvatar.bind(this))

    this.changeAvatar1Input.addEventListener(
      "change",
      this.handleChangeAvatar1InputChange.bind(this)
    )
    this.changeAvatar2Input.addEventListener(
      "change",
      this.handleChangeAvatar2InputChange.bind(this)
    )
  }

  handleChangeAvatar1InputChange(e) {
    const input = e.target
    const files = input.files
    if (files && files.length > 0) {
      input.closest(".QUERY-avatar-preview-section-box").classList.add("QUERY-is-loading")
      const file = files[0]
      const reader = new FileReader()
      reader.onload = (e) => {
        this.avatarPreview.src = e.target.result
        this.avatarPreview.style.maxHeight = "unset"
        input.closest(".QUERY-avatar-preview-section-box").classList.remove("QUERY-is-loading")
        this.pickedAvatars.avatarFile1 = file
      }
      reader.readAsDataURL(file)
    }
  }
  handleChangeAvatar2InputChange(e) {
    const input = e.target
    const files = input.files
    if (files && files.length > 0) {
      input.closest(".QUERY-avatar-preview-section-box").classList.add("QUERY-is-loading")
      const file = files[0]
      const reader = new FileReader()
      reader.onload = (e) => {
        this.avatarPreview2.src = e.target.result
        this.avatarPreview2.style.maxHeight = "unset"
        input.closest(".QUERY-avatar-preview-section-box").classList.remove("QUERY-is-loading")
        this.pickedAvatars.avatarFile2 = file
      }
      reader.readAsDataURL(file)
    }
  }

  switchToAvatarPreviewSection() {
    this.pickAvatarSection.classList.remove("QUERY-at-avatar-input-section")
    this.pickAvatarSection.classList.add("QUERY-at-avatar-preview-section")
  }

  switchToAvatarInputSection() {
    this.pickAvatarSection.classList.remove("QUERY-at-avatar-preview-section")
    this.pickAvatarSection.classList.add("QUERY-at-avatar-input-section")
  }

  handleAvatarInputChange(e) {
    const files = e.target.files
    if (files && files.length > 0) {
      if (files.length > this.#MAX_AVATAR_COUNT) {
        Toaster.error("Chỉ được chọn tối đa 2 ảnh cho 1 tài khoản")
        return
      }
      const file1 = files[0]
      const file2 = files[1]
      if (file1) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.avatarPreview.src = e.target.result
          this.avatarPreview.style.maxHeight = "unset"
          this.pickedAvatars.avatarFile1 = file1
        }
        reader.readAsDataURL(file1)
      } else {
        this.avatarPreview.style.maxHeight = "200px"
        this.avatarPreview.src = "/images/account/default-account-avatar.png"
      }
      if (file2) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.avatarPreview2.src = e.target.result
          this.avatarPreview2.style.maxHeight = "unset"
          this.pickedAvatars.avatarFile2 = file2
        }
        reader.readAsDataURL(file2)
      } else {
        this.avatarPreview2.style.maxHeight = "200px"
        this.avatarPreview2.src = "/images/account/default-account-avatar.png"
      }
      this.switchToAvatarPreviewSection()
    }
  }

  handleRemoveAvatar() {
    this.avatarPreview.src = ""
    this.avatarPreview2.src = ""
    this.avatarPreview.style.maxHeight = "fit-content"
    this.avatarPreview2.style.maxHeight = "fit-content"
    this.avatarInput.value = null
    this.switchToAvatarInputSection()
    this.cancelAllAvatars = true
  }

  showModal(accountId) {
    this.accountId = accountId
    const account = sharedData.gameAccounts.find((account) => account.id === accountId)
    const { avatar, acc_name, acc_code, avatar_2, acc_username } = account
    document.getElementById("update-account-name").textContent = acc_name
    this.updateAccountForm.querySelector("input[name='accCode']").value = acc_code
    this.updateAccountForm.querySelector("input[name='accUsername']").value = acc_username
    this.renderRanksSelect(account)
    this.renderStatusSelect(account)
    this.renderDeviceTypesSelect(account)
    this.renderAccTypeSelect(account)
    this.updateAccountModal.hidden = false
    this.avatarPreview.src = `/images/account/${avatar || "default-account-avatar.png"}`
    this.avatarPreview2.src = `/images/account/${avatar_2 || "default-account-avatar.png"}`
    if (!avatar) {
      this.avatarPreview.style.maxHeight = "200px"
    }
    if (!avatar_2) {
      this.avatarPreview2.style.maxHeight = "200px"
    }
    this.switchToAvatarPreviewSection()
  }

  hideModal() {
    this.updateAccountModal.hidden = true
    this.updateAccountForm.reset()
  }

  validateFormData({ rank, accCode, status, deviceType, accType, accUsername }) {
    if (!rank) {
      Toaster.error("Rank không được để trống")
      return false
    }
    if (!accCode) {
      Toaster.error("Mã account không được để trống")
      return false
    }
    if (!status) {
      Toaster.error("Trạng thái không được để trống")
      return false
    }
    if (!deviceType) {
      Toaster.error("Loại thiết bị không được để trống")
      return false
    }
    if (!accType) {
      Toaster.error("Loại acc không được để trống")
      return false
    }
    if (!accUsername) {
      Toaster.error("Tên đăng nhập không được để trống")
      return false
    }
    return true
  }

  submitUpdateAccount() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    const formData = new FormData(this.updateAccountForm)
    const data = {
      rank: formData.get("rank"),
      accCode: formData.get("accCode"),
      status: formData.get("status"),
      accUsername: formData.get("accUsername"),
      deviceType: formData.get("deviceType"),
      accType: formData.get("accType"),
    }
    if (!this.validateFormData({ ...data })) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show()
    GameAccountService.updateAccount(
      this.accountId,
      data,
      this.pickedAvatars.avatarFile1,
      this.pickedAvatars.avatarFile2,
      this.cancelAllAvatars
    )
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(this.accountId)
          Toaster.success("Thông báo", "Cập nhật tài khoản thành công")
        }
      })
      .catch((error) => {
        Toaster.error(
          "Cập nhật tài khoản thất bại",
          AxiosErrorHandler.handleHTTPError(error).message
        )
      })
      .finally(() => {
        this.isSubmitting = false
        AppLoadingHelper.hide()
      })
  }

  switchStatus(status) {
    if (this.isSubmitting) return
    this.isSubmitting = true

    AppLoadingHelper.show()
    GameAccountService.switchAccountStatus(this.accountId, status)
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(this.accountId)
          Toaster.success("Thông báo", "Cập nhật trạng thái tài khoản thành công")
        }
      })
      .catch((error) => {
        Toaster.error(
          "Cập nhật trạng thái tài khoản thất bại",
          AxiosErrorHandler.handleHTTPError(error).message
        )
      })
      .finally(() => {
        this.isSubmitting = false
        AppLoadingHelper.hide()
      })
  }

  initSwitchStatusQuickly() {
    this.accountsTableBody.addEventListener("change", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-status-select")) {
        target = target.parentElement
        if (target && target.classList.contains("QUERY-account-row-item")) {
          break
        }
      }
      if (!target || !target.classList.contains("QUERY-status-select")) return
      const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
      const status = target.value
      if (accountId && status) {
        const account = sharedData.gameAccounts.find((account) => account.id === accountId)
        if (account) {
          this.accountId = account.id
          this.switchStatus(status)
        }
      }
    })
  }

  switchDeviceType() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    AppLoadingHelper.show()
    GameAccountService.switchDeviceType(this.accountId)
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(this.accountId)
          Toaster.success("Thông báo", "Cập nhật loại máy thành công")
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        this.isSubmitting = false
        AppLoadingHelper.hide()
      })
  }

  initSwitchDeviceTypeQuickly() {
    this.accountsTableBody.addEventListener("click", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-switch-device-type-btn")) {
        target = target.parentElement
        if (
          (target && target.classList.contains("QUERY-account-row-item")) ||
          target.tagName === "BODY"
        ) {
          break
        }
      }
      if (!target || !target.classList.contains("QUERY-switch-device-type-btn")) return
      const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
      if (accountId) {
        const account = sharedData.gameAccounts.find((account) => account.id === accountId)
        if (account) {
          this.accountId = account.id
          this.switchDeviceType()
        }
      }
    })
  }

  switchRank(rank) {
    const accountId = this.accountId
    AppLoadingHelper.show()
    GameAccountService.updateAccount(accountId, { rank })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initSwitchRankQuickly() {
    this.accountsTableBody.addEventListener("change", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-ranks-select")) {
        target = target.parentElement
        if (target && target.classList.contains("QUERY-account-row-item")) {
          break
        }
      }
      if (!target || !target.classList.contains("QUERY-ranks-select")) return
      const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
      const rank = target.value
      if (accountId && rank) {
        const account = sharedData.gameAccounts.find((account) => account.id === accountId)
        if (account) {
          this.accountId = account.id
          this.switchRank(rank)
        }
      }
    })
  }

  switchAccType(accType) {
    const accountId = this.accountId
    AppLoadingHelper.show()
    GameAccountService.updateAccount(accountId, { accType })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initSwitchAccTypeQuickly() {
    this.accountsTableBody.addEventListener("change", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-acc-types-select")) {
        target = target.parentElement
        if (target && target.classList.contains("QUERY-account-row-item")) {
          break
        }
      }
      if (!target || !target.classList.contains("QUERY-acc-types-select")) return
      const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
      const accType = target.value
      if (accountId && accType) {
        const account = sharedData.gameAccounts.find((account) => account.id === accountId)
        if (account) {
          this.accountId = account.id
          this.switchAccType(accType)
        }
      }
    })
  }
}

// class này có load data dùng chung cho các class khác trong cùng file này
class FilterManager {
  constructor() {
    this.filtersSection = document.getElementById("filters-section")
    this.toggleFiltersBtn = document.getElementById("toggle-filters-btn")
    this.rankTypesSelect = this.filtersSection.querySelector(".QUERY-rank-types-select")
    this.statusesSelect = this.filtersSection.querySelector(".QUERY-statuses-select")
    this.deviceTypeSelect = this.filtersSection.querySelector(".QUERY-device-type-select")
    this.accTypeSelect = this.filtersSection.querySelector(".QUERY-acc-type-select")
    this.searchBtn = document.getElementById("search-btn")
    this.searchInput = document.getElementById("search-input")
    this.countAppliedFilters = document.getElementById("count-applied-filters")

    this.fieldsRenderedCount = 2
    this.appliedFiltersCount = 0
    this.urlForFilters = ""

    this.renderRankTypes()
    this.fetchStatuses()
    this.fetchDeviceTypes()
    this.fetchAccTypes()

    this.initShowFilters()
    this.initListeners()
  }

  initListeners() {
    this.toggleFiltersBtn.addEventListener("click", (e) => {
      if (this.filtersSection.hidden) {
        this.showFilters()
      } else {
        this.hideFilters()
      }
    })

    document
      .getElementById("reset-all-filters-btn")
      .addEventListener("click", this.resetAllFilters.bind(this))

    document
      .getElementById("apply-filters-btn")
      .addEventListener("click", this.applyFilters.bind(this))
  }

  initInputListeners() {
    this.rankTypesSelect.addEventListener("change", this.handleFilterChange.bind(this))
    this.statusesSelect.addEventListener("change", this.handleFilterChange.bind(this))
    this.deviceTypeSelect.addEventListener("change", this.handleFilterChange.bind(this))
    this.accTypeSelect.addEventListener("change", this.handleFilterChange.bind(this))
    this.searchBtn.addEventListener("click", this.searchAccounts.bind(this))
    this.searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        this.searchAccounts()
      }
    })
  }

  adjustAppliedFiltersCount() {
    this.countAppliedFilters.hidden = this.appliedFiltersCount === 0
    this.countAppliedFilters.textContent = this.appliedFiltersCount
    this.countAppliedFilters.dataset.countAppliedFilters = this.appliedFiltersCount
    initUtils.linkTooltip(
      this.countAppliedFilters,
      `Bộ lọc đã áp dụng: ${this.appliedFiltersCount}`
    )
  }

  hideFilters() {
    this.toggleFiltersBtn.classList.remove("bg-blue-50", "border-blue-300", "text-blue-700")
    this.toggleFiltersBtn.classList.add("border-gray-300", "text-gray-700", "hover:bg-gray-50")
    this.filtersSection.hidden = true
    LocalStorageHelper.setShowFilters(false)
  }

  showFilters() {
    this.toggleFiltersBtn.classList.remove("border-gray-300", "text-gray-700", "hover:bg-gray-50")
    this.toggleFiltersBtn.classList.add("bg-blue-50", "border-blue-300", "text-blue-700")
    this.filtersSection.hidden = false
    LocalStorageHelper.setShowFilters(true)
  }

  initShowFilters() {
    const showFilters = LocalStorageHelper.getShowFilters()
    if (showFilters && showFilters === "true") {
      this.showFilters()
    }
  }

  renderRankTypes() {
    const rankTypes = sharedData.rankTypes

    const allOption = document.createElement("option")
    allOption.value = "ALL"
    allOption.textContent = "Tất cả rank"
    this.rankTypesSelect.appendChild(allOption)

    for (const rankType of rankTypes) {
      const option = document.createElement("option")
      option.value = rankType.type
      option.textContent = rankType.type
      this.rankTypesSelect.appendChild(option)
    }

    this.fieldsRenderedCount++
    this.updateActiveFiltersDisplay()
  }

  fetchStatuses() {
    const allOption = document.createElement("option")
    allOption.value = "ALL"
    allOption.textContent = "Tất cả trạng thái"
    this.statusesSelect.appendChild(allOption)

    const statuses = ["Rảnh", "Bận", "Check"]
    for (const status of statuses) {
      const option = document.createElement("option")
      option.value = status
      option.textContent = status
      this.statusesSelect.appendChild(option)
    }

    this.fieldsRenderedCount++
    this.updateActiveFiltersDisplay()
  }

  fetchDeviceTypes() {
    const allOption = document.createElement("option")
    allOption.value = "ALL"
    allOption.textContent = "Tất cả loại máy"
    this.deviceTypeSelect.appendChild(allOption)

    const deviceTypes = ["Tất cả", "Only máy nhà"]
    for (const deviceType of deviceTypes) {
      const option = document.createElement("option")
      option.value = deviceType
      option.textContent = deviceType
      this.deviceTypeSelect.appendChild(option)
    }

    this.fieldsRenderedCount++
    this.updateActiveFiltersDisplay()
  }

  fetchAccTypes() {
    const allOption = document.createElement("option")
    allOption.value = "ALL"
    allOption.textContent = "Tất cả loại acc"
    this.accTypeSelect.appendChild(allOption)

    const accTypes = ["Thường", "Đặc biệt"]
    for (const accType of accTypes) {
      const option = document.createElement("option")
      option.value = accType
      option.textContent = accType
      this.accTypeSelect.appendChild(option)
    }

    this.fieldsRenderedCount++
    this.updateActiveFiltersDisplay()
  }

  saveQueryStringForFilters(keyValuePair = "rank=&status=&device_type=&acc_type=") {
    const currentUrlForFilters = new URL(this.urlForFilters || window.location.href)
    const params = new URLSearchParams(keyValuePair)
    for (const [key, value] of params.entries()) {
      if (value) {
        currentUrlForFilters.searchParams.set(key, value)
      } else {
        currentUrlForFilters.searchParams.delete(key)
      }
    }
    this.urlForFilters = currentUrlForFilters.toString()
  }

  handleFilterChange(e) {
    const field = e.currentTarget
    const value = field.value
    switch (field.id) {
      case "rank-type-filter-field":
        this.saveQueryStringForFilters(
          "rank=" + (value && value !== "ALL" ? encodeURIComponent(value) : "")
        )
        break
      case "status-filter-field":
        this.saveQueryStringForFilters(
          "status=" + (value && value !== "ALL" ? encodeURIComponent(value) : "")
        )
        break
      case "device-type-filter-field":
        this.saveQueryStringForFilters(
          "device_type=" + (value && value !== "ALL" ? encodeURIComponent(value) : "")
        )
        break
      case "acc-type-filter-field":
        this.saveQueryStringForFilters(
          "acc_type=" + (value && value !== "ALL" ? encodeURIComponent(value) : "")
        )
        break
    }
  }

  applyFilters() {
    NavigationHelper.pureNavigateTo(this.urlForFilters)
  }

  resetAllFilters() {
    this.saveQueryStringForFilters()
    this.applyFilters()
  }

  updateActiveFiltersDisplay() {
    if (this.fieldsRenderedCount < 5) return

    const rankValue = URLHelper.getUrlQueryParam("rank")
    if (rankValue) {
      this.rankTypesSelect.value = rankValue
      this.appliedFiltersCount++
    } else {
      this.rankTypesSelect.value = "ALL"
    }

    const statusValue = URLHelper.getUrlQueryParam("status")
    if (statusValue) {
      this.statusesSelect.value = statusValue
      this.appliedFiltersCount++
    } else {
      this.statusesSelect.value = "ALL"
    }

    const deviceTypeValue = URLHelper.getUrlQueryParam("device_type")
    if (deviceTypeValue) {
      this.deviceTypeSelect.value = deviceTypeValue
      this.appliedFiltersCount++
    } else {
      this.deviceTypeSelect.value = "ALL"
    }

    const accTypeValue = URLHelper.getUrlQueryParam("acc_type")
    if (accTypeValue) {
      this.accTypeSelect.value = accTypeValue
      this.appliedFiltersCount++
    } else {
      this.accTypeSelect.value = "ALL"
    }

    const searchTerm = URLHelper.getUrlQueryParam("search_term")
    if (searchTerm) {
      this.searchInput.value = searchTerm
      this.appliedFiltersCount++
    } else {
      this.searchInput.value = ""
    }

    this.adjustAppliedFiltersCount()
    this.initInputListeners()
  }

  searchAccounts() {
    const searchTerm = this.searchInput.value.trim()
    if (searchTerm) {
      this.saveQueryStringForFilters("search_term=" + encodeURIComponent(searchTerm))
    } else {
      this.saveQueryStringForFilters("search_term=")
    }
    this.applyFilters()
  }
}

const uiEditor = new UIEditor()
new AddNewAccountManager()
const updateAccountManager = new UpdateAccountManager()
const deleteAccountManager = new DeleteAccountManager()
new FilterManager()
new ManageGameAccountsPageManager()