<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const user = usePage().props.auth?.user;
const showDropdown = ref(false);
const showMobileMenu = ref(false);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const toggleMobileMenu = () => {
    showMobileMenu.value = !showMobileMenu.value;
};

const closeMenus = () => {
    showMobileMenu.value = false;
    showDropdown.value = false;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <nav
        class="fixed top-0 left-0 z-50 w-full
               bg-white/90 backdrop-blur-md
               shadow-sm border-b border-white/30"
    >
        <div
            class="mx-auto flex min-h-[76px] max-w-[1600px]
                   items-center justify-between
                   px-4 sm:px-6 lg:px-10 xl:px-16
                   py-3"
        >
            <!-- Logo and Title -->
            <Link
                :href="route('welcome')"
                class="flex min-w-0 items-center gap-2 sm:gap-3"
                @click="closeMenus"
            >
                <img
                    src="/imgs/boni_logo.jpg"
                    alt="OBO Logo"
                    class="h-10 w-10 shrink-0 rounded-full object-cover sm:h-12 sm:w-12"
                />

                <h1
                    class="text-sm font-bold leading-tight text-gray-900
                           sm:text-base md:text-lg lg:text-xl"
                >
                    <span class="text-yellow-500">OBO</span>
                    <span class="hidden sm:inline"> Online Permit System</span>
                    <span class="sm:hidden"> Permit System</span>
                </h1>
            </Link>

            <!-- Desktop Navigation -->
            <ul
                class="hidden items-center gap-5 text-sm font-semibold
                       text-gray-800 lg:flex xl:gap-8 xl:text-base"
            >
                <li>
                    <Link
                        :href="route('welcome')"
                        class="transition-colors hover:text-yellow-500"
                    >
                        Home
                    </Link>
                </li>

                <li>
                    <Link
                        :href="route('track_application')"
                        class="transition-colors hover:text-yellow-500"
                    >
                        Track Application
                    </Link>
                </li>

                <li v-if="user">
                    <Link
                        :href="route('buildings.index')"
                        class="transition-colors hover:text-yellow-500"
                    >
                        My Buildings
                    </Link>
                </li>
            </ul>

            <!-- Desktop Right Side -->
            <div class="hidden items-center gap-3 lg:flex">
                <template v-if="!user">
                    <Link
                        :href="route('login')"
                        class="font-semibold text-blue-800 transition hover:text-yellow-500"
                    >
                        Login
                    </Link>

                    <Link
                        :href="route('register')"
                        class="rounded-md bg-yellow-400 px-4 py-2
                               font-semibold text-gray-900 shadow
                               transition hover:bg-yellow-300"
                    >
                        Register
                    </Link>
                </template>

                <template v-else>
                    <div class="relative">
                        <button
                            type="button"
                            aria-label="Open account menu"
                            :aria-expanded="showDropdown"
                            class="rounded-full focus:outline-none
                                   focus:ring-2 focus:ring-yellow-400"
                            @click="toggleDropdown"
                        >
                            <img
                                src="/imgs/default.png"
                                alt="Avatar"
                                class="h-10 w-10 rounded-full border-2
                                       border-yellow-400 object-cover"
                            />
                        </button>

                        <transition name="fade">
                            <div
                                v-if="showDropdown"
                                class="absolute right-0 mt-3 w-56 overflow-hidden
                                       rounded-lg border border-gray-200
                                       bg-white shadow-xl"
                            >
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-gray-800">
                                        {{ user.name }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ user.email }}
                                    </p>
                                </div>

                                <Link
                                    :href="route('profile')"
                                    class="block px-4 py-3 text-sm text-gray-700
                                           transition hover:bg-gray-100"
                                    @click="closeMenus"
                                >
                                    Profile
                                </Link>

                                <button
                                    type="button"
                                    class="block w-full px-4 py-3 text-left
                                           text-sm text-red-600 transition
                                           hover:bg-red-50"
                                    @click="logout"
                                >
                                    Log out
                                </button>
                            </div>
                        </transition>
                    </div>
                </template>
            </div>

            <!-- Mobile Menu Button -->
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center
                       rounded-md text-gray-800 transition hover:bg-gray-100
                       focus:outline-none focus:ring-2 focus:ring-yellow-400
                       lg:hidden"
                :aria-expanded="showMobileMenu"
                aria-label="Toggle navigation menu"
                @click="toggleMobileMenu"
            >
                <!-- Hamburger -->
                <svg
                    v-if="!showMobileMenu"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                <!-- Close -->
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <transition name="fade">
            <div
                v-if="showMobileMenu"
                class="border-t border-gray-200 bg-white shadow-lg lg:hidden"
            >
                <div class="space-y-1 px-4 py-4 sm:px-6">
                    <Link
                        :href="route('welcome')"
                        class="block rounded-md px-4 py-3 font-medium
                               text-gray-800 transition hover:bg-yellow-50
                               hover:text-yellow-600"
                        @click="closeMenus"
                    >
                        Home
                    </Link>

                    <Link
                        :href="route('track_application')"
                        class="block rounded-md px-4 py-3 font-medium
                               text-gray-800 transition hover:bg-yellow-50
                               hover:text-yellow-600"
                        @click="closeMenus"
                    >
                        Track Application
                    </Link>

                    <Link
                        v-if="user"
                        :href="route('buildings.index')"
                        class="block rounded-md px-4 py-3 font-medium
                               text-gray-800 transition hover:bg-yellow-50
                               hover:text-yellow-600"
                        @click="closeMenus"
                    >
                        My Buildings
                    </Link>

                    <div class="my-3 border-t border-gray-200"></div>

                    <!-- Guest Actions -->
                    <template v-if="!user">
                        <Link
                            :href="route('login')"
                            class="block rounded-md px-4 py-3 font-semibold
                                   text-blue-800 transition hover:bg-gray-50"
                            @click="closeMenus"
                        >
                            Login
                        </Link>

                        <Link
                            :href="route('register')"
                            class="block rounded-md bg-yellow-400 px-4 py-3
                                   text-center font-semibold text-gray-900
                                   transition hover:bg-yellow-300"
                            @click="closeMenus"
                        >
                            Register
                        </Link>
                    </template>

                    <!-- Authenticated User -->
                    <template v-else>
                        <div class="flex items-center gap-3 px-4 py-3">
                            <img
                                src="/imgs/default.png"
                                alt="Avatar"
                                class="h-11 w-11 rounded-full border-2
                                       border-yellow-400 object-cover"
                            />

                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-800">
                                    {{ user.name }}
                                </p>
                                <p class="truncate text-sm text-gray-500">
                                    {{ user.email }}
                                </p>
                            </div>
                        </div>

                        <Link
                            :href="route('profile')"
                            class="block rounded-md px-4 py-3 text-gray-700
                                   transition hover:bg-gray-100"
                            @click="closeMenus"
                        >
                            Profile
                        </Link>

                        <button
                            type="button"
                            class="block w-full rounded-md px-4 py-3
                                   text-left font-medium text-red-600
                                   transition hover:bg-red-50"
                            @click="logout"
                        >
                            Log out
                        </button>
                    </template>
                </div>
            </div>
        </transition>
    </nav>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>