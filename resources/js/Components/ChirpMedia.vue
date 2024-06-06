<script setup>
import MediaModal from "./MediaModal.vue";
import MediaItem from "./MediaItem.vue";
import Chirp from "./Chirp.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import InputError from "@/Components/InputError.vue";
import AutoResizeTextarea from "@/Components/AutoResizeTextarea.vue";

const props = defineProps({
    media: Array,
    chirp: Object,
});
const emit = defineEmits(["updateChirpData"]);
const chirpData = ref(props.chirp);
const page = usePage();
const displayIndex = ref(3);
const showMediaModal = ref(false);
const updateChirpData = (newValue) => {
    chirpData.value = newValue;
    console.log("Chirp Media Emit");
    emit("updateChirpData", newValue);
};
// console.log(props.media);

const imageClick = (index) => {
    // console.log("Image Index: ", index);
    displayIndex.value = index;
    showMediaModal.value = true;
};

const isFocused = ref(false);
const textareaRows = ref(1);
const divClass = ref("flex-row");
const inputOnFocus = () => {
    isFocused.value = true;
    if (isFocused.value) {
        textareaRows.value = 2;
        divClass.value = "flex-col";
    }
};
const inputOnBlur = () => {
    isFocused.value = false;
    if (isFocused.value) {
        textareaRows.value = 1;
        divClass.value = "flex-row";
    }
};

const handleKeyPress = (event) => {
    if (event.key === "ArrowLeft" && displayIndex.value > 0) {
        displayIndex.value--;
    } else if (
        event.key === "ArrowRight" &&
        displayIndex.value < props.media.length - 1
    ) {
        displayIndex.value++;
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleKeyPress);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyPress);
});

// console.log(props.chirp);

const replyForm = useForm({
    message: "",
});
const postReply = async () => {
    await axios
        .post(route("chirp.reply", chirpData.value.id), {
            message: replyForm.message,
            parent_id: chirpData.value.id,
        })
        .then((response) => {
            console.log(response);
            chirpData.value = response.data;
            emit("updateChirpData", response.data);
            replyForm.reset();
        });
};
</script>

<template>
    <div class="flex justify-center max-w-lg mx-auto">
        <div v-if="media.length === 1" class="rounded-lg v-50 overflow-hidden">
            <MediaItem
                :media="media[0]"
                :index="0"
                @open-modal="imageClick"
                :length="media.length"
            />
        </div>
        <div
            v-if="media.length === 2 && media[0].media_type === 'image'"
            class="grid grid-cols-2 gap-1 rounded-lg overflow-hidden w-fit"
        >
            <MediaItem
                v-for="(image, index) in media"
                :media="image"
                :index="index"
                class="h-72 object-cover v-50"
                @open-modal="imageClick"
                :length="media.length"
            />
        </div>
        <div
            v-if="media.length === 3 && media[0].media_type === 'image'"
            class="grid grid-cols-2 rounded-lg overflow-hidden w-fit h-96 gap-1"
        >
            <MediaItem
                :media="media[0]"
                :index="0"
                class="object-cover v-50 row-span-2"
                @open-modal="imageClick"
                :length="media.length"
            />
            <MediaItem
                :media="media[1]"
                :index="1"
                class="object-cover v-50"
                @open-modal="imageClick"
                :length="media.length"
            />
            <MediaItem
                :media="media[2]"
                :index="2"
                class="object-cover v-50"
                @open-modal="imageClick"
                :length="media.length"
            />
        </div>
        <div
            v-if="media.length >= 4 && media[0].media_type === 'image'"
            class="grid grid-cols-2 rounded-lg overflow-hidden w-full h-96 gap-1"
        >
            <MediaItem
                v-for="(item, index) in media"
                :media="item"
                :index="index"
                class="object-cover v-50"
                @open-modal="imageClick"
                :length="media.length"
            />
        </div>
    </div>
    <MediaModal :show="showMediaModal" @close="showMediaModal = false">
        <div class="flex">
            <div class="relative flex-1 flex justify-center max-h-screen">
                <TransitionGroup
                    enter-active-class="ease-out duration-200"
                    enter-from-class="opacity-0 "
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-100"
                    leave-from-class="opacity-100 "
                    leave-to-class="opacity-0 "
                >
                    <div
                        v-for="(image, index) in media"
                        :key="index"
                        class="absolute h-screen p-2"
                        v-show="displayIndex === index"
                    >
                        <button
                            class="absolute pointer-events-auto active:bg-gray-500 z-50 top-8 left-10 bg-gray-600 bg-opacity-50 hover:bg-opacity-100 p-1 rounded-full text-white flex justify-center items-center"
                            @click="showMediaModal = false"
                        >
                            <span class="material-icons">close</span>
                        </button>

                        <img
                            :src="image.media_url"
                            class="h-full shadow-lg object-contain"
                        />
                        <button
                            class="p-1 bg-gray-300 bg-opacity-50 rounded-full absolute left-3 top-1/2 -translate-y-1/2 flex items-center justify-center hover:bg-gray-200 active:bg-gray-100 pointer-events-auto"
                            v-if="displayIndex > 0"
                            @click="displayIndex--"
                        >
                            <span style="font-size: 32px" class="material-icons"
                                >chevron_left</span
                            >
                        </button>
                        <button
                            class="p-1 bg-gray-300 bg-opacity-50 rounded-full absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center hover:bg-gray-200 active:bg-gray-100 pointer-events-auto"
                            v-if="displayIndex < media.length - 1"
                            @click="displayIndex++"
                        >
                            <span style="font-size: 32px" class="material-icons"
                                >chevron_right</span
                            >
                        </button>
                    </div>
                </TransitionGroup>
            </div>
            <div
                class="bg-gray-100 h-screen overflow-y-auto w-96 pointer-events-auto hidden md:block"
            >
                <Chirp
                    class="border-b-2"
                    :chirp="chirpData"
                    :main-chirp="true"
                    context="post"
                    @update-chirp-data="updateChirpData"
                />

                <div :class="`border-b-2 flex ${divClass} `">
                    <div class="flex px-6 mt-4 flex-1">
                        <img
                            :src="page.props.auth.user.profile_picture_url"
                            alt="Profile Picture"
                            class="rounded-full h-10 w-10 object-cover"
                        />
                        <div class="w-full">
                            <AutoResizeTextarea
                                v-model="replyForm.message"
                                class="w-full bg-gray-100 text-gray-900 focus:ring-0 max-h-18 focus:max-h-36 overflow-hidden focus:overflow-y-auto"
                                placeholder="Write a reply..."
                                @focus="inputOnFocus"
                                @blur="inputOnBlur"
                                :rows="textareaRows"
                            ></AutoResizeTextarea>
                            <InputError
                                :message="replyForm.errors.message"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="py-2 px-4 flex my-auto">
                        <PrimaryButton class="ms-auto" @click="postReply">
                            Reply
                        </PrimaryButton>
                    </div>
                </div>
                <div>
                    <Chirp
                        v-for="reply in chirpData.replies"
                        :chirp="reply"
                        :key="reply.id"
                        context="reply"
                        :name-truncate-length="6"
                    />
                </div>
            </div>
        </div>
    </MediaModal>
</template>
