<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import MainHeader from "@/Components/MainHeader.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import InputError from "@/Components/InputError.vue";

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
                context="reply"
            />
        </div>
    </AuthenticatedLayout>
</template>
