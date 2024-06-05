<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import { Link, Head, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";
import MainHeader from "@/Components/MainHeader.vue";
import axios from "axios";
import NewChirpInput from "../Components/NewChirpInput.vue";

const chirps = ref([]);
const pageNumber = ref(1);
const loading = ref(false);
const hasMore = ref(true);

const fetchChirps = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route("chirps.index", { page: pageNumber.value })
        );
        // console.log(response.data);
        // console.log(pageNumber.value);
        if (!response.data.next_page_url) {
            hasMore.value = false;
        }

        pageNumber.value += 1;
        return chirps.value.push(...response.data.data);
    } catch (error) {
        console.error("Error fetching chirps:", error);
        return [];
    } finally {
        loading.value = false;
    }
};

const updateChirpData = (newData) => {
    const updatedChirps = chirps.value.map((chirp) =>
        chirp.id === newData.id
            ? {
                  ...chirp,
                  message: newData.message,
                  likes: newData.likes,
                  replies: newData.replies,
                  rechirps: newData.rechirps,
                  parent: newData.parent,
              }
            : chirp
    );

    const updatedChirpParents = updatedChirps.map((chirp) => {
        if (chirp.parent) {
            return chirp.parent.id === newData.id
                ? {
                      ...chirp,
                      parent: {
                          ...chirp.parent,
                          message: newData.message,
                          likes: newData.likes,
                          replies: newData.replies,
                          rechirps: newData.rechirps,
                      },
                  }
                : chirp;
        } else {
            return chirp;
        }
    });

    // console.log("update: ", updatedChirpParents);
    chirps.value = updatedChirpParents;
};

const handleScroll = () => {
    if (
        window.innerHeight + window.scrollY >=
        document.body.offsetHeight - 500
    ) {
        fetchChirps();
    }
};

const refreshData = () => {
    fetchChirps();
};

onMounted(() => {
    fetchChirps();
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.addEventListener("scroll", handleScroll);
});
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="relative">
            <MainHeader :can-back="false" title="Home" class="sticky top-0" />
            <NewChirpInput @refresh-data="refreshData" />
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
                <div v-if="loading" class="text-center py-4">Loading...</div>
                <Chirp
                    v-for="chirp in chirps"
                    :key="chirp.id"
                    :chirp="chirp"
                    @refresh-data="refreshData"
                    @update-chirp-data="updateChirpData"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
