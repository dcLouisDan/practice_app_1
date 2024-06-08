<script setup>
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AutoResizeTextarea from "@/Components/AutoResizeTextarea.vue";
import axios from "axios";
import Chirp from "./Chirp.vue";

const props = defineProps({
    context: {
        type: String,
        default: "dashboard",
    },
    quotedChirp: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["refreshData", "newChirp"]);
const page = usePage();
const user = page.props.auth.user;
const mediaInput = ref(null);
const mediaPreview = ref([]);
const textareaPlaceholder = computed(() => {
    return props.context === "quote"
        ? "Add a comment..."
        : "What's on your mind?";
});

const form = useForm({
    message: "",
    media: [],
});

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    const images = files.filter((file) => file.type.startsWith("image/"));
    const videos = files.filter((file) => file.type.startsWith("video/"));

    if (videos.length > 1) {
        alert("You can only upload one video");
        resetFileInput();
        return;
    }

    if (videos.length === 1 && images.length > 0) {
        alert("You can upload multiple images or one video, but not both");
        resetFileInput();
        return;
    }

    form.media = files;
    mediaPreview.value = files.map((file) => ({
        url: URL.createObjectURL(file),
        type: file.type,
    }));
    // console.log(mediaPreview.value);
};
const resetFileInput = () => {
    form.media = [];
    mediaPreview.value = [];
    mediaInput.value.value = "";
};
const postChirp = async () => {
    const formData = new FormData();
    formData.append("message", form.message);
    form.media.forEach((file, index) => {
        formData.append(`media[${index}]`, file);
    });

    if (props.context === "quote" && props.quotedChirp !== null) {
        formData.append("quote_id", props.quotedChirp?.id);
        await axios
            .post(route("chirp.quote", props.quotedChirp?.id), formData)
            .then((response) => {
                emit("newChirp", response.data);
                form.reset();
                resetFileInput();
            });
    }
    if (props.context === "dashboard") {
        await axios.post(route("chirps.store"), formData).then((response) => {
            console.log(response.data);
            emit("newChirp", response.data);
            form.reset();
            resetFileInput();
        });
    }
};
const isImage = (file) => {
    const imageTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
    return imageTypes.includes(file);
};
</script>

<template>
    <div class="mx-auto px-4 pb-4 sm:p-4 sm:flex border-b-2 gap-2 pt-4 hidden">
        <Link :href="route('profile.view')" as="button" class="mb-auto">
            <img
                :src="user.profile_picture_url"
                alt="Profile Picture"
                class="rounded-full h-14 w-14 object-cover"
            />
        </Link>
        <form @submit.prevent="postChirp" class="flex flex-col flex-1">
            <AutoResizeTextarea
                v-model="form.message"
                :placeholder="textareaPlaceholder"
                class="block w-full focus:ring-0 max-h-36"
            ></AutoResizeTextarea>
            <InputError :message="form.errors.message" class="mt-2" />
            <div
                v-if="mediaPreview.length > 0"
                class="flex w-full overflow-x-auto mt-2"
            >
                <div class="flex gap-2">
                    <div
                        v-for="(media, index) in mediaPreview"
                        :key="index"
                        class="flex-shrink-0"
                    >
                        <img
                            v-if="isImage(media.type)"
                            :src="media.url"
                            alt="Image Preview"
                            class="w-32 h-32 object-cover rounded-lg"
                        />
                        <video
                            v-else
                            :src="media.url"
                            controls
                            class="w-20 h-20"
                        ></video>
                    </div>
                </div>
            </div>
            <div
                v-if="context === 'quote' && quotedChirp != null"
                class="border-2 rounded-xl"
            >
                <Chirp :chirp="quotedChirp" context="quote" />
            </div>
            <div class="flex mt-2 items-center">
                <button
                    type="button"
                    @click="$refs.mediaInput.click()"
                    class="flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-full p-1 h-10 w-10 active:text-gray-800 active:bg-gray-300"
                >
                    <span class="material-icons-outlined">image</span>
                </button>
                <input
                    type="file"
                    multiple
                    hidden
                    ref="mediaInput"
                    accept="image/jpg, image/jpeg, image/png, image/gif, video/mp4, video/mov, video/avi"
                    @change="handleFileUpload"
                />
                <PrimaryButton class="ms-auto">Chirp</PrimaryButton>
            </div>
        </form>
    </div>
</template>
