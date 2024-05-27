<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps(["user"]);
const user = ref(props.user);
const page = usePage();
const authUser = page.props.auth.user;
const profilePicture = computed(() => {
    return user.value.profile_picture || "images/profile_placeholder.png";
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
        console.log(response);
        user.value = response.data;
    });
};
const unfollow = async () => {
    await axios
        .delete(route("user.unfollow", user.value.id))
        .then((response) => {
            console.log(response);
            user.value = response.data;
        });
};
</script>

<template>
    <div class="w-full bg-gray-200 flex items-center gap-4 px-5 py-3">
        <Link :href="route('profile.show', user.id)" as="button">
            <img
                :src="profilePicture"
                alt="Profile Picture"
                class="rounded-full border-2 border-gray-500 h-12 w-12 object-cover"
            />
        </Link>
        <div class="lg:flex flex-col hidden">
            <Link
                :href="route('profile.show', user.id)"
                as="button"
                class="text-lg text-gray-800 hover:text-gray-900 text-start"
                >{{ user.name }}</Link
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
</template>