<script setup>
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    registration_no: '',
});

const submitTracking = async () => {
    const registration_no = form.registration_no.trim();
    if (!registration_no) {
        Swal.fire({
            icon: 'warning',
            title: 'Reference number required',
            text: 'Please enter your reference number.',
        });
        return;
    }

    try {
        const res = await axios.post(route('track.application.check'), { registration_no });

        if (res.data.application) {
            // Redirect to timeline page
            router.get(route('track.application.status', res.data.application.registration_no));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Not Found',
                text: 'Application with this reference number does not exist.',
            });
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong. Please try again.',
        });
    }
};
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
                    <div class="space-y-6 animate-fadeInUp max-w-3xl">

                        <h2 class="text-4xl font-semibold uppercase text-[#facc15]">
                            Office of the Building Official
                        </h2>

                        <h1 class="text-[52px] font-bold leading-tight">
                            Apply & Track Your
                            <span class="text-[#facc15]">Building Permit</span>
                        </h1>

                        <p class="text-gray-200">
                            Apply for a permit or track your application status using your reference number.
                        </p>

                        <!-- ACTION BUTTONS -->
                        <div class="flex gap-4 pt-4">
                            <Link
                                :href="user ? route('create_building') : route('register')"
                                class="rounded-md px-6 py-3 bg-[#facc15] text-black font-medium shadow hover:bg-yellow-400 transition"
                            >
                                Apply for Permit
                            </Link>
                        </div>

                        <!-- TRACK APPLICATION CARD -->
                        <div class="mt-10 bg-white/95 backdrop-blur rounded-xl p-6 shadow-lg text-gray-900">

                            <h3 class="text-xl font-bold mb-2">
                                Track Your Application
                            </h3>

                            <p class="text-sm text-gray-600 mb-4">
                                Enter your reference number to check the status of your building application.
                            </p>

                            <form @submit.prevent="submitTracking" class="flex gap-3">
                                <input
                                    v-model="form.registration_no"
                                    type="text"
                                    placeholder="e.g. BP-2025-8F4K9X"
                                    class="flex-1 rounded-md border-gray-300 focus:ring-[#facc15] focus:border-[#facc15]"
                                />

                                <button
                                    type="submit"
                                    class="px-6 py-2 rounded-md bg-gray-900 text-white font-medium hover:bg-gray-800 transition"
                                >
                                    Track
                                </button>
                            </form>


                            <p class="text-xs text-gray-500 mt-3 italic">
                                You can track your application even without logging in.
                            </p>
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
                        src="/imgs/melindo_tala.jpg"
                        alt="Engr. Melindo B. Tala"
                        class="object-cover w-full h-full"
                    />
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        Engr. Gilvert C. Maglinte
                    </h3>
                    <p class="text-[#facc15] text-lg font-semibold">
                        Department Head
                    </p>
                    <p class="mt-3 text-gray-600 max-w-2xl">
                        Ensuring safety, compliance, and efficient processing of all building permit applications.
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
