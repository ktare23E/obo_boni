<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props from controller
const props = defineProps({
    user: Object
});

// Reactive states
const editing = ref(false);
const showPasswordModal = ref(false);
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);


// Profile form
const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email || '',
    contact_number: props.user.contact_number || '',
    address: props.user.address || '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

// Profile functions
const startEditing = () => editing.value = true;

const cancelEditing = () => {
    editing.value = false;
    form.reset();
};

const saveProfile = () => {
    form.put(route('staff.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your information has been successfully updated!',
                confirmButtonColor: '#f97316',
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'Please check your inputs.',
                confirmButtonColor: '#f97316',
            });
        }
    });
};

// Password modal functions
const openPasswordModal = () => showPasswordModal.value = true;
const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordForm.reset();
};
const updatePassword = () => {
    passwordForm.put(route('staff.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            closePasswordModal();
            Swal.fire({
                icon: 'success',
                title: 'Password Updated',
                text: 'Your password has been changed successfully!',
                confirmButtonColor: '#f97316',
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Password Update Failed',
                text: 'Please check your inputs.',
                confirmButtonColor: '#f97316',
            });
        }
    });
};
</script>

<template>

    <Head title="Profile" />
    <StaffLayout :title="'Profile'">

        <div class="bg-white p-6 rounded-lg shadow-lg max-w-3xl mx-auto mt-4">

            <!-- View Profile -->
            <div v-if="!editing">
                <h2 class="text-2xl font-bold text-orange-600 mb-4">Profile Information</h2>
                <div class="space-y-2 text-gray-700">
                    <p><strong>First Name:</strong> {{ form.first_name }}</p>
                    <p><strong>Last Name:</strong> {{ form.last_name }}</p>
                    <p><strong>Email:</strong> {{ form.email }}</p>
                    <!-- <p><strong>Phone:</strong> {{ form.contact_number || 'N/A' }}</p> -->
                    <p><strong>Address:</strong> {{ form.address || 'N/A' }}</p>
                </div>
                <div class="mt-6 flex space-x-4">
                    <button @click="startEditing" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Edit Profile
                    </button>
                    <button @click="openPasswordModal"
                        class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                        Change Password
                    </button>
                </div>
            </div>

            <!-- Edit Profile -->
            <div v-else>
                <h2 class="text-2xl font-bold text-orange-600 mb-4">Edit Profile</h2>
                <form @submit.prevent="saveProfile" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">First Name</label>
                            <input v-model="form.first_name" type="text" class="w-full border px-3 py-2 rounded" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{ form.errors.first_name
                                }}</span>
                        </div>
                        <div>
                            <label class="block font-medium">Last Name</label>
                            <input v-model="form.last_name" type="text" class="w-full border px-3 py-2 rounded" />
                            <span v-if="form.errors.last_name" class="text-red-500 text-sm">{{ form.errors.last_name
                                }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium">Email</label>
                        <input v-model="form.email" type="email" class="w-full border px-3 py-2 rounded" />
                        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                    </div>

                    <!-- <div>
                        <label class="block font-medium">Phone</label>
                        <input v-model="form.contact_number" type="text" class="w-full border px-3 py-2 rounded" />
                    </div >-->

                    <div>
                        <label class="block font-medium">Address</label>
                        <input v-model="form.address" type="text" class="w-full border px-3 py-2 rounded" />
                    </div> 

                    <div class="flex space-x-4 mt-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Save
                        </button>
                        <button type="button" @click="cancelEditing"
                            class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Modal -->
        <!-- Password Modal -->
        <div v-if="showPasswordModal"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h2 class="text-xl font-bold mb-4 text-orange-600 text-center">Change Password</h2>
                <form @submit.prevent="updatePassword" class="space-y-4">

                    <!-- Current Password -->
                    <div>
                        <label class="block font-medium">Current Password</label>
                        <div class="relative">
                            <input :type="showCurrentPassword ? 'text' : 'password'"
                                v-model="passwordForm.current_password" class="w-full border px-3 py-2 rounded" />
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute right-2 top-2 text-gray-500">
                                <svg v-if="showCurrentPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3l18 18M9.879 9.879a3 3 0 0 0 4.242 4.242M12 12l9 9m-9-9-9 9" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.5c-7.5 0-12 7.5-12 7.5s4.5 7.5 12 7.5 12-7.5 12-7.5-4.5-7.5-12-7.5zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                            </button>
                        </div>
                        <span v-if="passwordForm.errors.current_password" class="text-red-500 text-sm">{{
                            passwordForm.errors.current_password }}</span>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block font-medium">New Password</label>
                        <div class="relative">
                            <input :type="showNewPassword ? 'text' : 'password'" v-model="passwordForm.new_password"
                                class="w-full border px-3 py-2 rounded" />
                            <button type="button" @click="showNewPassword = !showNewPassword"
                                class="absolute right-2 top-2 text-gray-500">
                                <svg v-if="showNewPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3l18 18M9.879 9.879a3 3 0 0 0 4.242 4.242M12 12l9 9m-9-9-9 9" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.5c-7.5 0-12 7.5-12 7.5s4.5 7.5 12 7.5 12-7.5 12-7.5-4.5-7.5-12-7.5zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                            </button>
                        </div>
                        <span v-if="passwordForm.errors.new_password" class="text-red-500 text-sm">{{
                            passwordForm.errors.new_password }}</span>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block font-medium">Confirm New Password</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'"
                                v-model="passwordForm.new_password_confirmation"
                                class="w-full border px-3 py-2 rounded" />
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-2 top-2 text-gray-500">
                                <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3l18 18M9.879 9.879a3 3 0 0 0 4.242 4.242M12 12l9 9m-9-9-9 9" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.5c-7.5 0-12 7.5-12 7.5s4.5 7.5 12 7.5 12-7.5 12-7.5-4.5-7.5-12-7.5zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" @click="closePasswordModal"
                            class="bg-gray-400 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </StaffLayout>
</template>
