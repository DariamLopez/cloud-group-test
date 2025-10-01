import api from '../api';
export default {
    getTasks() {
        return api.get('/tasks'); // llamará a /api/tasks
    },
    getTask(id) {
        return api.get(`/tasks/${id}`);
    },
    createTask(payload) {
        return api.post('/tasks', payload);
    },
    updateTask(id, payload) {
        return api.put(`/tasks/${id}`, payload);
    },
    deleteTask(id) {
        return api.delete(`/tasks/${id}`);
    },
};
