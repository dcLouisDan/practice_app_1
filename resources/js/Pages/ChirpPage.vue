<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import InputError from "@/Components/InputError.vue";
const props = defineProps({
    chirp: Object,
});

const chirpData = ref(props.chirp);
const page = usePage();
const replyForm = useForm({
    message: "",
});
const postReply = async () => {
    await axios
        .post(route("replies.store", chirpData.value.id), {
            message: replyForm.message,
        })
        .then((response) => {
            chirpData.value = response.data;
            replyForm.reset();
        });
};

// console.log(chirpData.value);
</script>

<template>
    <AuthenticatedLayout>
        <div class="pt-10">
            <Chirp class="border-b-2 border-t-2" :chirp="chirp" />
        </div>
        <div class="border-b-2">
            <div class="flex px-6 mt-4">
                <img
                    :src="page.props.auth.user.profile_picture_url"
                    alt="Profile Picture"
                    class="rounded-full h-10 w-10 object-cover"
                />
                <div class="w-full">
                    <textarea
                        v-model="replyForm.message"
                        class="w-full bg-gray-100 text-gray-900 border-none resize-none focus:ring-0"
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
        </div>
        <div>
            <Chirp
                v-for="reply in chirpData.replies"
                :chirp="reply"
                :key="reply.id"
            />
        </div>
    </AuthenticatedLayout>
</template>
