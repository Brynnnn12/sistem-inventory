export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    phone_number?: string;
    email_verified_at?: string | null;
};

export type Auth = {
    user: User;
    assignedWarehouses: Array<{ id: number; name: string }>;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
