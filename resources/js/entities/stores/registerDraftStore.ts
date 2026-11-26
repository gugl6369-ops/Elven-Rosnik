import { defineStore } from 'pinia';
import { ref } from 'vue';

import type { RegisterStep1DraftFields } from '@/entities/user';

export const REGISTER_DRAFT_EMAIL_STORAGE_KEY = 'elfi-register-draft-email';

const PASSWORD_SESSION_KEY = 'elfi-register-draft-password';
const PASSWORD_CONFIRM_SESSION_KEY =
    'elfi-register-draft-password-confirmation';

const readSession = (key: string): string => {
    if (typeof sessionStorage === 'undefined') {
        return '';
    }

    return sessionStorage.getItem(key) ?? '';
};

const writeSession = (key: string, value: string): void => {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    if (value.trim() === '') {
        sessionStorage.removeItem(key);

        return;
    }

    sessionStorage.setItem(key, value);
};

const clearSessionSecrets = (): void => {
    writeSession(PASSWORD_SESSION_KEY, '');
    writeSession(PASSWORD_CONFIRM_SESSION_KEY, '');
};

export const useRegisterDraftStore = defineStore(
    'registerDraft',
    () => {
        const email = ref('');
        const password = ref('');
        const password_confirmation = ref('');

        const hydrateSecretsFromSession = (): void => {
            password.value = readSession(PASSWORD_SESSION_KEY);
            password_confirmation.value = readSession(
                PASSWORD_CONFIRM_SESSION_KEY,
            );
        };

        const syncStep1 = (
            fields: Partial<RegisterStep1DraftFields>,
        ): void => {
            if (fields.email !== undefined) {
                email.value = fields.email;
            }

            if (fields.password !== undefined) {
                password.value = fields.password;
                writeSession(PASSWORD_SESSION_KEY, fields.password);
            }

            if (fields.password_confirmation !== undefined) {
                password_confirmation.value = fields.password_confirmation;
                writeSession(
                    PASSWORD_CONFIRM_SESSION_KEY,
                    fields.password_confirmation,
                );
            }
        };

        const hasStep1 = (): boolean =>
            email.value.trim() !== '' &&
            password.value !== '' &&
            password_confirmation.value !== '';

        const clear = (): void => {
            email.value = '';
            password.value = '';
            password_confirmation.value = '';
            clearSessionSecrets();
        };

        hydrateSecretsFromSession();

        return {
            email,
            password,
            password_confirmation,
            syncStep1,
            hasStep1,
            clear,
            hydrateSecretsFromSession,
        };
    },
    {
        persist: {
            key: REGISTER_DRAFT_EMAIL_STORAGE_KEY,
            storage: localStorage,
            pick: ['email'],
        },
    },
);