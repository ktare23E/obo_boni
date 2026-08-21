<script setup>
    import { Head, router, useForm } from '@inertiajs/vue3';
    import WelcomeNav from '@/Components/WelcomeNav.vue';
    import { ref, watch, computed, onMounted } from 'vue';
    import L from 'leaflet';
    import 'leaflet/dist/leaflet.css';

    import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
    import markerIcon from 'leaflet/dist/images/marker-icon.png'
    import markerShadow from 'leaflet/dist/images/marker-shadow.png'

    delete L.Icon.Default.prototype._getIconUrl

    L.Icon.Default.mergeOptions({
        iconRetinaUrl: markerIcon2x,
        iconUrl: markerIcon,
        shadowUrl: markerShadow,
    })
    
    // Props from Inertia
    const props = defineProps({
        requirements: {
            type: Array,
            required: true,
        },
    });
    
    // Form setup
    const requirementType = ref('');
    const selectedBarangay = ref('');
    const formLat = ref(null);
    const formLng = ref(null);
    
    const form = useForm({
        business_name: '',
        address: '',
        type_of_business: '', // user-input
        building_type: '',    // auto-filled
        image_path: null,
        requirement_type: '',
        place_description: '',
        lat: null,
        lng: null,
        files: {}, // requirement_id => Array<File>
    });
    
    // Reset files and set building_type when requirement type changes
    watch(requirementType, (newType) => {
        form.requirement_type = newType;
        form.files = {};
    
        if (newType === "Building Permit") {
            form.building_type = "Building Permit";
        } else if (newType === "Indigenous") {
            form.building_type = "Indigenous";
        } else {
            form.building_type = "";
        }
    });
    
    // Filter requirements
    const filteredRequirements = computed(() =>
        props.requirements.filter((r) => r.requirement_type === requirementType.value)
    );
    
    // Preview modal state
    const showModal = ref(false);
    const sampleToShow = ref('');
    const isImage = (path) => /\.(png|jpg|jpeg|gif|webp)$/i.test(path);
    
    // Drag/zoom for preview
    const scale = ref(1);
    const pos = ref({ x: 0, y: 0 });
    let isDragging = false;
    let start = { x: 0, y: 0 };
    const resetZoom = () => { scale.value = 1; pos.value = { x: 0, y: 0 }; };
    const handleWheel = (e) => { e.preventDefault(); const delta = e.deltaY < 0 ? 0.1 : -0.1; scale.value = Math.min(Math.max(scale.value + delta, 0.5), 3); };
    const startDrag = (e) => { isDragging = true; start = { x: e.clientX - pos.value.x, y: e.clientY - pos.value.y }; };
    const onDrag = (e) => { if (!isDragging) return; pos.value = { x: e.clientX - start.x, y: e.clientY - start.y }; };
    const endDrag = () => { isDragging = false; };
    const previewSample = (sample) => { sampleToShow.value = `/storage/${sample.requirement_sample}`; showModal.value = true; resetZoom(); };
    const closeModal = () => { showModal.value = false; resetZoom(); };
    
    // Barangays
    const addresses = [
        { "name": "Santa Maria (Baga)", "lat": 8.0957, "lng": 123.7524 },
        { "name": "Balatacan", "lat": 8.0416, "lng": 123.6693 },
        { "name": "Banglay", "lat": 8.1451, "lng": 123.7123 },
        { "name": "Mantic", "lat": 8.0581, "lng": 123.7484 },
        { "name": "Migcanaway", "lat": 8.0588, "lng": 123.7519 },
        { "name": "Bintana", "lat": 8.0782, "lng": 123.6852 },
        { "name": "Bocator", "lat": 8.0476, "lng": 123.6773 },
        { "name": "Bongabong", "lat": 8.1085, "lng": 123.7212 },
        { "name": "Caniangan", "lat": 8.0911, "lng": 123.6983 },
        { "name": "Capalaran", "lat": 8.1143, "lng": 123.7440 },
        { "name": "Catagan", "lat": 8.0997, "lng": 123.7014 },
        { "name": "Barangay I - City Hall (Poblacion)", "lat": 8.0606, "lng": 123.7504 },
        { "name": "Barangay II - Marilou Annex (Poblacion)", "lat": 8.0636, "lng": 123.7505 },
        { "name": "Barangay III - Market Kalubian (Poblacion)", "lat": 8.0671, "lng": 123.7456 },
        { "name": "Barangay IV - St. Michael (Poblacion)", "lat": 8.0646, "lng": 123.7452 },
        { "name": "Isidro D. Tan (Dimalooc)", "lat": 8.0738, "lng": 123.7529 },
        { "name": "Garang", "lat": 8.0518, "lng": 123.7299 },
        { "name": "Guinabot", "lat": 8.0725, "lng": 123.6505 },
        { "name": "Guinalaban", "lat": 8.0811, "lng": 123.6697 },
        { "name": "Kauswagan", "lat": 8.0855, "lng": 123.7241 },
        { "name": "Kimat", "lat": 8.1284, "lng": 123.6920 },
        { "name": "Labuyo", "lat": 8.0633, "lng": 123.7191 },
        { "name": "Lorenzo Tan", "lat": 8.0572, "lng": 123.7027 },
        { "name": "Lower Polao (Poblacion)", "lat": 8.0713, "lng": 123.7430 },
        { "name": "Lumban", "lat": 8.0940, "lng": 123.7151 },
        { "name": "Maloro", "lat": 8.0637, "lng": 123.7541 },
        { "name": "Malubog (Poblacion)", "lat": 8.0593, "lng": 123.7457 },
        { "name": "Manga", "lat": 8.0896, "lng": 123.7374 },
        { "name": "Maquilao", "lat": 8.0484, "lng": 123.7214 },
        { "name": "Minsubong", "lat": 8.0862, "lng": 123.7620 },
        { "name": "Owayan", "lat": 8.1124, "lng": 123.6485 },
        { "name": "Paiton", "lat": 8.0992, "lng": 123.6646 },
        { "name": "Panalsalan", "lat": 8.0522, "lng": 123.6863 },
        { "name": "Pangabuan", "lat": 8.0532, "lng": 123.6938 },
        { "name": "Prenza", "lat": 8.0782, "lng": 123.7121 },
        { "name": "Salimpuno", "lat": 8.1353, "lng": 123.7026 },
        { "name": "San Antonio", "lat": 8.1125, "lng": 123.6762 },
        { "name": "San Apolinario", "lat": 8.0560, "lng": 123.7410 },
        { "name": "San Vicente", "lat": 8.0572, "lng": 123.6596 },
        { "name": "Santa Cruz", "lat": 8.0676, "lng": 123.7368 },
        { "name": "Santo Niño", "lat": 8.1122, "lng": 123.7043 },
        { "name": "Sicot", "lat": 8.1165, "lng": 123.6608 },
        { "name": "Silanga", "lat": 8.0663, "lng": 123.7666 },
        { "name": "Silangit", "lat": 8.0747, "lng": 123.6650 },
        { "name": "Simasay", "lat": 8.0889, "lng": 123.6421 },
        { "name": "Sumirap", "lat": 8.0375, "lng": 123.6569 },
        { "name": "Taguite", "lat": 8.1021, "lng": 123.6875 },
        { "name": "Tituron", "lat": 8.1163, "lng": 123.7213 },
        { "name": "Tugas", "lat": 8.0527, "lng": 123.6455 },
        { "name": "Upper Polao (Poblacion)", "lat": 8.0769, "lng": 123.7383 },
        { "name": "Villaba", "lat": 8.1312, "lng": 123.7320 }
    ];
    
    // Handle barangay selection
    const onBarangaySelect = () => {
        const barangay = addresses.find(b => b.name === selectedBarangay.value);
        if (barangay) {
            form.address = barangay.name;
            formLat.value = barangay.lat;
            formLng.value = barangay.lng;
        }
    };
    
    // Handle file uploads
    const onFilesChange = (requirementId, event) => {
        const files = Array.from(event.target.files || []);
        form.files = { ...form.files, [requirementId]: files };
    };
    
    // Form submission
    const submit = () => {
        form.transform((data) => {
            const fd = new FormData();
            fd.append("business_name", data.business_name);
            fd.append("address", data.address);
            fd.append("place_description", data.place_description);
            fd.append("type_of_business", data.type_of_business);
            fd.append("building_type", data.building_type);      // <-- include building_type
            fd.append("requirement_type", data.requirement_type);
            fd.append("lat", formLat.value);
            fd.append("lng", formLng.value);
    
            if (data.image_path) fd.append("image_path", data.image_path);
    
            Object.keys(data.files).forEach(reqId => {
                data.files[reqId].forEach((file, index) => {
                    fd.append(`files[${reqId}][${index}]`, file);
                });
            });
    
            return fd;
        });
    
        form.post(route('store_building'), {
            forceFormData: true,
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Application Submitted",
                    text: "Your application has been successfully submitted!",
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => router.visit(route('buildings.index')), 2000);
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Submission Failed",
                    text: "Please check your inputs and try again."
                });
            }
        });
    };
    
    // Leaflet map
    let map, marker;
    onMounted(() => {
        map = L.map('map').setView([8.0606, 123.7504], 13)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map)

        // IMPORTANT for production
        setTimeout(() => {
            map.invalidateSize()
        }, 300)
    })
    
    watch([formLat, formLng], ([lat, lng]) => {
        if (lat !== null && lng !== null) {
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map)

                marker.on('dragend', () => {
                    const pos = marker.getLatLng()
                    formLat.value = pos.lat
                    formLng.value = pos.lng
                })
            } else {
                marker.setLatLng([lat, lng])
            }

            map.setView([lat, lng], 16)
        }
    })


    </script>
    
    <template>
    <Head title="Apply for Building" />
    <div class="flex flex-col min-h-screen bg-gray-50">
        <WelcomeNav />
    
        <section class="relative h-[40vh] flex items-center justify-center bg-orange-700 text-white shadow-lg">
            <div class="text-center px-6">
                <h1 class="text-4xl font-bold uppercase tracking-wide">Apply for a Building</h1>
                <p class="mt-3 text-orange-100 max-w-2xl mx-auto">
                    Submit your building details and upload the necessary requirements to start your application.
                </p>
            </div>
        </section>
    
        <section class="max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow-xl mt-[-60px] relative z-10 border-t-4 border-orange-500">
            <button type="button" @click="router.visit(route('buildings.index'))" class="flex items-center gap-2 text-gray-700 hover:text-orange-600 mb-6 font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg> Back
            </button>
    
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Building info -->
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 border-b-2 border-orange-400 pb-2 mb-4">Building Information</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Building Name</label>
                            <input v-model="form.business_name" type="text" required class="w-full border-gray-300 rounded-lg p-2" />
                        </div>
    
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                            <select v-model="selectedBarangay" @change="onBarangaySelect" required class="w-full border-gray-300 rounded-lg p-2">
                                <option value="" disabled>Select Barangay</option>
                                <option v-for="addr in addresses" :key="addr.name" :value="addr.name">{{ addr.name }}</option>
                            </select>
                        </div>
    
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Description</label>
                            <textarea v-model="form.place_description" rows="3" class="w-full border-gray-300 rounded-lg p-2" required></textarea>
                        </div>
    
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type of Building</label>
                            <input v-model="form.type_of_business" type="text" required class="w-full border-gray-300 rounded-lg p-2" />
                        </div>
    
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Building Type</label>
                            <input v-model="form.building_type" type="text" readonly class="w-full bg-gray-100 border-gray-300 rounded-lg p-2" />
                        </div>
    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Building Image</label>
                            <input type="file" @change="e => form.image_path = e.target.files[0]" accept="image/*" class="w-full" />
                        </div>
                    </div>
    
                    <div id="map" class="w-full h-64 mt-4 rounded-lg border"></div>
                    <div
                        v-if="formLat !== null && formLng !== null"
                        class="mt-2 text-sm text-gray-600"
                    >
                        <p>Latitude: {{ formLat.toFixed(6) }}</p>
                        <p>Longitude: {{ formLng.toFixed(6) }}</p>
                        <p class="text-xs text-gray-500 italic">
                            Drag the pin to mark the exact building location
                        </p>
                    </div>


                </div>
    
                <!-- Requirement type -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4">Application Type</h2>
                    <select v-model="requirementType" required class="w-full md:w-1/2 border-gray-300 rounded-lg p-2">
                        <option value="" disabled>Select requirement type</option>
                        <option value="Building Permit">Building Permit</option>
                        <option value="Indigenous">Indigenous</option>
                    </select>
                </div>
    
                <!-- Dynamic requirements -->
                <div v-if="requirementType">
                    <h2 class="text-2xl font-semibold mb-4">Upload Requirements ({{ requirementType }})</h2>
                    <div class="space-y-6">
                        <div v-for="req in filteredRequirements" :key="req.id" class="p-5 border rounded-lg">
                            <div class="flex flex-col md:flex-row justify-between gap-3">
                                <div class="md:w-2/3">
                                    <p class="font-semibold">{{ req.title }}</p>
                                    <p class="text-sm text-gray-600">{{ req.description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Allowed: <span class="font-semibold">{{ req.file_type_allowed ? req.file_type_allowed.toUpperCase() : 'No Sample' }}</span>
                                    </p>
                                    <div v-if="req.samples?.length" class="mt-2">
                                        <p class="text-xs text-gray-600">Samples:</p>
                                        <div class="flex gap-2 flex-wrap">
                                            <button v-for="s in req.samples" :key="s.id" type="button" @click="previewSample(s)" class="text-orange-600 underline text-xs">View Sample #{{ s.id }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="md:w-1/3">
                                    <label class="text-xs mb-1 font-medium text-gray-700">Upload files (multiple allowed)</label>
                                    <input type="file" multiple :accept="req.file_type_allowed === 'image' ? 'image/*' : req.file_type_allowed === 'pdf' ? 'application/pdf' : '.doc,.docx'" @change="e => onFilesChange(req.id, e)" class="w-full" />
                                    <p v-if="form.files[req.id]?.length" class="text-xs mt-2 text-gray-600">Selected: {{form.files[req.id].map(f => f.name).join(', ')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded">Submit Application</button>
                </div>
            </form>
        </section>
    
        <footer class="bg-gradient-to-r from-orange-700 to-orange-900 text-orange-100 py-6 text-center mt-10">
            © {{ new Date().getFullYear() }} Office of the Building Official
        </footer>
    </div>
    
    <!-- Preview modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl w-[90%] max-w-2xl p-6 relative">
            <button class="absolute top-2 right-2 text-red-500 font-bold text-xl" @click="closeModal">✕</button>
            <div v-if="isImage(sampleToShow)" class="overflow-hidden relative cursor-grab active:cursor-grabbing" @wheel.prevent="handleWheel" @mousedown="startDrag" @mousemove="onDrag" @mouseup="endDrag" @mouseleave="endDrag" style="height: 500px;">
                <img :src="sampleToShow" class="rounded-md select-none" draggable="false" :style="{ transform: `translate(${pos.x}px, ${pos.y}px) scale(${scale})`, transition: isDragging ? 'none' : 'transform 0.1s ease' }" />
                <button class="absolute bottom-3 right-3 bg-black/60 text-white px-3 py-1 rounded text-xs" @click.stop="resetZoom">Reset Zoom</button>
            </div>
            <div v-else class="text-center">
                <p class="text-gray-700">File Preview</p>
                <a :href="sampleToShow" target="_blank" class="block mt-4 bg-orange-500 text-white px-6 py-2 rounded-lg">Open File</a>
            </div>
        </div>
    </div>
    </template>
    
    <style>
    /* modal fade */
    .modal-fade-enter-active,
    .modal-fade-leave-active {
        transition: opacity 0.2s ease;
    }
    .modal-fade-enter-from,
    .modal-fade-leave-to {
        opacity: 0;
    }
    </style>
    