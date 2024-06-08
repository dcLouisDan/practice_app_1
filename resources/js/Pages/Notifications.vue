<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MainHeader from "@/Components/MainHeader.vue";
import NotificationListItem from "@/Components/NotificationListItem.vue";
import { ref, onMounted } from "vue";
const notifications = ref([]);

const pageNumber = ref(1);
const loading = ref(false);
const hasMore = ref(true);

const fetchNotifications = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route("notifications.get", {
                page: pageNumber.value,
            })
        );

        if (!response.data.next_page_url) {
            hasMore.value = false;
        }

        pageNumber.value += 1;
        return notifications.value.push(...response.data.data);
    } catch (error) {
        console.error("Error fetching chirps:", error);
        return [];
    } finally {
        loading.value = false;
    }
};
onMounted(() => {
    fetchNotifications();
});
</script>
<template>
    <AuthenticatedLayout>
        <MainHeader title="Notifications" />
        <div class="divide-y-2 border-b-2 divide-gray-300">
            <div
                class="text-center px-12 py-14 text-lg text-gray-400"
                v-if="notifications.length === 0"
            >
                You're all caught up! No new notifications.
            </div>
            <NotificationListItem
                v-for="notification in notifications"
                :notification="notification"
            />
            <button
                class="text-sm font-bold text-center py-3 w-full hover:bg-gray-200 active:bg-gray-50"
                v-if="hasMore"
                @click="fetchNotifications"
            >
                Show More
            </button>
        </div>
    </AuthenticatedLayout>
</template>
