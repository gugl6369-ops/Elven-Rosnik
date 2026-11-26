import { defineStore } from 'pinia';

import type { ContinueRegisterDraftFields } from './types';

const emptyDraft = (): ContinueRegisterDraftFields => ({
    username: '',
    name: '',
    status: '',
});

export const CONTINUE_REGISTER_DRAFT_STORAGE_KEY =
    'elfi-continue-register-draft';

export const useContinueRegisterDraftStore = defineStore(
    'continueRegisterDraft',
    {
        state: (): ContinueRegisterDraftFields => emptyDraft(),
        actions: {
            syncFrom(fields: Partial<ContinueRegisterDraftFields>): void {
                if (fields.username !== undefined) {
                    this.username = fields.username;
                }

                if (fields.name !== undefined) {
                    this.name = fields.name;
                }

                if (fields.status !== undefined) {
                    this.status = fields.status;
                }
            },
            hasDraft(): boolean {
                return (
                    this.username.trim() !== '' ||
                    this.name.trim() !== '' ||
                    this.status.trim() !== ''
                );
            },
            clear(): void {
                this.$reset();
            },
        },
        persist: {
            key: CONTINUE_REGISTER_DRAFT_STORAGE_KEY,
            storage: localStorage,
            pick: ['username', 'name', 'status'],
        },
    },
);