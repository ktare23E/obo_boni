<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';
import { onMounted } from 'vue';
import {Link} from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

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
    <StaffLayout :title="'Inspectors'">
        

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
                    <template #Action="{ row }">
                        <Link :href="route('view_inspector_schedule', row.id)"
                            class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700">
                            view schedules
                        </Link>
                    </template>
                </DynamicTable>
            </div>
        </div>
    </StaffLayout>
</template>
