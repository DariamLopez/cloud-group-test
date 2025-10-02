<script setup>
const props = defineProps({
  pagination: { type: Object, required: true },
  pages: { type: Array, required: true },
});
const emit = defineEmits(['change-page']);

function go(page) {
  if (page < 1 || page > props.pagination.last_page) return;
  emit('change-page', page);
}
</script>

<template>
  <nav v-if="pagination.last_page > 1" aria-label="Page navigation">
    <ul class="pagination">
      <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
        <button class="page-link" @click.prevent="go(pagination.current_page - 1)" :disabled="pagination.current_page <= 1">Previous</button>
      </li>

      <li class="page-item" v-for="p in pages" :key="p" :class="{ active: p === pagination.current_page }">
        <button class="page-link" @click.prevent="go(p)">{{ p }}</button>
      </li>

      <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
        <button class="page-link" @click.prevent="go(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page">Next</button>
      </li>
    </ul>
  </nav>
</template>
