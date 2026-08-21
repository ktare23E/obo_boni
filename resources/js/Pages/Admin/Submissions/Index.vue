<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { computed, onMounted } from 'vue';

const props = defineProps({
    businesses: Array
});

const columns = ['Building', 'Owner', 'Status', 'Action'];


const rows = computed(() => {
    return props.businesses.map(business => ({
        id: business.id,
        'Building': business.business_name,
        'Owner': business.user 
            ? `${business.user.first_name} ${business.user.last_name}` 
            : 'N/A',
        'Status': business.status,
    }));
});

onMounted(() => { 
    setTimeout(() => { $('#myTable').DataTable(); }, 0); 
});
</script>

<template>
    <Head title="Requirement Submissions" />
    <AdminLayout :title="'Requirement Submissions'">

        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <DynamicTable :columns="columns" :rows="rows" class="text-sm">
                <template #Action="{ row }">
                    <Link
                        :href="route('requirement_submissions.show', row.id)"
                        class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700">
                        view submissions
                    </Link>
                </template>
            </DynamicTable>
        </div>

    </AdminLayout>
</template>
