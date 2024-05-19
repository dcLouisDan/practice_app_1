<script setup>
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps(["chirps"]);

const form = useForm({
    message: "",
});
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
                <Chirp v-for="chirp in chirps" :key="chirp.id" :chirp="chirp" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
