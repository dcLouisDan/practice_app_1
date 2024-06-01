<script setup>
import { computed } from "vue";
const props = defineProps({
    notification: Object,
});

const notificationData = computed(() => {
    return props.notification.data;
});
</script>
<template>
    <div>{{ notificationData.message }}</div>
</template>
