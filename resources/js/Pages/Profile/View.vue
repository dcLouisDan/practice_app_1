<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps(["chirps"]);
const page = usePage();
const user = page.props.auth.user;
const chirpsWithLikes = computed(() =>
    props.chirps.map((chirp) => ({
        ...chirp,
        isLikedByUser: chirp.likes.some((like) => like.user_id === user.id),
    }))
);
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <div class="px-3 py-5 border-b-2">
            <h1 class="text-lg font-bold">{{ user.name }}</h1>
            <p class="text-gray-500">{{ user.email }}</p>
        </div>
        <div class="divide-y border-b-2">
            <Chirp
                v-for="chirp in chirpsWithLikes"
                :key="chirp.id"
                :chirp="chirp"
                :isLiked="chirp.isLikedByUser"
            />
        </div>
    </AuthenticatedLayout>
</template>
