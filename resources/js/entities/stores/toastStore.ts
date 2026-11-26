import { defineStore } from 'pinia';
import { ref } from 'vue';

import type { ToastItem, ToastVariant } from '@/shared/lib/toast';

const DISMISS_MS = 4000;

let idCounter = 0;

const nextId = (): string => {
    idCounter += 1;

    return `toast-${idCounter}`;
};

export const useToastStore = defineStore('appToast', () => {
    const items = ref<ToastItem[]>([]);

    const dismiss = (id: string): void => {
        items.value = items.value.filter((item) => item.id !== id);
    };

    const push = (message: string, variant: ToastVariant): void => {
        const id = nextId();

        items.value.push({ id, message, variant });

        window.setTimeout(() => {
            dismiss(id);
        }, DISMISS_MS);
    };

    const success = (message: string): void => {
        push(message, 'success');
    };

    const error = (message: string): void => {
        push(message, 'error');
    };

    return {
        items,
        push,
        success,
        error,
        dismiss,
    };
});