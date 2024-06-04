<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, onMounted, ref, watch } from "vue";
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
const replyForm = useForm({
    message: "",
});
const postReply = async () => {
    await axios
        .post(route("chirp.reply", props.chirp_id), {
            message: replyForm.message,
            parent_id: props.chirp_id,
        })
        .then((response) => {
            // console.log(response);
            emit("updateChirpData", response.data);
            replyForm.reset();
        });
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
            </div>
        </div>
        <div class="py-2 px-4 flex my-auto">
            <PrimaryButton class="ms-auto" @click="postReply">
                Reply
            </PrimaryButton>
        </div>
    </div>
</template>
