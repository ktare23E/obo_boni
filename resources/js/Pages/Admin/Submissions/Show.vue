<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

// ✅ FIX: Leaflet marker icons (REQUIRED for production)
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
})

// Props
const props = defineProps({
    business: Object,
    submissions: Array,
    hasFutureAvailableSchedule: Boolean,
})

// Helpers
const isImage = (filePath) => {
    const ext = filePath.split('.').pop().toLowerCase()
    return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)
}

// ================= MODAL =================
const showModal = ref(false)
const selectedImage = ref('')

const openModal = (filePath) => {
    selectedImage.value = `/storage/${filePath}`
    showModal.value = true
    resetZoom()
}

const closeModal = () => {
    showModal.value = false
    selectedImage.value = ''
}

// ================= ZOOM + DRAG =================
const scale = ref(1)
const pos = ref({ x: 0, y: 0 })

let isDragging = false
let start = { x: 0, y: 0 }

const resetZoom = () => {
    scale.value = 1
    pos.value = { x: 0, y: 0 }
}

const handleWheel = (e) => {
    e.preventDefault()
    const delta = e.deltaY < 0 ? 0.1 : -0.1
    scale.value = Math.min(Math.max(scale.value + delta, 0.5), 3)
}

const startDrag = (e) => {
    isDragging = true
    start = { x: e.clientX - pos.value.x, y: e.clientY - pos.value.y }
}

const onDrag = (e) => {
    if (!isDragging) return
    pos.value = {
        x: e.clientX - start.x,
        y: e.clientY - start.y,
    }
}

const endDrag = () => {
    isDragging = false
}

// ================= ACTIONS =================
const approve = (submissionId) => {
    Swal.fire({
        title: 'Approve Submission?',
        text: 'This will mark the submission as approved.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, approve it',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('submissions.approve', submissionId), {}, {
                onSuccess: () => {
                    Swal.fire('Approved!', 'The submission has been approved.', 'success')
                },
            })
        }
    })
}

const reject = (submissionId) => {
    Swal.fire({
        title: 'Decline Submission?',
        input: 'text',
        inputLabel: 'Remarks (optional)',
        inputPlaceholder: 'Enter reason for decline',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Decline',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('submissions.reject', submissionId), {
                remarks: result.value || 'Declined by admin',
            }, {
                onSuccess: () => {
                    Swal.fire('Declined!', 'The submission has been declined.', 'success')
                },
            })
        }
    })
}

const approveAll = () => {
    const pendingSubmissions = props.submissions.filter(
        (s) => s.status === 'submitted'
    )

    if (pendingSubmissions.length === 0) {
        Swal.fire('Nothing to Approve', 'All submissions are already processed.', 'info')
        return
    }

    Swal.fire({
        title: 'Approve All Submissions?',
        text: `Are you sure you want to approve all ${pendingSubmissions.length} submitted requirements?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, approve all',
    }).then((result) => {
        if (result.isConfirmed) {
            const submissionIds = pendingSubmissions.map((s) => s.id)

            router.post(route('submissions.approveAll'), { ids: submissionIds }, {
                onSuccess: () => {
                    Swal.fire('Approved!', 'All submissions have been approved.', 'success')
                    submissionIds.forEach((id) => {
                        const sub = props.submissions.find((s) => s.id === id)
                        if (sub) sub.status = 'approved'
                    })
                    props.business.status = 'Approved'
                },
                onError: () => {
                    Swal.fire('Error', 'Failed to approve submissions.', 'error')
                },
            })
        }
    })
}

// ================= LEAFLET MAP =================
const mapContainer = ref(null)
let mapInstance = null

onMounted(() => {
    if (
        mapContainer.value &&
        props.business.lat !== null &&
        props.business.lng !== null
    ) {
        // Safety: remove old map (Inertia revisit)
        if (mapInstance) {
            mapInstance.remove()
            mapInstance = null
        }

        mapInstance = L.map(mapContainer.value).setView(
            [props.business.lat, props.business.lng],
            16
        )

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(mapInstance)

        L.marker([props.business.lat, props.business.lng])
            .addTo(mapInstance)
            .bindPopup(
                `<b>${props.business.business_name}</b><br>${props.business.address}`
            )
            .openPopup()

        // ✅ REQUIRED FOR PRODUCTION LAYOUTS
        setTimeout(() => {
            mapInstance.invalidateSize()
        }, 300)
    }
})
</script>
    
    
    <template>
    <Head title="Requirement Submissions" />
    <AdminLayout :title="'Requirement Submissions'">
        <Link :href="route('submissions')" class="bg-black py-2 px-3 text-white rounded-full">back</Link>
    
        <!-- Business Info -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-6">
                    <img :src="`/storage/${business.image_path}`" alt="Business Image"
                        class="w-24 h-24 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-80 transition"
                        @click="openModal(business.image_path)" />
                    <div>
                        <h2 class="text-2xl font-semibold">Building:{{ business.business_name }}</h2>
                        <p class="text-sm opacity-90">Address:{{ business.address }}</p>
                        <p class="text-sm opacity-90">Address Description:{{ business.place_description }}</p>
                        <p class="text-sm opacity-90">Building Type:{{ business.type_of_business }}</p>
                        <p class="mt-2 text-sm">
                            <span class="font-medium">Status:</span>
                            <span :class="{
                                'text-yellow-500': business.status === 'Pending',
                                'text-green-600': business.status === 'Approved',
                                'text-red-600': business.status === 'Rejected'
                            }">
                                {{ business.status }}
                            </span>
                        </p>
                    </div>
                </div>
                <div>
                    <button
                        v-if="props.business.status === 'Under Review of Admin'"
                        :disabled="!props.hasFutureAvailableSchedule"
                        @click="approveAll"
                        class="px-4 py-2 rounded-lg transition"
                        :class="!props.hasFutureAvailableSchedule
                            ? 'bg-gray-400 cursor-not-allowed text-gray-600'
                            : 'bg-green-600 hover:bg-green-700 text-white'"
                    >
                        Approve All Requirements
                    </button>
                    <p v-if="!props.hasFutureAvailableSchedule"
                        class="text-sm text-red-600 mt-2">
                        ⚠ No available future inspection schedule found.
                    </p>

                </div>
            </div>
        </div>
    
        <!-- Business Map -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Building Location</h3>
            <div ref="mapContainer" class="w-full h-64 rounded-lg"></div>
        </div>
    
        <!-- Submissions Table -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Submitted Requirements</h3>
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr class="text-left text-gray-600">
                        <th class="px-4 py-2 border-b">#</th>
                        <th class="px-4 py-2 border-b">Requirement Title</th>
                        <th class="px-4 py-2 border-b">Type</th>
                        <th class="px-4 py-2 border-b">File</th>
                        <th class="px-4 py-2 border-b">Status</th>
                        <th class="px-4 py-2 border-b">Remarks</th>
                        <th class="px-4 py-2 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(submission, index) in submissions" :key="submission.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800">{{ submission.requirement.title }}</td>
                        <td class="px-4 py-2">{{ submission.requirement.requirement_type }}</td>
                        <td class="px-4 py-2">
                            <div v-if="isImage(submission.file_path)">
                                <img :src="`/storage/${submission.file_path}`"
                                    class="w-20 h-20 object-cover rounded border cursor-pointer hover:opacity-80 transition"
                                    @click="openModal(submission.file_path)"
                                    @contextmenu.prevent />

                            </div>
                            <div v-else>
                                <a
                                :href="route('submissions.file', submission.id)"
                                target="_blank"
                                class="text-blue-600 underline hover:text-blue-800"
                                >
                                View File
                                </a>
                                <!-- <a :href="`/storage/${submission.file_path}`" target="_blank">View File</a> -->

                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize" :class="{
                                'bg-yellow-100 text-yellow-800': submission.status === 'submitted',
                                'bg-green-100 text-green-800': submission.status === 'approved',
                                'bg-red-100 text-red-800': submission.status === 'rejected'
                            }">
                                {{ submission.status == 'rejected' ? 'decline' : submission.status }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span v-if="submission.remarks">{{ submission.remarks }}</span>
                            <span v-else class="text-gray-400 italic">No remarks</span>
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <template v-if="submission.status === 'submitted'">
                                <button
                                    @click="approve(submission.id)"
                                    :disabled="!props.hasFutureAvailableSchedule"
                                    class="text-xs font-semibold px-3 py-1 rounded transition"
                                    :class="!props.hasFutureAvailableSchedule
                                        ? 'bg-gray-400 cursor-not-allowed text-gray-600'
                                        : 'bg-green-600 hover:bg-green-700 text-white'"
                                >
                                    Approve
                                </button>
                                <button @click="reject(submission.id)"
                                    class="text-xs font-semibold px-3 py-1 rounded bg-red-100 hover:bg-red-200 text-red-600 transition">
                                    Decline
                                </button>
                            </template>
                            <span v-else class="text-gray-500 italic text-sm">No actions available</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="submissions.length === 0" class="text-center py-8 text-gray-500">
                No submissions found for this building.
            </div>
        </div>
    
        <!-- 🖼️ IMAGE MODAL WITH ZOOM + DRAG -->
        <transition name="fade">
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[9999]"
                @click.self="closeModal">
    
                <div class="bg-white rounded-lg p-4 relative max-w-3xl w-full overflow-hidden">
                    <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" @click="closeModal">✕</button>
    
                    <div class="relative overflow-hidden cursor-grab active:cursor-grabbing" style="height: 80vh;"
                        @wheel.prevent="handleWheel" @mousedown="startDrag" @mousemove="onDrag" @mouseup="endDrag"
                        @mouseleave="endDrag">
                        <img :src="selectedImage" class="select-none mx-auto"
                            draggable="false"
                            @contextmenu.prevent
                            :style="{
                                transform: `translate(${pos.x}px, ${pos.y}px) scale(${scale})`,
                                transition: isDragging ? 'none' : 'transform 0.1s ease',
                            }"
                        />

                        <button class="absolute bottom-4 right-4 bg-black/60 text-white px-3 py-1 text-xs rounded"
                            @click.stop="resetZoom">
                            Reset Zoom
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AdminLayout>
    </template>
    
    <style scoped>
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.25s ease;
    }
    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
    </style>
    