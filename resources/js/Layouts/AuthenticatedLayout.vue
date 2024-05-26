<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import HomeIcon from "vue-material-design-icons/Home.vue";
import MagnifyIcon from "vue-material-design-icons/Magnify.vue";
import AccountIcon from "vue-material-design-icons/Account.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <div class="bg-gray-100">
        <div
            class="min-h-screen container mx-auto flex relative flex-col sm:flex-row justify-center"
        >
            <header
                class="sticky top-0 w-full flex justify-center py-3 border-b-2 sm:hidden"
            >
                <ApplicationLogo style="height: 50px" />
            </header>
            <nav
                class="sm:p-3 border-t-2 sm:border-t-0 w-full sm:w-fit border-gray-300 flex sm:flex-col fixed bottom-0 left-0 sm:sticky sm:min-h-screen sm:max-h-screen"
            >
                <ApplicationLogo style="height: 50px" class="hidden sm:block" />
                <div
                    class="p-4 w-full bg-gray-100 sm:p-0 sm:mt-4 flex justify-around sm:justify-normal sm:flex-col lg:w-60 flex-1"
                >
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
                        :active="route().current('profile.view')"
                        :href="route('profile.view')"
                    >
                        Profile
                    </NavLink>
                </div>
                <div class="content-end mb-5 lg:flex flex-col hidden">
                    <Link
                        :href="route('profile.view')"
                        as="button"
                        class="text-lg text-gray-800 hover:text-gray-900 text-start"
                        >{{ user.name }}</Link
                    >
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
            <main class="flex-1 max-w-screen-md sm:border-l-2">
                <slot />
            </main>
            <section class="hidden lg:block lg:w-60 border-l-2"></section>
        </div>
    </div>
</template>
