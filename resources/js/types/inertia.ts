export interface AuthUser {
    id: string;
    username: string;
    role: string;
}

export interface FlashMessages {
    success: string | null;
    error: string | null;
}

/** Props yang selalu di-share oleh HandleInertiaRequests ke setiap halaman. */
export interface SharedPageProps {
    auth: {
        user: AuthUser | null;
    };
    flash: FlashMessages;
    [key: string]: unknown;
}
