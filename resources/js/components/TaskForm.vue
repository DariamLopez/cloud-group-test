<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  keywords: { type: Array, default: () => [] },
  selectedKeywords: { type: Array, default: () => [] },
  creating: { type: Boolean, default: false },
});

const emit = defineEmits(['create-task', 'create-keyword', 'toggle-keyword', 'clear-keywords']);

const title = ref('');
const newKeyword = ref('');
const keywordsInput = ref('');

watch(() => props.selectedKeywords, (v) => {
    console.log('Selected keywords changed:', v);
  keywordsInput.value = v.map(k => k.name).join(', ');
}, { immediate: true });

function onSubmit() {
  emit('create-task', { title: title.value });
}

function onCreateKeyword() {
  if (!newKeyword.value) return;
  emit('create-keyword', newKeyword.value);
  newKeyword.value = '';
}

function onClear() {
  emit('clear-keywords');
}
</script>

<template>
  <div>
    <form @submit.prevent="onSubmit">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input v-model="title" class="form-control" placeholder="Add a Title for your task" />
      </div>

      <div class="mb-3">
        <label class="form-label">Keywords</label>
        <div class="input-group">
          <input type="text" readonly class="form-control" v-model="keywordsInput" />
          <button class="btn btn-outline-secondary" type="button" @click="onClear">&times;</button>
        </div>

        <select class="form-select mt-1" multiple>
          <option v-for="k in keywords" :key="k.id" :value="k.id" @click.prevent="$emit('toggle-keyword', k)">{{ k.name }}</option>
        </select>

        <button :disabled="props.creating" class="btn btn-primary mt-2" type="submit">{{ props.creating ? 'Creating...' : 'Create new task' }}</button>
      </div>
    </form>

    <div class="mt-3">
      <input v-model="newKeyword" class="form-control" placeholder="New keyword name" />
      <button @click="onCreateKeyword" class="btn btn-primary mt-2">Create new keyword</button>
    </div>
  </div>
</template>
