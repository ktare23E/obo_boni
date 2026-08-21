<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
})

/**
 * OFFICIAL STATUS FLOW (ORDER MATTERS)
 */
const steps = [
    'Under Review of Admin',
    'For Inspection',
    'For Creating Permit',
    'For Permit Release',
    'Permit Released',
]

/**
 * Find current status index
 */
const currentStepIndex = steps.indexOf(props.application.status)

/**
 * Helpers
 */
const isCompleted = (index) => index < currentStepIndex
const isCurrent = (index) => index === currentStepIndex
</script>


<template>
    <Head title="Track Application" />

    <div class="min-h-screen bg-gray-100 py-10">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    Building Permit Application Tracking
                </h1>

                <p class="text-gray-600 mt-1">
                    Reference No:
                    <span class="font-semibold text-gray-900">
                        {{ application.reference_number }}
                    </span>
                </p>

                <p class="text-sm text-gray-500">
                Submitted on {{
                    new Date(application.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                    })
                }}
                </p>
            </div>

            <!-- STATUS BADGE -->
            <div class="mb-8">
                <span
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
                    :class="{
                        'bg-yellow-100 text-yellow-800': application.status !== 'Permit Released',
                        'bg-green-100 text-green-800': application.status === 'Permit Released'
                    }"
                >
                    Current Status: {{ application.status }}
                </span>
            </div>

            <!-- TIMELINE -->
            <div class="space-y-6">

                <div
                    v-for="(step, index) in steps"
                    :key="step"
                    class="flex items-start gap-4"
                >
                    <!-- ICON -->
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center border-2"
                            :class="{
                                'bg-green-500 border-green-500 text-white': isCompleted(index),
                                'bg-blue-500 border-blue-500 text-white animate-pulse': isCurrent(index),
                                'bg-white border-gray-300 text-gray-400': !isCompleted(index) && !isCurrent(index),
                            }"
                        >
                            <span v-if="isCompleted(index)">✓</span>
                            <span v-else-if="isCurrent(index)">•</span>
                        </div>

                        <!-- LINE -->
                        <div
                            v-if="index !== steps.length - 1"
                            class="w-px h-10"
                            :class="{
                                'bg-green-500': isCompleted(index),
                                'bg-gray-300': !isCompleted(index),
                            }"
                        ></div>
                    </div>

                    <!-- CONTENT -->
                    <div>
                        <h3
                            class="font-semibold"
                            :class="{
                                'text-gray-900': isCompleted(index) || isCurrent(index),
                                'text-gray-400': !isCompleted(index) && !isCurrent(index),
                            }"
                        >
                            {{ step }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            <span v-if="isCompleted(index)">
                                Completed
                            </span>
                            <span v-else-if="isCurrent(index)">
                                Currently in progress
                            </span>
                            <span v-else>
                                Pending
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- FOOTER ACTION -->
            <div class="mt-10 flex justify-between items-center">
                <Link
                    :href="route('welcome')"
                    class="text-sm text-gray-600 hover:underline"
                >
                    ← Back to Home
                </Link>

                <p class="text-xs text-gray-400 italic">
                    Please keep your reference number for future tracking.
                </p>
            </div>

        </div>
    </div>
</template>
