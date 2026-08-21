<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import CreateButton from '@/Components/CreateButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

// toggle for password visibility
const showPassword = ref(false);

// Inertia form
const form = useForm({
    first_name: '',
    last_name: '',
    address: '',
    email: '',
    password: '',
    user_type: 'Inspector',
    status: 'Active'
});

const create = () => {
    form.post(route('store_user'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'User has been created successfully.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => {
                router.visit('inspector')
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while creating the user.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Try Again'
            });
        }
    });
};
</script>

<template>
    <Head title="Create Inspector" />
    <AdminLayout :title="'Create Inspector'">
        <div class="mt-4 w-full flex justify-start">
            <CreateButton :name="'Back'" :href="route('inspector')" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <form @submit.prevent="create" class="w-full grid grid-cols-1 gap-2">
                <!-- First Name -->
                <div class="col-span-2">
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
                <div class="col-span-2">
                    <InputLabel for="address" value="Address" />
                    <TextInput id="address" type="text" class="mt-1 block w-full"
                        v-model="form.address" required autocomplete="street-address" />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <!-- Email -->
                <div class="col-span-2">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full"
                        v-model="form.email" required autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div class="col-span-2">
                    <InputLabel for="password" value="Password" />
                    <div class="relative">
                        <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                            class="mt-1 block w-full pr-10" v-model="form.password"
                            autocomplete="new-password" />
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                            @click="showPassword = !showPassword">
                            <span v-if="showPassword">🙈</span>
                            <span v-else>👁️</span>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- User Type -->
                <!-- <div>
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
                </div> -->

                <!-- Submit Button -->
                <div class="col-span-2">
                    <button type="submit"
                        class="py-1 px-3 w-full bg-[#0F766E] text-white rounded-full cursor-pointer">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
