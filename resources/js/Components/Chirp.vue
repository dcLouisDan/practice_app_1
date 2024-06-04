<script setup>
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
import InputError from "./InputError.vue";
import DropdownLink from "./DropdownLink.vue";
import Dropdown from "./Dropdown.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { computed, ref } from "vue";
import { Link, useForm, usePage, useRemember } from "@inertiajs/vue3";
import Modal from "./Modal.vue";
import ChirpMedia from "./ChirpMedia.vue";
import axios from "axios";
import NewChirpReplyInput from "./NewChirpReplyInput.vue";

dayjs.extend(relativeTime);
dayjs.extend(timezone);
dayjs.extend(utc);

const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
dayjs.tz.setDefault(userTimeZone);
// console.log(userTimeZone);
const emit = defineEmits(["updateChirpData", "refreshData"]);
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
    context: {
        type: String,
        default: "dashboard",
    },
    nameTruncateLength: {
        type: Number,
        default: 20,
    },
});

const chirpData = ref(props.chirp);
const currentTime = dayjs(chirpData.value.created_at);
// console.log("edit", currentTime.format());

const form = useForm({
    message: chirpData.value.message,
    content: props.context,
});

const isLiked = computed(() => {
    return props.chirp.likes.some((like) => like.user_id === user.id);
});
const likeCount = computed(() => props.chirp.likes.length);
const replyCount = computed(() => props.chirp.replies.length);
const isRechirped = computed(() => {
    return chirpData.value.rechirps.some(
        (rechirp) => rechirp.user_id === user.id
    );
});
const rechirpCount = computed(() => props.chirp.rechirps.length);
const rechirp = async () => {
    await axios
        .post(route("chirp.rechirp", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
            console.log(response.data);
            emit("updateChirpData", response.data);
        });
};
const unrechirp = async () => {
    await axios
        .delete(route("chirp.unrechirp", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
            console.log(response.data);
            emit("updateChirpData", response.data);
        });
};
const likeChirp = async () => {
    await axios
        .post(route("chirps.like", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
            emit("updateChirpData", response.data);
        });
};

const updateChirpData = (newData) => {
    chirpData.value = newData;
    if (route().current("chirp.show", chirpData.value.parent_id)) {
        emit("updatechirpdata", newdata);
    }
    isReplyModalShow.value = false;

    emit("refreshData", { refresh: true });
};

const unlikeChirp = async () => {
    await axios
        .delete(route("chirps.unlike", chirpData.value.id))
        .then((response) => {
            chirpData.value = response.data;
            emit("updateChirpData", response.data);
            // console.log(response);
        });
};
const profilePicture = computed(() => {
    return (
        chirpData.value.user.profile_picture_url ||
        "images/profile_placeholder.png"
    );
});
const truncate = (value, length) => {
    if (value.length > length) {
        return value.substring(0, length) + "...";
    } else {
        return value;
    }
};
const refreshData = () => {
    // console.log("refresh");
    emit("refreshData", { refresh: true });
};

// console.log(props.context);
const iconSize = "18px";
const iconSizeLg = "24px";
const editing = ref(false);
const isReplyModalShow = ref(false);
const chirpClass = computed(() => {
    return props.mainChirp
        ? "flex flex-col z-10"
        : "flex flex-row gap-1 items-center z-10";
});
const media = computed(() => {
    return chirpData.value.media ? chirpData.value.media : [];
});

const rechirped = computed(() => {
    return chirpData.value.rechirper !== undefined;
});
const rechirper = computed(() => {
    return chirpData.value.rechirper.name === user.name
        ? "You"
        : chirpData.value.rechirper.name;
});
// console.log(chirpData.value);
</script>

<template>
    <div class="flex flex-col hover:bg-gray-50">
        <div class="relative">
            <Chirp
                v-if="
                    chirpData.parent &&
                    !rechirped &&
                    !route().current('chirp.show', chirpData.parent.id)
                "
                :chirp="chirpData.parent"
                @refresh-data="refreshData"
            />
            <div
                class="h-full border-l-2 top-4 border-gray-300 absolute left-[43px]"
            ></div>
        </div>
        <div
            class="flex items-center gap-1 text-sm px-6 pt-3 text-gray-500"
            v-if="rechirped"
        >
            <span class="material-icons" style="font-size: 20px">cached</span>
            Rechirped by
            {{ rechirper }}
        </div>
        <div class="px-6 py-3 flex relative gap-3">
            <div class="z-10 pointer-events-none">
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
            </div>

            <div class="flex-1 z-10">
                <div class="pointer-events-none">
                    <div class="flex justify-between items-center">
                        <div :class="chirpClass">
                            <Link
                                :href="route(`profile.show`, chirpData.user.id)"
                                class="text-gray-800 pointer-events-auto font-bold hidden md:block"
                                >{{
                                    truncate(
                                        chirpData.user.name,
                                        nameTruncateLength
                                    )
                                }}</Link
                            >
                            <Link
                                :href="route(`profile.show`, chirpData.user.id)"
                                class="text-gray-800 pointer-events-auto font-bold block md:hidden"
                                >{{ truncate(chirpData.user.name, 8) }}</Link
                            >
                            <p class="text-gray-500">
                                @{{ truncate(chirpData.user.username, 8) }}
                            </p>
                            <small
                                class="text-xs text-gray-600"
                                v-if="!mainChirp"
                                >&bull;
                                {{
                                    dayjs(chirpData.created_at).fromNow(true)
                                }}</small
                            >
                            <small
                                v-if="
                                    chirpData.created_at !==
                                        chirpData.updated_at && !mainChirp
                                "
                                class="text-sm text-gray-600"
                                >&middot; edited</small
                            >
                        </div>
                        <Dropdown
                            v-if="
                                chirpData.user.id === $page.props.auth.user.id
                            "
                            class="z-50 pointer-events-auto"
                        >
                            <template #trigger>
                                <button
                                    class="pointer-events-auto active:bg-gray-300 hover:bg-gray-200 h-7 w-7 flex items-center justify-center rounded-full transition-all ease-out"
                                >
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
                                    :href="
                                        route('chirps.destroy', {
                                            chirp: chirpData.id,
                                            _query: {
                                                context: context,
                                            },
                                        })
                                    "
                                    @click="refreshData"
                                    method="delete"
                                >
                                    Delete
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                    <form
                        v-if="editing && !mainChirp"
                        @submit.prevent="
                            form.put(route('chirps.update', chirpData.id), {
                                onSuccess: () => {
                                    editing = false;
                                    refreshData();
                                },
                            })
                        "
                        class="z-20"
                    >
                        <textarea
                            v-model="form.message"
                            class="pointer-events-auto z-20 mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        ></textarea>
                        <InputError
                            :message="form.errors.message"
                            class="mt-2"
                        />
                        <div class="z-20 space-x-2 pointer-events-auto">
                            <PrimaryButton class="z-10 mt-4"
                                >Save</PrimaryButton
                            >
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
                        class="z-10 text-lg text-gray-900 mb-2"
                    >
                        {{ chirpData.message }}
                    </p>
                </div>
                <ChirpMedia
                    :chirp="chirpData"
                    v-if="media.length > 0 && !mainChirp"
                    :media="media"
                    @update-chirp-data="updateChirpData"
                />
            </div>
            <Link
                :href="route('chirp.show', chirpData.id)"
                v-if="!route().current('chirp.show', chirpData.id)"
                class="absolute h-full w-full top-0 left-0 z-0"
            ></Link>
        </div>
        <div v-if="mainChirp" class="text-xl px-6 pb-4">
            <form
                v-if="editing && mainChirp"
                @submit.prevent="
                    form.put(route('chirps.update', chirpData.id), {
                        onSuccess: () => (editing = false),
                    })
                "
                class="z-20"
            >
                <textarea
                    v-model="form.message"
                    class="pointer-events-auto z-20 mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                ></textarea>
                <InputError :message="form.errors.message" class="mt-2" />
                <div class="z-20 space-x-4 pointer-events-auto">
                    <PrimaryButton class="z-10 mt-4">Save</PrimaryButton>
                    <button
                        class="mt-4 text-base text-gray-600"
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
            <div v-else class="mb-4">{{ chirpData.message }}</div>
            <ChirpMedia
                :chirp="chirpData"
                v-if="media.length > 0"
                :media="media"
                @update-chirp-data="updateChirpData"
            />
        </div>

        <div class="ps-6 pb-3 space-x-1" v-if="mainChirp">
            <small class="text-sm text-gray-600">
                {{ currentTime.format("h:mm A &bull; MMM D, YYYY") }}
            </small>
            <small
                v-if="chirpData.created_at !== chirpData.updated_at"
                class="text-sm text-gray-600"
                >&bull; edited</small
            >
        </div>
        <div
            class="pt-1 pb-1 px-8 flex z-0 justify-around"
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
                    class="text-green-600 active:animate-likeBounce flex items-center"
                    v-if="isRechirped"
                    @click="unrechirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSize}`"
                        >cached</span
                    >
                </button>
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-else
                    @click="rechirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSize}`"
                        >cached</span
                    >
                </button>
                <div>{{ rechirpCount }}</div>
            </div>
            <div class="flex gap-3">
                <button
                    class="text-red-600 active:animate-likeBounce flex items-center"
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
                    class="text-green-600 active:animate-likeBounce flex items-center"
                    v-if="isRechirped"
                    @click="unrechirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSizeLg}`"
                        >cached</span
                    >
                </button>
                <button
                    class="text-gray-500 active:animate-likeBounce flex items-center"
                    v-else
                    @click="rechirp"
                >
                    <span
                        class="material-icons"
                        :style="`font-size: ${iconSizeLg}`"
                        >cached</span
                    >
                </button>
                <div>{{ rechirpCount }}</div>
            </div>
            <div class="flex gap-3">
                <button
                    class="text-red-600 active:animate-likeBounce flex items-center"
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

            <NewChirpReplyInput
                :chirp_id="chirpData.id"
                @update-chirp-data="updateChirpData"
                class="pb-3"
            />
        </Modal>
    </div>
</template>
