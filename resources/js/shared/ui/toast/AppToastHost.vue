<script setup lang="ts">
import { useAppToast, type ToastVariant } from '@/shared/lib/toast';

const { items, dismiss } = useAppToast();

const variantClass = (variant: ToastVariant): string =>
    variant === 'success'
        ? 'border-[#21483f] bg-[#21483f] text-amber-50'
        : 'border-red-600 bg-white text-red-600';
</script>

<template>
    <div
        class="pointer-events-none fixed inset-x-0 bottom-8 z-[100] flex flex-col items-center gap-3 px-4"
        aria-live="polite"
    >
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <button
                v-for="item in items"
                :key="item.id"
                type="button"
                class="pointer-events-auto max-w-md w-full rounded-[18px] border-2 px-5 py-3 text-center text-sm shadow-md"
                :class="variantClass(item.variant)"
                @click="dismiss(item.id)"
            >
                {{ item.message }}
            </button>
        </TransitionGroup>
    </div>
</template>