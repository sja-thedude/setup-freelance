export interface Workspace {
    id: number;
    name: string;
    title: string;
}

export interface User {
    id: number;
    email: string;
    name: string;
    first_name: string;
    last_name: string;
}

export interface Workspace2 {
    id: number;
    name: string;
    title: string;
}

export interface Category {
    id: number;
    name: string;
    favoriet_friet: boolean;
    kokette_kroket: boolean;
}

export interface Vat {
    id: number;
    name: string;
    in_house: number;
    take_out: number;
    delivery: number;
    country_id: number;
    created_at: string;
    updated_at: string;
}

export interface Allergenen {
    id: number;
    icon: string;
    type: number;
    type_display: string;
}

export interface Label {
    id: number;
    type: number;
    type_display: any;
}

export interface Workspace3 {
    id: number;
    name: string;
}

export interface OptionItem2 {
    id: number;
    deleted_at: any;
    name: string;
    price: string;
    currency: string;
    available: boolean;
    master: boolean;
    order: number;
    type: number;
    type_display: string;
}

export interface OptionItem {
    option_item_id: number;
    option_item: OptionItem2;
}

export interface Pivot {
    product_id: number;
    user_id: number;
}

export interface ItemOption {
    id: number;
    deleted_at: any;
    name: string;
    price: string;
    currency: string;
    available: boolean;
    master: boolean;
    order: number;
}

export interface Option2 {
    id: number;
    deleted_at: any;
    workspace_id: number;
    workspace: Workspace3;
    name: string;
    min: number;
    max: number;
    type: number;
    type_display: string;
    is_ingredient_deletion: boolean;
    order: number;
    items: ItemOption[];
}

export interface Option {
    option_id: number;
    option: Option2;
    option_items: OptionItem[];
}

export interface ProductFavorite {
    id: number;
    role_id: number;
    email: string;
    email_tmp: string;
    password_tmp: any;
    is_super_admin: number;
    is_admin: number;
    active: number;
    is_verified: number;
    verify_token?: string;
    verify_expired_at?: string;
    platform: number;
    name: string;
    first_name: string;
    last_name: string;
    photo: string;
    description: any;
    birthday: any;
    gender?: number;
    address?: string;
    lng?: string;
    lat?: string;
    phone: any;
    last_login: any;
    workspace_id: any;
    credit: number;
    first_login: any;
    status: number;
    gsm: string;
    last_session: any;
    locale: string;
    timezone: any;
    api_token: string;
    fb_id: any;
    fb_token: any;
    gg_id: any;
    gg_token: any;
    tw_id: any;
    tw_token: any;
    created_at: string;
    updated_at: string;
    deleted_at: any;
    pivot: Pivot;
}

export interface Product {
    id: number;
    created_at: string;
    updated_at: string;
    deleted_at: any;
    active: boolean;
    name: string;
    description: any;
    workspace_id: number;
    workspace: Workspace2;
    category_id: number;
    category: Category;
    vat_id: number;
    vat: Vat;
    currency: string;
    price: string;
    use_category_option: number;
    time_no_limit: number;
    is_suggestion: any;
    order: number;
    photo: string;
    photo_path: string;
    allergenens: Allergenen[];
    labels: Label[];
    productFavorites: ProductFavorite[];
}

export interface Item {
    product_id: number;
    product: Product;
    price: string;
    quantity: number;
    subtotal: string;
    total_price: string;
    vat_percent: string;
    paid: boolean;
    coupon_id: any;
    coupon: any;
    coupon_discount: any;
    redeem_history_id: any;
    redeem_history: any;
    redeem_discount: any;
    group_discount: any;
    available_discount: boolean;
    options: Option[];
}

export interface SettingDeliveryCondition {
    id: number;
    area_start: number;
    area_end: number;
    price_min: number;
    price: number;
    free: number;
    workspace_id: number;
    created_at: string;
    updated_at: string;
}

export interface Group {
    address_display: string
    discount: number
    discount_type: number
    id: number
    is_product_limit: number
    name: string
    percentage: any,
    active: number
}

export interface OrderDetailModel {
    id: number;
    created_at: string;
    updated_at: string;
    date_time: string;
    extra_code: number;
    code: string;
    date: string;
    time: string;
    workspace_id: number;
    workspace: Workspace;
    user_id: number;
    is_test_account: boolean;
    user: User;
    parent_id: any;
    parent: any;
    parent_code: string;
    group_id: any;
    group: Group;
    payment_method: number;
    payment_method_display: string;
    payment_status: any;
    payment_status_display: string;
    address: string;
    address_type: number;
    lat: string;
    lng: string;
    type: number;
    note: string;
    subtotal: string;
    total_price: string;
    total_paid: string;
    currency: string;
    status: number;
    status_display: string;
    items_count: any;
    group_discount: any;
    items: Item[];
    coupon_id: any;
    coupon: any;
    coupon_discount: any;
    redeem_history_id: any;
    redeem_history: any;
    redeem_discount: any;
    ship_price: string;
    setting_delivery_condition_id: number;
    setting_delivery_condition: SettingDeliveryCondition;
    service_cost: string;
}
