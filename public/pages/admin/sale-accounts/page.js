import { SaleAccountService } from "../../../services/sale-account-service.js"
import { AccountPreviewRow, SaleAccountRow } from "../../../utils/scripts/components.js"
import {
  LitHTMLHelper,
  AppLoadingHelper,
  AxiosErrorHandler,
  Toaster,
  URLHelper,
  NavigationHelper,
  LocalStorageHelper,
  StringHelper,
} from "../../../utils/scripts/helpers.js"
import { initUtils } from "../../../utils/scripts/init-utils.js"

const sharedData = {
  saleAccounts: [],
}

class SaleAccountsPageManager {
  constructor() {
    this.accountsTableBody = document.getElementById("accounts-table-body")
    this.loadMoreContainer = document.getElementById("load-more-container")
    this.scrollToTopBtn = document.getElementById("scroll-to-top-btn")
    this.scrollToBottomBtn = document.getElementById("scroll-to-bottom-btn")

    this.isFetchingItems = false
    this.isMoreItems = true
    this.SELL_TO_TIME_INPUT_FORMAT = "YYYY-MM-DD HH:mm:ss"
    this.clickedAccountRowId = null

    this.fetchAccounts()

    this.watchScrolling()

    this.initInputListeners()
    this.initListeners()
  }

  getAccountsTableBodyEle() {
    return this.accountsTableBody
  }

  getLastAccount() {
    const accounts = sharedData.saleAccounts
    if (accounts.length > 0) {
      return accounts.at(-1)
    }
    return null
  }

  fetchAccounts() {
    if (this.isFetchingItems || !this.isMoreItems) return
    this.isFetchingItems = true

    AppLoadingHelper.show("Đang tải dữ liệu...")
    const lastAccount = this.getLastAccount()
    let last_id = lastAccount ? lastAccount.id : null
    const status = URLHelper.getUrlQueryParam("status")
    const search_term = URLHelper.getUrlQueryParam("search_term")
    const letter = URLHelper.getUrlQueryParam("letter")

    SaleAccountService.fetchAccounts(last_id, status, search_term, letter)
      .then((accounts) => {
        if (accounts && accounts.length > 0) {
          const startOrderNumber = sharedData.saleAccounts.length + 1
          sharedData.saleAccounts = [...sharedData.saleAccounts, ...accounts]
          this.renderNewAccounts(accounts, startOrderNumber)
          this.initCatchDeleteAndUpdateAccountBtnClick()
          this.initCatchStatusSelectChange()
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

  updateAccountSellToTime(accountId, sellToTime) {
    // Kiểm tra định dạng "HH:mm DD/MM/YYYY" bằng dayjs
    const formattedSellToTime = dayjs(sellToTime, "HH:mm DD/MM/YYYY", true)
    if (!formattedSellToTime.isValid()) {
      Toaster.error(
        "Định dạng không phù hợp",
        "Thời gian sale phải theo định dạng HH:mm DD/MM/YYYY (ví dụ: 13:00 05/08/2025)"
      )
      return
    } else if (formattedSellToTime.isBefore(dayjs())) {
      Toaster.error("Thời gian không hợp lệ", "Thời gian sale phải lớn hơn thời gian hiện tại")
      return
    }
    const timeToUpdate = formattedSellToTime.format(this.SELL_TO_TIME_INPUT_FORMAT)
    // cập nhật thời gian sale
    AppLoadingHelper.show("Đang cập nhật thời gian sale...")
    SaleAccountService.updateAccount(accountId, { sell_to_time: timeToUpdate })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
          Toaster.success("Thông báo", "Cập nhật thời gian sale thành công")
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
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
      if (input.classList.contains("QUERY-sell-to-time-input")) {
        const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
        this.updateAccountSellToTime(accountId, input.value)
      }
    })
    this.accountsTableBody.addEventListener("keydown", (e) => {
      if (e.key !== "Enter") return
      e.preventDefault()
      let target = e.target
      while (target && !target.classList.contains("QUERY-sell-to-time-input")) {
        target = target.parentElement
        if (target && (target.id === "QUERY-account-row-item" || target.tagName === "BODY")) {
          return
        }
      }
      if (target && target.classList.contains("QUERY-sell-to-time-input")) {
        const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
        this.updateAccountSellToTime(accountId, target.value)
      }
    })
  }

  initCatchDeleteAndUpdateAccountBtnClick() {
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

  updateAccountStatus(accountId, status) {
    AppLoadingHelper.show("Đang cập nhật trạng thái...")
    SaleAccountService.updateAccount(accountId, { status })
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
          Toaster.success("Thông báo", "Cập nhật trạng thái thành công")
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initCatchStatusSelectChange() {
    this.accountsTableBody.addEventListener("change", (e) => {
      let target = e.target
      while (target && !target.classList.contains("QUERY-status-select")) {
        target = target.parentElement
        if (target && (target.id === "QUERY-account-row-item" || target.tagName === "BODY")) {
          return
        }
      }
      if (target && target.classList.contains("QUERY-status-select")) {
        const accountId = target.closest(".QUERY-account-row-item").dataset.accountId * 1
        this.updateAccountStatus(accountId, target.value)
      }
    })
  }

  renderNewAccounts(newAccounts, startOrderNumber) {
    let order_number = startOrderNumber
    for (const account of newAccounts) {
      const accountRow = LitHTMLHelper.getFragment(SaleAccountRow, account, order_number)
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
      const THRESHOLD = 300
      if (window.scrollY > THRESHOLD) {
        this.scrollToTopBtn.classList.remove("bottom-[-4.26em]")
        this.scrollToTopBtn.classList.add("bottom-[1.71em]")
      } else {
        this.scrollToTopBtn.classList.remove("bottom-[1.71em]")
        this.scrollToTopBtn.classList.add("bottom-[-4.26em]")
      }
      if (window.scrollY < document.body.scrollHeight - window.innerHeight - THRESHOLD) {
        this.scrollToBottomBtn.classList.remove("bottom-[-4.26em]")
        this.scrollToBottomBtn.classList.add("bottom-[7.14em]")
      } else {
        this.scrollToBottomBtn.classList.remove("bottom-[7.14em]")
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
}

class UIEditor {
  constructor() {
    this.accountsTableBody = document.getElementById("accounts-table-body")
  }

  setAccountRow(accountRow, accountData, orderNumber) {
    const newAccountRow = LitHTMLHelper.getFragment(SaleAccountRow, accountData, orderNumber)
    accountRow.replaceWith(newAccountRow)
  }

  refreshAccountRowOnUI(accountId) {
    SaleAccountService.fetchSingleAccount(accountId)
      .then((data) => {
        if (data && data.success) {
          const account = data.account
          if (account) {
            sharedData.saleAccounts = sharedData.saleAccounts.map((acc) =>
              acc.id === accountId ? account : acc
            )
            const accountRow = this.accountsTableBody.querySelector(
              `.QUERY-account-row-item-${accountId}`
            )
            if (accountRow) {
              this.setAccountRow(accountRow, account, accountRow.dataset.accountOrderNumber * 1)
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
  constructor() {
    this.addNewAccountModal = document.getElementById("add-new-account-modal")
    this.addNewAccountForm = document.getElementById("add-new-account-form")
    this.pickAvatarSection = document.getElementById("pick-avatar--add-section")
    this.avatarPreview = document.getElementById("avatar-preview-img--add-section")
    this.avatarInput = document.getElementById("avatar-input--add-section")

    this.isSubmitting = false
    this.SELL_TO_TIME_INPUT_FORMAT = "YYYY-MM-DD HH:mm:ss"

    this.initListeners()
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

  handleAvatarInputChange(e) {
    const file = e.target.files[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (e) => {
        this.avatarPreview.src = e.target.result
        this.pickAvatarSection.classList.remove("QUERY-at-avatar-input-section")
        this.pickAvatarSection.classList.add("QUERY-at-avatar-preview-section")
      }
      reader.readAsDataURL(file)
    }
  }

  handleRemoveAvatar() {
    this.avatarPreview.src = ""
    this.avatarInput.value = null
    this.pickAvatarSection.classList.remove("QUERY-at-avatar-preview-section")
    this.pickAvatarSection.classList.add("QUERY-at-avatar-input-section")
  }

  showModal() {
    this.addNewAccountModal.hidden = false
  }

  hideModal() {
    this.addNewAccountModal.hidden = true
    this.addNewAccountForm.reset()
  }

  validateFormData({ gmail, price, status, letter, description }) {
    if (!gmail) {
      Toaster.error("Gmail không được để trống")
      return false
    }
    if (!price) {
      Toaster.error("Giá không được để trống")
      return false
    }
    if (!letter) {
      Toaster.error("Thư không được để trống")
      return false
    }
    if (!status) {
      Toaster.error("Trạng thái không được để trống")
      return false
    }
    if (!description) {
      Toaster.error("Mô tả không được để trống")
      return false
    }
    return true
  }

  submitAddAccount() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    const formData = new FormData(this.addNewAccountForm)
    const data = {
      description: formData.get("description"),
      status: formData.get("status"),
      letter: formData.get("letter"),
      gmail: formData.get("gmail"),
      price: formData.get("price"),
      sell_to_time: dayjs().add(1, "month").format(this.SELL_TO_TIME_INPUT_FORMAT),
    }
    if (!this.validateFormData({ ...data })) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show()
    SaleAccountService.addNewAccounts([data], this.avatarInput.files?.[0])
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
    const account = sharedData.saleAccounts.find((account) => account.id === this.accountId)
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
    SaleAccountService.deleteAccount(this.accountId)
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
  constructor() {
    this.updateAccountModal = document.getElementById("update-account-modal")
    this.updateAccountForm = document.getElementById("update-account-form")
    this.pickAvatarSection = document.getElementById("pick-avatar--update-section")
    this.avatarPreview = document.getElementById("avatar-preview-img--update-section")
    this.avatarInput = document.getElementById("avatar-input--update-section")
    this.accountsTableBody = document.getElementById("accounts-table-body")

    this.isSubmitting = false

    this.initListeners()
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
  }

  switchLetter(accountId) {
    AppLoadingHelper.show("Đang đổi thư...")
    SaleAccountService.switchLetterQuickly(accountId)
      .then((data) => {
        if (data && data.success) {
          uiEditor.refreshAccountRowOnUI(accountId)
          Toaster.success("Thành công", "Đổi thư thành công")
        }
      })
      .catch((error) => {
        Toaster.error(AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
      })
  }

  initSwitchLetterQuickly() {
    this.accountsTableBody.addEventListener("click", (e) => {
      const target = e.target
      while (target && !target.classList.contains("QUERY-switch-letter-btn")) {
        target = target.parentElement
        if (target && (target.id === "QUERY-account-row-item" || target.tagName === "BODY")) {
          return
        }
      }
      if (target && target.classList.contains("QUERY-switch-letter-btn")) {
        const accountId = target.closest(".QUERY-account-row-item").dataset.accountId
        this.switchLetter(accountId)
      }
    })
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
    const file = e.target.files[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (e) => {
        this.avatarPreview.src = e.target.result
        this.switchToAvatarPreviewSection()
      }
      reader.readAsDataURL(file)
    }
  }

  handleRemoveAvatar() {
    this.avatarPreview.src = ""
    this.avatarPreview.style.maxHeight = "fit-content"
    this.avatarInput.value = null
    this.switchToAvatarInputSection()
  }

  showModal(accountId) {
    this.accountId = accountId
    const account = sharedData.saleAccounts.find((account) => account.id === this.accountId)
    const { description, status, letter, avatar, gmail, price } = account
    // document.getElementById("update-account-name").textContent = letter
    this.updateAccountForm.querySelector("textarea[name='description']").value = description || ""
    this.updateAccountForm.querySelector("input[name='status']").value = status
    this.updateAccountForm.querySelector("select[name='letter']").value = letter
    this.updateAccountForm.querySelector("input[name='gmail']").value = gmail
    this.updateAccountForm.querySelector("input[name='price']").value = price
    this.updateAccountModal.hidden = false
    this.avatarPreview.src = `/images/account/${avatar || "default-account-avatar.png"}`
    if (!avatar) {
      this.avatarPreview.style.maxHeight = "200px"
    }
    this.switchToAvatarPreviewSection()
  }

  hideModal() {
    this.updateAccountModal.hidden = true
    this.updateAccountForm.reset()
  }

  validateFormData({ gmail, price, status, letter, description }) {
    if (!gmail) {
      Toaster.error("Gmail không được để trống")
      return false
    }
    if (!price) {
      Toaster.error("Giá không được để trống")
      return false
    }
    if (!letter) {
      Toaster.error("Thư không được để trống")
      return false
    }
    if (!status) {
      Toaster.error("Trạng thái không được để trống")
      return false
    }
    if (!description) {
      Toaster.error("Mô tả không được để trống")
      return false
    }
    return true
  }

  submitUpdateAccount() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    const formData = new FormData(this.updateAccountForm)
    const data = {
      description: formData.get("description"),
      status: formData.get("status"),
      letter: formData.get("letter"),
      gmail: formData.get("gmail"),
      price: formData.get("price"),
    }
    if (!this.validateFormData({ ...data })) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show()
    SaleAccountService.updateAccount(this.accountId, data, this.avatarInput.files?.[0])
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
}

// class ImportExportManager {
//   constructor() {
//     this.accountsPreviewModal = document.getElementById("accounts-preview-modal")

//     this.accountsImporting = []

//     this.initListeners()
//   }

//   initListeners() {
//     document.getElementById("export-accounts-table-to-excel-btn").addEventListener("click", (e) => {
//       this.exportAccountsTableToExcel()
//     })

//     document.getElementById("import-accounts-from-excel-btn").addEventListener("click", (e) => {
//       this.importAccountsFromExcel()
//     })

//     this.accountsPreviewModal
//       .querySelector(".QUERY-accounts-preview-overlay")
//       .addEventListener("click", (e) => {
//         this.accountsImporting = []
//         this.hideAccountsPreviewModal()
//       })

//     document.getElementById("start-importing-accounts-btn").addEventListener("click", (e) => {
//       this.processImportAccounts()
//     })

//     document.getElementById("cancel-importing-accounts-btn").addEventListener("click", (e) => {
//       this.accountsImporting = []
//       this.hideAccountsPreviewModal()
//     })
//   }

//   showAccountsPreviewModal() {
//     this.accountsPreviewModal.hidden = false
//     const accountsPreviewTableBody = document.getElementById("accounts-preview-table-body")
//     accountsPreviewTableBody.innerHTML = ""

//     let order_number = 1
//     const accounts = this.accountsImporting
//     for (const account of accounts) {
//       const accountRow = LitHTMLHelper.getFragment(AccountPreviewRow, account, order_number)
//       accountsPreviewTableBody.appendChild(accountRow)
//       order_number++
//     }

//     initUtils.initTooltip()
//   }

//   hideAccountsPreviewModal() {
//     this.accountsPreviewModal.hidden = true
//   }

//   exportAccountsTableToExcel() {
//     const rows = []
//     const headerRow = ["Avatar", "Thư", "Giá", "Gmail", "Trạng thái", "Mô tả"]
//     rows.push(headerRow)

//     // Lấy tất cả các hàng từ tbody
//     const tbody = document.getElementById("accounts-table-body")
//     const trList = tbody.querySelectorAll("tr")

//     trList.forEach((tr) => {
//       const tds = tr.querySelectorAll("td")

//       // Cấu trúc cột (dựa trên thứ tự column bạn định nghĩa):
//       const avatar = tds[1]?.querySelector("img")?.src.split("images/account/")[1]
//       const descRow = tds[6]
//       const description = descRow?.querySelector(".QUERY-no-description")
//         ? undefined
//         : descRow.innerText.trim()
//       const row = [
//         avatar, // avatar
//         tds[2]?.innerText.trim(), // letter
//         tds[3]?.innerText.trim(), // price
//         tds[4]?.innerText.trim(), // gmail
//         tds[5]?.innerText.trim(), // status
//         description, // description
//       ]

//       rows.push(row)
//     })

//     const worksheet = XLSX.utils.aoa_to_sheet(rows)
//     const workbook = XLSX.utils.book_new()
//     XLSX.utils.book_append_sheet(workbook, worksheet, "SaleAccounts")

//     const today = dayjs().format("YYYY-MM-DD_HH-mm-ss")
//     XLSX.writeFile(workbook, `DanhSachTaiKhoanSale_${today}.xlsx`)
//   }

//   importAccountsFromExcel() {
//     const input = document.createElement("input")
//     input.type = "file"
//     input.accept = ".xlsx,.xls"
//     input.style.display = "none"

//     input.addEventListener("change", (event) => {
//       const file = event.target.files[0]
//       if (!file) return

//       const reader = new FileReader()
//       reader.onload = (e) => {
//         try {
//           const data = new Uint8Array(e.target.result)
//           const workbook = XLSX.read(data, { type: "array" })
//           const worksheet = workbook.Sheets[workbook.SheetNames[0]]
//           const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 })

//           // Bỏ qua header row
//           const rows = jsonData.slice(1)

//           if (rows.length === 0) {
//             Toaster.error("Lỗi", "File Excel không có dữ liệu")
//             return
//           }

//           const accounts = rows
//             .map((row) => ({
//               letter: row[1] || "",
//               price: row[2] || "",
//               gmail: row[3] || "",
//               status: row[4] || "",
//               description: row[5] || "",
//             }))
//             .filter((account) => account.letter && account.price && account.gmail && account.status)

//           if (accounts.length === 0) {
//             Toaster.error("Lỗi", "Không có dữ liệu hợp lệ trong file Excel")
//             return
//           }

//           this.accountsImporting = accounts
//           this.showAccountsPreviewModal()
//         } catch (error) {
//           Toaster.error("Lỗi", "Lỗi khi đọc file Excel")
//         }
//       }

//       reader.readAsArrayBuffer(file)
//     })

//     document.body.appendChild(input)
//     input.click()
//     document.body.removeChild(input)
//   }

//   processImportAccounts() {
//     const accounts = this.accountsImporting
//     AppLoadingHelper.show()
//     SaleAccountService.addNewAccounts(accounts)
//       .then((data) => {
//         if (data && data.success) {
//           Toaster.success("Thông báo", `Đã tải lên thành công ${accounts.length} tài khoản`, () => {
//             NavigationHelper.reloadPage()
//           })
//         }
//       })
//       .catch((error) => {
//         Toaster.error("Lỗi thêm tài khoản", AxiosErrorHandler.handleHTTPError(error).message)
//       })
//       .finally(() => {
//         AppLoadingHelper.hide()
//       })
//   }
// }

class FilterManager {
  constructor() {
    this.filtersSection = document.getElementById("filters-section")
    this.toggleFiltersBtn = document.getElementById("toggle-filters-btn")
    this.statusInput = this.filtersSection.querySelector(".QUERY-status-input")
    this.letterSelect = this.filtersSection.querySelector(".QUERY-letter-select")
    this.searchBtn = document.getElementById("search-btn")
    this.searchInput = document.getElementById("search-input")
    this.countAppliedFilters = document.getElementById("count-applied-filters")

    this.appliedFiltersCount = 0
    this.urlForFilters = ""

    this.initShowFilters()
    this.initListeners()
    this.updateActiveFiltersDisplay()
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
    this.letterSelect.addEventListener("change", this.handleFilterSelectChange.bind(this))
    this.searchBtn.addEventListener("click", this.searchAccounts.bind(this))
    this.searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        this.searchAccounts()
      }
    })
    this.statusInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        this.applyFilters()
      }
    })
  }

  handleFilterSelectChange(e) {
    const formField = e.currentTarget
    const value = formField.value
    switch (formField.id) {
      case "letter-filter-field":
        this.saveQueryStringForFilters(
          "letter=" + (value && value !== "ALL" ? encodeURIComponent(value) : "")
        )
        break
    }
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

  saveQueryStringForFilters(keyValuePair = "status=&letter=&search_term=") {
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

  saveInputValueToQueryString() {
    const statusValue = this.statusInput.value?.trim()
    if (statusValue) {
      this.saveQueryStringForFilters(
        "status=" + encodeURIComponent(StringHelper.capitalizeFirstLetter(statusValue))
      )
    } else {
      this.saveQueryStringForFilters("status=")
    }
  }

  applyFilters() {
    this.saveInputValueToQueryString()
    NavigationHelper.pureNavigateTo(this.urlForFilters)
  }

  resetAllFilters() {
    this.saveQueryStringForFilters()
    NavigationHelper.pureNavigateTo(this.urlForFilters)
  }

  updateActiveFiltersDisplay() {
    const statusValue = URLHelper.getUrlQueryParam("status")
    if (statusValue) {
      this.statusInput.value = statusValue
      this.appliedFiltersCount++
    }

    const letterValue = URLHelper.getUrlQueryParam("letter")
    if (letterValue) {
      this.letterSelect.value = letterValue
      this.appliedFiltersCount++
    } else {
      this.letterSelect.value = "ALL"
    }

    const searchTerm = URLHelper.getUrlQueryParam("search_term")
    if (searchTerm) {
      this.searchInput.value = searchTerm
      this.appliedFiltersCount++
    }

    this.adjustAppliedFiltersCount()
    this.initInputListeners()
  }

  searchAccounts() {
    const searchTerm = this.searchInput.value?.trim()
    if (searchTerm) {
      this.saveQueryStringForFilters("search_term=" + encodeURIComponent(searchTerm))
    } else {
      this.saveQueryStringForFilters("search_term=")
    }
    this.applyFilters()
  }
}

const uiEditor = new UIEditor()
// new ImportExportManager()
new AddNewAccountManager()
const updateAccountManager = new UpdateAccountManager()
const deleteAccountManager = new DeleteAccountManager()
new FilterManager()
new SaleAccountsPageManager()
