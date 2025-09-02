export type UserDataModel = {
    id: number;
    created_at: string;
    updated_at: string;
    name: string;
    first_name: string;
    last_name: string;
    email: string;
    description: string;
    phone: string;
    username: string;
    birthday: string;
    gender: any;
    gender_display: string;
    photo: string;
    address: string;
    lng: string;
    lat: string;
    last_login: string;
    token?: string;
    locale?: string;
    timezone?: string;
    gsm?: string;
    first_login?: boolean; // only in login social
} | null;

export interface ResponseCommon<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface ModalFilterItem {
    title: string;
    value: string | number;
}

export interface ResponseList<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: any;
    path: string;
    per_page: number;
    prev_page_url: any;
    to: number;
    total: number;
}
