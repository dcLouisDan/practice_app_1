<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    icon: {
        type: [Object, Function],
        required: true,
    },
    iconSize: {
        type: Number,
        default: 36,
    },
    haveBadge: {
        type: Boolean,
        default: false,
    },
    badgeData: {
        type: Number,
        default: 0,
    },
});

const classes = computed(() =>
    props.active
        ? "inline-flex items-center px-4 py-2 font-medium text-lg leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out hover:bg-gray-200 rounded-full w-fit"
        : "inline-flex items-center px-4 py-2 font-medium text-lg leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out hover:bg-gray-200 rounded-full w-fit"
);
</script>

<template>
    <Link :href="href" :class="classes">
        <div class="relative">
            <div
                v-if="haveBadge && badgeData > 0"
                class="absolute bg-red-600 text-white top-0 right-0 text-xs h-4 w-4 p-1 rounded-full flex items-center justify-center"
            >
                {{ badgeData }}
            </div>
            <icon :is="icon" :size="iconSize" />
        </div>
        <div class="hidden lg:block pe-3">
            <slot />
        </div>
    </Link>
</template>
