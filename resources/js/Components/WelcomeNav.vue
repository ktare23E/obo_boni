f<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const user = usePage().props.auth?.user;
const showDropdown = ref(false);

const toggleDropdown = () => (showDropdown.value = !showDropdown.value);
const logout = () => router.post(route('logout'));
</script>

<template>
    <nav
        class="fixed top-0 z-50 w-full text-[17px] px-[160px] py-[20px]
               bg-white/30 backdrop-blur-md flex justify-between items-center shadow-sm border-b border-white/20">
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <img src="/imgs/obo.jpeg" alt="Logo" class="w-12 rounded-full" />
            <h1 class="text-xl font-semibold text-black drop-shadow">
                <span class="text-[#facc15]">OBO</span> Online Permit System
            </h1>
        </div>

        <!-- Navigation -->
        <ul class="flex gap-[32px] text-[#facc15] font-medium drop-shadow">
            <!-- <li v-if="user" class="cursor-pointer hover:text-[#facc15] transition-colors">
                <Link :href="route('client_dashboard')">Dashboard</Link>
            </li> -->
            <Link :href="route('welcome')" class="cursor-pointer hover:text-[#facc15] transition-colors">Home</Link>
            <Link :href="route('track_application')" class="cursor-pointer hover:text-[#facc15] transition-colors">Track Application</Link>
<!--         
            <li class="cursor-pointer hover:text-[#facc15] transition-colors">About</li>
            <li class="cursor-pointer hover:text-[#facc15] transition-colors">Contact</li> -->
         
            <li v-if="user" class="cursor-pointer hover:text-[#facc15] transition-colors">
                <Link :href="route('buildings.index')">My Buildings</Link>
            </li>
            <!-- <li v-if="user" class="cursor-pointer hover:text-[#facc15] transition-colors">
                <Link :href="route('permits.create')">Apply Permit</Link>
            </li> -->
        </ul>

        <!-- Right Side -->
        <div class="flex items-center gap-3 relative">
            <!-- Guest -->
            <template v-if="!user">
                <Link
                    :href="route('login')"
                    class="text-blue-800 font-medium hover:text-[#facc15] transition drop-shadow">
                    Login
                </Link>
                <Link
                    :href="route('register')"
                    class="bg-[#facc15]/90 text-black px-4 py-2 rounded-md font-medium shadow hover:bg-yellow-400 transition">
                    Register
                </Link>
            </template>

            <!-- Authenticated -->
            <template v-else>
                <div class="relative">
                    <img
                        src="/imgs/default.png"
                        alt="Avatar"
                        class="w-10 h-10 rounded-full border-2 border-[#facc15] cursor-pointer"
                        @click="toggleDropdown"
                    />

                    <!-- Dropdown -->
                    <transition name="fade">
                        <div
                            v-if="showDropdown"
                            class="absolute right-0 mt-3 w-44 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm text-gray-700 font-semibold">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div>
                            <ul class="text-gray-700 text-sm">
                                <li>
                                    <Link
                                        :href="route('profile')"
                                        class="block px-4 py-2 hover:bg-gray-100 transition">
                                        Profile
                                    </Link>
                                </li>
                                <li>
                                    <button
                                        @click="logout"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition">
                                        Log out
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </transition>
                </div>
            </template>
        </div>
    </nav>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
