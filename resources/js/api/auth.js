import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Accept": "application/json", 
        "Content-Type": "application/json" 
    } 
});

export default {
  login(data) {
    return api.post("/login", data);
  },
  register(data) {
    return api.post("/register", data);
  },
  logout() {
    return api.post("/logout");
  },
  me() {
    return api.get("/me");
  }
};
