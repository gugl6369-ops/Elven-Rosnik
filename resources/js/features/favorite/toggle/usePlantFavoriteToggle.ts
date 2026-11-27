import { router } from '@inertiajs/vue3';
import { ref, watch, type Ref } from 'vue';

import { useAppToast } from '@/shared/lib/toast';

function favoriteUrl(plantId: number): string {
    return `/plants/${plantId}/favorite`;
}

export function usePlantFavoriteToggle(
    plantId: number,
    isFavorited: Ref<boolean>,
) {
    const toast = useAppToast();
    const favorited = ref(isFavorited.value);
    const busy = ref(false);

    watch(isFavorited, (value) => {
        favorited.value = value;
    });

    const toggle = (): void => {
        if (busy.value) {
            return;
        }

        busy.value = true;
        const wasFavorited = favorited.value;
        favorited.value = !wasFavorited;

        const options = {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                favorited.value = wasFavorited;
                toast.error('Не удалось обновить избранное');
            },
            onFinish: () => {
                busy.value = false;
            },
        };

        if (wasFavorited) {
            router.delete(favoriteUrl(plantId), options);
        } else {
            router.post(favoriteUrl(plantId), {}, options);
        }
    };

    return { favorited, busy, toggle };
}