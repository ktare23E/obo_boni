<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WelcomeNav from '@/Components/WelcomeNav.vue';
import { ref, computed } from 'vue';

// Props
const props = defineProps({
    buildings: {
        type: Array,
        required: true
    }
});

// Reactive search
const searchQuery = ref('');

// Computed filtered list (reactive to search)
const filteredBuildings = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.buildings;

    return props.buildings.filter(b =>
        (b.business_name || '').toLowerCase().includes(query)
    );
});

</script>

<template>

    <Head title="My Buildings" />

    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Hero Section -->
        <section class="relative h-[55vh] md:h-[60vh] w-full">
            <div
                class="h-full bg-[url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center">

                <!-- Dark Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black/40"></div>

                <!-- Hero Text -->
                <div class="relative h-full flex flex-col justify-center px-6 md:pl-[140px] text-white">
                    <div class="space-y-3 animate-fadeInUp max-w-2xl text-center md:text-left">
                        <h2 class="text-4xl md:text-5xl font-bold uppercase text-[#facc15] drop-shadow-lg">
                            My Buildings
                        </h2>

                        <p class="text-base md:text-lg text-gray-200 leading-relaxed">
                            View and manage your registered buildings, track application statuses, or apply for a
                            building permit with ease.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Buildings Section -->
        <section class="max-w-7xl mx-auto px-6 py-12">

            <!-- Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-end mb-10 gap-4">


                <!-- Add Button -->
                <Link :href="route('create_building')"
                    class="flex items-center gap-2 px-6 py-3.5 bg-orange-500 text-white font-semibold rounded-xl shadow-md hover:bg-orange-600 active:scale-95 transition-all whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 font-bold" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Building
                </Link>
            </div>

            <!-- Building Cards -->
            <div v-if="filteredBuildings.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                <Link v-for="building in filteredBuildings" :key="building.id"
                    :href="route('show_building', building.id)"
                    class="group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-xl hover:border-gray-300 transition-all duration-300">
                <!-- Image -->
                <div class="relative h-52 overflow-hidden">
                    <img :src="`/storage/${building.image_path}`" alt="Building Image"
                        class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500" />

                    <!-- Soft Gradient Overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-50 transition-opacity duration-300">
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-gray-900 transition-colors">
                        {{ building.business_name }}
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ building.location }}
                    </p>

                    <!-- Footer row -->
                    <div class="flex items-center justify-between pt-3">

                        <!-- Status Badge -->
                        <span :class="{
                            'bg-yellow-50 text-yellow-700 border border-yellow-200': building.status === 'Pending',
                            'bg-blue-50 text-blue-700 border border-blue-200': building.status === 'Under Review',
                            'bg-indigo-50 text-indigo-700 border border-indigo-200': building.status === 'For Inspection',
                            'bg-green-50 text-green-700 border border-green-200': building.status === 'Approved',
                            'bg-red-50 text-red-700 border border-red-200': building.status === 'Rejected',
                        }" class="px-3 py-1 text-xs font-semibold rounded-full">
                            {{ building.status }}
                        </span>

                        <!-- View link -->
                        <span
                            class="text-yellow-600 hover:text-yellow-700 text-sm font-semibold transition-all flex items-center gap-1">
                            View Details
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </span>
                    </div>
                </div>
                </Link>
            </div>


            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 py-10 text-lg">
                No buildings found.
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-6 text-center text-sm mt-auto">
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
