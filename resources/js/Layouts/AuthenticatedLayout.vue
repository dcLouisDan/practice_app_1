<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import HomeIcon from "vue-material-design-icons/Home.vue";
import MagnifyIcon from "vue-material-design-icons/Magnify.vue";
import AccountIcon from "vue-material-design-icons/Account.vue";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="bg-gray-100">
        <div class="min-h-screen container mx-auto flex">
            <nav class="p-3 border-r-2 w-60 border-gray-300">
                <ApplicationLogo style="height: 50px" />
                <div class="mt-4">
                    <NavLink
                        :href="route('dashboard')"
                        class="items-center text-2xl flex gap-4 w-full"
                        :active="route().current('dashboard')"
                    >
                        <home-icon :size="36" />
                        Home
                    </NavLink>
                    <NavLink class="items-center text-2xl flex gap-4 w-full">
                        <magnify-icon :size="36" />
                        Explore
                    </NavLink>
                    <NavLink class="items-center text-2xl flex gap-4 w-full">
                        <account-icon :size="36" />
                        Profile
                    </NavLink>
                </div>
            </nav>
            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
            <section class="w-60 border-l-2"></section>
        </div>
    </div>
</template>
