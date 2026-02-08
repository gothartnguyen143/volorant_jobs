import { render } from "https://esm.run/lit-html@1"

export class LitHTMLHelper {
  static getFragment(Render, ...data) {
    const container = document.createElement("div")
    render(Render(...data), container)
    return container.firstElementChild
  }
}

export class AxiosErrorHandler {
  static MAX_LEN_OF_ERROR_MESSAGE = 100

  static handleHTTPError(originalError) {
    let statusCode = 500 // Internal Server Error
    let message = "Unknown Error!"
    let isCanceled = false

    if (this.isAxiosError(originalError)) {
      const responseOfError = originalError.response

      if (responseOfError) {
        // if error was made by server at backend
        statusCode = responseOfError.status // update error status

        const dataOfResponse = responseOfError.data

        if (typeof dataOfResponse === "string") {
          message = "Invalid request"
        } else {
          message = dataOfResponse.message // update error message

          if (message.length > AxiosErrorHandler.MAX_LEN_OF_ERROR_MESSAGE) {
            message = `${message.slice(0, AxiosErrorHandler.MAX_LEN_OF_ERROR_MESSAGE)}...`
          }
        }
      } else if (originalError.request) {
        // The request was made but no response was received
        statusCode = 502 // Bad Gateway
        message = "Bad network or error from server."
      } else {
        // Something happened in setting up the request that triggered an Error
        message = originalError.message
      }
    } else if (originalError instanceof axios.CanceledError) {
      isCanceled = true
      message = originalError.message
    } else if (originalError instanceof Error) {
      message = originalError.message
    }

    return {
      originalError,
      statusCode,
      message,
      isCanceled,
    }
  }

  static isAxiosError(error) {
    return error instanceof axios.AxiosError
  }
}

export class Toaster {
  static success(title, message, callback) {
    Swal.fire({
      icon: "success",
      title,
      text: message,
    }).then((result) => {
      if (result.isConfirmed) {
        if (callback) {
          callback()
        }
      }
    })
  }

  static error(title, message, callback) {
    Swal.fire({
      icon: "error",
      title,
      text: message,
    }).then((result) => {
      if (result.isConfirmed) {
        if (callback) {
          callback()
        }
      }
    })
  }
}

export class AppLoadingHelper {
  static element = document.getElementById("app-loading")

  static show(message) {
    this.setMessage(message)
    this.element.hidden = false
  }

  static hide() {
    this.element.hidden = true
  }

  static setMessage(message) {
    this.element.querySelector(".QUERY-app-loading-message").textContent = message
  }
}

export class URLHelper {
  static currentUrlSearchParams = new URLSearchParams(window.location.search)

  static getUrlQueryParam(key) {
    const value = this.currentUrlSearchParams.get(key)
    return value ? decodeURIComponent(value) : ""
  }
}

export class NavigationHelper {
  static pureNavigateTo(locationHref) {
    window.location.href = locationHref
  }

  static reloadPage() {
    window.location.reload()
  }
}

export class LocalStorageHelper {
  static KEY_SHOW_FILTERS = "show-filters"

  static setShowFilters(show) {
    localStorage.setItem(this.KEY_SHOW_FILTERS, show)
  }

  static getShowFilters() {
    return localStorage.getItem(this.KEY_SHOW_FILTERS)
  }
}

export class TimeHelper {
  static OUT_OF_TIME = "OUT_OF_TIME"
  static INVALID_TIME = "INVALID_TIME"
  static NOT_STARTED = "NOT_STARTED"

  static getRemainingRentalTime(rentFrom, rentTo, NOT_STARTED_TEXT, OUT_OF_TIME_TEXT) {
    const now = new Date()
    const fromTime = new Date(rentFrom)
    const toTime = new Date(rentTo)

    if (now < fromTime) {
      return NOT_STARTED_TEXT || this.NOT_STARTED
    }

    if (now >= toTime) {
      return OUT_OF_TIME_TEXT || this.OUT_OF_TIME
    }

    const remainingMs = toTime - now

    const totalSeconds = Math.floor(remainingMs / 1000)
    const hours = Math.floor(totalSeconds / 3600)
    const minutes = Math.floor((totalSeconds % 3600) / 60)
    const seconds = totalSeconds % 60

    return `${hours} giờ ${minutes} phút ${seconds} giây`
  }

  static getRemainingSellToTime(futureTimeStr, INVALID_TIME_TEXT) {
    const now = new Date()
    const future = new Date(futureTimeStr)

    if (isNaN(future.getTime()) || future <= now) {
      return INVALID_TIME_TEXT || this.INVALID_TIME
    }

    const diffInSeconds = Math.floor((future - now) / 1000)

    const days = Math.floor(diffInSeconds / (60 * 60 * 24))
    const hours = Math.floor((diffInSeconds % (60 * 60 * 24)) / (60 * 60))
    const minutes = Math.floor((diffInSeconds % (60 * 60)) / 60)
    const seconds = diffInSeconds % 60

    return `Còn ${days} ngày, ${hours} giờ, ${minutes} phút, ${seconds} giây`
  }

  static getRentalDuration(rentFrom, rentTo, INVALID_TIME_TEXT) {
    const from = new Date(rentFrom)
    const to = new Date(rentTo)

    if (isNaN(from) || isNaN(to)) {
      return INVALID_TIME_TEXT || this.INVALID_TIME
    }

    const durationMs = to - from

    if (durationMs <= 0) {
      return INVALID_TIME_TEXT || this.INVALID_TIME
    }

    const totalMinutes = Math.ceil(durationMs / 60000)
    const hours = Math.floor(totalMinutes / 60)
    const minutes = totalMinutes % 60

    return `${hours} giờ ${minutes} phút`
  }
}

export class ValidationHelper {
  static isPureInteger(text) {
    return /^\d+$/.test(text)
  }

  /**
   * Chỉ chứa chữ cái, số, gạch dưới; độ dài 3-20 ký tự.
   * @param {string} username
   * @returns {boolean}
   */
  static isValidUsername(username) {
    if (!username || typeof username !== "string") return false
    const regex = /^[a-zA-Z0-9_]{3,20}$/
    return regex.test(username)
  }
}

export class StringHelper {
  static capitalizeFirstLetter(text) {
    return text.charAt(0).toUpperCase() + text.slice(1)
  }
}

export class AccountHelper {
  static getAccountStatusColor(lowerCasedStatus, defaultColor = "bg-white") {
    if (lowerCasedStatus === "rảnh") {
      return "bg-green-600"
    } else if (lowerCasedStatus === "bận") {
      return "bg-red-600"
    } else if (lowerCasedStatus === "check") {
      return "bg-yellow-400"
    }
    return defaultColor
  }

  static getAccRowBgColorByStatus(lowerCasedStatus, defaultColor = "bg-white") {
    if (lowerCasedStatus === "bận") {
      return "bg-red-100"
    } else if (lowerCasedStatus === "check") {
      return "bg-yellow-100"
    }
    return defaultColor
  }
}

export class ThemeHelper {
  static updateCssVariable(cssVarName, value) {
    document.documentElement.style.setProperty(cssVarName, value)
  }

  static updateAccountStateColor(color, fromColor, viaColor, toColor) {
    this.updateCssVariable("--vcn-regular-acc-state-cl", color)
    this.updateCssVariable("--vcn-regular-acc-state-from-cl", fromColor)
    this.updateCssVariable("--vcn-regular-acc-state-via-cl", viaColor)
    this.updateCssVariable("--vcn-regular-acc-state-to-cl", toColor)
  }
}
