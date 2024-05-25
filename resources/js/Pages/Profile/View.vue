<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import axios from "axios";

const props = defineProps(["chirps", "followers", "following"]);
const page = usePage();
const user = page.props.auth.user;
const chirpsWithLikes = computed(() =>
    props.chirps.map((chirp) => ({
        ...chirp,
        isLikedByUser: chirp.likes.some((like) => like.user_id === user.id),
        context: "profile",
    }))
);
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <div class="px-3 py-5 border-b-2">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold">{{ user.name }}</h1>
                    <p class="text-gray-500">{{ user.email }}</p>
                </div>
            </div>
            <div class="flex gap-5 text-sm text-gray-500 font-bold">
                <div class="flex gap-3 items-center mt-5">
                    <p>Followers</p>
                    <p>{{ followers.length }}</p>
                </div>
                <div class="flex gap-3 items-center mt-5">
                    <p>Following</p>
                    <p>{{ following.length }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y border-b-2">
            <Chirp
                v-for="chirp in chirpsWithLikes"
                :key="chirp.id"
                :chirp="chirp"
                :isLiked="chirp.isLikedByUser"
                :context="chirp.context"
            />
        </div>
    </AuthenticatedLayout>
</template>
