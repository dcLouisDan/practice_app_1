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
        <NotificationListItem
            v-for="notification in notifications"
            :notification="notification"
        />
    </AuthenticatedLayout>
</template>
