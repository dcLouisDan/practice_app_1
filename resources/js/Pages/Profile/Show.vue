<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";

const props = defineProps(["user"]);
const page = usePage();
const authUser = page.props.auth.user;
const user = ref(page.props.user);
const chirps = ref([]);
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

const fetchChirps = async () => {
    try {
        const response = await axios.get(
            route("profile.chirps.show", user.value.id)
        );
        // console.log(response.data);
        return response.data;
    } catch (error) {
        console.error("Error fetching chirps:", error);
        return [];
    }
};
onMounted(async () => {
    chirps.value = await fetchChirps();
});
const updateChirpData = (newData) => {
    const updatedChirps = chirps.value.map((chirp) =>
        chirp.id === newData.id
            ? {
                  ...chirp,
                  message: newData.message,
                  likes: newData.likes,
                  replies: newData.replies,
                  rechirps: newData.rechirps,
                  parent: newData.parent,
              }
            : chirp
    );

    const updatedChirpParents = updatedChirps.map((chirp) => {
        if (chirp.parent) {
            return chirp.parent.id === newData.id
                ? {
                      ...chirp,
                      parent: {
                          ...chirp.parent,
                          message: newData.message,
                          likes: newData.likes,
                          replies: newData.replies,
                          rechirps: newData.rechirps,
                      },
                  }
                : chirp;
        } else {
            return chirp;
        }
    });

    // console.log("update: ", updatedChirpParents);
    chirps.value = updatedChirpParents;
};
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <div class="px-3 py-5 border-b-2">
            <div class="px-4 py-10">
                <img
                    :src="user.profile_picture_url"
                    alt="Profile Picture"
                    class="rounded-full border-4 border-gray-500 h-36 w-36 object-cover"
                />
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold">{{ user.name }}</h1>
                    <p class="text-gray-500">@{{ user.username }}</p>
                </div>
                <div>
                    <PrimaryButton
                        v-if="isFollowingByAuthUser"
                        class="w-28 text-center"
                        @click="unfollow"
                        @mouseover="isUnfollowBtnHovered = true"
                        @mouseleave="isUnfollowBtnHovered = false"
                    >
                        {{ unfollowText }}
                    </PrimaryButton>
                    <SecondaryButton v-else @click="follow">
                        Follow
                    </SecondaryButton>
                </div>
            </div>
            <div class="flex gap-5 text-sm text-gray-500 font-bold">
                <div class="flex gap-3 items-center mt-5">
                    <p>Followers</p>
                    <p>{{ user.followers.length }}</p>
                </div>
                <div class="flex gap-3 items-center mt-5">
                    <p>Following</p>
                    <p>{{ user.following.length }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y border-b-2">
            <Chirp
                v-for="chirp in chirps"
                :key="chirp.id"
                :chirp="chirp"
                @update-chirp-data="updateChirpData"
            />
        </div>
    </AuthenticatedLayout>
</template>
