
export interface RewardData {
    id: number
    workspace_id: number
    title: string
    description: string
    type: number
    score: number
    reward: string
    expire_date: string
    repeat: number
    created_at: string
    updated_at: string
    discount_type: number
    percentage: any
    photo: any
}

export interface User {
    id: number
    created_at: string
    updated_at: string
    name: string
    first_name: string
    last_name: string
    email: string
    description: any
    phone: any
    gsm: string
    birthday: any
    gender: number
    gender_display: string
    photo: string
    address: string
    lng: string
    lat: string
    last_login: any
    locale: string
    timezone: any
}

export interface MyRedeemModel {
    id: number
    created_at: string
    updated_at: string
    loyalty_id: number
    reward_level_id: number
    reward_level_type: number
    reward_data: RewardData
    user: User
}