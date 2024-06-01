<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MainHeader from "@/Components/MainHeader.vue";
import NotificationListItem from "@/Components/NotificationListItem.vue";
import { ref, onMounted } from "vue";
const notifications = ref([]);

onMounted(() => {
    axios.get(route("notifications.get")).then((response) => {
        // console.log(response.data);
        notifications.value = response.data;
    });
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
        </div>
    </AuthenticatedLayout>
</template>
