<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
const props = defineProps({
    chirp: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="pt-10">
            <Chirp class="border-b-2 border-t-2" :chirp="chirp" />
        </div>
    </AuthenticatedLayout>
</template>
