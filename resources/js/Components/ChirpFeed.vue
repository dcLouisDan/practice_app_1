<script setup>
import Chirp from "@/Components/Chirp.vue";
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";

const props = defineProps({
    route: {
        type: String,
        default: "chirps.index",
    },
    userParam: {
        type: Number,
        default: null,
    },
    searchParam: {
        type: String,
        default: "",
    },
});

const chirps = ref([]);
const pageNumber = ref(1);
const loading = ref(false);
const hasMore = ref(true);

const fetchChirps = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route(props.route, {
                page: pageNumber.value,
                user: props.userParam,
                query: props.searchParam,
            })
        );
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

const refreshData = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        chirps.value = [];
        pageNumber.value = 1;
        const response = await axios.get(
            route(props.route, {
                page: pageNumber.value,
                user: props.userParam,
                query: props.searchParam,
            })
        );
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

const addChirp = (newData) => {
    chirps.value.unshift(newData);
};

onMounted(() => {
    fetchChirps();
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.addEventListener("scroll", handleScroll);
});

defineExpose({
    refreshData,
    newChirp(newChirpData) {
        chirps.value.unshift(newChirpData);
    },
});
</script>

<template>
    <div class="divide-y-2">
        <div v-if="loading" class="text-center py-4">Loading...</div>
        <div
            v-if="searchParam !== '' && chirps.length === 0"
            class="text-center text-gray-500 py-5"
        >
            No Chirps found
        </div>
        <div
            v-if="!loading && chirps.length === 0"
            class="text-center text-gray-500 py-10 px-2 sm:px-10"
        >
            <div class="font-bold text-xl">Welcome to Chirper!</div>
            <div>
                Your feed is currently empty. To see content, you can either
                create your first chirp or follow other users.
            </div>
        </div>
        <Chirp
            v-for="chirp in chirps"
            :key="chirp.id"
            :chirp="chirp"
            @refresh-data="refreshData"
            @update-chirp-data="updateChirpData"
            @new-chirp="addChirp"
        />
    </div>
</template>
