import axios from "axios";
import { useAuthStore } from '../stores/auth';

const api = axios.create({
    baseURL: "/api/",
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
});

// Debugging
api.interceptors.request.use(config => {
    const token = localStorage.getItem('authToken');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    console.log("REQUEST:", config.method.toUpperCase(), config.url);
    return config;
});

api.interceptors.response.use(
    response => {
        console.log("AXIOS RESPONSE:", response);
        return response;
    },
    error => {
        console.error("AXIOS RESPONSE ERROR:", error);
        return Promise.reject(error);
    }
);

export default {
    index() {
        return api.get("/notes");
    },
    create(data) {
        return api.post("/notes", data);
    },
    get(id) {
        return api.get(`/notes/${id}`);
    },
    update(id, data) {
        console.log("API CALL → PUT", `/notes/${id}`, data);
        return api.put(`/notes/${id}`, data);
    },
    delete(id) {
        console.log("API CALL → DELETE", `/notes/${id}`);
        return api.delete(`/notes/${id}`);
    }
};
