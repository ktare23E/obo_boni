<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import StaffLayout from '@/Layouts/StaffLayout.vue';

import { onMounted } from 'vue';
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    inspections: Array
});


const columns = ['Building', 'Owner', 'Address', 'Inspector','Inspection Date','Inspection Status'];

const rows = computed(() => {
    return props.inspections.map(data => ({
        id: data.id,
        'Building': data.business.business_name,
        'Owner': data.business.user.first_name + ' ' + data.business.user.last_name,
        'Address': data.business.address,
        'Inspector': data.user.first_name + ' ' + data.user.last_name,
        'Inspection Date': new Date(data.schedule.available_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }),
        'Inspection Status': data.status,
    }));
});



onMounted(() => { 
    setTimeout(() => { $('#myTable').DataTable(); }, 0); 
});
</script>

<template>
    <Head title="Inspection List" />
    <StaffLayout :title="'Inspection List'">

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
                   
                </DynamicTable>
            </div>
        </div>
    </StaffLayout>
</template>
