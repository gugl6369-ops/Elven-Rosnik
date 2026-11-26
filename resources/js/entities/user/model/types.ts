/** Serialized `app.user` / shared `auth.user` (no password). */
export type AuthUser = {
    id: number;
    username: string;
    name: string | null;
    email: string | null;
    status: string | null;
    avatar: string | null;
    is_admin?: boolean;
    pole_id?: number | null;
    created_at?: string | null;
};

export type AccountPageProps = {
    user: AuthUser;
    auth: {
        user: AuthUser | null;
    };
};

/** Step 1 registration fields held client-side until final POST. */
export type RegisterStep1DraftFields = {
    email: string;
    password: string;
    password_confirmation: string;
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