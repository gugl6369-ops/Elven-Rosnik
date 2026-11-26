import { defineStore } from 'pinia';
import { ref } from 'vue';

import type { ContinueRegisterDraftFields } from '@/entities/user';

export const CONTINUE_REGISTER_DRAFT_STORAGE_KEY =
    'elfi-continue-register-draft';

export const useContinueRegisterDraftStore = defineStore(
    'continueRegisterDraft',
    () => {
        const username = ref('');
        const name = ref('');
        const status = ref('');

        const syncFrom = (
            fields: Partial<ContinueRegisterDraftFields>,
        ): void => {
            if (fields.username !== undefined) {
                username.value = fields.username;
            }

            if (fields.name !== undefined) {
                name.value = fields.name;
            }

            if (fields.status !== undefined) {
                status.value = fields.status;
            }
        };

        const hasDraft = (): boolean =>
            username.value.trim() !== '' ||
            name.value.trim() !== '' ||
            status.value.trim() !== '';

        const clear = (): void => {
            username.value = '';
            name.value = '';
            status.value = '';
        };

        return {
            username,
            name,
            status,
            syncFrom,
            hasDraft,
            clear,
        };
    },
    {
        persist: {
            key: CONTINUE_REGISTER_DRAFT_STORAGE_KEY,
            storage: localStorage,
            pick: ['username', 'name', 'status'],
        },
    },
);