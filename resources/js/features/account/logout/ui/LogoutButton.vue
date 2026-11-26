<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

import { logout } from '@/routes';
import { Button } from '@/shared/ui';

const loggingOut = ref(false);

const submit = (): void => {
    loggingOut.value = true;

    router.post(
        logout.url(),
        {},
        {
            onFinish: () => {
                loggingOut.value = false;
            },
        },
    );
};
</script>

<template>
    <Button
        type="button"
        :disabled="loggingOut"
        @click="submit"
    >
        Выйти
    </Button>
</template>