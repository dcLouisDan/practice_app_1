<script setup>
import UserResult from "./UserResult.vue";
import { onMounted, ref } from "vue";
const props = defineProps({
    searchParam: {
        type: String,
        default: "",
    },
});

const users = ref([]);
const pageNumber = ref(1);
const loading = ref(false);
const hasMore = ref(true);

const fetchUsers = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route("search.users", {
                page: pageNumber.value,
                query: props.searchParam,
            })
        );
        // console.log(response.data);
        // console.log(pageNumber.value);
        if (!response.data.next_page_url) {
            hasMore.value = false;
        }

        pageNumber.value += 1;
        return users.value.push(...response.data.data);
    } catch (error) {
        console.error("Error fetching chirps:", error);
        return [];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <div v-if="loading" class="text-center py-4">Loading...</div>
    <div
        class="text-center text-gray-500 py-5"
        v-if="searchParam !== '' && users.length === 0"
    >
        No people found.
    </div>
    <UserResult v-for="user in users" :user="user" :key="user.id" />
    <button
        class="text-center text-sm py-3 w-full hover:bg-gray-200 active:bg-gray-50"
        v-if="hasMore"
        @click="fetchUsers"
    >
        Show More
    </button>
</template>
