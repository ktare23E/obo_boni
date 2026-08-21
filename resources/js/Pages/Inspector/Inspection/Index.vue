<script setup>
import InspectorLayout from '@/Layouts/InspectorLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';

import { onMounted } from 'vue';
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    inspections: Array
});


const columns = ['Building', 'Owner', 'Address', 'Building Type','Inspection Date','Inspection Status','Action'];

const rows = computed(() => {
    return props.inspections.map(data => ({
        id: data.id,
        business_id : data.business.id,
        'Building': data.business.business_name,
        'Owner': data.business.user.first_name + ' ' + data.business.user.last_name,
        'Address': data.business.address,
        'Building Type': data.business.type_of_business,
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
    <InspectorLayout :title="'Inspection List'">

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
                    <template #Action="{ row }">
                        <Link :href="route('ins_requirement_submissions.show',row.business_id)"
                            class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700">
                            view details
                        </Link>
                    </template>
                </DynamicTable>
            </div>
        </div>
    </InspectorLayout>
</template>
