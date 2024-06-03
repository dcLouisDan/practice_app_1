<script setup>
import Modal from "./Modal.vue";
import MediaItem from "./MediaItem.vue";

const props = defineProps({
    media: Array,
});

console.log(props.media);
</script>

<template>
    <div class="flex justify-center max-w-lg">
        <div v-if="media.length === 1" class="rounded-lg v-50 overflow-hidden">
            <MediaItem :media="media[0]" />
        </div>
        <div
            v-if="media.length === 2 && media[0].media_type === 'image'"
            class="flex gap-1 rounded-lg overflow-hidden w-fit"
        >
            <MediaItem
                v-for="image in media"
                :media="image"
                class="h-72 w-72 object-cover v-50"
            />
        </div>
        <div
            v-if="media.length === 3 && media[0].media_type === 'image'"
            class="grid grid-cols-2 rounded-lg overflow-hidden w-fit h-96 gap-1"
        >
            <MediaItem :media="media[0]" class="object-cover v-50 row-span-2" />
            <MediaItem :media="media[1]" class="object-cover v-50" />
            <MediaItem :media="media[2]" class="object-cover v-50" />
        </div>
        <div
            v-if="media.length === 4 && media[0].media_type === 'image'"
            class="grid grid-cols-2 rounded-lg overflow-hidden w-fit h-96 gap-1"
        >
            <MediaItem
                v-for="item in media"
                :media="item"
                class="object-cover v-50"
            />
        </div>
    </div>
    <!-- <Modal :show="true"> </Modal> -->
</template>
