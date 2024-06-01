<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    notification: Object,
});

const notificationData = computed(() => {
    return props.notification.data;
});

console.log(props.notification);

const wasRead = computed(() => {
    return props.notification.read_at != null;
});
const icons = {
    like: {
        icon: "favorite",
        class: "text-red-600",
    },
    reply: {
        icon: "comment",
        class: "text-blue-500",
    },
    follow: {
        icon: "person",
        class: "text-green-500",
    },
};

const bg = computed(() => {
    return wasRead.value ? "bg-gray-100" : "bg-blue-100";
});
</script>
<template>
    <div :class="`flex px-4 py-3 ${bg}`">
        <div :class="icons[notificationData.type].class">
            <span class="material-icons" style="font-size: 2em">{{
                icons[notificationData.type].icon
            }}</span>
        </div>
        <div class="flex-1 px-4">
            <Link
                :href="route('profile.show', notificationData.user_id)"
                as="button"
            >
                <img
                    :src="notificationData.user_profile_picture_url"
                    alt="Profile Picture"
                    class="rounded-full h-8 w-8 object-cover"
                />
            </Link>
            <div class="font-bold">{{ notificationData.message }}</div>
            <div v-if="notificationData.type === 'reply'" class="text-gray-600">
                {{ notificationData.content }}
            </div>
        </div>
    </div>
</template>
