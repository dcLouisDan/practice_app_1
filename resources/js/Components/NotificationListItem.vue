<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    notification: Object,
});

const notificationData = computed(() => {
    return props.notification.data;
});

console.log(notificationData.value);

const wasRead = computed(() => {
    return props.notification.read_at != null;
});
const notif = {
    like: {
        icon: "favorite",
        class: "text-red-600",
        href: notificationData.value.chirp_id
            ? route("chirp.show", notificationData.value.chirp_id)
            : null,
    },
    reply: {
        icon: "comment",
        class: "text-blue-500",
        href: notificationData.value.chirp_id
            ? route("chirp.show", notificationData.value.chirp_id)
            : null,
    },
    follow: {
        icon: "person",
        class: "text-indigo-500",
        href: route("profile.show", notificationData.value.user_id),
    },
    rechirp: {
        icon: "cached",
        class: "text-green-500",
        href: notificationData.value?.chirp_id
            ? route("chirp.show", notificationData.value?.chirp_id)
            : null,
    },
    quote: {
        icon: "cached",
        class: "text-green-500",
        href: notificationData.value?.quote_chirp_id
            ? route("chirp.show", notificationData.value?.quote_chirp_id)
            : null,
    },
};

const bg = computed(() => {
    return wasRead.value ? "bg-gray-100" : "bg-blue-100";
});

const typesWithContent = ["reply", "rechirp", "quote"];
</script>
<template>
    <div :class="`flex px-4 py-3 ${bg} relative`">
        <div :class="`${notif[notificationData.type].class} z-10`">
            <span class="material-icons" style="font-size: 2em">{{
                notif[notificationData.type].icon
            }}</span>
        </div>
        <div class="flex-1 px-4 z-10 pointer-events-none">
            <Link
                :href="route('profile.show', notificationData.user_id)"
                as="button"
                class="pointer-events-auto"
            >
                <img
                    :src="notificationData.user_profile_picture_url"
                    alt="Profile Picture"
                    class="rounded-full h-8 w-8 object-cover"
                />
            </Link>
            <div class="font-bold">{{ notificationData.message }}</div>
            <div
                v-if="typesWithContent.indexOf(notification.data.type) != -1"
                class="text-gray-600 mt-1"
            >
                {{ notificationData.content }}
            </div>
        </div>
        <Link
            :href="notif[notificationData.type].href"
            class="h-full w-full absolute top-0 left-0 hover:bg-gray-200 transition-all ease-in"
        ></Link>
    </div>
</template>
