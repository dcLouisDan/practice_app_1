<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    user: Object,
    size: {
        type: String,
        default: "base",
    },
});
const user = ref(props.user);
const page = usePage();
const authUser = page.props.auth.user;
const profilePicture = computed(() => {
    return user.value.profile_picture_url;
});

const isUnfollowBtnHovered = ref(false);
const unfollowText = computed(() => {
    return isUnfollowBtnHovered.value ? "Unfollow" : "Following";
});
const isFollowingByAuthUser = computed(() => {
    return user.value.followers.some((follower) => follower.id === authUser.id);
});
const follow = async () => {
    await axios.post(route("user.follow", user.value.id)).then((response) => {
        user.value = response.data;
    });
};
const truncate = (value, length) => {
    if (value.length > length) {
        return value.substring(0, length) + "...";
    } else {
        return value;
    }
};
</script>

<template>
    <div
        class="w-full bg-gray-200 flex items-center gap-4 px-5 py-3"
        v-if="size === 'base'"
    >
        <Link :href="route('profile.show', user.id)" as="button">
            <img
                :src="profilePicture"
                alt="Profile Picture"
                class="rounded-full border-2 border-gray-500 h-12 w-12 object-cover"
            />
        </Link>
        <div class="lg:flex flex-col">
            <Link
                :href="route('profile.show', user.id)"
                as="button"
                class="text-lg text-gray-800 hover:text-gray-900 text-start"
                >{{ truncate(user.name, 20) }}</Link
            >
            <p class="text-sm text-gray-600">@{{ user.username }}</p>
        </div>
        <div class="ms-auto">
            <PrimaryButton
                v-if="isFollowingByAuthUser"
                class="w-28 text-center"
                @click="unfollow"
                @mouseover="isUnfollowBtnHovered = true"
                @mouseleave="isUnfollowBtnHovered = false"
            >
                {{ unfollowText }}
            </PrimaryButton>
            <SecondaryButton v-else @click="follow"> Follow </SecondaryButton>
        </div>
    </div>
    <div
        class="w-full bg-gray-200 flex items-center gap-1 px-2 py-3"
        v-if="size === 'sm'"
    >
        <Link :href="route('profile.show', user.id)" as="button">
            <img
                :src="profilePicture"
                alt="Profile Picture"
                class="rounded-full border-2 border-gray-500 h-8 w-8 object-cover"
            />
        </Link>
        <div class="lg:flex flex-col hidden">
            <Link
                :href="route('profile.show', user.id)"
                as="button"
                class="text-sm text-gray-800 hover:text-gray-900 text-start"
                >{{ truncate(user.name, 10) }}</Link
            >
            <p class="text-sm text-gray-600">
                @{{ truncate(user.username, 8) }}
            </p>
        </div>
        <div class="ms-auto">
            <PrimaryButton
                v-if="isFollowingByAuthUser"
                class="w-28 text-center text-sm"
                @click="unfollow"
                @mouseover="isUnfollowBtnHovered = true"
                @mouseleave="isUnfollowBtnHovered = false"
            >
                {{ unfollowText }}
            </PrimaryButton>
            <SecondaryButton v-else @click="follow"> Follow </SecondaryButton>
        </div>
    </div>
</template>
