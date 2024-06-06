<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Search from "@/Components/Search.vue";
import UserResultList from "@/Components/UserResultList.vue";
import { computed } from "vue";
import ChirpFeed from "@/Components/ChirpFeed.vue";
const props = defineProps({ query: String, users: Array, chirps: Array });

const isQueryEmpty = computed(() => {
    return props.query === "";
});
// console.log(props);
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
            <UserResultList :searchParam="query" />
        </div>
        <div
            class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
            v-if="props.chirps"
        >
            <h4 class="font-bold px-4 py-2">Chirps</h4>
            <ChirpFeed route="search.chirps" :searchParam="query" />
        </div>
    </AuthenticatedLayout>
</template>
