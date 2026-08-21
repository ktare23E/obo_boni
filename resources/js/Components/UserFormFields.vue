<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { toRefs } from 'vue';

const props = defineProps({
    form: Object,
    showPassword: Boolean,
    isEdit: { type: Boolean, default: false } // 👈 new prop
});
const emit = defineEmits(['update:form', 'update:showPassword']);

const { form, showPassword, isEdit } = toRefs(props);
</script>

<template>
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

    <!-- Password (only in create mode) -->
    <div v-if="!isEdit">
        <InputLabel for="password" value="Password" />
        <div class="relative">
            <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                       class="mt-1 block w-full pr-10"
                       v-model="form.password" autocomplete="new-password" />
            <button type="button"
                    class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                    @click="$emit('update:showPassword', !showPassword)">
                <span v-if="showPassword">🙈</span>
                <span v-else>👁️</span>
            </button>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />
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
</template>
