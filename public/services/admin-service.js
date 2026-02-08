import { axiosClient } from "../utils/scripts/api-client.js"

export class AdminService {
  static async updateProfile(adminData, rulesData, commitmentData) {
    const dataToSubmit = new FormData()
    dataToSubmit.set("adminData", JSON.stringify(adminData))
    if (rulesData) {
      dataToSubmit.set("rulesData", rulesData)
    }
    if (commitmentData) {
      dataToSubmit.set("commitmentData", commitmentData)
    }
    const { data } = await axiosClient.post("/admin/update-profile", dataToSubmit)
    return data
  }
}
