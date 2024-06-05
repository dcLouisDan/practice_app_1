<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Search from "@/Components/Search.vue";
import Chirp from "@/Components/Chirp.vue";
import UserResult from "@/Components/UserResult.vue";
import { ref, computed } from "vue";
const props = defineProps({ query: String, users: Array, chirps: Array });

const isQueryEmpty = computed(() => {
    return props.query === "";
});
const chirps = ref(props.chirps);
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

    console.log("update: ", updatedChirpParents);
    chirps.value = updatedChirpParents;
};
// console.log(props);
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-3 py-5">
            <Search :query="props.query" :isFocused="isQueryEmpty" />
        </div>
        <div
            class="text-center text-gray-500 py-5"
            v-if="!props.users && !props.chirps"
        >
            Try seaching for people or keywords.
        </div>
        <div
            class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
            v-if="props.users"
        >
            <h4 class="font-bold px-4 py-2">People</h4>
            <div
                class="text-center text-gray-500 py-5"
                v-if="props.users.length === 0"
            >
                No people found.
            </div>
            <UserResult
                v-for="user in props.users"
                :user="user"
                :key="user.id"
            />
        </div>
        <div
            class="divide-y divide-gray-300 border-b-2 border-b-gray-300"
            v-if="props.chirps"
        >
            <h4 class="font-bold px-4 py-2">Chirps</h4>
            <div
                class="text-center text-gray-500 py-5"
                v-if="props.chirps.length === 0"
            >
                No chirps found.
            </div>
            <Chirp
                v-for="chirp in chirps"
                :chirp="chirp"
                :key="chirp.id"
                @update-chirp-data="updateChirpData"
            />
        </div>
    </AuthenticatedLayout>
</template>
