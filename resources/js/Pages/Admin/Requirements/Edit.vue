<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import CreateButton from '@/Components/CreateButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextArea from '@/Components/TextArea.vue';
import Modal from '@/Components/Modal.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    requirement: Object
});

// UseForm with all fields, including optional file
const form = useForm({
    id: props.requirement.id,
    title: props.requirement.title,
    description: props.requirement.description,
    file_type_allowed: props.requirement.file_type_allowed,
    requirement_type: props.requirement.requirement_type,
});


// Update requirement
const update = () => {
    form.put(route('update_requirement', form.id), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Requirement updated successfully.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => router.visit(route('requirements')));
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while updating the requirement.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Try Again'
            });
        }
    });
};
</script>

<template>
    <Head title="Edit Requirement" />
    <AdminLayout :title="'Edit Requirement'">
        <div class="mt-4 w-full flex justify-start">
            <CreateButton :name="'Back'" :href="route('requirements')" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <form @submit.prevent="update" class="w-full grid grid-cols-2 gap-4">

                <!-- Requirement Name -->
                <div class="col-span-2">
                    <InputLabel for="title" value="Requirement Name" />
                    <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <TextArea id="description" v-model="form.description" label="Description" />
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
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled>Select a file type</option>
                        <option value="pdf">PDF</option>
                        <option value="docx">Docx</option>
                        <option value="image">Image</option>
                    </select>
                    <InputError :message="form.errors.file_type_allowed" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="col-span-2">
                    <button type="submit"
                        class="py-2 px-3 w-full bg-[#0F766E] text-white rounded-full font-semibold hover:bg-[#0a5d56] transition">
                        Update Requirement
                    </button>
                </div>

            </form>
        </div>

        <!-- Image Preview Modal -->
        <Modal :show="showPreview" @close="showPreview = false">
            <div class="p-5 flex flex-col items-center">
                <img :src="previewImage" class="max-h-[75vh] rounded shadow-md border mb-4 object-contain" />
                <button @click="showPreview = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Close
                </button>
            </div>
        </Modal>
    </AdminLayout>
</template>
