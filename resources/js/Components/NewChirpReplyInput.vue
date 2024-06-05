<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import AutoResizeTextarea from "@/Components/AutoResizeTextarea.vue";

const props = defineProps({
    chirp_id: Number,
});
const emit = defineEmits(["updateChirpData"]);

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

const page = usePage();
const mediaInput = ref(null);
const mediaPreview = ref([]);
const replyForm = useForm({
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

    replyForm.media = files;
    mediaPreview.value = files.map((file) => ({
        url: URL.createObjectURL(file),
        type: file.type,
    }));
    // console.log(mediaPreview.value);
};
const resetFileInput = () => {
    replyForm.media = [];
    mediaPreview.value = [];
    mediaInput.value.value = "";
    isFocused.value = false;
};

const postReply = async () => {
    const formData = new FormData();
    formData.append("message", replyForm.message);
    formData.append("parent_id", props.chirp_id);
    replyForm.media.forEach((file, index) => {
        formData.append(`media[${index}]`, file);
    });

    await axios
        .post(route("chirp.reply", props.chirp_id), formData)
        .then((response) => {
            // console.log(response);
            emit("updateChirpData", response.data);
            replyForm.reset();
            resetFileInput;
        });
};
const isImage = (file) => {
    const imageTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
    return imageTypes.includes(file);
};
</script>

<template>
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
                <InputError :message="replyForm.errors.message" class="mt-2" />
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
            </div>
        </div>
        <div class="flex items-center py-2 pe-4 ps-4 md:ps-14">
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
            <PrimaryButton class="ms-auto" @click="postReply"
                >Chirp</PrimaryButton
            >
        </div>
    </div>
</template>
