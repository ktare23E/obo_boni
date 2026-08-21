<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed } from 'vue';
import CreateButton from '@/Components/CreateButton.vue';
import Modal from '@/Components/Modal.vue';
import UserFormFields from '@/Components/UserFormFields.vue';
import { onMounted } from 'vue';
import {Link} from '@inertiajs/vue3';

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
    <StaffLayout :title="'Requirement Submissions'">

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" :class="'text-sm'">
                    <template #Action="{ row }">
                        <Link
                        :href="route('staff_requirement_submissions', row.id)"
                        class="bg-[#0F766E] text-white px-3 py-1 rounded-sm text-sm hover:bg-blue-700">
                        view submissions
                    </Link>
                    </template>
                </DynamicTable>
            </div>
        </div>
    </StaffLayout>
</template>
