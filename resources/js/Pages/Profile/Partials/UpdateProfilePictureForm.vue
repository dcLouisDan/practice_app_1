<script setup>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, reactive, ref } from "vue";
import VuePictureCropper, { cropper } from "vue-picture-cropper";
defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const uploadInput = ref(null);
const isShowModal = ref(false);
const pic = ref("");
const imageform = useForm({
    _method: "patch",
    profile_picture: null,
});
const user = usePage().props.auth.user;
const selectFile = (e) => {
    pic.value = "";

    const { files } = e.target;
    if (!files || !files.length) return;

    const file = files[0];
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        pic.value = String(reader.result);

        isShowModal.value = true;

        if (!uploadInput.value) return;
        uploadInput.value.value = "";
    };
};

const getResult = async () => {
    if (!cropper) return;
    const blob = await cropper.getBlob();
    if (!blob) return;
    isShowModal.value = false;
    imageform.profile_picture = blob;
    uploadImage();
};

const uploadImage = () => {
    imageform.post(route("profilePicture.update"), {
        onSuccess: (response) => {
            imageform.reset();
        },
        onError: (response) => {
            console.error(response);
        },
    });
};

const clear = () => {
    if (!cropper) return;
    cropper.clear();
};

const reset = () => {
    if (!cropper) return;
    cropper.reset();
};

const ready = () => {
    if (!cropper) return;
};

const profilePicture = computed(() => {
    return user.profile_picture_url || "images/profile_placeholder.png";
});
</script>

<template>
    <section class="flex items-center gap-3 flex-col">
        <img
            :src="profilePicture"
            alt="Profile Picture"
            class="rounded-full border-4 border-gray-500 h-36 w-36 object-cover"
        />
        <PrimaryButton class="w-fit" @click="$refs.uploadInput.click()">
            <span>Upload Profile Picture</span>
        </PrimaryButton>
        <input
            class="hidden"
            ref="uploadInput"
            type="file"
            accept="image/jpg, image/jpeg, image/png, image/gif"
            @change="selectFile"
        />
    </section>

    <Modal :show="isShowModal" maxWidth="xl">
        <div>
            <div class="p-4 rounded-lg overflow-hidden max-h-[600px]">
                <VuePictureCropper
                    :boxStyle="{
                        width: '100%',
                        height: '100%',
                        backgroundColor: '#000000',
                        margin: 'auto',
                    }"
                    :img="pic"
                    :options="{
                        viewMode: 1,
                        dragMode: 'crop',
                        aspectRatio: 1 / 1,
                    }"
                    @ready="ready"
                />
            </div>
            <div class="flex gap-2 p-4">
                <SecondaryButton @click="isShowModal = false" class="me-auto">
                    Cancel
                </SecondaryButton>
                <SecondaryButton @click="clear"> Clear </SecondaryButton>
                <SecondaryButton @click="reset"> Reset </SecondaryButton>
                <PrimaryButton @click="getResult"> Save Changes </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
