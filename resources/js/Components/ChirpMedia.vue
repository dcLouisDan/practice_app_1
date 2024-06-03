<script setup>
const props = defineProps({
    media: Array,
});

console.log(props.media);
</script>

<template>
    <div class="flex justify-center">
        <div v-if="media[0].media_type === 'video'">
            <video controls :src="media[0].media_url" class="rounded-lg v-50" />
        </div>
        <div v-if="media.length === 1 && media[0].media_type === 'image'">
            <img :src="media[0].media_url" alt="" class="rounded-lg v-50" />
        </div>
        <div
            v-if="media.length === 2 && media[0].media_type === 'image'"
            class="flex rounded-lg overflow-hidden w-fit"
        >
            <img
                v-for="image in media"
                :src="image.media_url"
                class="h-72 w-72 object-cover v-50"
            />
        </div>
    </div>
</template>
