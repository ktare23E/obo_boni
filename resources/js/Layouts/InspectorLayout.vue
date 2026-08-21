<script setup>
import { ref } from "vue";
import NavLink from "@/Components/NavLink.vue";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";

const page = usePage(); 

const sidebarOpen = ref(true); // default visible

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const props = defineProps({
    title: String
})
</script>

<template>
    <div class="bg-gray-100 font-sans">
        <div class="flex h-screen overflow-hidden">

            <!-- Sidebar -->
            <transition enter-active-class="transition duration-300" enter-from-class="-ml-64" enter-to-class="ml-0"
                leave-active-class="transition duration-300" leave-from-class="ml-0" leave-to-class="-ml-64">

                <div v-if="sidebarOpen" class="flex flex-col w-64 bg-[#1E293B] text-white">
                    <!-- Brand -->
                    <div
                        class="flex flex-col items-center justify-center h-16 px-4 bg-[#111827] border-b border-gray-700">
                        <span class="text-xl font-bold text-[#FBBF24]">Business Permit</span>
                        <span class="text-xl font-bold ">Inspector Side</span>
                        <button @click="toggleSidebar" class="text-gray-400 hover:text-white md:hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Nav -->
                    <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                        <nav class="flex-1 space-y-2">
                            <NavLink :href="route('inspector_dashboard')"
                                :active="route().current('inspector_dashboard')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                </svg>
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('inspector_schedule')"
                                :active="route().current('inspector_schedule')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                                <p class="text-sm">Schedules</p>
                            </NavLink>
                            <NavLink :href="route('assigned_inpsection')"
                                :active="route().current('assigned_inpsection')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>

                                <p class="text-sm">Building Inspection</p>
                            </NavLink>
                        </nav>
                    </div>

                    <!-- User Profile -->
                    <div class="p-4 border-t border-gray-700 bg-[#111827]">
                        <Link :href="route('inspector_profile')" class="flex items-center space-x-3 cursor-pointer">
                            <!-- Avatar -->
                            <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center text-white font-semibold">
                                {{ $page.props.auth.user.first_name.charAt(0) }}
                            </div>

                            <!-- Name + Role -->
                            <div class="leading-tight">
                                <p class="text-white font-semibold text-sm">
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p class="text-gray-400 text-xs">
                                    Inspector
                                </p>
                            </div>
                        </Link>
                    </div>
                    <!-- Logout -->
                    <div class="p-4 border-t border-gray-700 bg-[#111827]">
                        <button @click="router.post(route('logout'))"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium text-left text-white hover:bg-red-600 rounded-lg space-x-2">
                            <!-- Logout Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                            </svg>

                            <h1>Logout</h1>
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-hidden">
                <!-- Top Navigation -->
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                    <div class="flex items-center">
                        <button @click="toggleSidebar" class="text-gray-500 focus:outline-none md:hidden">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800 ml-4">{{ props.title }}</h1>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>
</template>