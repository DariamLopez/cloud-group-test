<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    task: { type: Object, required: true },
    selectedKeywords: { type: Array, default: () => [] },
});
const emit = defineEmits(['toggle-done', 'attach-keywords', 'detach-keyword', 'toggle-keyword']);

function onToggleDone() {
    emit('toggle-done', props.task);
}

function onAttach() {
    emit('attach-keywords', props.task.id);
}

function onDetach(keywordId) {
    emit('detach-keyword', keywordId, props.task.id);
}

function onToggleKeyword(keyword) {
    emit('toggle-keyword', keyword);
}
</script>

<template>
    <tr>
        <th scope="row">{{ task.id }}</th>
        <td>{{ task.title }}</td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" :id="`doneSwitch-${task.id}`" :checked="task.is_done" @change="onToggleDone" />
            </div>
        </td>
        <td>
            <span
                @click="onDetach(keyword.id)"
                v-for="keyword in task.keywords"
                :key="keyword.id"
                class="badge bg-secondary me-1"
                style="cursor: pointer"
            >
                {{ keyword.name }}
            </span>
        </td>
        <td>
            <button class="btn btn-sm btn-outline-primary" @click="onAttach">Attach Keywords</button>
        </td>
    </tr>
</template>
