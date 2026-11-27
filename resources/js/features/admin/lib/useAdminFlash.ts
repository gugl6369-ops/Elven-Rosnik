import { router, usePage } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';

import { useAppToast } from '@/shared/lib/toast';

type FlashProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

export function useAdminFlash(): void {
    const page = usePage<FlashProps>();
    const toast = useAppToast();

    const showFlash = (): void => {
        const success = page.props.flash?.success;
        const error = page.props.flash?.error;

        if (typeof success === 'string' && success.trim() !== '') {
            toast.success(success);
        }

        if (typeof error === 'string' && error.trim() !== '') {
            toast.error(error);
        }
    };

    onMounted(showFlash);

    watch(
        () => page.props.flash,
        () => showFlash(),
        { deep: true },
    );

    router.on('finish', () => {
        showFlash();
    });
}