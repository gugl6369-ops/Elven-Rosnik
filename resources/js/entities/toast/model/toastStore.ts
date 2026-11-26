import { defineStore } from 'pinia';

import type { ToastItem, ToastVariant } from './types';

const DISMISS_MS = 4000;

let idCounter = 0;

const nextId = (): string => {
    idCounter += 1;

    return `toast-${idCounter}`;
};

export const useToastStore = defineStore('appToast', {
    state: () => ({
        items: [] as ToastItem[],
    }),
    actions: {
        push(message: string, variant: ToastVariant): void {
            const id = nextId();

            this.items.push({ id, message, variant });

            window.setTimeout(() => {
                this.dismiss(id);
            }, DISMISS_MS);
        },
        success(message: string): void {
            this.push(message, 'success');
        },
        error(message: string): void {
            this.push(message, 'error');
        },
        dismiss(id: string): void {
            this.items = this.items.filter((item) => item.id !== id);
        },
    },
});