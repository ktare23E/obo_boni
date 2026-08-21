<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    pendingInspectionCount: String,
    releasedPermitCount: String,
    approvedPermitCount: String,
    totalCount: String,
    recentBusiness: Array
})

// Format date to "October 25, 2025"
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Dynamic color mapping for statuses
const statusColor = (status) => {
    switch (status) {
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'Under Review':
            return 'bg-blue-100 text-blue-800';
        case 'For Inspection':
            return 'bg-orange-100 text-orange-800';
        case 'Approved':
            return 'bg-green-100 text-green-800';
        case 'Rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

</script>

<template>

    <Head title="Dashboard" />
    <AdminLayout :title="'Dashboard'">
        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Applications -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Applications</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ totalCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved Permits -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved Permits</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ approvedPermitCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Inspections -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-hard-hat text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Inspections</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ pendingInspectionCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Released Permits -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <i class="fas fa-stamp text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Released Permits</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ releasedPermitCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Recent Applications -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Applications</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buildings
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Filed
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="data in props.recentBusiness" :key="data.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ data.business_name }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ data.user.first_name + ' ' + data.user.last_name }}
                                </td>

                                <!-- Human readable date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(data.created_at) }}
                                </td>

                                <!-- Dynamic status badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="statusColor(data.status)">
                                        {{ data.status }}
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Application Status Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="border-b border-gray-200 pb-3 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Application Summary</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-700">Pending</span>
                            <span class="text-sm text-gray-700">17%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 17%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-700">Inspected</span>
                            <span class="text-sm text-gray-700">20%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-700">Approved</span>
                            <span class="text-sm text-gray-700">45%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-700">Released</span>
                            <span class="text-sm text-gray-700">18%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 18%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Link :href="route('submissions')"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-2">
                    <i class="fas fa-plus text-lg"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">New Application</span>
                </Link>

                <Link :href="route('admin_inspection')"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-yellow-50 hover:border-yellow-200 transition">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mb-2">
                    <i class="fas fa-hard-hat text-lg"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Schedule Inspection</span>
                </Link>

                <Link :href="route('business_list')"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mb-2">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Approve Permit</span>
                </Link>

                <Link :href="route('permit_list')"
                    class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mb-2">
                    <i class="fas fa-stamp text-lg"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Release Permit</span>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
