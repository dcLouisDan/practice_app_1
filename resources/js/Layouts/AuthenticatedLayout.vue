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
            <nav class="p-3 border-r-2 border-gray-300">
                <ApplicationLogo style="height: 50px" />
                <div class="mt-4 flex flex-col sm:w-60">
                    <NavLink
                        :href="route('dashboard')"
                        class="items-center text-2xl flex gap-4"
                        :active="route().current('dashboard')"
                        :icon="HomeIcon"
                    >
                        Home
                    </NavLink>
                    <NavLink
                        :icon="MagnifyIcon"
                        class="items-center text-2xl flex gap-4"
                    >
                        Explore
                    </NavLink>
                    <NavLink
                        :icon="AccountIcon"
                        class="items-center text-2xl flex gap-4"
                    >
                        Profile
                    </NavLink>
                </div>
            </nav>
            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
            <section class="sm:w-60 border-l-2"></section>
        </div>
    </div>
</template>
