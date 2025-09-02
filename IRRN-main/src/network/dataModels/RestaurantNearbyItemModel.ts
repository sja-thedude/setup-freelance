
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

export interface OpenTimeSlotShort {
    day_number_display: string
    start_time: string
    end_time: string
    day_number: number
    status: number
    id: number
}

export interface SettingOpenHourShort {
    type: number
    type_display: string
    active: boolean
    timeslots: OpenTimeSlotShort[]
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
    opties_id?: number
    holiday_text?: string
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

export interface SettingDeliveryMinCondition {
    price_min: string
    price: string
    free: string
    delivery_min_time: number
}

export interface SettingGenerals {
    id: number
    workspace_id: number
    title?: string
    subtitle?: string
    primary_color?: string
    second_color?: string
    created_at: string
    updated_at: string
    instellingen?: string
}

export interface RestaurantNearbyItemModel {
    id: number
    name: string
    gsm: string
    address: string
    email: string
    country_id: number
    country: Country
    lat?: string
    lng?: string
    is_online: boolean
    is_test_mode: boolean
    distance: number
    photo?: string
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
    favoriet_friet: boolean
    kokette_kroket: boolean
    is_open: boolean
}