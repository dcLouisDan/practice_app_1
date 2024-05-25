<script setup>
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps(["chirps"]);
const page = usePage();
const user = page.props.auth.user;
const form = useForm({
    message: "",
});

const chirpsWithLikes = computed(() =>
    props.chirps.map((chirp) => ({
        ...chirp,
        isLikedByUser: chirp.likes.some((like) => like.user_id === user.id),
        context: "feed",
    }))
);
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="mx-auto px-4 pb-4 sm:p-6 lg:p-8 border-b-2">
                <form
                    @submit.prevent="
                        form.post(route('chirps.store'), {
                            onSuccess: () => form.reset(),
                        })
                    "
                    class="flex flex-col"
                >
                    <textarea
                        v-model="form.message"
                        placeholder="What's on your mind"
                        class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md"
                    ></textarea>
                    <InputError :message="form.errors.message" class="mt-2" />
                    <PrimaryButton class="mt-4 ms-auto">Chirp</PrimaryButton>
                </form>
            </div>
            <div class="divide-y border-b-2">
                <Chirp
                    v-for="chirp in chirpsWithLikes"
                    :key="chirp.id"
                    :chirp="chirp"
                    :isLiked="chirp.isLikedByUser"
                    :context="chirp.context"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
