<script setup>
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import InputError from "./InputError.vue";
import DropdownLink from "./DropdownLink.vue";
import Dropdown from "./Dropdown.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

dayjs.extend(relativeTime);
const page = usePage();
const user = page.props.auth.user;
const props = defineProps(["chirp", "isLiked"]);
const chirpData = ref(props.chirp);
const form = useForm({
    message: chirpData.value.message,
});
const isLiked = computed(() => {
    return chirpData.value.likes.some((like) => like.user_id === user.id);
});
const likeCount = computed(() => chirpData.value.likes.length);
const replyCount = computed(() => chirpData.value.replies.length);
console.log(props.chirp.context);
const likeChirp = async () => {
    await axios
        .post(route("chirps.like", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
        });
};
const unlikeChirp = async () => {
    await axios
        .delete(route("chirps.unlike", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
        });
};
const profilePicture = computed(() => {
    return (
        chirpData.value.user.profile_picture_url ||
        "images/profile_placeholder.png"
    );
});

console.log(props.chirp.user);

const editing = ref(false);
</script>

<template>
    <div class="flex flex-col relative">
        <div class="p-6 flex space-x-4 z-10 pointer-events-none">
            <Link
                :href="route(`profile.show`, chirpData.user.id)"
                class="pointer-events-auto"
            >
                <img
                    :src="profilePicture"
                    alt="Profile Picture"
                    class="rounded-full border-2 border-gray-500 h-12 w-12 object-cover"
                />
            </Link>

            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <div>
                        <Link
                            :href="route(`profile.show`, chirpData.user.id)"
                            class="text-gray-800 pointer-events-auto"
                            >{{ chirpData.user.name }}</Link
                        >
                        <small class="ml-2 text-sm text-gray-600">{{
                            dayjs(chirpData.created_at).fromNow()
                        }}</small>
                        <small
                            v-if="chirpData.created_at !== chirpData.updated_at"
                            class="text-sm text-gray-600"
                            >&middot; edited</small
                        >
                    </div>
                    <Dropdown
                        v-if="chirpData.user.id === $page.props.auth.user.id"
                    >
                        <template #trigger>
                            <button>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                                    />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <button
                                class="blox w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:bg-gray-100 transition duration-150 ease-in-out"
                                @click="editing = true"
                            >
                                Edit
                            </button>
                            <DropdownLink
                                as="button"
                                :href="route('chirps.destroy', chirpData.id)"
                                method="delete"
                            >
                                Delete
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
                <form
                    v-if="editing"
                    @submit.prevent="
                        form.put(route('chirps.update', chirpData.id), {
                            onSuccess: () => (editing = false),
                        })
                    "
                >
                    <textarea
                        v-model="form.message"
                        class="mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    ></textarea>
                    <InputError :message="form.errors.message" class="mt-2" />
                    <div class="space-x-2">
                        <PrimaryButton class="mt-4">Save</PrimaryButton>
                        <button
                            class="mt-4"
                            @click="
                                editing = false;
                                form.reset();
                                form.clearErrors();
                            "
                        >
                            Cancel
                        </button>
                    </div>
                </form>
                <p v-else class="mt-4 text-lg text-gray-900">
                    {{ chirpData.message }}
                </p>
            </div>
        </div>
        <div class="py-2 px-8 flex z-10 justify-around">
            <div class="flex gap-2">
                <button class="text-gray-500">
                    <span class="material-icons">comment</span>
                </button>
                <div>{{ replyCount }}</div>
            </div>
            <div class="flex gap-2">
                <button
                    class="text-gray-500 active:animate-likeBounce"
                    v-if="isLiked"
                    @click="unlikeChirp"
                >
                    <span class="material-icons">favorite</span>
                </button>
                <button
                    class="text-gray-500 active:animate-likeBounce"
                    v-else
                    @click="likeChirp"
                >
                    <span class="material-icons">favorite_border</span>
                </button>
                <div>{{ likeCount }}</div>
            </div>
        </div>
        <Link
            :href="route('chirp.show', chirpData.id)"
            v-if="!route().current('chirp.show', chirpData.id)"
            class="absolute h-full w-full"
        ></Link>
    </div>
</template>
