<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import MainHeader from "@/Components/MainHeader.vue";
import NewChirpInput from "../Components/NewChirpInput.vue";
import ChirpFeed from "@/Components/ChirpFeed.vue";
import { ref } from "vue";

const chirpFeed = ref(null);
const newChirp = (newData) => {
    chirpFeed.value.newChirp(newData);
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="relative">
            <MainHeader :can-back="false" title="Home" class="sticky top-0" />
            <NewChirpInput
                @refresh-data="$refs.chirpFeed.refreshData()"
                @new-chirp="newChirp"
            />
            <div class="divide-y border-b-2">
                <!-- <div class="text-center px-5 py-10" v-if="chirps.length === 0">
                    <h1 class="font-bold text-xl">
                        Not seeing any chirps yet?
                    </h1>
                    <p class="text-gray-500">
                        Follow some users to see their chirps here or be the
                        first to chirp!
                    </p>
                </div> -->
                <ChirpFeed ref="chirpFeed" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
