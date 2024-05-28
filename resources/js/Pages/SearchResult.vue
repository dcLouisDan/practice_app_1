<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Search from "@/Components/Search.vue";
import Chirp from "@/Components/Chirp.vue";
import UserResult from "@/Components/UserResult.vue";
import { computed } from "vue";
const props = defineProps({ query: String, users: Array, chirps: Array });

const isQueryEmpty = computed(() => {
    return props.query === "";
});

console.log(props.query);
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-3 py-5">
            <Search :query="props.query" :isFocused="isQueryEmpty" />
        </div>
        <div
            class="text-center text-gray-500 py-5"
            v-if="!props.users && !props.chirps"
        >
            Try seaching for people or keywords.
        </div>
        <div
            class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
            v-if="props.users"
        >
            <h4 class="font-bold px-4 py-2">People</h4>
            <div
                class="text-center text-gray-500 py-5"
                v-if="props.users.length === 0"
            >
                No people found.
            </div>
            <UserResult
                v-for="user in props.users"
                :user="user"
                :key="user.id"
            />
        </div>
        <div
            class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
            v-if="props.chirps"
        >
            <h4 class="font-bold px-4 py-2">Chirps</h4>
            <div
                class="text-center text-gray-500 py-5"
                v-if="props.chirps.length === 0"
            >
                No chirps found.
            </div>
            <Chirp
                v-for="chirp in props.chirps"
                :chirp="chirp"
                :key="chirp.id"
            />
        </div>
    </AuthenticatedLayout>
</template>
