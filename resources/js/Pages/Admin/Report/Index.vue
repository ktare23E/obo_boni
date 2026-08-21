<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import flatpickr from "flatpickr";
import "flatpickr/dist/themes/airbnb.css";
import { ref, onMounted } from "vue";

const dateRange = ref("");
const status = ref("");
const business_type = ref("");

// Apply filters (optional preview)
const applyFilters = () => {
    router.get(route("reports"), {
        date: dateRange.value,
        status: status.value,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Generate PDF in a new tab
const generateReport = () => {
    const params = new URLSearchParams({
        date: dateRange.value,
        status: status.value,
        business_type: business_type.value,
    }).toString();

    window.open(route("reports.generate") + "?" + params, "_blank");
};

onMounted(() => {
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: (selectedDates, dateStr) => {
            dateRange.value = dateStr;
        }
    });
});
</script>

<template>
    <Head title="Generate Report" />
    <AdminLayout :title="'Generate Report'">

        <div class="flex items-center justify-center w-full">
            <div class="bg-white rounded-lg shadow p-6 mt-4 w-full max-w-xl">
                
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                    Building Permit Report Filters
                </h2>

                <div class="flex flex-col gap-4">
                    
                    <!-- Date Range -->
                    <div>
                        <label for="dateRangePicker" class="text-sm font-medium text-gray-600 mb-1 block">
                            Date Range
                        </label>
                        <input 
                            id="dateRangePicker"
                            type="text"
                            class="border rounded px-3 py-2 w-full focus:ring focus:ring-emerald-300"
                            placeholder="Select date range"
                            readonly
                        />
                    </div>

                    <!-- Type of Business Dropdown -->
                    <div>
                        <label for="businessTypeFilter" class="text-sm font-medium text-gray-600 mb-1 block">
                            Type of Building
                        </label>
                        <select 
                            id="businessTypeFilter"
                            v-model="business_type"
                            class="border rounded px-3 py-2 w-full focus:ring focus:ring-emerald-300"
                        >
                            <option value="">All</option>
                            <option value="Indigenous">Indigenous</option>
                            <option value="Building Permit">Building Permit</option>
                        </select>
                    </div>

                    <!-- Status Dropdown -->
                    <div>
                        <label for="statusFilter" class="text-sm font-medium text-gray-600 mb-1 block">
                            Status
                        </label>
                        <select 
                            id="statusFilter"
                            v-model="status"
                            class="border rounded px-3 py-2 w-full focus:ring focus:ring-emerald-300"
                        >
                            <option value="">All</option>
                            <option value="Under Review of Admin">Under Review of Admin</option>
                            <option value="For Inspection">For Inspection</option>
                            <option value="For Creating Permit">For Creating Permit</option>
                            <option value="For Permit Release">For Permit Release</option>
                            <option value="Permit Released">Permit Released</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col gap-2 mt-2">
                        <button 
                            @click="generateReport"
                            class="px-4 py-2 bg-[#1D4ED8] text-white hover:bg-[#1E40AF] rounded-full w-full transition">
                            Generate PDF Report
                        </button>
                    </div>

                </div>
            </div>
        </div>
        
    </AdminLayout>
</template>
