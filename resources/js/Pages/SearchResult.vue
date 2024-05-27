<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Search from "@/Components/Search.vue";
import Chirp from "@/Components/Chirp.vue";
import UserResult from "@/Components/UserResult.vue";
const props = defineProps(["query", "users", "chirps"]);

console.log(props.users);
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-3 py-5">
            <Search :query="props.query" />
        </div>
        <div class="divide-y divide-gray-300 border-b-2 border-b-gray-300">
            <h4 class="font-bold px-4 py-2">People</h4>
            <UserResult
                v-for="user in props.users"
                :user="user"
                :key="user.id"
            />
        </div>
        <div class="divide-y divide-gray-300 border-b-2 border-b-gray-300">
            <h4 class="font-bold px-4 py-2">Chirps</h4>
            <Chirp
                v-for="chirp in props.chirps"
                :chirp="chirp"
                :key="chirp.id"
            />
        </div>
    </AuthenticatedLayout>
</template>
