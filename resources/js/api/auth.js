import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Accept": "application/json", 
        "Content-Type": "application/json" 
    } 
});

api.interceptors.request.use(config => {
  // Switch to cookie-based authentication with Sanctum
  axios.defaults.withCredentials = true;
  if (token) config.headers.Authorization = `Bearer ${token}`;
  console.log("REQUEST:", config.method.toUpperCase(), config.url);
  return config;
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
