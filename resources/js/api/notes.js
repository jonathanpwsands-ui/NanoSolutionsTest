import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
});

// Debugging
api.interceptors.request.use(config => {
    console.log("AXIOS REQUEST:", config.method.toUpperCase(), config.url, config.data);
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
    list() {
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
