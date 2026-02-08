import { axiosClient } from "../utils/scripts/api-client.js?v=natk"

export class AuthService {
  static async login(username, password) {
    const dataToSubmit = new FormData()
    dataToSubmit.append("username", username)
    dataToSubmit.append("password", password)
    const { data } = await axiosClient.post("/auth/login", dataToSubmit)
    return data
  }

  static async logout() {
    const { data } = await axiosClient.get("/auth/logout")
    return data
  }
}
