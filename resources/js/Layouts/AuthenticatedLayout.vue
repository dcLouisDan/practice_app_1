<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import HomeIcon from "vue-material-design-icons/Home.vue";
import MagnifyIcon from "vue-material-design-icons/Magnify.vue";
import AccountIcon from "vue-material-design-icons/Account.vue";

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <div class="bg-gray-100">
        <div class="min-h-screen container mx-auto flex">
            <nav class="p-3 border-r-2 border-gray-300 flex flex-col">
                <ApplicationLogo style="height: 50px" />
                <div class="mt-4 flex flex-col sm:w-60 flex-1">
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
                        href="#"
                    >
                        Explore
                    </NavLink>
                    <NavLink
                        :icon="AccountIcon"
                        class="items-center text-2xl flex gap-4"
                        href="#"
                    >
                        Profile
                    </NavLink>
                </div>
                <div class="content-end mb-5 flex flex-col">
                    <div class="text-lg">{{ user.name }}</div>
                    <Link
                        method="post"
                        class="text-sm text-gray-600 hover:text-gray-800 text-start"
                        :href="route('logout')"
                        as="button"
                        >Logout</Link
                    >
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
