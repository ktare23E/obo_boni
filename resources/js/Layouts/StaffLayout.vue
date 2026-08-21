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
                        <span class="text-xl font-bold text-[#FBBF24]">Building Permit</span>
                        <span class="text-xl font-bold ">Staff Side</span>
                        <button @click="toggleSidebar" class="text-gray-400 hover:text-white md:hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Nav -->
                    <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                        <nav class="flex-1 space-y-2">
                            <NavLink :href="route('staff_dashboard')"
                                :active="route().current('staff_dashboard')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                </svg>
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('staff_inspector')" :active="route().current('staff_inspector')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>

                                <p class="text-sm">Inspector</p>
                            </NavLink>
                            <NavLink :href="route('staff_clients')" :active="route().current('staff_clients')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                <p class="text-sm">Clients</p>
                            </NavLink>
                            <NavLink :href="route('staff_submissions')"
                                :active="route().current('staff_submissions')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                </svg>
                                <p class="text-sm">Submissions</p>
                            </NavLink>
                            <NavLink :href="route('staff_inspection')" :active="route().current('staff_inspection')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>

                                <p class="text-xs">Inspections</p>
                            </NavLink>
                            <NavLink :href="route('staff_business_list')" :active="route().current('staff_business_list')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>

                                <p class="text-sm">Buildings</p>
                            </NavLink>
                            <NavLink :href="route('staff_permit_list')" :active="route().current('staff_permit_list')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                </svg>


                                <p class="text-sm">Building Permit</p>
                            </NavLink>
                            <!-- <NavLink :href="route('assigned_inpsection')"
                                :active="route().current('assigned_inpsection')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>

                                <p class="text-sm">Business Inspection</p>
                            </NavLink> -->
                        </nav>
                    </div>

                        <!-- User Profile -->
                    <div class="p-4 border-t border-gray-700 bg-[#111827]">
                        <Link :href="route('staff_profile')" class="flex items-center space-x-3 cursor-pointer">
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
                                    Staff
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