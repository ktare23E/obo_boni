<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import CreateButton from '@/Components/CreateButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    user : Object
})

// Inertia form
const form = useForm({
    id : props.user.id,
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    address: props.user.address,
    email: props.user.email,
    user_type: props.user.user_type,
    status: props.user.status,
});

const update = () => {
    form.put(route('update_user', form.id), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'User has been updated successfully.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => {
                router.visit(route('users'));
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while updating the user.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Try Again'
            });
        }
    });
};

</script>

<template>
    <Head title="Edit User" />
    <AdminLayout :title="'Edit User'">
        <div class="mt-4 w-full flex justify-start">
            <CreateButton :name="'Back'" :href="route('users')" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <form @submit.prevent="update" class="w-full grid grid-cols-2 gap-2">
                <!-- First Name -->
                <div>
                    <InputLabel for="first_name" value="First Name" />
                    <TextInput id="first_name" type="text" class="mt-1 block w-full"
                        v-model="form.first_name" required autocomplete="given-name" />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>

                <!-- Last Name -->
                <div>
                    <InputLabel for="last_name" value="Last Name" />
                    <TextInput id="last_name" type="text" class="mt-1 block w-full"
                        v-model="form.last_name" required autocomplete="family-name" />
                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>

                <!-- Address -->
                <div>
                    <InputLabel for="address" value="Address" />
                    <TextInput id="address" type="text" class="mt-1 block w-full"
                        v-model="form.address" required autocomplete="street-address" />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full"
                        v-model="form.email" required autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- User Type -->
                <div>
                    <InputLabel for="user_type" value="User Type" />
                    <select id="user_type" v-model="form.user_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled>Select a role</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                        <option value="Inspector">Inspector</option>
                        <option value="Client">Client</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.user_type" />
                </div>

                <!-- User Type -->
                <div>
                    <InputLabel for="user_type" value="Status" />
                    <select id="status" v-model="form.status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled>Select a role</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div>

                <!-- Submit Button -->
                <div class="col-span-2">
                    <button type="submit"
                        class="py-1 px-3 w-full bg-[#0F766E] text-white rounded-full cursor-pointer">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
