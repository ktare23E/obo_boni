<!-- Components/SlideModal.vue -->
<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;
            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';
            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();
        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});
</script>



<template>
    <dialog class="z-50 m-0 bg-transparent backdrop:bg-transparent" ref="dialog">
        <div class="fixed inset-0 z-50 flex justify-end items-end px-4 py-6 sm:px-6">
            <!-- Overlay -->
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 bg-black bg-opacity-50"
                    @click="close"
                />
            </Transition>

            <!-- Sliding modal -->
            <Transition
                enter-active-class="transform transition ease-out duration-300"
                enter-from-class="translate-y-10 translate-x-10 opacity-0"
                enter-to-class="translate-y-0 translate-x-0 opacity-100"
                leave-active-class="transform transition ease-in duration-200"
                leave-from-class="translate-y-0 translate-x-0 opacity-100"
                leave-to-class="translate-y-10 translate-x-10 opacity-0"
            >
                <div
                    v-show="show"
                    class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-md"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>
