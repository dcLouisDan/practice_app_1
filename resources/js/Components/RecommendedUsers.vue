<script setup>
import { onMounted, ref } from "vue";
import UserResult from "./UserResult.vue";

const recommendedUsers = ref([]);

const fetchUsers = async () => {
    return await axios.get(route("users.recommend")).then((response) => {
        // console.log(response.data);
        recommendedUsers.value = response.data.data;
    });
};

onMounted(() => {
    fetchUsers();
    console.log(recommendedUsers.value);
});
</script>

<template>
    <div
        class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
        v-if="recommendedUsers"
    >
        <h4 class="font-bold px-4 py-2">People to Follow</h4>
        <div
            class="text-center text-gray-500 py-5 text-xs"
            v-if="recommendedUsers.length === 0"
        >
            Wow! It seems you're already following everybody!
        </div>
        <UserResult
            v-for="user in recommendedUsers"
            :user="user"
            :key="user.id"
            size="sm"
        />
    </div>
</template>
