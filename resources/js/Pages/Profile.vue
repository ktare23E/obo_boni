<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WelcomeNav from '@/Components/WelcomeNav.vue';
import { ref } from 'vue';

// Props from backend (authenticated client user)
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

// Reactive states
const editing = ref(false);
const showPasswordModal = ref(false);

const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

// Profile form (split names)
const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email,
    phone: props.user.contact_number || '',
    address: props.user.address || '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

// Profile edit functions
const startEditing = () => (editing.value = true);

const cancelEditing = () => {
    editing.value = false;
    form.reset();
};

const saveChanges = () => {
    form.put(route('client.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile information has been successfully updated!',
                confirmButtonColor: '#f97316',
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'Please check your inputs and try again.',
                confirmButtonColor: '#f97316',
            });
        },
    });
};

// Password modal functions
const openPasswordModal = () => (showPasswordModal.value = true);

const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordForm.reset();
};

const updatePassword = () => {
    passwordForm.put(route('client.password.update'), {
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
                text: 'Please check your inputs and try again.',
                confirmButtonColor: '#f97316',
            });
        },
    });
};
</script>

<template>

    <Head title="Profile" />
    <WelcomeNav />

    <!-- Background container -->
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full bg-white shadow-lg rounded-lg p-8">

            <!-- Profile Info -->
            <div v-if="!editing" class="text-center">
                <h2 class="text-3xl font-bold mb-6 text-orange-600">Profile Information</h2>
                <div class="space-y-2 text-gray-700">
                    <p><strong>First Name:</strong> {{ form.first_name }}</p>
                    <p><strong>Last Name:</strong> {{ form.last_name }}</p>
                    <p><strong>Email:</strong> {{ form.email }}</p>
                    <p><strong>Phone:</strong> {{ form.phone }}</p>
                    <p><strong>Address:</strong> {{ form.address }}</p>
                </div>

                <div class="mt-8 flex justify-center space-x-4">
                    <button @click="startEditing"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg shadow transition">
                        Edit Profile
                    </button>
                    <button @click="openPasswordModal"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg shadow transition">
                        Change Password
                    </button>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div v-else>
                <h2 class="text-3xl font-bold mb-6 text-center text-orange-600">Edit Profile</h2>
                <form @submit.prevent="saveChanges" class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700">First Name</label>
                            <input v-model="form.first_name" type="text"
                                class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{ form.errors.first_name
                            }}</span>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Last Name</label>
                            <input v-model="form.last_name" type="text"
                                class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
                            <span v-if="form.errors.last_name" class="text-red-500 text-sm">{{ form.errors.last_name
                            }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
                        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700">Phone</label>
                        <input v-model="form.phone" type="text"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700">Address</label>
                        <input v-model="form.address" type="text"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
                    </div>

                    <div class="flex justify-center space-x-4 mt-6">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">
                            Save
                        </button>
                        <button type="button" @click="cancelEditing"
                            class="bg-gray-400 hover:bg-gray-500 text-white px-5 py-2 rounded-lg shadow transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div v-if="showPasswordModal" @click.self="closePasswordModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4 text-orange-600 text-center">Change Password</h2>
            <form @submit.prevent="updatePassword" class="space-y-4">
                <div>
                    <label class="block font-medium">Current Password</label>
                    <div class="relative">
                        <input :type="showCurrent ? 'text' : 'password'" v-model="passwordForm.current_password"
                            class="w-full border rounded px-3 py-2 pr-10" />
                        <button type="button" @click="showCurrent = !showCurrent"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                            <i :class="showCurrent ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <span v-if="passwordForm.errors.current_password" class="text-red-500 text-sm">
                        {{ passwordForm.errors.current_password }}
                    </span>
                </div>

                <div>
                    <label class="block font-medium">New Password</label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'" v-model="passwordForm.new_password"
                            class="w-full border rounded px-3 py-2 pr-10" />
                        <button type="button" @click="showNew = !showNew"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                            <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <span v-if="passwordForm.errors.new_password" class="text-red-500 text-sm">
                        {{ passwordForm.errors.new_password }}
                    </span>
                </div>

                <div>
                    <label class="block font-medium">Confirm New Password</label>
                    <div class="relative">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            v-model="passwordForm.new_password_confirmation"
                            class="w-full border rounded px-3 py-2 pr-10"
                        />
                        <button
                            type="button"
                            @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700"
                        >
                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>


                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" @click="closePasswordModal"
                        class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</template>
