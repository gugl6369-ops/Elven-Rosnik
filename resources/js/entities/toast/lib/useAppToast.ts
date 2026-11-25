import { storeToRefs } from 'pinia';

import { useToastStore, type ToastVariant } from '@/entities/toast/model';

export function useAppToast() {
    const store = useToastStore();
    const { items } = storeToRefs(store);

    return {
        items,
        push: (message: string, variant: ToastVariant = 'error') =>
            store.push(message, variant),
        success: (message: string) => store.success(message),
        error: (message: string) => store.error(message),
        dismiss: (id: string) => store.dismiss(id),
    };
}