
<script setup>
import WelcomeNav from '@/Components/WelcomeNav.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';

const user = usePage().props.auth?.user;
</script>

<template>
    <Head title="Welcome" />

    <div class="flex min-h-screen flex-col overflow-x-hidden bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Hero Section -->
        <section
            class="relative min-h-[650px] h-[90vh] max-h-[1000px] w-full"
        >
            <!-- Background Image -->
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')"
            ></div>

            <!-- Dark Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-b
                       from-black/75 via-black/60 to-black/60"
            ></div>

            <!-- Hero Content -->
            <div
                class="relative z-10 mx-auto flex h-full max-w-[1600px]
                       items-center px-5 pb-8 pt-28
                       sm:px-8 sm:pt-28
                       md:px-12
                       lg:px-16 xl:px-24"
            >
                <div
                    class="w-full max-w-4xl space-y-5 text-center
                           text-white animate-fadeInUp
                           sm:space-y-6 sm:text-left"
                >
                    <p
                        class="text-sm font-bold uppercase tracking-[0.15em]
                               text-yellow-400
                               sm:text-base md:text-lg"
                    >
                        Office of the Building Official
                    </p>

                    <h1
                        class="text-3xl font-extrabold leading-tight
                               tracking-tight text-white
                               sm:text-4xl
                               md:text-5xl
                               lg:text-6xl"
                    >
                        Streamline Your
                        <span class="text-yellow-400">
                            Building Permit
                        </span>
                        Application Online
                    </h1>

                    <p
                        class="mx-auto max-w-xl text-sm leading-relaxed
                               text-gray-200
                               sm:mx-0 sm:text-base
                               md:text-lg"
                    >
                        Apply for, track, and manage your building permit
                        requests anytime and anywhere.
                    </p>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col justify-center gap-3 pt-3
                               sm:flex-row sm:justify-start sm:gap-4"
                    >
                        <Link
                            v-if="!user"
                            :href="route('register')"
                            class="inline-flex min-h-12 items-center justify-center
                                   rounded-md bg-yellow-400 px-6 py-3
                                   text-sm font-bold text-gray-900 shadow-lg
                                   transition hover:bg-yellow-300
                                   focus:outline-none focus:ring-2
                                   focus:ring-yellow-300 focus:ring-offset-2
                                   focus:ring-offset-gray-900
                                   sm:text-base"
                        >
                            Apply for Permit
                        </Link>

                        <Link
                            v-if="user"
                            :href="route('create_building')"
                            class="inline-flex min-h-12 items-center justify-center
                                   rounded-md bg-yellow-400 px-6 py-3
                                   text-sm font-bold text-gray-900 shadow-lg
                                   transition hover:bg-yellow-300
                                   focus:outline-none focus:ring-2
                                   focus:ring-yellow-300 focus:ring-offset-2
                                   focus:ring-offset-gray-900
                                   sm:text-base"
                        >
                            Apply for Permit
                        </Link>

                        <Link
                            :href="route('track_application')"
                            class="inline-flex min-h-12 items-center justify-center
                                   rounded-md border border-white/70
                                   bg-white/10 px-6 py-3 text-sm
                                   font-semibold text-white backdrop-blur-sm
                                   transition hover:bg-white/20
                                   sm:text-base"
                        >
                            Track Application
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Department Head -->
        <section class="bg-white py-12 sm:py-16 lg:py-20">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center
                       gap-7 px-5 text-center
                       sm:px-8
                       md:flex-row md:gap-10 md:px-10 md:text-left
                       lg:gap-14"
            >
                <!-- Department Head Image -->
                <div
                    class="h-36 w-36 shrink-0 overflow-hidden rounded-full
                           border-4 border-yellow-400 shadow-lg
                           sm:h-44 sm:w-44
                           lg:h-52 lg:w-52"
                >
                    <img
                        src="/imgs/boni_head.jpg"
                        alt="Engr. Gilvert C. Maglinte"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    />
                </div>

                <!-- Department Head Details -->
                <div class="min-w-0">
                    <p
                        class="mb-2 text-sm font-bold uppercase
                               tracking-widest text-yellow-600"
                    >
                        Meet Our Department Head
                    </p>

                    <h2
                        class="text-xl font-bold leading-tight text-gray-900
                               sm:text-2xl md:text-3xl"
                    >
                        Engr. Gilvert C. Maglinte
                    </h2>

                    <p class="mt-2 text-base font-semibold text-yellow-600 sm:text-lg">
                        Department Head
                    </p>

                    <p
                        class="mx-auto mt-4 max-w-2xl text-sm leading-7
                               text-gray-600
                               sm:text-base md:mx-0"
                    >
                        Under the Office of the Building Official, Engr.
                        Maglinte ensures compliance with safety and structural
                        standards across all city constructions, promoting
                        efficient and transparent public service.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="mt-auto bg-gray-900 px-4 py-6 text-center
                   text-xs leading-relaxed text-gray-400
                   sm:px-6 sm:text-sm"
        >
            © {{ new Date().getFullYear() }} Office of the Building Official
            – All Rights Reserved.
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
    animation: fadeInUp 0.8s ease-out;
}

@media (prefers-reduced-motion: reduce) {
    .animate-fadeInUp {
        animation: none;
    }
}
</style>
