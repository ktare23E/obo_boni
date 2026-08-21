<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';
import Modal from '@/Components/Modal.vue';
import UserFormFields from '@/Components/UserFormFields.vue';
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    clients: Array
});


const columns = ['First Name', 'Last Name', 'Address', 'Email','Contact Number', 'Status', 'Action'];

const rows = computed(() => {
    return props.clients.map(account => ({
        id: account.id,
        'First Name': account.first_name,
        'Last Name': account.last_name,
        'Address': account.address,
        'Email': account.email,
        'Contact Number' : account.contact_number,
        'Status': account.status,
    }));
});



onMounted(() => {
    setTimeout(() => { $('#myTable').DataTable(); }, 0);
});
</script>

<template>

    <Head title="Clients" />
    <AdminLayout :title="'Clients'">
        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
                    <template #Action="{ row }">
                        <Link :href="route('edit_user', row.id)"
                            class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700">
                        edit
                        </Link>
                    </template>
                </DynamicTable>
            </div>
        </div>
    </AdminLayout>
</template>
