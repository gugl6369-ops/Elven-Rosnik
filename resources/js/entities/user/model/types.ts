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

export type RegisterStep1DraftFields = {
    email: string;
    password: string;
    password_confirmation: string;
};

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
