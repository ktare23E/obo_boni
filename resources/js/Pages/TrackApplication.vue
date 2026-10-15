<script setup>
import axios from 'axios';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import WelcomeNav from '@/Components/WelcomeNav.vue';

const user = usePage().props.auth?.user;

const form = useForm({
    registration_no: '',
});

const submitTracking = async () => {
    const registration_no = form.registration_no.trim();

    if (!registration_no) {
        Swal.fire({
            icon: 'warning',
            title: 'Reference Number Required',
            text: 'Please enter your reference number.',
            confirmButtonColor: '#eab308',
        });
        return;
    }

    form.clearErrors();

    try {
        const res = await axios.post(
            route('track.application.check'),
            { registration_no }
        );

        if (res.data.application) {
            router.get(
                route(
                    'track.application.status',
                    res.data.application.registration_no
                )
            );
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Application Not Found',
                text: 'No application was found with this reference number. Please check it and try again.',
                confirmButtonColor: '#111827',
            });
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Unable to Track Application',
            text: err.response?.data?.message
                || 'Something went wrong. Please try again.',
            confirmButtonColor: '#111827',
        });
    }
};
</script>

<template>

    <Head title="Track Application" />

    <div class="flex min-h-screen flex-col overflow-x-hidden bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Hero / Tracking Section -->
        <section class="relative min-h-[760px] w-full
                   sm:min-h-[780px] lg:min-h-[820px]">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')">
            </div>

            <!-- Background Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b
                       from-black/75 via-black/65 to-black/70"></div>

            <!-- Main Content -->
            <div class="relative z-10 mx-auto flex min-h-[760px]
                       max-w-[1600px] items-center
                       px-5 pb-12 pt-28
                       sm:min-h-[780px] sm:px-8 sm:pt-28
                       md:px-12
                       lg:min-h-[820px] lg:px-16
                       xl:px-24">
                <div class="w-full max-w-4xl space-y-6
                           text-center text-white
                           animate-fadeInUp sm:text-left">
                    <!-- Heading -->
                    <div class="space-y-3 sm:space-y-4">
                        <p class="text-sm font-bold uppercase
                                   tracking-[0.15em] text-yellow-400
                                   sm:text-base md:text-lg">
                            Office of the Building Official
                        </p>

                        <h1 class="text-3xl font-extrabold leading-tight
                                   tracking-tight sm:text-4xl
                                   md:text-5xl lg:text-6xl">
                            Apply &amp; Track Your
                            <span class="text-yellow-400">
                                Building Permit
                            </span>
                        </h1>

                        <p class="mx-auto max-w-2xl text-sm leading-relaxed
                                   text-gray-200 sm:mx-0
                                   sm:text-base md:text-lg">
                            Apply for a permit or track your application
                            status using your reference number.
                        </p>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex justify-center pt-1 sm:justify-start">
                        <Link :href="user
                            ? route('create_building')
                            : route('register')" class="inline-flex min-h-12 w-full
                                   items-center justify-center
                                   rounded-md bg-yellow-400 px-6 py-3
                                   text-sm font-bold text-gray-900
                                   shadow-lg transition
                                   hover:bg-yellow-300
                                   focus:outline-none focus:ring-2
                                   focus:ring-yellow-300
                                   focus:ring-offset-2
                                   focus:ring-offset-gray-900
                                   sm:w-auto sm:text-base">
                            Apply for Permit
                        </Link>
                    </div>

                    <!-- Tracking Card -->
                    <div class="mt-6 rounded-xl border border-white/30
                               bg-white/95 p-5 text-left text-gray-900
                               shadow-2xl backdrop-blur-md
                               sm:mt-8 sm:p-7 md:p-8">
                        <!-- Card Heading -->
                        <div class="mb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0
                                           items-center justify-center
                                           rounded-lg bg-yellow-100
                                           text-yellow-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" />
                                    </svg>
                                </div>

                                <div>
                                    <h2 class="text-lg font-bold
                                               text-gray-900 sm:text-xl">
                                        Track Your Application
                                    </h2>

                                    <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                                        Check your building permit status online.
                                    </p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-relaxed
                                       text-gray-600">
                                Enter the reference number provided
                                when you submitted your application.
                            </p>
                        </div>

                        <!-- Tracking Form -->
                        <form @submit.prevent="submitTracking" class="space-y-3 sm:space-y-0">
                            <div class="flex flex-col gap-3
                                       sm:flex-row sm:items-stretch">
                                <div class="min-w-0 flex-1">
                                    <label for="registration_no" class="mb-2 block text-sm
                                               font-semibold text-gray-700">
                                        Application Reference Number
                                    </label>

                                    <input id="registration_no" v-model="form.registration_no" type="text"
                                        name="registration_no" placeholder="e.g. BP-2025-8F4K9X" autocomplete="off"
                                        :disabled="form.processing" class="block min-h-12 w-full rounded-md
                                               border border-gray-300
                                               bg-white px-4 py-3 text-sm
                                               text-gray-900 shadow-sm
                                               placeholder:text-gray-400
                                               focus:border-yellow-400
                                               focus:outline-none
                                               focus:ring-2
                                               focus:ring-yellow-400/40
                                               disabled:cursor-not-allowed
                                               disabled:bg-gray-100
                                               sm:text-base" />
                                </div>

                                <div class="sm:self-end">
                                    <button type="submit" :disabled="form.processing" class="inline-flex min-h-12 w-full
                                               items-center justify-center
                                               gap-2 rounded-md
                                               bg-gray-900 px-7 py-3
                                               text-sm font-semibold
                                               text-white shadow
                                               transition
                                               hover:bg-gray-800
                                               focus:outline-none
                                               focus:ring-2
                                               focus:ring-gray-500
                                               focus:ring-offset-2
                                               disabled:cursor-not-allowed
                                               disabled:opacity-60
                                               sm:w-auto sm:text-base">
                                        <svg v-if="form.processing" class="h-5 w-5 animate-spin"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                        </svg>

                                        {{ form.processing ? 'Checking...' : 'Track Application' }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Helper Text -->
                        <div class="mt-5 flex items-start gap-2
                                   border-t border-gray-200 pt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <p class="text-xs leading-relaxed text-gray-500 sm:text-sm">
                                You can track your application even without
                                logging in. Keep your reference number safe
                                for future status checks.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Department Head -->
        <section class="bg-white py-12 sm:py-16 lg:py-20">
            <div class="mx-auto flex max-w-6xl flex-col items-center
                       gap-7 px-5 text-center
                       sm:px-8
                       md:flex-row md:gap-10 md:px-10 md:text-left
                       lg:gap-14">
                <!-- Image -->
                <div class="h-36 w-36 shrink-0 overflow-hidden
                           rounded-full border-4 border-yellow-400
                           shadow-lg sm:h-44 sm:w-44
                           lg:h-52 lg:w-52">
                    <img src="/imgs/boni_head.jpg" alt="Engr. Gilvert C. Maglinte" class="h-full w-full object-cover"
                        loading="lazy" />
                </div>

                <!-- Details -->
                <div class="min-w-0">
                    <p class="mb-2 text-sm font-bold uppercase
                               tracking-widest text-yellow-600">
                        Meet Our Department Head
                    </p>

                    <h2 class="text-xl font-bold leading-tight
                               text-gray-900 sm:text-2xl md:text-3xl">
                        Engr. Gilvert C. Maglinte
                    </h2>

                    <p class="mt-2 text-base font-semibold text-yellow-600 sm:text-lg">
                        Department Head
                    </p>

                    <p class="mx-auto mt-4 max-w-2xl text-sm
                               leading-7 text-gray-600
                               sm:text-base md:mx-0">
                        Ensuring safety, compliance, and efficient processing
                        of all building permit applications.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-auto bg-gray-900 px-4 py-6 text-center
                   text-xs leading-relaxed text-gray-400
                   sm:px-6 sm:text-sm">
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
