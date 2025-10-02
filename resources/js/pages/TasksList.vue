<script setup>
import { onMounted, ref, computed } from 'vue';
import Pagination from '@/components/Pagination.vue';
import TaskRow from '@/components/TaskRow.vue';
import api from '../api';

const tasks = ref([]);
const keywords = ref([]);
const error = ref(null);

// Para el input de keywords
const selectedKeywords = ref([]); // array de objetos { id, name }
const keywordsInput = ref(''); // texto que mostrará el input
const newKeywordName = ref(''); // para crear una nueva keyword


const newTitle = ref('');
const creating = ref(false);

// Server / validation errors from backend (Laravel FormRequest returns 422 + { errors })
const validationErrors = ref({});
const errorMessage = ref(null);
const titleError = ref(false);
// pagination state
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});
onMounted(async () => {
    await fetchTasks(1);
    try {
        const resKeywords = await api.get('/keywords'); // llamará a /api/keywords
        keywords.value = resKeywords.data;
    } catch (e) {
        error.value = e;
        console.error(e);
    }
});
const pages = computed(() => {
    const out = [];
    const last = pagination.value.last_page || 1;
    for (let i = 1; i <= last; i++) out.push(i);
    return out;
});
const attachKeyword = async (taskId) => {
    try {
        const res = await api.post(`/keywords/attach-to-task/${taskId}`, {
            keywords: selectedKeywords.value.map((k) => k.name),
        });
        // Actualiza la tarea en la lista
        const index = tasks.value.data.findIndex((t) => t.id === taskId);
        if (index !== -1) {
            tasks.value.data[index] = res.data;
        }
    } catch (e) {
        errorKeywordAttach = true;
    }
};
const detachKeyword = async (keywordId, taskId) => {
    try {
        const res = await api.post(`/keywords/${keywordId}/dettach-from-task/${taskId}`);
        // Actualiza la tarea en la lista
        const index = tasks.value.data.findIndex((t) => t.id === taskId);
        if (index !== -1) {
            tasks.value.data[index] = res.data;
        }
    } catch (e) {
        console.error(e);
    }
};
const cleanKeywords = () => {
    selectedKeywords.value = [];
    keywordsInput.value = '';
};
const createKeyword = async () => {
    try {
        const res = await api.post('/keywords',{name: newKeywordName.value});
        keywords.value.push(res.data);
        newKeywordName.value = '';
    } catch (e) {
        if (e.response && e.response.status === 422) {
            validationErrors.value = e.response.data.errors || {};
            errorMessage.value = e.response.data.message || null;
        } else {
            console.error(e);
            errorMessage.value = e.message || String(e);
        }
    }
};
// Toggle selection de una keyword (añade o quita)
function toggleKeywordSelect(keyword) {
    const idx = selectedKeywords.value.findIndex((k) => k.id === keyword.id);
    if (idx === -1) {
        selectedKeywords.value.push(keyword);
    } else {
        selectedKeywords.value.splice(idx, 1);
    }
    // Actualizar el input con los nombres concatenados
    keywordsInput.value = selectedKeywords.value.map((k) => k.name).join(', ');
}
// Toggle done
async function toggleDone(task) {
    try {
        const payload = { is_done: !task.is_done };
        const res = await api.put(`/tasks/${task.id}`, payload);
        // actualizar localmente la tarea (soporta paginación tasks.data)
        if (tasks.value && Array.isArray(tasks.value.data)) {
            const i = tasks.value.data.findIndex((t) => t.id === task.id);
            if (i !== -1) tasks.value.data[i] = res.data;
        } else if (Array.isArray(tasks.value)) {
            const i = tasks.value.findIndex((t) => t.id === task.id);
            if (i !== -1) tasks.value[i] = res.data;
        }
    } catch (e) {
        console.error(e);
    }
}
async function createNewTask() {
    if (newTitle.value.length === 0) {
        titleError.value = true;
        console.log("Title is required");
        return;
    }
    try {
        let keyword_name = selectedKeywords.value.map((k) => k.name);
        creating.value = true;
        const payload = {
            title: newTitle.value,
            keywords: keyword_name,
        };
        const res = await api.post('/tasks', payload);
        const created = res.data;

        // Insertar la tarea creada al inicio, soportando paginación o array
        if (tasks.value && Array.isArray(tasks.value.data)) {
            tasks.value.data.unshift(created);
        } else if (Array.isArray(tasks.value)) {
            tasks.value.unshift(created);
        }

        // reset form
        newTitle.value = '';
        selectedKeywords.value = [];
        keywordsInput.value = '';
    } catch (e) {
        console.error("Error: " + e);
        error.value = e;
    } finally {
        creating.value = false;
    }
}
// Fetch tasks with page param and normalize response
async function fetchTasks(page = 1) {
    try {
        const res = await api.get('/tasks', { params: { page } });
        const payload = res.data;
        // If API returns plain array
        if (Array.isArray(payload)) {
            tasks.value = { data: payload };
            pagination.value = { current_page: 1, last_page: 1, per_page: payload.length, total: payload.length };
            return;
        }

        // If API returns Laravel-style paginator (data + meta)
        if (payload && Array.isArray(payload.data)) {
            tasks.value = payload;
            const meta = payload.meta ?? payload;
            pagination.value.current_page = meta.current_page ?? payload.current_page ?? 1;
            pagination.value.last_page = meta.last_page ?? payload.last_page ?? 1;
            pagination.value.per_page = meta.per_page ?? pagination.value.per_page;
            pagination.value.total = meta.total ?? pagination.value.total;
            return;
        }

        // Fallback
        tasks.value = { data: [] };
    } catch (e) {
        error.value = e;
        console.error(e);
    }
}
</script>
<template>
    <div class="container mt-5">
        <h1 class="mb-4">Tasks</h1>

        <!-- Server error / validation message -->
        <div v-if="errorMessage" class="alert alert-danger" role="alert">
            {{ errorMessage }}
        </div>

        <table class="table-striped-columns table-hover table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">DONE</th>
                    <th scope="col">KEYWORDS</th>
                    <th scope="col">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <TaskRow v-for="task in tasks.data" :task="task" :selectedKeywords="selectedKeywords" @toggle-done="toggleDone" @attach-keywords="attachKeyword" @detach-keyword="detachKeyword"/>
            </tbody>
        </table>
        <Pagination :pagination="pagination" :pages="pages" @change-page="fetchTasks"/>
    </div>
    <div class="container mt-3">
        <h1 class="mb-4">Tasks Form</h1>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Title</label>
                <label v-if="titleError" class="text-danger">* Title is required</label>
                <!-- Server validation error for title -->
                <div v-if="validationErrors.title" class="text-danger">{{ validationErrors.title[0] }}</div>
                <input class="form-control" id="exampleInputTitle" v-model="newTitle" placeholder="Add a Title for your task" />
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Keywords</label>
                <div class="input-group">
                    <input type="text" readonly class="form-control" id="InputKeywords" v-model="keywordsInput" placeholder="Add keywords">
                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        @click="cleanKeywords"
                        title="Clear keywords"
                        aria-label="Clear keywords"
                    >
                       &times;
                    </button>
                </input>
                </div>
                <!-- Server validation error for keywords (when creating task) -->
                <div v-if="validationErrors.keywords" class="text-danger">{{ validationErrors.keywords[0] }}</div>
                <select class="form-select mt-1" multiple aria-label="Multiple select example">
                    <option @click="toggleKeywordSelect(keyword)" v-for="keyword in keywords" :value="keyword.id">{{ keyword.name }}</option>
                </select>
                <button :disabled="creating" @click="createNewTask" class="btn btn-primary">
                    {{ creating ? 'Creating...' : 'Create new task' }}
                </button>
            </div>
        <input class="form-control mt-3" id="exampleInputTitle" v-model="newKeywordName" placeholder="Type a keyword" />
        <!-- Server validation for keyword creation -->
        <div v-if="validationErrors.name" class="text-danger">{{ validationErrors.name[0] }}</div>
        <button @click="createKeyword" class="btn btn-primary">
            Create new keyword
        </button>
    </div>
</template>

<style scoped>
.form-control:focus {
    border-color: #00000000 !important;
    box-shadow: 0 0 0 0.25rem rgba(245, 48, 3, 0.15) !important;
    outline: none;
}
</style>
