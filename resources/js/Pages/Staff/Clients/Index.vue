<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';
import Modal from '@/Components/Modal.vue';
import UserFormFields from '@/Components/UserFormFields.vue';
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

const props = defineProps({
    clients: Array
});


const columns = ['First Name', 'Last Name', 'Address', 'Email', 'Status'];

const rows = computed(() => {
    return props.clients.map(account => ({
        id: account.id,
        'First Name': account.first_name,
        'Last Name': account.last_name,
        'Address': account.address,
        'Email': account.email,

        'Status': account.status,
    }));
});



onMounted(() => {
    setTimeout(() => { $('#myTable').DataTable(); }, 0);
});
</script>

<template>

    <Head title="Clients" />
    <StaffLayout :title="'Clients'">
        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
            
                </DynamicTable>
            </div>
        </div>
    </StaffLayout>
</template>
