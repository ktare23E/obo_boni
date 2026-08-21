<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WelcomeNav from '@/Components/WelcomeNav.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    building: {
        type: Object,
        required: true
    },
    submissions: {
        type: Array,
        required: true
    }
});

// Computed for building image
const imageUrl = computed(() =>
    props.building.image_path ? `/storage/${props.building.image_path}` : 'https://via.placeholder.com/600x400?text=No+Image'
);

// Form for re-upload
const reuploadForms = ref({}); // track each submission form

function handleFileChange(event, submissionId) {
    const file = event.target.files[0];
    if (!file) return;

    if (!reuploadForms.value[submissionId]) {
        reuploadForms.value[submissionId] = useForm({ file: null });
    }

    const form = reuploadForms.value[submissionId];
    form.file = file;

    form.post(route('requirements.reupload', submissionId), {
        preserveScroll: true,
        onSuccess: () => {
            alert('File re-uploaded successfully!');
        },
        onError: () => {
            alert('Failed to re-upload file. Please try again.');
        }
    });
}

function triggerFileInput(id) {
    document.getElementById(`file-input-${id}`).click();
}

// Image preview modal
const showModal = ref(false);
const selectedImage = ref(null);

function openImageModal(src) {
    selectedImage.value = src;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedImage.value = null;
}
</script>
    
<template>

    <Head :title="`${building.business_name} | Building Details`" />

    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Navbar -->
        <WelcomeNav />

        <!-- Header -->
        <section class="relative h-[40vh] flex items-center justify-center bg-orange-700 text-white shadow-lg">
            <div class="text-center px-6">
                <h1 class="text-4xl font-bold uppercase tracking-wide">{{ building.business_name }}</h1>
                <p class="mt-3 text-orange-100 max-w-2xl mx-auto">
                    Detailed information and requirement submissions for this building application.
                </p>
            </div>
        </section>

        <!-- Building Details -->
        <section
            class="max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow-xl mt-[-60px] relative z-10 border-t-4 border-orange-500">
            <!-- 🔙 Back Button -->
            <button
                type="button"
                @click="$inertia.visit(route('buildings.index'))"
                class="flex items-center gap-2 text-gray-700 hover:text-orange-600 mb-6 font-medium transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
            <h2 class="text-2xl font-semibold text-gray-800 border-b-2 border-orange-400 pb-2 mb-6">
                Building Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <img :src="imageUrl"
                        alt="Building Image"
                        class="w-full h-64 object-cover rounded-lg shadow cursor-pointer"
                        @click="openImageModal(imageUrl)"
                        @contextmenu.prevent />
                </div>

                <div class="space-y-3 text-gray-700">
                    <p><span class="font-medium text-gray-900">Building Name:</span> {{ building.business_name }}</p>
                    <p><span class="font-medium text-gray-900">Reference Number:</span> {{ building.registration_no ?? '---' }}</p>
                    <p><span class="font-medium text-gray-900">Address:</span> {{ building.address }}</p>
                    <p><span class="font-medium text-gray-900">Address Description:</span> {{ building.place_description }}</p>
                    <p><span class="font-medium text-gray-900">Type of Building:</span> {{ building.type_of_business }}</p>
                    <p>
                        <span class="font-medium text-gray-900">Progress:</span>
                        <span class="font-medium text-gray-900">{{ building.remarks ?? '--' }}</span>
                    </p>

                    <p>
                        <span class="font-medium text-gray-900">Date of Application:</span>
                        {{ new Date(building.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }) }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Requirement Submissions -->
        <section class="max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow-xl mt-8 border-t-4 border-orange-500">
            <h2 class="text-2xl font-semibold text-gray-800 border-b-2 border-orange-400 pb-2 mb-6">
                Requirement Submissions
            </h2>

            <div v-if="submissions.length">
                <div v-for="(submission, index) in submissions" :key="submission.id"
                    class="p-5 mb-4 border border-orange-100 rounded-lg hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row justify-between md:items-center">
                        <!-- Info -->
                        <div class="flex-1 space-y-1">
                            <p class="font-medium text-gray-900">
                                {{ index + 1 }}. {{ submission.requirement.title }}
                            </p>
                            <p class="text-sm text-gray-600">{{ submission.requirement.description }}</p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Allowed File Type:</span>
                                {{ submission.requirement.file_type_allowed ?
                                    submission.requirement.file_type_allowed.toUpperCase() : 'No Sample' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Requirement Type:</span>
                                {{ submission.requirement.requirement_type }}
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">Status:</span>
                                <span :class="{
                                    'bg-green-100 text-green-700': submission.status === 'approved',
                                    'bg-yellow-100 text-yellow-700': submission.status === 'submitted',
                                    'bg-red-100 text-red-700': submission.status === 'rejected',
                                }" class="px-3 py-1 rounded-full text-xs font-medium ml-2 capitalize">
                                    {{ submission.status }}
                                </span>
                            </p>

                            <!-- Re-upload button for rejected -->
                            <div v-if="submission.status === 'rejected'" class="mt-3">
                                <input type="file" :id="`file-input-${submission.id}`" class="hidden"
                                    @change="e => handleFileChange(e, submission.id)" />
                                <button @click="triggerFileInput(submission.id)"
                                    class="px-4 py-2 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600 transition">
                                    Re-upload Correct File
                                </button>
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <template v-if="submission.requirement.file_type_allowed === 'image'">
                                <img :src="`/storage/${submission.file_path}`"
                                        alt="Requirement Image"
                                        class="w-32 h-32 object-cover rounded-lg shadow cursor-pointer"
                                        @click="openImageModal(`/storage/${submission.file_path}`)"
                                        @contextmenu.prevent />
                            </template>

                            <template v-else>
                                <a :href="`/storage/${submission.file_path}`" target="_blank"
                                    class="px-4 py-2 bg-orange-500 text-white rounded-lg shadow hover:opacity-90 transition">
                                    View File
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="text-center text-gray-500 py-10">
                No requirement submissions available.
            </div>
        </section>

        <!-- Image Modal -->
        <div v-if="showModal"
                class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[9999]"
                @click.self="closeModal">
            <img :src="selectedImage"
                    class="max-h-[85vh] w-auto rounded-xl shadow-2xl select-none"
                    draggable="false"
                    @contextmenu.prevent />
        </div>

        <!-- Footer -->
        <footer
            class="bg-gradient-to-r from-orange-700 via-orange-800 to-orange-900 text-orange-100 py-6 text-center text-sm mt-10">
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
    