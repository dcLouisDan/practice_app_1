<script setup>
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AutoResizeTextarea from "@/Components/AutoResizeTextarea.vue";
import Chirp from "@/Components/Chirp.vue";
import { Link, Head, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import MainHeader from "@/Components/MainHeader.vue";
import axios from "axios";

const page = usePage();
const user = page.props.auth.user;
const form = useForm({
    message: "",
});

const chirps = ref([]);
onMounted(() => {
    axios.get(route("chirps.index")).then((response) => {
        chirps.value = response.data;
        console.log(response.data);
    });
});
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <MainHeader :can-back="false" title="Home" />
        <div class="">
            <div
                class="mx-auto px-4 pb-4 sm:p-4 sm:flex border-b-2 gap-2 pt-4 hidden"
            >
                <Link :href="route('profile.view')" as="button" class="mb-auto">
                    <img
                        :src="user.profile_picture_url"
                        alt="Profile Picture"
                        class="rounded-full h-14 w-14 object-cover"
                    />
                </Link>
                <form
                    @submit.prevent="
                        form.post(route('chirps.store'), {
                            onSuccess: () => form.reset(),
                        })
                    "
                    class="flex flex-col flex-1"
                >
                    <AutoResizeTextarea
                        v-model="form.message"
                        placeholder="What's on your mind?"
                        class="block w-full focus:ring-0 border-none max-h-36"
                    ></AutoResizeTextarea>
                    <InputError :message="form.errors.message" class="mt-2" />
                    <PrimaryButton class="mt-2 ms-auto">Chirp</PrimaryButton>
                </form>
            </div>
            <div class="divide-y border-b-2">
                <div class="text-center px-5 py-10" v-if="chirps.length === 0">
                    <h1 class="font-bold text-xl">
                        Not seeing any chirps yet?
                    </h1>
                    <p class="text-gray-500">
                        Follow some users to see their chirps here or be the
                        first to chirp!
                    </p>
                </div>
                <Chirp v-for="chirp in chirps" :key="chirp.id" :chirp="chirp" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
