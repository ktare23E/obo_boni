<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WelcomeNav from '@/Components/WelcomeNav.vue';
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Hero Section -->
        <section class="relative h-[60vh]">
            <div
                class="h-full bg-[url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center">
                <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black/40"></div>

                <div class="relative h-full flex flex-col justify-center pl-[140px] text-white">
                    <div class="space-y-3 animate-fadeInUp">
                        <h2 class="text-5xl font-bold uppercase text-[#facc15] drop-shadow-lg">
                            Client Dashboard
                        </h2>
                        <p class="text-lg text-gray-200 w-3/5 leading-relaxed">
                            Manage your buildings, monitor permit applications, and track inspection updates in one place.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Stats Section -->
        <section class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white shadow-md rounded-xl p-6 text-center hover:shadow-lg transition">
                    <h2 class="text-4xl font-bold text-[#facc15]">3</h2>
                    <p class="text-gray-700 mt-2 font-medium">Buildings Registered</p>
                </div>

                <div class="bg-white shadow-md rounded-xl p-6 text-center hover:shadow-lg transition">
                    <h2 class="text-4xl font-bold text-[#facc15]">1</h2>
                    <p class="text-gray-700 mt-2 font-medium">Pending Permits</p>
                </div>

                <div class="bg-white shadow-md rounded-xl p-6 text-center hover:shadow-lg transition">
                    <h2 class="text-4xl font-bold text-[#facc15]">2</h2>
                    <p class="text-gray-700 mt-2 font-medium">Approved Permits</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-16">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link
                        href="route('buildings.index')"
                        class="flex flex-col items-center justify-center bg-white rounded-xl shadow-md hover:shadow-lg p-8 transition text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#facc15] mb-3" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span class="text-gray-800 font-medium">Manage My Buildings</span>
                    </Link>

                    <Link
                        href="#"
                        class="flex flex-col items-center justify-center bg-white rounded-xl shadow-md hover:shadow-lg p-8 transition text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#facc15] mb-3" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-gray-800 font-medium">Apply for Permit</span>
                    </Link>

                    <Link
                        href="route('inspections.index')"
                        class="flex flex-col items-center justify-center bg-white rounded-xl shadow-md hover:shadow-lg p-8 transition text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#facc15] mb-3" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6 1A9 9 0 1 1 3 12a9 9 0 0 1 18 0z" />
                        </svg>
                        <span class="text-gray-800 font-medium">Track Inspections</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-6 text-center text-sm">
            © {{ new Date().getFullYear() }} Office of the Building Official – All Rights Reserved.
        </footer>
    </div>
</template>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeInUp {
  animation: fadeInUp 1s ease-out;
}
</style>
