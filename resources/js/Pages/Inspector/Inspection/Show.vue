<script setup>
    import { Head, Link, router } from '@inertiajs/vue3'
    import InspectorLayout from '@/Layouts/InspectorLayout.vue'
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import L from 'leaflet'
    import 'leaflet/dist/leaflet.css'
    
    // ✅ REQUIRED: Fix Leaflet marker icons in production
    import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
    import markerIcon from 'leaflet/dist/images/marker-icon.png'
    import markerShadow from 'leaflet/dist/images/marker-shadow.png'
    
    delete L.Icon.Default.prototype._getIconUrl
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: markerIcon2x,
        iconUrl: markerIcon,
        shadowUrl: markerShadow,
    })
    
    // =======================
    // PROPS
    // =======================
    const props = defineProps({
        business: Object,
        submissions: Array,
    })
    
    // =======================
    // IMAGE MODAL
    // =======================
    const showModal = ref(false)
    const selectedImage = ref('')
    
    const openModal = (filePath) => {
        selectedImage.value = `/storage/${filePath}`
        showModal.value = true
    }
    const closeModal = () => {
        showModal.value = false
        selectedImage.value = ''
    }
    
    // =======================
    // REJECT MODAL
    // =======================
    const showRejectModal = ref(false)
    const rejectionReason = ref('')
    
    const openRejectModal = () => {
        showRejectModal.value = true
    }
    
    const submitRejection = () => {
        if (!rejectionReason.value.trim()) {
            Swal.fire('Error', 'Please provide a reason for decline.', 'error')
            return
        }
    
        router.post(`/admin/businesses/${props.business.id}/reject`, {
            reason: rejectionReason.value,
        }, {
            onSuccess: () => {
                Swal.fire('Declined!', 'The business has been declined.', 'success')
                showRejectModal.value = false
                rejectionReason.value = ''
            },
        })
    }
    
    // =======================
    // APPROVE BUSINESS
    // =======================
    const approveBusiness = () => {
        Swal.fire({
            title: 'Approve this business?',
            text: 'Once approved, this business will be marked as approved.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve',
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/admin/businesses/${props.business.id}/approve`, {}, {
                    onSuccess: () => {
                        Swal.fire('Approved!', 'The business has been approved.', 'success')
                    },
                })
            }
        })
    }
    
    // =======================
    // FILE TYPE CHECKER
    // =======================
    const isImage = (filePath) => {
        const ext = filePath.split('.').pop().toLowerCase()
        return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)
    }
    
    // ===============================
    // 🌍 MAP + LIVE GPS TRACKING
    // ===============================
    const mapContainer = ref(null)
    let mapInstance = null
    
    const userLocation = ref(null)
    const userMarker = ref(null)
    const userAccuracyCircle = ref(null)
    const watchId = ref(null)
    const isTracking = ref(true)
    const routeLine = ref(null)
    const locating = ref(false)
    
    // -----------------------
    // START GPS TRACKING
    // -----------------------
    const startWatchingLocation = () => {
        if (!navigator.geolocation || watchId.value !== null) return
    
        locating.value = true
    
        watchId.value = navigator.geolocation.watchPosition(
            (pos) => {
                locating.value = false
    
                const lat = pos.coords.latitude
                const lng = pos.coords.longitude
                const accuracy = pos.coords.accuracy ?? 0
    
                userLocation.value = { lat, lng, accuracy }
    
                // 👤 User marker
                if (!userMarker.value) {
                    userMarker.value = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                            iconSize: [30, 30],
                        }),
                    }).addTo(mapInstance).bindPopup('<b>You are here</b>')
                } else {
                    userMarker.value.setLatLng([lat, lng])
                }
    
                // 🎯 Accuracy circle
                if (!userAccuracyCircle.value) {
                    userAccuracyCircle.value = L.circle([lat, lng], {
                        radius: accuracy,
                    }).addTo(mapInstance)
                } else {
                    userAccuracyCircle.value
                        .setLatLng([lat, lng])
                        .setRadius(accuracy)
                }
    
                // 🎯 Auto-center
                if (isTracking.value) {
                    mapInstance.setView([lat, lng], 17)
                }
    
                // 📏 Straight route line
                if (
                    props.business.lat !== null &&
                    props.business.lng !== null
                ) {
                    const points = [
                        [lat, lng],
                        [props.business.lat, props.business.lng],
                    ]
    
                    if (!routeLine.value) {
                        routeLine.value = L.polyline(points, { weight: 4 })
                            .addTo(mapInstance)
                    } else {
                        routeLine.value.setLatLngs(points)
                    }
                }
            },
            (err) => {
                locating.value = false
                console.error('GPS error:', err)
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 }
        )
    }
    
    const stopWatchingLocation = () => {
        if (watchId.value !== null) {
            navigator.geolocation.clearWatch(watchId.value)
            watchId.value = null
        }
        isTracking.value = false
    }
    
    const toggleTracking = () => {
        if (watchId.value === null) {
            isTracking.value = true
            startWatchingLocation()
        } else {
            stopWatchingLocation()
        }
    }
    
    const recenterToUser = () => {
        if (!userLocation.value) {
            Swal.fire('Location not ready', 'Please allow location access first.', 'warning')
            startWatchingLocation()
            return
        }
        mapInstance.setView([userLocation.value.lat, userLocation.value.lng], 17)
    }
    
    // =======================
    // GOOGLE MAPS DIRECTIONS
    // =======================
    const openDirections = () => {
        if (!userLocation.value) {
            Swal.fire('Location not ready', 'Please allow location access first.', 'warning')
            startWatchingLocation()
            return
        }
    
        const origin = `${userLocation.value.lat},${userLocation.value.lng}`
        const dest = `${props.business.lat},${props.business.lng}`
    
        window.open(
            `https://www.google.com/maps/dir/?api=1&origin=${origin}&destination=${dest}&travelmode=driving`,
            '_blank'
        )
    }
    
    // =======================
    // INIT MAP
    // =======================
    onMounted(() => {
        if (
            mapContainer.value &&
            props.business.lat !== null &&
            props.business.lng !== null
        ) {
            if (mapInstance) {
                mapInstance.remove()
                mapInstance = null
            }
    
            mapInstance = L.map(mapContainer.value).setView(
                [props.business.lat, props.business.lng],
                17
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
    
            // ✅ REQUIRED for production layouts
            setTimeout(() => {
                mapInstance.invalidateSize()
            }, 300)
    
            startWatchingLocation()
        }
    })
    
    // =======================
    // CLEANUP
    // =======================
    onBeforeUnmount(() => {
        stopWatchingLocation()
    })
    </script>
    
    
    <template>
    
        <Head title="Requirement Submissions" />
    
        <InspectorLayout :title="'Requirement Submissions'">
            <Link :href="route('assigned_inpsection')" class="bg-black py-2 px-3 text-white rounded-full">back</Link>
    
            <!-- Business Info -->
            <div class="bg-white rounded-lg shadow p-6 mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <img :src="`/storage/${business.image_path}`"
                        class="w-24 h-24 object-cover rounded-lg shadow-md cursor-pointer"
                        @click="openModal(business.image_path)" />
    
                    <div>
                        <h2 class="text-2xl font-semibold">Building:{{ business.business_name }}</h2>
                        <p class="text-sm opacity-90">Address: {{ business.address }}</p>
                        <p class="text-sm opacity-90">Address Description: {{ business.place_description }}</p>
                        <p class="text-sm opacity-90">Building Progress: {{ business.remarks }}</p>
                        <p class="text-sm opacity-90">Building Type: {{ business.type_of_business }}</p>
    
                        <p class="mt-2 text-sm">
                            <span class="font-medium">Inspection Status:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': business.inspection.status === 'Pending',
                                    'bg-green-100 text-green-800': business.inspection.status === 'Passed',
                                    'bg-red-100 text-red-800': business.inspection.status === 'Rejected'
                                }">
                                {{ business.inspection.status }}
                            </span>
                        </p>
                    </div>
                </div>
    
                <div class="space-x-2" v-if="business.inspection.status != 'Passed'">
                    <button @click="approveBusiness"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Approve</button>
    
                    <button @click="openRejectModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">Decline</button>
                </div>
            </div>
    
            <!-- MAP SECTION -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-3">Building Location</h3>
    
                <div ref="mapContainer" class="w-full h-72 rounded-lg border"></div>
    
                <div class="mt-3 flex items-center space-x-3">
                    <button @click="openDirections" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Navigate with Google Maps
                    </button>
    
                    <button @click="recenterToUser" class="px-3 py-2 bg-gray-200 rounded-lg">
                        Recenter to Me
                    </button>
    
                    <button @click="toggleTracking" class="px-3 py-2 bg-gray-200 rounded-lg">
                        {{ isTracking ? 'Stop Tracking' : 'Start Tracking' }}
                    </button>
    
                    <span v-if="locating" class="text-gray-500 text-sm">Locating…</span>
                </div>
            </div>
    
            <!-- SUBMISSIONS TABLE -->
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
                        </tr>
                    </thead>
    
                    <tbody>
                        <tr v-for="(submission, index) in submissions" :key="submission.id"
                            class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ index + 1 }}</td>
    
                            <td class="px-4 py-2 font-medium text-gray-800">
                                {{ submission.requirement.title }}
                            </td>
    
                            <td class="px-4 py-2">{{ submission.requirement.requirement_type }}</td>
    
                            <td class="px-4 py-2">
                                <div v-if="isImage(submission.file_path)">
                                    <img :src="`/storage/${submission.file_path}`"
                                        class="w-20 h-20 object-cover rounded border cursor-pointer"
                                        @click="openModal(submission.file_path)" />
                                </div>
    
                                <div v-else>
                                    <a
                                    :href="route('submissions.file', submission.id)"
                                    target="_blank"
                                    class="text-blue-600 underline hover:text-blue-800"
                                    >
                                    View File
                                    </a>
                                </div>
                            </td>
    
                            <td class="px-4 py-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': submission.status === 'submitted',
                                        'bg-green-100 text-green-800': submission.status === 'approved',
                                        'bg-red-100 text-red-800': submission.status === 'rejected'
                                    }">
                                    {{ submission.status === 'rejected' ? 'declined' : submission.status }}
                                </span>
                            </td>
    
                            <td class="px-4 py-2">
                                <span v-if="submission.remarks">{{ submission.remarks }}</span>
                                <span v-else class="text-gray-400 italic">No remarks</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
    
                <div v-if="submissions.length === 0" class="text-center py-8 text-gray-500">
                    No submissions found for this business.
                </div>
            </div>
    
            <!-- IMAGE MODAL -->
            <transition name="fade">
                <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[9999]"
                    @click.self="closeModal">
    
                    <div class="bg-white rounded-lg p-4 relative max-w-3xl w-full">
                        <button class="absolute top-2 right-2 text-gray-500" @click="closeModal">✕</button>
    
                        <img :src="selectedImage"
                            class="max-h-[80vh] w-auto mx-auto rounded-lg shadow-lg select-none" />
                    </div>
                </div>
            </transition>
    
            <!-- REJECT MODAL -->
            <div v-if="showRejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Reason for Decline</h3>
    
                    <textarea v-model="rejectionReason" rows="4" class="w-full border rounded-lg p-2"
                        placeholder="Enter reason..."></textarea>
    
                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="showRejectModal = false" class="px-4 py-2 bg-gray-300 rounded-lg">Cancel</button>
                        <button @click="submitRejection" class="px-4 py-2 bg-red-600 text-white rounded-lg">Submit</button>
                    </div>
                </div>
            </div>
    
        </InspectorLayout>
    </template>
    
    <style scoped>
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity .25s ease;
    }
    
    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
    </style>
    