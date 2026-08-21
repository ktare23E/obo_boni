<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import DynamicTable from '@/Components/DynamicTable.vue';
import { ref, computed, onMounted } from 'vue';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    permits: Array
});

const columns = ['Building', 'Owner', 'Permit Release Date', 'Status','Action'];

const rows = computed(() => {
    return props.permits.map(data => ({
        id: data.id,
        business_id : data.business.id,
        'Building': data.business.business_name,
        'Owner': data.business.user.first_name + ' ' + data.business.user.last_name,
        'Permit Release Date': new Date(data.release_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }),
        raw_status: data.status, // <-- add this
        'Status': data.status.charAt(0).toUpperCase() + data.status.slice(1),
    }));
});

// Handle Release
// const releasePermit = (id) => {
//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You are about to mark this permit as released.",
//         icon: 'question',
//         showCancelButton: true,
//         confirmButtonColor: '#0F766E',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, release it!'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             router.post(`/admin/permits/${id}/release`, {}, {
//                 onSuccess: () => {
//                     Swal.fire(
//                         'Released!',
//                         'The permit has been marked as released.',
//                         'success'
//                     );
//                 },
//                 onError: () => {
//                     Swal.fire(
//                         'Error!',
//                         'Something went wrong while releasing the permit.',
//                         'error'
//                     );
//                 }
//             });
//         }
//     });
// };

onMounted(() => { 
    setTimeout(() => { $('#myTable').DataTable(); }, 0); 
});
</script>

<template>
    <Head title="Building Permit List" />
    <AdminLayout :title="'Building Permit List'">
        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center mt-4">
            <div class="w-full">
                <DynamicTable :columns="columns" :rows="rows" class="text-sm">
                    <template #Action="{ row }">
                        <!-- <button
                            @click="releasePermit(row.id)" v-if="row.raw_status == 'ready'"
                            class="bg-[#0F766E] hover:bg-[#115e57] text-white px-3 py-1 rounded-sm text-sm">
                            release
                        </button> -->
                        <!-- v-if="row.raw_status == 'ready'" -->
                        <Link
                        :href="route('new_report', { permit: row.id })"
                       
                            class="bg-[#0F766E] hover:bg-[#115e57] text-white px-3 py-1 rounded-sm text-sm"
                        >
                        release
                        </Link>
                        <!-- <span v-else>
                            -----
                        </span> -->
                    </template>
                </DynamicTable>
            </div>
        </div>
    </AdminLayout>
</template>
