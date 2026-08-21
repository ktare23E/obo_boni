<script setup>
import { Head } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { onMounted, ref } from 'vue';

// FullCalendar imports
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    events: Array
});

const calendarRef = ref(null);

onMounted(() => {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        events: props.events,

        // ----------------------------------------------
        // 📌 ADMIN: Show business info only (read-only)
        // ----------------------------------------------
        eventClick: function (info) {

            if (info.event.extendedProps.status !== "booked") {
                return; // admin only views booked schedules
            }

            const readableDate = info.event.start.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            Swal.fire({
                title: info.event.extendedProps.businessName,
                html: `
                    <p><strong>Address:</strong> ${info.event.extendedProps.businessAddress}</p>
                    <p><strong>Inspection Status:</strong> ${info.event.extendedProps.inspectionStatus}</p>
                    <p><strong>Inspection Date:</strong> ${readableDate}</p>
                `,
                icon: "info",
                confirmButtonText: "OK"
            });
        },

        // ----------------------------------------------
        // ❌ DISABLE DATE CLICK (NO CREATE/REMOVE ACTION)
        // ----------------------------------------------
        dateClick: function () {
            return; // admin cannot change schedules
        }
    });

    calendar.render();
    calendarRef.value = calendar;
});
</script>

<template>
    <Head title="Inspector Schedule" />

    <StaffLayout :title="'Inspector Schedule'">
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div id="calendar"></div>
        </div>
    </StaffLayout>
</template>
