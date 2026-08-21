<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';

const props = defineProps({
    requirements: Array
});

// ============ VIEW SAMPLES MODAL ============
const showSamplesModal = ref(false);
const modalSamples = ref([]);

const openSamples = (requirement) => {
    modalSamples.value = requirement.samples;
    showSamplesModal.value = true;
};

// ============ CHANGE SAMPLE MODAL ============
const showChangeModal = ref(false);
const selectedSample = ref(null);
const selectedFile = ref(null);

// Strict file type check for re-upload
const fileAccept = computed(() => {
    if (!selectedSample.value) return '';

    const type = selectedSample.value.file_type_allowed?.toLowerCase();

    if (type === 'pdf') return '.pdf';
    if (type === 'doc') return '.doc';
    if (type === 'docx') return '.doc,.docx';
    if (type === 'image') return 'image/*';
    return '';
});

const openChangeSample = (sample) => {
    selectedSample.value = sample;
    selectedFile.value = null;
    showChangeModal.value = true;
};


const uploadSample = () => {
    if (!selectedFile.value) {
        return Swal.fire({
            icon: 'warning',
            title: 'No File Selected',
            text: 'Please select a file to upload.'
        });
    }

    const allowedType = selectedSample.value.file_type_allowed?.toLowerCase();
    const fileExt = selectedFile.value.name.split('.').pop().toLowerCase();

    if (allowedType === 'image' && !['png', 'jpg', 'jpeg'].includes(fileExt)) {
        return Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'You must upload an image file (png, jpg, jpeg).'
        });
    }
    if (allowedType === 'pdf' && fileExt !== 'pdf') {
        return Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'You must upload a PDF file.'
        });
    }
    if (allowedType === 'doc' && fileExt !== 'doc') {
        return Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'You must upload a DOC file.'
        });
    }
    if (allowedType === 'docx' && fileExt !== 'docx') {
        return Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'You must upload a DOCX file.'
        });
    }

    const formData = new FormData();
    formData.append('requirement_sample', selectedFile.value);

    router.post(
        route('update_requirement_sample', selectedSample.value.id),
        formData,
        {
            forceFormData: true,
            onSuccess: () => {
                showChangeModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Uploaded!',
                    text: 'Requirement sample re-uploaded successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }
    );
};


// ============ FULL IMAGE PREVIEW ============
const showPreview = ref(false);
const previewImage = ref('');
const zoomed = ref(false);

const openPreview = (imagePath) => {
    previewImage.value = '/storage/' + imagePath;
    showPreview.value = true;
    zoomed.value = false;
};

const toggleZoom = () => {
    zoomed.value = !zoomed.value;
};

// ============ TABLE ROWS ============
const rows = computed(() => {
    return props.requirements.map(r => ({
        id: r.id,
        title: r.title,
        description: r.description,
        requirement_type: r.requirement_type,
        file_type_allowed: r.file_type_allowed,
        samples: r.samples
    }));
});

onMounted(() => {
    setTimeout(() => $('#requirementsTable').DataTable(), 250);
});
</script>

<template>

    <Head title="Requirements" />
    <AdminLayout :title="'Requirements'">

        <div class="mt-4 w-full flex justify-end">
            <CreateButton :name="'Create Requirements'" :href="route('create_requirements')" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-4 overflow-x-auto">
            <table id="requirementsTable" class="w-full text-sm">
                <thead class="bg-gray-100 text-center">
                    <tr>
                        <th class="p-3">Title</th>
                        <th class="p-3">Description</th>
                        <th class="p-3">Requirement Type</th>
                        <th class="p-3">File Type</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in rows" :key="row.id" class="border-t">
                        <td class="p-3">{{ row.title }}</td>
                        <td class="p-3">{{ row.description }}</td>
                        <td class="p-3">{{ row.requirement_type }}</td>
                        <td class="p-3 uppercase text-xs">{{ row.file_type_allowed }}</td>
                        <td class="p-3 flex gap-2">
                            <Link :href="route('edit_requirement', row.id)"
                                class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-xs">
                            edit
                            </Link>
                            <button class="bg-orange-500 text-white px-3 py-1 rounded-sm text-xs"
                                @click="openSamples(row)">
                                view samples
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- VIEW SAMPLES MODAL -->
        <transition name="modal-fade">
            <div v-if="showSamplesModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50"
                @click.self="showSamplesModal = false">
                <div class="bg-white p-6 rounded-xl shadow-xl w-[600px] max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4 text-center">Requirement Samples</h2>
                    <div v-if="modalSamples.length" class="space-y-4">
                        <div v-for="s in modalSamples" :key="s.id" class="border p-3 rounded-lg">
                            <!-- IMAGE -->
                            <div v-if="s.requirement_sample.endsWith('.png') || s.requirement_sample.endsWith('.jpg') || s.requirement_sample.endsWith('.jpeg')"
                                @click="openPreview(s.requirement_sample)" class="cursor-pointer">
                                <img :src="'/storage/' + s.requirement_sample"
                                    class="w-full rounded border object-contain max-h-60 hover:scale-105 transition-transform duration-200" />
                            </div>
                            <!-- PDF -->
                            <div v-else-if="s.requirement_sample.endsWith('.pdf')" class="text-center">
                                <a :href="'/storage/' + s.requirement_sample" target="_blank"
                                    class="text-blue-600 underline">
                                    View PDF
                                </a>
                            </div>
                            <!-- DOCX -->
                            <div v-else class="text-center">
                                <a :href="'/storage/' + s.requirement_sample" target="_blank"
                                    class="text-blue-600 underline">
                                    Open File
                                </a>
                            </div>
                            <button class="mt-3 bg-yellow-500 text-white px-3 py-1 rounded-sm text-xs"
                                @click="openChangeSample(s)">
                                Change Sample
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-6">
                        No samples available.
                    </div>
                    <div class="text-center mt-4">
                        <button class="px-6 py-2 bg-gray-700 text-white rounded hover:bg-gray-800"
                            @click="showSamplesModal = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- FULL IMAGE PREVIEW MODAL WITH ZOOM -->
        <transition name="modal-fade">
            <div v-if="showPreview" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50"
                @click.self="showPreview = false">
                <div class="bg-white p-6 rounded-xl shadow-xl max-w-3xl w-full flex flex-col items-center">
                    <img :src="previewImage" :class="zoomed ? 'scale-150 cursor-zoom-out' : 'scale-100 cursor-zoom-in'"
                        class="w-full max-h-[80vh] object-contain rounded transition-transform duration-300"
                        @click="toggleZoom" />
                    <div class="text-right mt-4 w-full flex justify-center">
                        <button class="px-6 py-2 bg-gray-700 text-white rounded hover:bg-gray-800"
                            @click="showPreview = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- CHANGE SAMPLE MODAL -->
        <transition name="modal-fade">
            <div v-if="showChangeModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50"
                @click.self="showChangeModal = false">
                <div class="bg-white p-6 rounded-xl shadow-xl w-[420px]">
                    <h2 class="text-lg font-semibold mb-4">Change Requirement Sample</h2>
                    <input type="file" :accept="fileAccept" @change="selectedFile = $event.target.files[0]"
                        class="w-full border rounded p-2 mb-4" />
                    <div class="flex justify-end gap-3">
                        <button class="px-5 py-2 rounded bg-gray-600 text-white hover:bg-gray-700"
                            @click="showChangeModal = false">
                            Cancel
                        </button>
                        <button class="px-5 py-2 rounded bg-[#0F766E] text-white hover:bg-[#0a5d56]"
                            @click="uploadSample">
                            Upload
                        </button>
                    </div>
                </div>
            </div>
        </transition>

    </AdminLayout>
</template>

<style>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
