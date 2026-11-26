export type ToastVariant = 'success' | 'error';

export type ToastItem = {
    id: string;
    message: string;
    variant: ToastVariant;
};