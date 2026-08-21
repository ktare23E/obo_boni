<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';
import { onMounted } from 'vue';
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    inspector: Array
});


const columns = ['First Name', 'Last Name', 'Address', 'Email', 'Status', 'Action'];

const rows = computed(() => {
    return props.inspector.map(account => ({
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
    <Head title="Inspectors" />
    <AdminLayout :title="'Inspectors'">
        <div class="mt-4 w-full flex justify-end">
            <CreateButton :name="'Create Inspector'" :href="route('create_inspector')">
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
                        <Link :href="route('edit_inspector', row.id)"
                            class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700 mr-2">
                        edit
                        </Link>
                        <Link :href="route('admin_view_inspector_schedule',row.id)"
                            class="bg-black text-white px-3 py-1 rounded-sm text-sm">
                            view schedules
                        </Link>
                    </template>
                </DynamicTable>
            </div>
        </div>
    </AdminLayout>
</template>
