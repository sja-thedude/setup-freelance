
export interface Workspace {
    id: number
    name: string
}

export interface Product {
    id: number
    name: string
}

export interface CouponDetailModal {
    id: number
    created_at: string
    updated_at: string
    code: string
    promo_name: string
    workspace_id: number
    workspace: Workspace
    max_time_all: number
    max_time_single: number
    currency: string
    discount: string
    expire_time: string
    discount_type: number
    percentage: number
    categories: any[]
    products: Product[]
}
