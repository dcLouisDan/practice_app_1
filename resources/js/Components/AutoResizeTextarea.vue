<script setup>
import { ref, onMounted, watch } from "vue";
const props = defineProps({
    placeholder: String,
});
const content = ref("");
const textarea = ref(null);
console.log(textarea.value);
const adjustHeight = () => {
    if (textarea.value) {
        textarea.value.style.height = "auto";
        textarea.value.style.height = `${textarea.value.scrollHeight}px`;
    }
};

watch(content, adjustHeight);

onMounted(adjustHeight);
</script>

<template>
    <textarea
        class="resize-none bg-inherit text-lg"
        v-model="content"
        ref="textarea"
        :placeholder="placeholder"
        @input="adjustHeight"
    ></textarea>
</template>
