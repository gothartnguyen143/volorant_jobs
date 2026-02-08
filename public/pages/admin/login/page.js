import {
  Toaster,
  NavigationHelper,
  AxiosErrorHandler,
  AppLoadingHelper,
} from "../../../utils/scripts/helpers.js"
import { AuthService } from "../../../services/auth-service.js"

class LoginPageManager {
  constructor() {
    this.hideShowPasswordSection = document.getElementById("hide-show-password-section")
    this.usernameInput = document.getElementById("username-input")
    this.passwordInput = document.getElementById("password-input")
    this.loginForm = document.getElementById("login-form")

    this.isSubmitting = false

    this.initListeners()
    this.initErrorMessage()
    this.initHideShowPasswordSection()
  }

  initErrorMessage() {
    const errorMessage = document.getElementById("error-message")
    if (errorMessage) {
      Toaster.error("Lỗi đăng nhập", errorMessage.textContent)
    }
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

  initListeners() {
    this.loginForm.addEventListener("submit", this.submitLogin.bind(this))
  }

  validateFormData({ username, password }) {
    if (!username) {
      Toaster.error("Tên người dùng không được để trống")
      return false
    }
    if (!password) {
      Toaster.error("Mật khẩu không được để trống")
      return false
    }
    return true
  }

  submitLogin(e) {
    e.preventDefault()
    if (this.isSubmitting) return
    this.isSubmitting = true

    const username = this.usernameInput.value
    const password = this.passwordInput.value

    if (!this.validateFormData({ username, password })) {
      this.isSubmitting = false
      return
    }

    AppLoadingHelper.show()
    AuthService.login(username, password)
      .then((data) => {
        if (data.success) {
          NavigationHelper.pureNavigateTo("/admin/manage-game-accounts")
        } else {
          Toaster.error(data.message)
        }
      })
      .catch((error) => {
        Toaster.error("Lỗi đăng nhập", AxiosErrorHandler.handleHTTPError(error).message)
      })
      .finally(() => {
        AppLoadingHelper.hide()
        this.isSubmitting = false
      })
  }
}

new LoginPageManager()
