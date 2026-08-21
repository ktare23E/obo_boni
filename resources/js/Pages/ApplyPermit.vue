<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    building_id: '',
    description: '',
    documents: null
});

const submit = () => {
    form.post(route('permits.store'));
};
</script>

<template>
    <Head title="Apply for Building Permit" />
    <div class="min-h-screen bg-gray-50 py-10 px-10">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Apply for Building Permit</h1>

        <form @submit.prevent="submit" class="bg-white p-8 rounded-xl shadow-md max-w-2xl">
            <div class="mb-5">
                <label class="block font-medium text-gray-700 mb-2">Select Building</label>
                <select v-model="form.building_id" class="w-full border rounded-md px-3 py-2">
                    <option value="">-- Select --</option>
                    <option v-for="i in 3" :key="i" :value="i">Building {{ i }}</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block font-medium text-gray-700 mb-2">Project Description</label>
                <textarea
                    v-model="form.description"
                    rows="4"
                    class="w-full border rounded-md px-3 py-2"
                    placeholder="Brief description of construction..."
                ></textarea>
            </div>

            <div class="mb-5">
                <label class="block font-medium text-gray-700 mb-2">Upload Required Documents</label>
                <input type="file" multiple @change="form.documents = $event.target.files" class="w-full border rounded-md px-3 py-2" />
            </div>

            <button
                type="submit"
                class="bg-[#facc15] px-6 py-2 rounded-md text-black font-medium hover:bg-yellow-400 transition">
                Submit Application
            </button>
        </form>
    </div>
</template>
