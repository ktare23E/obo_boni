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
    users: Array
});


const columns = ['First Name', 'Last Name', 'Address', 'Email','User Type', 'Status', 'Action'];

const rows = computed(() => {
    return props.users.map(account => ({
        id: account.id,
        'First Name': account.first_name,
        'Last Name': account.last_name,
        'Address': account.address,
        'Email': account.email,
        'User Type': account.user_type,
        'Status': account.status,
    }));
});



onMounted(() => {
    setTimeout(() => { $('#myTable').DataTable(); }, 0);
});
</script>

<template>

    <Head title="Staffs" />
    <AdminLayout :title="'Staffs'">
        <div class="mt-4 w-full flex justify-end">
            <CreateButton :name="'Create Staff'" :href="route('create_user')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </CreateButton>
        </div>

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
