export type AuthUser = {
    username: string;
    name: string | null;
    status: string | null;
    avatar: string | null;
};

/** Profile fields for continue-registration (form + persisted draft). */
export type ContinueRegisterProfileFields = {
    username: string;
    name: string;
    status: string;
};

export type ContinueRegisterFormValues = ContinueRegisterProfileFields;

export type ContinueRegisterDraftFields = ContinueRegisterProfileFields;

export type AuthPageProps = {
    auth: {
        user: AuthUser | null;
    };
};