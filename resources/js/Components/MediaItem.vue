<script setup>
import { computed } from "vue";

const props = defineProps({
    media: Object,
    index: Number,
    length: Number,
});

const emit = defineEmits(["openModal"]);
const openModal = () => {
    emit("openModal", props.index);
};
const extra = computed(() => {
    return props.length - 4;
});
</script>

<template>
    <div
        v-if="index < 4"
        class="overflow-hidden flex items-center justify-center relative"
    >
        <button
            v-if="media.media_type === 'image'"
            class="w-full h-full absolute top-0 left-0 z-10"
            @click="openModal"
        ></button>
        <div
            v-if="length > 4 && index === 3"
            class="bg-black absolute top-0 left-0 opacity-60 w-full h-full flex items-center justify-center text-white text-4xl"
        >
            + {{ extra }}
        </div>
        <img
            :src="media.media_url"
            v-if="media.media_type === 'image'"
            class="h-full object-cover w-full"
            onerror="this.src='images/media_placeholder.png'"
            alt="image"
        />
        <video
            controls
            v-if="media.media_type === 'video'"
            :src="media.media_url"
            class="rounded-lg v-50"
        />
    </div>
</template>
