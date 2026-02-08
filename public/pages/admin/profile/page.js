import {
  Toaster,
  AxiosErrorHandler,
  AppLoadingHelper,
  NavigationHelper,
} from "../../../utils/scripts/helpers.js"
import { AdminService } from "../../../services/admin-service.js"

class ProfilePageManager {
  constructor() {
    this.hideShowPasswordSection = document.getElementById("hide-show-password-section")
    this.usernameInput = document.getElementById("username-input")
    this.fullNameInput = document.getElementById("full-name-input")
    this.phoneInput = document.getElementById("phone-input")
    this.facebookLinkInput = document.getElementById("facebook-link-input")
    this.zaloLinkInput = document.getElementById("zalo-link-input")
    this.passwordInput = document.getElementById("password-input")
    this.scrollToTopBtn = document.getElementById("scroll-to-top-btn")
    this.updateProfileBtn = document.getElementById("update-profile-btn")
    this.rulesTextarea = document.getElementById("rules-textarea")
    this.commitmentTextarea = document.getElementById("commitment-textarea")

    this.MIN_LENGTH_OF_PASSWORD = 6

    this.isSubmitting = false

    this.initListeners()
    this.watchScrolling()
  }

  validateFormData({ username, full_name, phone, facebook_link, zalo_link, password }) {
    if (!username) {
      Toaster.error("Tên người dùng không được để trống")
      return false
    }
    if (!full_name) {
      Toaster.error("Tên đầy đủ không được để trống")
      return false
    }
    if (!phone) {
      Toaster.error("Số điện thoại không được để trống")
      return false
    }
    if (!facebook_link) {
      Toaster.error("Link facebook không được để trống")
      return false
    }
    if (!zalo_link) {
      Toaster.error("Link zalo không được để trống")
      return false
    }
    if (password && password.length < this.MIN_LENGTH_OF_PASSWORD) {
      Toaster.error(`Mật khẩu phải có ít nhất ${this.MIN_LENGTH_OF_PASSWORD} ký tự`)
      return false
    }
    return true
  }

  submitUpdateProfile() {
    if (this.isSubmitting) return
    this.isSubmitting = true

    const dataToSend = {
      username: this.usernameInput.value,
      full_name: this.fullNameInput.value,
      phone: this.phoneInput.value,
      facebook_link: this.facebookLinkInput.value,
      zalo_link: this.zaloLinkInput.value,
      password: this.passwordInput.value,
    }

    if (!this.validateFormData(dataToSend)) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show("Đang cập nhật hồ sơ...")
    AdminService.updateProfile(
      dataToSend,
      this.rulesTextarea.value || null,
      this.commitmentTextarea.value || null
    )
      .then((res) => {
        Toaster.success(
          "Cập nhật thành công",
          "Thông tin cá nhân đã được cập nhật thành công",
          () => {
            NavigationHelper.reloadPage()
          }
        )
      })
      .catch((err) => {
        AxiosErrorHandler.handleHTTPError(err)
      })
      .finally(() => {
        AppLoadingHelper.hide()
        this.isSubmitting = false
      })
  }

  showPassword() {
    this.hideShowPasswordSection.classList.remove("QUERY-hide-password")
    this.hideShowPasswordSection.classList.add("QUERY-show-password")
    this.passwordInput.type = "text"
  }

  hidePassword() {
    this.hideShowPasswordSection.classList.remove("QUERY-show-password")
    this.hideShowPasswordSection.classList.add("QUERY-hide-password")
    this.passwordInput.type = "password"
  }

  initHideShowPasswordSection() {
    this.hideShowPasswordSection.addEventListener("click", () => {
      if (this.hideShowPasswordSection.classList.contains("QUERY-hide-password")) {
        this.showPassword()
      } else {
        this.hidePassword()
      }
    })
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

  catchEnterKey(e) {
    if (e.key === "Enter") {
      this.submitUpdateProfile()
    }
  }

  initListeners() {
    this.initHideShowPasswordSection()
    this.scrollToTopBtn.addEventListener("click", this.scrollToTop.bind(this))
    this.updateProfileBtn.addEventListener("click", this.submitUpdateProfile.bind(this))

    this.usernameInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.fullNameInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.phoneInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.facebookLinkInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.zaloLinkInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.passwordInput.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.rulesTextarea.addEventListener("keydown", this.catchEnterKey.bind(this))
    this.commitmentTextarea.addEventListener("keydown", this.catchEnterKey.bind(this))
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
    })
  }
}

new ProfilePageManager()
