<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import MainHeader from "@/Components/MainHeader.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import InputError from "@/Components/InputError.vue";
import AutoResizeTextarea from "@/Components/AutoResizeTextarea.vue";

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

const props = defineProps(["chirp"]);
// console.log(props.chirp);
const chirpData = ref(props.chirp);
const page = usePage();
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
            replyForm.reset();
        });
};
// console.log(chirpData.value);
function updateChirpData(newValue) {
    chirpData.value = newValue;
}

onMounted(() => {
    axios.get(route("chirp.show.data", props.chirp.id)).then((response) => {
        chirpData.value = response.data;
        // console.log(response.data);
    });
});
</script>

<template>
    <Head title="Post" />
    <AuthenticatedLayout>
        <MainHeader :canBack="true" title="Post" />
        <Chirp
            class="border-b-2"
            :chirp="chirpData"
            :main-chirp="true"
            context="post"
            @update-chirp-data="updateChirpData"
        />

        <!-- Reply input div-->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
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
        </Transition>
        <div>
            <Chirp
                v-for="reply in chirpData.replies"
                :chirp="reply"
                :key="reply.id"
                context="reply"
            />
        </div>
    </AuthenticatedLayout>
</template>
