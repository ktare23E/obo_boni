<script setup>
    import { ref, onMounted } from 'vue'
    import { Link } from '@inertiajs/vue3'
    
    const props = defineProps({ permit: Object })
    
    // Logos
    const leftLogo = '/imgs/tangubicon.png'
    const rightLogo = '/imgs/obo.jpeg'
    
    // Reactive fields
    const permitNumber = ref('')
    const dateIssued = ref('')
    const fsecNo = ref('')
    const receiptNo = ref('')
    const datePaid = ref('')
    const owner = ref('')
    const projectTitle = ref('')
    const location = ref('')
    const occupancy = ref('')
    const scopeOfWork = ref('')
    const totalCost = ref('')
    const professionalInCharge = ref('')
    const buildingOfficial = ref('Melindo B. Tala')
    
    // Fill fields from permit object
    const fillPermitInfo = () => {
        permitNumber.value = props.permit.permit_number || ''
        dateIssued.value = props.permit.release_date || ''
        fsecNo.value = props.permit.fsec_no || ''
        receiptNo.value = props.permit.receipt_no || ''
        datePaid.value = props.permit.date_paid || ''
        owner.value = props.permit.business.user.first_name + ' ' + props.permit.business.user.last_name
        projectTitle.value = props.permit.business.business_name
        location.value = props.permit.business.address + ', Tangub City, Misamis Occidental, 7214'
        occupancy.value = props.permit.occupancy || ''
        scopeOfWork.value = props.permit.scope_of_work || 'Concrete works, Masonry works, Roof framing and roofing works, ceiling works, Painting works, doors and windows, Plumbing and Electrical works'
        totalCost.value = props.permit.total_cost || ''
        professionalInCharge.value = props.permit.professional_in_charge || ''
    }
    
    // Back
    const goBack = () => window.history.back()
    
    // Print only permit content
    const printPermit = () => {
        fillPermitInfo()
        window.print()
    }
    
    onMounted(() => fillPermitInfo())
    </script>
    
    <template>
    <div class="min-h-screen bg-gray-100 p-4 flex flex-col items-center">
        <!-- Back Button -->
        <Link :href="route('permit_list')" class="self-start mb-4 px-4 py-2 bg-gray-200 rounded no-print">
            ← Back
        </Link>
    
        <!-- Permit -->
        <div id="permitContent" class="border-2 border-black p-4 w-full max-w-6xl bg-white">
            <!-- Header -->
            <div class="flex justify-between items-center mb-2">
                <img :src="leftLogo" class="h-16" />
                <div class="text-center">
                    <p class="text-sm">Republic of the Philippines</p>
                    <p class="text-sm">City/Municipality of Tangub</p>
                    <p class="text-sm">Province of Misamis Occidental</p>
                    <h1 class="text-2xl font-bold uppercase">Office of the Building Official</h1>
                    <h2 class="text-xl font-bold uppercase">Building Permit</h2>
                    <!-- Checkboxes -->
                    <div class="mt-1 flex justify-center gap-4 text-sm">
                        <label class="flex items-center gap-1">
                            <input type="checkbox" /> New
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="checkbox" /> Renewal
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="checkbox" /> Amendatory
                        </label>
                    </div>
                </div>
                <img :src="rightLogo" class="h-16" />
            </div>
    
            <!-- Permit Info -->
            <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                <div class="space-y-1">
                    <label class="block">
                        <span class="font-bold">Building Permit No:</span>
                        <input v-model="permitNumber" class="underline-input" />
                    </label>
                    <label class="block">
                        <span class="font-bold">Date Issued:</span>
                        <input v-model="dateIssued" class="underline-input" />
                    </label>
                    <label class="block">
                        <span class="font-bold">FSEC No:</span>
                        <input v-model="fsecNo" class="underline-input" />
                    </label>
                </div>
                <div class="space-y-1">
                    <label class="block">
                        <span class="font-bold">Official Receipt No:</span>
                        <input v-model="receiptNo" class="underline-input" />
                    </label>
                    <label class="block">
                        <span class="font-bold">Date Paid:</span>
                        <input v-model="datePaid" class="underline-input" />
                    </label>
                </div>
            </div>
    
            <p class="mt-2 text-sm mx-4">
                This PERMIT is issued pursuant to Sections 207, 301, 302, 303 and 304 of
                the National Building Code of the Philippines (PD 1096), its Revised IRR, other Referral Codes and its Terms and Conditions.
            </p>
    
            <!-- Owner / Project Info -->
            <div class="mt-2 text-sm space-y-1">
                <div class="flex items-end">
                    <span class="font-bold w-56">Owner / Permittee:</span>
                    <input v-model="owner" class="underline-input flex-1" />
                </div>
                <div class="flex items-end">
                    <span class="font-bold w-56">Project Title:</span>
                    <input v-model="projectTitle" class="underline-input flex-1" />
                </div>
                <div class="flex items-end">
                    <span class="font-bold w-56">Location of Construction:</span>
                    <input v-model="location" class="underline-input flex-1" />
                </div>
                <div class="flex items-end">
                    <span class="font-bold w-56">Use / Occupancy:</span>
                    <input v-model="occupancy" class="underline-input flex-1" />
                </div>
                <div class="flex items-start">
                    <span class="font-bold w-56">Scope of Work:</span>
                    <textarea v-model="scopeOfWork" rows="3" class="underline-textarea"></textarea>
                </div>
                <div class="flex items-end">
                    <span class="font-bold w-56">Total Project Cost:</span>
                    <input v-model="totalCost" class="underline-input flex-1" />
                </div>
                <div class="flex items-end">
                    <span class="font-bold w-56">Professional In Charge:</span>
                    <input v-model="professionalInCharge" class="underline-input flex-1" />
                </div>
            </div>
    
            <!-- Signature -->
            <div class="mt-4 text-center">
                <input v-model="buildingOfficial" class="underline-input text-center font-bold" />
                <p>BUILDING OFFICIAL</p>
                <p class="italic text-sm">(Signature Over Printed Name)</p>
            </div>
    
            <!-- Footer -->
            <p class="mt-2 text-xs text-center">
                THIS PERMIT MAY BE CANCELLED OR REVOKED PURSANT TO SECTIONS 207, 305 AND 306 OF THE NATIONAL BUILDING CODE OF THE PHILIPPINES (PD 1096) AND ITS REVISED IRR
            </p>
        </div>
    
        <!-- Print Button -->
        <button @click="printPermit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded no-print">
            Print Permit
        </button>
    </div>
    </template>
    
    <style scoped>
    .underline-input {
        border: none;
        border-bottom: 1px solid black;
        outline: none;
        padding: 2px 4px;
        width: auto;
        background: transparent;
    }
    
    .underline-textarea {
        border: 1px solid black;
        padding: 2px 4px;
        width: 100%;
        resize: none;
    }
    
    /* LANDSCAPE PRINT */
    @media print {
        @page {
            size: landscape;
            margin: 0.5cm;
        }
    
        body * {
            visibility: hidden;
        }
    
        #permitContent, #permitContent * {
            visibility: visible;
        }
    
        #permitContent {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translateX(-50%) scale(0.9); /* shrink to fit 1 page */
            transform-origin: top center;
            width: 100%;
        }
    
        textarea {
            height: auto !important;
            overflow: hidden;
            font-size: 0.9em;
        }
    
        .no-print {
            display: none !important;
        }
    }
    </style>
    