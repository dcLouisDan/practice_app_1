<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import NewChirpReplyInput from "@/Components/NewChirpReplyInput.vue";
import MainHeader from "@/Components/MainHeader.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const props = defineProps(["chirp"]);
// console.log(props.chirp);
const chirpData = ref(props.chirp);

// console.log(chirpData.value);
function updateChirpData(newValue) {
    chirpData.value = newValue;
}

const fetchChirps = async () => {
    try {
        const response = await axios.get(
            route("chirp.show.data", props.chirp.id)
        );
        // console.log(response.data);
        return response.data;
    } catch (error) {
        console.error("Error fetching chirps:", error);
        return [];
    }
};

const refreshData = async () => {
    chirpData.value = await fetchChirps();
};

onMounted(async () => {
    chirpData.value = await fetchChirps();
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
        <NewChirpReplyInput
            :chirp_id="chirpData.id"
            @update-chirp-data="refreshData"
        />
        <div>
            <Chirp
                v-for="reply in chirpData.replies"
                :chirp="reply"
                :key="reply.id"
                context="reply"
                @update-chirp-data="updateChirpData"
            />
        </div>
    </AuthenticatedLayout>
</template>
