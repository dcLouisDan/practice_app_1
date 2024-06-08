<script setup>
import { ref, onMounted, watch, nextTick } from "vue";
const props = defineProps({
    placeholder: String,
    rows: Number,
});

const content = defineModel({
    type: String,
    required: true,
});

const emit = defineEmits(["focus", "blur"]);

const textarea = ref(null);
const adjustHeight = () => {
    nextTick(() => {
        if (textarea.value) {
            textarea.value.style.height = "auto";
            textarea.value.style.height = `${textarea.value.scrollHeight}px`;
        }
    });
};

const handleFocus = () => {
    emit("focus");
    adjustHeight();
};

const handleBlur = () => {
    emit("blur");
};

watch(() => props.modelValue, adjustHeight);

onMounted(adjustHeight);
</script>

<template>
    <textarea
        class="resize-none bg-inherit text-lg border-x-0 border-t-0 border-b-0 focus:border-b-2 focus:border-none focus:ring-0 w-full"
        v-model="content"
        ref="textarea"
        :placeholder="placeholder"
        @focus="handleFocus"
        @blur="handleBlur"
        @input="adjustHeight"
        :rows="rows"
    ></textarea>
</template>
