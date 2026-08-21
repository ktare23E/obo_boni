<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import CreateButton from '@/Components/CreateButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextArea from '@/Components/TextArea.vue';
import { ref, computed, watch } from 'vue';

const form = useForm({
    title: '',
    description: '',
    file_type_allowed: '',
    requirement_type: '',
    requirement_sample: [],
});



// Dynamically update file input accept attribute based on selected file type
const fileAccept = computed(() => {
    if (form.file_type_allowed === 'pdf') return '.pdf';
    if (form.file_type_allowed === 'docx') return '.docx';
    if (form.file_type_allowed === 'image') return 'image/*';
    return '';
});

const fileInput = ref(null);


const openFilePicker = () => {
    if (!form.file_type_allowed) {
        Swal.fire({
            icon: 'error',
            title: 'Select File Type First',
            text: 'Please choose a File Type Allowed before uploading files.',
        });
        return;
    }

    fileInput.value.click(); // <-- TRIGGER THE FILE INPUT
};

// Update file handler
const handleFileUpload = (e) => {
    form.requirement_sample = Array.from(e.target.files);
};

const create = () => {
    form.post(route('store_requirement'), {
        forceFormData: true, // Required to send file
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Requirement has been stored successfully.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => {
                router.visit(route('requirements'));
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while storing the requirement.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Try Again'
            });
        }
    });
};
</script>

<template>

    <Head title="Create Requirement" />
    <AdminLayout :title="'Create Requirement'">
        <div class="mt-4 w-full flex justify-start">
            <CreateButton :name="'Back'" :href="route('requirements')" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <form @submit.prevent="create" class="w-full grid grid-cols-2 gap-4">

                <!-- Requirement Name -->
                <div class="col-span-2">
                    <InputLabel for="title" value="Requirement Name" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <TextArea v-model="form.description" label="Description" placeholder="Type something..."
                        id="description" />
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>


                <!-- Requirement Type -->
                <div>
                    <InputLabel for="requirement_type" value="Requirement Type" />
                    <select id="requirement_type" v-model="form.requirement_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled>Select Requirement Type</option>
                        <option value="Indigenous">Indigenous</option>
                        <option value="Building Permit">Building Permit</option>
                    </select>
                    <InputError :message="form.errors.requirement_type" class="mt-2" />
                </div>

                <!-- File Type Allowed -->
                <div>
                    <InputLabel for="file_type_allowed" value="File Type Allowed (Optional)" />
                    <select id="file_type_allowed" v-model="form.file_type_allowed"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="" disabled>Select a file type</option>
                        <option value="pdf">PDF</option>
                        <option value="docx">Docx</option>
                        <option value="image">Image</option>
                    </select>
                    <InputError :message="form.errors.file_type_allowed" class="mt-2" />
                </div>


                <!-- Requirement Sample (Multiple File Uploads) -->
                <div class="col-span-2">
                    <InputLabel for="requirement_sample" value="Requirement Sample (Multiple Files Allowed)" />

                    <!-- Hidden file input OUTSIDE the label -->
                    <input type="file" ref="fileInput" id="requirement_sample" class="hidden" multiple
                        :accept="fileAccept" @change="handleFileUpload" />

                    <!-- The clickable upload box -->
                    <div @click="openFilePicker" class="flex items-center justify-center w-full p-4 mt-1 border-2 border-dashed
        border-gray-300 rounded-lg cursor-pointer hover:border-[#0F766E] transition">
                        <span class="text-sm text-gray-600">
                            Click here to upload
                            <span v-if="form.file_type_allowed">
                                {{ form.file_type_allowed.toUpperCase() }} files
                            </span>
                            <span v-else>(Select a file type first)</span>
                        </span>
                    </div>

                    <!-- Display selected files -->
                    <div v-if="form.requirement_sample.length" class="mt-2 space-y-1">
                        <p v-for="(file, index) in form.requirement_sample" :key="index" class="text-sm text-gray-800">
                            • {{ file.name }}
                        </p>
                    </div>

                    <InputError :message="form.errors.requirement_sample" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="col-span-2">
                    <button type="submit"
                        class="py-2 px-3 w-full bg-[#0F766E] text-white rounded-full font-semibold hover:bg-[#0a5d56] transition">
                        Create Requirement
                    </button>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>
