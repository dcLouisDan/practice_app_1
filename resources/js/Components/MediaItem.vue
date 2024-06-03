<script setup>
const props = defineProps({
    media: Object,
});

console.log(props.media);
</script>

<template>
    <div class="overflow-hidden flex items-center justify-center">
        <img
            :src="media.media_url"
            v-if="media.media_type === 'image'"
            class="object-cover w-96 h-96"
        />
        <video
            controls
            v-if="media.media_type === 'video'"
            :src="media.media_url"
            class="rounded-lg v-50"
        />
    </div>
</template>
