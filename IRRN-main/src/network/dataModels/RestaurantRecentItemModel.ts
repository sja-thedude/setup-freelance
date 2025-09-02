export interface Country {
    id: number
    name: string
}

export interface Gallery {
    id: number
    foreign_id: number
    foreign_model: string
    foreign_type: string
    field_name: any
    file_name: string
    file_type: string
    file_size: string
    file_path: string
    full_path: string
    created_at: string
    updated_at: string
    active: number
    order?: number
}

export interface ApiGallery {
    id: number
    created_at: string
    updated_at: string
    file_name: string
    file_type: string
    file_size: string
    file_path: string
    full_path: string
    active: number
    order: number
}

export interface Category {
    id: number
    name: string
}

export interface Extra {
    id: number
    workspace_id: number
    active: boolean
    type: number
    type_display: string
}

export interface OpenTimeSlot {
    id: number
    workspace_id: number
    foreign_id: number
    foreign_model: string
    start_time: string
    end_time: string
    created_at: string
    updated_at: string
    day_number: number
    status: number
}

export interface SettingOpenHour {
    id: number
    created_at: string
    updated_at: string
    type: number
    active: boolean
    workspace_id: number
    open_time_slots: OpenTimeSlot[]
}

export interface SettingPreference {
    id: number
    workspace_id: number
    takeout_min_time: number
    takeout_day_order: number
    delivery_min_time: number
    delivery_day_order: number
    mins_before_notify: number
    use_sms_whatsapp: boolean
    use_email: boolean
    receive_notify: boolean
    sound_notify: boolean
    opties_id: any
    holiday_text: string
    created_at: string
    updated_at: string
}

export interface SettingDeliveryCondition {
    id: number
    area_start: number
    area_end: number
    price_min: number
    price: number
    free: number
    workspace_id: number
    created_at: string
    updated_at: string
}

export interface SettingGenerals {
    id: number
    workspace_id: number
    title: string
    subtitle: string
    primary_color: string
    second_color: string
    created_at: string
    updated_at: string
    instellingen: string
}

export interface Workspace {
    id: number
    name: string
}

export interface User {
    id: number
    email: string
    name: string
    first_name: string
    last_name: string
}

export interface Workspace3 {
    id: number
    name: string
}

export interface Option2 {
    id: number
    deleted_at: any
    workspace_id: number
    workspace: Workspace3
    name: string
    min: number
    max: number
    type: number
    type_display: string
    is_ingredient_deletion: boolean
    order: number
}
export interface OptionItem2 {
    id: number
    deleted_at: any
    name: string
    price: string
    currency: string
    available: boolean
    master: boolean
    order: number
    type: number
    type_display: string
}

export interface OptionItem {
    option_item_id: number
    option_item: OptionItem2
}

export interface Option {
    option_id: number
    option: Option2
    option_items: OptionItem[]
}

export interface Workspace2 {
    id: number
    name: string
}

export interface Category2 {
    id: number
    name: string
    favoriet_friet: boolean
    kokette_kroket: boolean
}

export interface Vat {
    id: number
    name: string
    in_house: number
    take_out: number
    delivery: number
    country_id: number
    created_at: string
    updated_at: string
}

export interface Allergenen {
    id: number
    icon: string
    type: number
    type_display: string
}

export interface Label {
    id: number
    type: number
    type_display: any
}

export interface Pivot {
    product_id: number
    user_id: number
}

export interface ProductFavorite {
    id: number
    role_id?: number
    email: string
    email_tmp?: string
    password_tmp: any
    is_super_admin: number
    is_admin: number
    active: number
    is_verified: number
    verify_token?: string
    verify_expired_at?: string
    platform: number
    name: string
    first_name: string
    last_name: string
    photo: string
    description: any
    birthday?: string
    gender?: number
    address?: string
    lng?: string
    lat?: string
    phone: any
    last_login?: string
    workspace_id: any
    credit: number
    first_login?: string
    status: number
    gsm: string
    last_session?: string
    locale: string
    timezone: any
    api_token: string
    fb_id: any
    fb_token: any
    gg_id: any
    gg_token: any
    tw_id: any
    tw_token: any
    created_at?: string
    updated_at: string
    deleted_at: any
    pivot: Pivot
}

export interface Product {
    id: number
    created_at: string
    updated_at: string
    deleted_at: any
    active: boolean
    name: string
    description?: string
    workspace_id: number
    workspace: Workspace2
    category_id: number
    category: Category2
    vat_id: number
    vat: Vat
    currency?: string
    price: string
    use_category_option: number
    time_no_limit: number
    is_suggestion: any
    order: number
    photo: string
    photo_path: string
    allergenens: Allergenen[]
    labels: Label[]
    productFavorites: ProductFavorite[]
}

export interface Item {
    product_id: number
    product: Product
    price: string
    quantity: number
    subtotal: string
    total_price: string
    vat_percent: string
    paid: boolean
    coupon_id: any
    coupon: any
    coupon_discount: any
    redeem_history_id: any
    redeem_history: any
    redeem_discount: any
    group_discount: any
    available_discount: boolean
    options: Option[]
}

export interface LatestOrder {
    id: number
    created_at: string
    updated_at: string
    date_time: string
    code: string
    date: string
    time: string
    workspace_id: number
    workspace: Workspace
    user_id: number
    is_test_account: boolean
    user: User
    parent_id: any
    parent: any
    parent_code: string
    group_id: any
    group: any
    payment_method: number
    payment_method_display: string
    payment_status: any
    payment_status_display: string
    address: any
    address_type: number
    lat: any
    lng: any
    type: number
    note: any
    subtotal: string
    total_price: string
    total_paid: string
    currency?: string
    status: number
    status_display: string
    items_count: any
    group_discount: any
    items: Item[]
    coupon_id: any
    coupon: any
    coupon_discount: any
    redeem_history_id: any
    redeem_history: any
    redeem_discount: any
    setting_delivery_condition_id: any
    setting_delivery_condition: any
}

export interface RestaurantRecentItemModel {
    id: number
    name: string
    gsm: string
    address: string
    email: string
    country_id: number
    country: Country
    lat: string
    lng: string
    is_online: boolean
    is_test_mode: boolean
    distance: number
    photo: string
    gallery: Gallery[]
    api_gallery: ApiGallery[]
    categories: Category[]
    extras: Extra[]
    setting_open_hours: SettingOpenHour[]
    setting_preference: SettingPreference
    setting_delivery_conditions: SettingDeliveryCondition[]
    setting_generals: SettingGenerals
    address_line_1?: string
    address_line_2?: string
    facebook_enabled: number
    facebook_id: any
    facebook_key: any
    google_enabled: number
    google_id?: string
    google_key?: string
    apple_enabled: number
    apple_id?: string
    apple_key?: string
    btw_nr: string
    latest_order: LatestOrder
    favoriet_friet: boolean
    kokette_kroket: boolean
    is_open: boolean
}
