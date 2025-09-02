
export interface Workspace {
    id: number
    name: string
}

export interface Workspace2 {
    id: number
    name: string
}

export interface Category {
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
    role_id: number
    email: string
    email_tmp: string
    password_tmp: any
    is_super_admin: number
    is_admin: number
    active: number
    is_verified: number
    verify_token: string
    verify_expired_at: any
    platform: number
    name: string
    first_name: string
    last_name: string
    photo: string
    description: any
    birthday: string
    gender: number
    address: string
    lng: string
    lat: string
    phone: any
    last_login: any
    workspace_id: any
    credit: number
    first_login: any
    status: number
    gsm: string
    last_session: string
    locale: string
    timezone: any
    api_token: string
    fb_id: any
    fb_token: any
    gg_id: any
    gg_token: any
    tw_id: any
    tw_token: any
    created_at: string
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
    category: Category
    vat_id: number
    vat: Vat
    currency: string
    price: string
    use_category_option: number
    time_no_limit: number
    is_suggestion: any
    order: number
    photo?: string
    photo_path?: string
    allergenens: Allergenen[]
    labels: Label[]
    liked: boolean
    productFavorites: ProductFavorite[]
}

export interface ProductSectionModel {
    id: number
    created_at: string
    updated_at: string
    name: string
    description: any
    workspace_id: number
    workspace: Workspace
    available_delivery: boolean
    favoriet_friet: boolean
    kokette_kroket: boolean
    order: number
    photo: string
    photo_path: string
    products: Product[]
}