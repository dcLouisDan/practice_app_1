<script setup>
import { computed, onMounted, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import RecommendedUsers from "@/Components/RecommendedUsers.vue";
import HomeIcon from "vue-material-design-icons/Home.vue";
import MagnifyIcon from "vue-material-design-icons/Magnify.vue";
import AccountIcon from "vue-material-design-icons/Account.vue";
import BellIcon from "vue-material-design-icons/Bell.vue";
import Search from "@/Components/Search.vue";
import axios from "axios";
const page = usePage();
const user = page.props.auth.user;
const unreadNotifications = ref([]);
const unreadNotificationsCount = computed(() => {
    return unreadNotifications.value.length;
});
const profilePicture = computed(() => {
    return user.profile_picture_url || "images/profile_placeholder.png";
});

onMounted(() => {
    axios.get(route("notifications.get.unread")).then((response) => {
        // console.log(response.data);
        unreadNotifications.value = response.data;
    });
});
</script>

<template>
    <div class="bg-gray-100">
        <div
            class="min-h-screen container mx-auto flex relative flex-col sm:flex-row justify-center"
        >
            <nav
                class="sm:p-3 border-t-2 sm:border-t-0 sm:w-fit lg:w-64 border-gray-300 flex sm:flex-col fixed bottom-0 left-0 sm:sticky sm:top-0 sm:min-h-screen sm:max-h-screen w-full bg-gray-100 z-50"
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
                        :href="route('notifications.show')"
                        class="items-center text-2xl flex gap-4"
                        :active="route().current('notifications.show')"
                        :icon="BellIcon"
                        :icon-size="32"
                        :have-badge="true"
                        :badge-data="unreadNotificationsCount"
                    >
                        Notifications
                    </NavLink>
                    <NavLink
                        :icon="MagnifyIcon"
                        class="items-center text-2xl flex gap-4"
                        :href="route('search')"
                        :active="route().current('search')"
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
                <div class="content-end mb-5 hidden sm:flex gap-3">
                    <Link :href="route('profile.view')" as="button">
                        <img
                            :src="profilePicture"
                            alt="Profile Picture"
                            class="rounded-full h-12 w-12 object-cover"
                        />
                    </Link>
                    <div class="lg:flex flex-col hidden">
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
                </div>
            </nav>
            <!-- Page Content -->
            <main class="flex-1 max-w-screen-sm sm:border-x-2">
                <slot />
                <div class="w-full h-40"></div>
            </main>
            <section class="hidden lg:block lg:w-72">
                <div class="pt-5 px-3 sm:sticky sm:top-0">
                    <Search v-if="!route().current('search')" />
                    <div
                        class="bg-white mt-3 border-2 rounded-lg overflow-hidden"
                    >
                        <RecommendedUsers />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
