<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

const props = defineProps({
    business: Array
});

// Table setup
const columns = ['Building', 'Owner', 'Address', 'Building Type', 'Status', 'Action'];

const rows = computed(() => {
    return props.business.map(data => ({
        id: data.id,
        'Building': data.business_name,
        'Owner': data.user.first_name,
        'Address': data.address,
        'Building Type': data.type_of_business,
        'Status': data.status,
        permit : data.permit,
        Business: data.business_name,

    }));
});

// Modal + form
const showModal = ref(false);
const selectedBusiness = ref(null);

const form = useForm({
    business_id: '',
    release_date: '',
    status: 'pending'
});

// Initialize flatpickr
const initFlatpickr = () => {
    setTimeout(() => {
        flatpickr("#permitDate", {
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: (selectedDates, dateStr) => {
                form.release_date = dateStr;
            }
        });
    }, 100);
};

// Open modal
const openModal = (business) => {
    selectedBusiness.value = business;
    form.business_id = business.id;
    showModal.value = true;
    initFlatpickr();
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    form.reset();
    selectedBusiness.value = null;
};

// Submit form
const submitForm = () => {
    form.post(route('permits.store'), {
        onSuccess: () => {
            closeModal();

            // ✅ SweetAlert success notification
            Swal.fire({
                icon: 'success',
                title: 'Permit Created!',
                text: 'The building permit has been successfully created.',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            }).then(() =>{
                router.visit(route('permit_list'))
            });
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Something went wrong. Please check the form.',
                confirmButtonColor: '#0F766E',
            });
        }
    });
};

onMounted(() => { 
    setTimeout(() => { $('#myTable').DataTable(); }, 0); 
});
</script>

<template>
    <Head title="Building List" />
    <AdminLayout :title="'Building List'">

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" class="text-sm">
                    <template #Action="{ row }">
                        <button 
                            v-if="row.Status === 'Approved' && !row.permit"
                            @click="openModal(row)"
                            class="py-1 px-2 bg-[#0F766E] text-white rounded-sm hover:bg-[#115E59]"
                        >
                            Create Permit
                        </button>
                        <span 
                            v-else 
                            class="text-gray-500 italic text-xs"
                        >
                            -----
                        </span>
                    </template>
                </DynamicTable>
            </div>
        </div>

        <!-- Modal -->
        <div 
            v-if="showModal" 
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            @click.self="closeModal"
        >
            <div class="bg-white p-6 rounded-lg w-[400px] relative">
                <h2 class="text-lg font-semibold mb-4">Create Permit</h2>
                
                <p class="text-sm mb-3">
                    <strong>Building:</strong> {{ selectedBusiness?.Business }}
                </p>

                <label for="permitDate" class="block text-sm mb-1">Select Release Date</label>
                <input 
                    id="permitDate"
                    type="text" 
                    v-model="form.release_date"
                    class="border border-gray-300 rounded-md px-3 py-2 w-full mb-4"
                    placeholder="Select date"
                />
                <div v-if="form.errors.release_date" class="text-red-600 text-sm mb-2">
                    {{ form.errors.release_date }}
                </div>

                <div class="flex justify-end space-x-2">
                    <button 
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="submitForm"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-[#0F766E] text-white rounded hover:bg-[#115E59]"
                    >
                        <span v-if="!form.processing">Create</span>
                        <span v-else>Creating...</span>
                    </button>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>

