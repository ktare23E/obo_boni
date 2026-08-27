<script setup>
import WelcomeNav from '@/Components/WelcomeNav.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';

const user = usePage().props.auth?.user;
</script>

<template>
    <Head title="Welcome" />
    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Hero Section -->
        <section class="relative h-[90vh]">
            <div
                class="h-full bg-[url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center">
                <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/60 to-black/50"></div>

                <div class="relative h-full flex flex-col justify-center pl-[140px] text-white">
                    <div class="space-y-4 animate-fadeInUp">
                        <h2 class="text-4xl font-semibold tracking-wide uppercase text-[#facc15]">
                            Office of the Building Official
                        </h2>
                        <h1 class="text-[52px] font-bold leading-tight max-w-3xl">
                            Streamline Your <span class="text-[#facc15]">Building Permit</span> Application Online
                        </h1>
                        <p class="text-gray-200 max-w-lg">
                            Apply for, track, and manage your building permit requests anytime and anywhere.
                        </p>

                        <div class="flex gap-5 pt-4">
                            <Link
                                v-if="!user"
                                :href="route('register')"
                                class="rounded-md px-6 py-3 bg-[#facc15] text-black font-medium shadow hover:bg-yellow-400 transition">
                                Apply for Permit
                            </Link>


                            <Link
                                v-if="user"
                                :href="route('create_building')"
                                class="rounded-md px-6 py-3 bg-[#facc15] text-black font-medium shadow hover:bg-yellow-400 transition">
                                Apply for Permit
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Department Head -->
        <section class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center gap-10">
                <div class="w-48 h-48 rounded-full overflow-hidden shadow-md border-4 border-[#facc15]">
                    <img
                        src="/imgs/boni_head.jpg"
                        alt="Engr. Melindo B. Tala"
                        class="object-cover w-full h-full"
                    />
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Engr. Gilvert C. Maglinte</h3>
                    <p class="text-[#facc15] text-lg font-semibold">Department Head</p>
                    <p class="mt-3 text-gray-600 max-w-2xl">
                        Under the Office of the Building Official, Engr. Maglinte ensures compliance with safety and
                        structural standards across all city constructions, promoting efficient and transparent public service.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-6 text-center text-sm">
            © {{ new Date().getFullYear() }} Office of the Building Official – All Rights Reserved.
        </footer>
    </div>
</template>

<style>
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
