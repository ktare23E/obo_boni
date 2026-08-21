<script setup>
import { Head } from '@inertiajs/vue3';
import InspectorLayout from '@/Layouts/InspectorLayout.vue';
import { onMounted, ref } from 'vue';

// FullCalendar imports
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    events: Array
});

const calendarRef = ref(null);

console.log(props.events);




onMounted(() => {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        selectable: true,
        events: props.events,
        validRange: {
            start: new Date().toISOString().split('T')[0] // block past dates
        },

        // ----------------------------------------------
        // 📌 SHOW BUSINESS INFO WHEN CLICKING BOOKED DATE
        // ----------------------------------------------
        eventClick: function (info) {

            const readableDate = info.event.start.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            if (info.event.extendedProps.status === "booked") {
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
                return;
            }
        },

        // ----------------------------------------------
        // 📌 CLICK ON DATE TO ADD / REMOVE AVAILABILITY
        // ----------------------------------------------
        dateClick: function (info) {
            const formattedDate = new Date(info.dateStr).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Check if date already has event
            const existingEvent = calendar.getEvents().find(e => e.startStr === info.dateStr);

            if (existingEvent) {

                // 🚫 Prevent removal if inspection already passed
                if (existingEvent.extendedProps.inspectionStatus === "Passed") {

                    Swal.fire({
                        title: "Removal not allowed",
                        text: "This schedule cannot be removed because the inspection is already PASSED.",
                        icon: "info",
                        confirmButtonText: "OK"
                    });

                    return;
                } else {

                    // Ask to remove availability
                    Swal.fire({
                        title: 'Remove schedule?',
                        text: `Do you want to remove availability on ${formattedDate}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(route('inspector.schedule.destroy', existingEvent.id))
                                .then(res => {
                                    if (res.data.success) {
                                        existingEvent.remove();

                                        Swal.fire({
                                            title: 'Success',
                                            text: res.data.message || `${formattedDate} removed from availability`,
                                            icon: 'success'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Warning',
                                            text: res.data.message || 'Something went wrong.',
                                            icon: 'warning'
                                        });
                                    }
                                })
                                .catch(error => {
                                    const msg = error.response?.data?.message || 'Failed to remove schedule.';
                                    Swal.fire({
                                        title: 'Error',
                                        text: msg,
                                        icon: 'error'
                                    });
                                });
                        }
                    });
                }
            }

            // Add availability
            else {
                Swal.fire({
                    title: 'Set availability?',
                    text: `Do you want to mark ${formattedDate} as available?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, set available',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(route('inspector.schedule.store'), { date: info.dateStr })
                            .then(res => {
                                if (res.data.success) {
                                    calendar.addEvent(res.data.event);
                                    Swal.fire('Success', `${formattedDate} set as available!`, 'success');
                                }
                            })
                    }
                });
            }
        }
    });

    calendar.render();
    calendarRef.value = calendar;
});
</script>

<template>
    <Head title="Inspector Schedule" />

    <InspectorLayout :title="'Inspector Schedule'">
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div id="calendar"></div>
        </div>
    </InspectorLayout>
</template>
