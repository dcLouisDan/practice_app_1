<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    query: String,
    isFocused: { type: Boolean, default: false },
});
const form = useForm({ query: props.query });
const input = ref(null);

onMounted(() => {
    if (props.isFocused) {
        input.value.focus();
    }
});
</script>

<template>
    <form @submit.prevent="form.get(route('search'))">
        <div
            class="border-2 bg-white relative border-gray-300 w-full rounded-full focus:border-indigo-500 focus:ring-indigo-500 overflow-clip"
        >
            <div
                class="absolute top-1/2 left-3 -translate-y-1/2 flex items-center text-gray-500"
            >
                <span class="material-icons">search</span>
            </div>
            <input
                class="w-full border-none ms-8 focus:ring-0"
                v-model="form.query"
                ref="input"
                placeholder="Search"
            />
        </div>
    </form>
</template>
