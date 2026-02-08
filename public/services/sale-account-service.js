import { axiosClient } from "../utils/scripts/api-client.js"

export class SaleAccountService {
  static FETCH_ACCOUNTS_LIMIT = 10

  static async fetchAccounts(last_id, status, search_term, letter) {
    const params = {
      limit: SaleAccountService.FETCH_ACCOUNTS_LIMIT,
    }
    if (last_id) params.last_id = last_id
    if (status) params.status = status
    if (search_term) params.search_term = search_term
    if (letter) params.letter = letter
    const { data } = await axiosClient.get("/sale-accounts/load-more", { params })
    return data.accounts
  }

  static async addNewAccounts(accountsFormData, imgFile) {
    const dataToSubmit = new FormData()
    dataToSubmit.set("accounts", JSON.stringify(accountsFormData))
    if (imgFile) dataToSubmit.set("avatar", imgFile)
    const { data } = await axiosClient.post("/sale-accounts/add-new", dataToSubmit)
    return data
  }

  static async updateAccount(accountId, updatesData, avatar) {
    const dataToSubmit = new FormData()
    dataToSubmit.set("account", JSON.stringify(updatesData))
    if (avatar) dataToSubmit.set("avatar", avatar)
    const { data } = await axiosClient.post(`/sale-accounts/update/${accountId}`, dataToSubmit)
    return data
  }

  static async deleteAccount(accountId) {
    const { data } = await axiosClient.delete(`/sale-accounts/delete/${accountId}`)
    return data
  }

  static async fetchSingleAccount(accountId) {
    const { data } = await axiosClient.get(`/sale-accounts/fetch-single-account/${accountId}`)
    return data
  }

  static async switchLetterQuickly(accountId) {
    const { data } = await axiosClient.put(`/sale-accounts/switch-letter-quickly/${accountId}`)
    return data
  }
}
