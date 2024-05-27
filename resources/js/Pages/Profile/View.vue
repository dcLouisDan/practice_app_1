<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import Dropdown from "@/Components/Dropdown.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import RoundedPrimaryButton from "@/Components/RoundedPrimaryButton.vue";
import axios from "axios";
import DropdownLink from "@/Components/DropdownLink.vue";

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

const profilePicture = computed(() => {
    return user.profile_picture_url || "images/profile_placeholder.png";
});
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <div class="px-3 py-5 border-b-2">
            <div class="px-4 py-10">
                <img
                    :src="profilePicture"
                    alt="Profile Picture"
                    class="rounded-full border-4 border-gray-500 h-36 w-36 object-cover"
                />
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold">{{ user.name }}</h1>
                    <p class="text-gray-500">@{{ user.username }}</p>
                </div>
                <Dropdown>
                    <template #trigger>
                        <RoundedPrimaryButton>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-gray-50"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                                />
                            </svg>
                        </RoundedPrimaryButton>
                    </template>
                    <template #content>
                        <DropdownLink
                            as="button"
                            :href="route('profile.edit')"
                            method="get"
                        >
                            Edit Profile
                        </DropdownLink>
                        <DropdownLink
                            as="button"
                            :href="route('logout')"
                            method="post"
                        >
                            Logout
                        </DropdownLink>
                    </template>
                </Dropdown>
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
