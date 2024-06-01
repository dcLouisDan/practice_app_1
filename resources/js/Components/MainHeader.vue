<script setup>
import ApplicationLogo from "./ApplicationLogo.vue";
const props = defineProps({
    canBack: {
        type: Boolean,
        default: false,
    },
    title: String,
});
const back = () => window.history.back();
</script>
<template>
    <div class="bg-gray-100 z-50">
        <div class="flex items-center justify-center py-2 sm:hidden">
            <ApplicationLogo class="h-12" />
        </div>
        <div
            class="py-3 px-5 flex gap-3 items-center border-b-2 border-gray-300"
        >
            <button
                @click="back"
                class="flex items-center text-gray-600 hover:bg-gray-300 rounded-full p-1 transition-all ease-in-out"
                v-if="canBack"
            >
                <span class="material-icons">arrow_back</span>
            </button>
            <h1 class="font-bold text-lg text-gray-600">{{ title }}</h1>
        </div>
    </div>
</template>
