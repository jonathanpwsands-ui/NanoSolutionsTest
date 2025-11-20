import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
});

export default {
    list() {
        return api.get('/notes');
    },
    get(id) {
        return api.get(`/notes/${id}`);
    },
    create(data) {
        return api.post('/notes', data);
    },
    update(id, data) {
        return api.put(`/notes/${id}`, data);
    },
    delete(id) {
        return api.delete(`/notes/${id}`);
    }
};
