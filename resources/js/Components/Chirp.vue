<script setup>
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import InputError from "./InputError.vue";
import DropdownLink from "./DropdownLink.vue";
import Dropdown from "./Dropdown.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { onMounted, computed, ref } from "vue";
import { Link, useForm, usePage, router } from "@inertiajs/vue3";
import Modal from "./Modal.vue";
import axios from "axios";

dayjs.extend(relativeTime);
const page = usePage();
const user = page.props.auth.user;
const props = defineProps({
    chirp: Object,
    isModal: {
        type: Boolean,
        default: false,
    },
    mainChirp: {
        type: Boolean,
        default: false,
    },
});
const chirpData = ref(props.chirp);
const form = useForm({
    message: chirpData.value.message,
});

const isLiked = computed(() => {
    return chirpData.value.likes.some((like) => like.user_id === user.id);
});
const likeCount = computed(() => chirpData.value.likes.length);
const replyCount = computed(() => chirpData.value.replies.length);
const likeChirp = async () => {
    await axios
        .post(route("chirps.like", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
        });
};
const emit = defineEmits(["updateChirpData"]);
const replyForm = useForm({
    message: "",
    parent_id: chirpData.value.id,
});
const postReply = async () => {
    // console.log(replyForm);
    await axios
        .post(route("chirp.reply", chirpData.value.id), {
            message: replyForm.message,
            parent_id: replyForm.parent_id,
        })
        .then((response) => {
            if (route().current("chirp.show", chirpData.value.parent_id)) {
                emit("updateChirpData", response.data);
            }
            chirpData.value = response.data;
            isReplyModalShow.value = false;
            replyForm.reset();
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
function truncate(value, length) {
    if (value.length > length) {
        return value.substring(0, length) + "...";
    } else {
        return value;
    }
}

// console.log(chirpData.value);
const iconSize = "18px";
const iconSizeLg = "24px";
const editing = ref(false);
const isReplyModalShow = ref(false);
const chirpClass = computed(() => {
    return props.mainChirp
        ? "flex flex-col z-10"
        : "flex flex-row gap-1 items-center z-10";
});
</script>

<template>
    <div class="flex flex-col">
        <div class="px-6 py-3 flex relative gap-3">
            <Link
                :href="route(`profile.show`, chirpData.user.id)"
                class="pointer-events-auto z-10"
            >
                <img
                    :src="profilePicture"
                    alt="Profile Picture"
                    class="rounded-full h-10 w-10 object-cover"
                />
            </Link>

            <div class="flex-1 pointer-events-none">
                <div class="flex justify-between items-center">
                    <div :class="chirpClass">
                        <Link
                            :href="route(`profile.show`, chirpData.user.id)"
                            class="text-gray-800 pointer-events-auto font-bold hidden md:block"
                            >{{ truncate(chirpData.user.name, 20) }}</Link
                        >
                        <Link
                            :href="route(`profile.show`, chirpData.user.id)"
                            class="text-gray-800 pointer-events-auto font-bold block md:hidden"
                            >{{ truncate(chirpData.user.name, 8) }}</Link
                        >
                        <p class="text-gray-500">
                            @{{ truncate(chirpData.user.username, 8) }}
                        </p>
                        <small class="text-sm text-gray-600" v-if="!mainChirp"
                            >&bull;
                            {{ dayjs(chirpData.created_at).fromNow() }}</small
                        >
                        <small
                            v-if="chirpData.created_at !== chirpData.updated_at"
                            class="text-sm text-gray-600"
                            >&middot; edited</small
                        >
                    </div>
                    <Dropdown
                        v-if="chirpData.user.id === $page.props.auth.user.id"
                        class="z-50 pointer-events-auto"
                    >
                        <template #trigger>
                            <button class="pointer-events-auto">
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
                        class="z-10 mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    ></textarea>
                    <InputError
                        :message="form.errors.message"
                        class="z-10 mt-2"
                    />
                    <div class="z-10 space-x-2">
                        <PrimaryButton class="z-10 mt-4">Save</PrimaryButton>
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
                <p
                    v-else-if="!editing && !mainChirp"
                    class="z-10 text-lg text-gray-900"
                >
                    {{ chirpData.message }}
                </p>
            </div>
            <Link
                :href="route('chirp.show', chirpData.id)"
                v-if="!route().current('chirp.show', chirpData.id)"
                class="absolute h-full w-full top-0 left-0"
            ></Link>
        </div>
        <div v-if="mainChirp" class="text-xl px-6 pb-4">
            {{ chirpData.message }}
        </div>
        <div class="ps-6 pb-3" v-if="mainChirp">
            <small class="text-sm text-gray-600">
                {{ dayjs(chirpData.created_at) }}</small
            >
        </div>
        <div
            class="pt-1 pb-1 px-8 flex z-10 justify-around"
            v-if="!isModal && !mainChirp"
        >
            <div class="flex gap-3">
                <button
                    @click="isReplyModalShow = true"
                    class="text-gray-500 flex items-center"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSize}`"
                        >comment</span
                    >
                </button>
                <div>{{ replyCount }}</div>
            </div>
            <div class="flex gap-3">
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-if="isLiked"
                    @click="unlikeChirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSize}`"
                        >favorite</span
                    >
                </button>
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-else
                    @click="likeChirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSize}`"
                        >favorite_border</span
                    >
                </button>
                <div>{{ likeCount }}</div>
            </div>
        </div>
        <div
            class="py-2 px-8 flex z-10 justify-around border-t-2"
            v-if="!isModal && mainChirp"
        >
            <div class="flex gap-3">
                <button
                    @click="isReplyModalShow = true"
                    class="text-gray-500 flex items-center"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSizeLg}`"
                        >comment</span
                    >
                </button>
                <div>{{ replyCount }}</div>
            </div>
            <div class="flex gap-3">
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-if="isLiked"
                    @click="unlikeChirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSizeLg}`"
                        >favorite</span
                    >
                </button>
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-else
                    @click="likeChirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSizeLg}`"
                        >favorite_border</span
                    >
                </button>
                <div>{{ likeCount }}</div>
            </div>
        </div>

        <Modal
            :show="isReplyModalShow"
            :closeable="true"
            @close="isReplyModalShow = false"
        >
            <div class="flex border-b-2 py-1 px-3">
                <button
                    class="flex items-center p-1 rounded-full hover:bg-gray-100"
                    @click="isReplyModalShow = false"
                >
                    <span class="material-icons">close</span>
                </button>
            </div>
            <Chirp :chirp="chirpData" :isModal="true" />
            <div class="border-s-2 ps-5 ms-10 py-1">
                <span class="text-gray-500">Replying to </span>
                <span class="text-gray-600"
                    >@{{ chirpData.user.username }}</span
                >
            </div>

            <div class="flex px-6 mt-4">
                <img
                    :src="page.props.auth.user.profile_picture_url"
                    alt="Profile Picture"
                    class="rounded-full h-10 w-10 object-cover"
                />
                <div class="w-full">
                    <textarea
                        v-model="replyForm.message"
                        class="w-full text-gray-900 border-none resize-none focus:ring-0"
                        placeholder="Write a reply..."
                    ></textarea>
                    <InputError
                        :message="replyForm.errors.message"
                        class="mt-2"
                    />
                </div>
            </div>
            <div class="py-2 px-4 flex">
                <PrimaryButton class="ms-auto" @click="postReply">
                    Reply
                </PrimaryButton>
            </div>
        </Modal>
    </div>
</template>
