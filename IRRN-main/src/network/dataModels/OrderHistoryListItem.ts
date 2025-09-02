
export interface Workspace {
    id: number
    name: string
    title: string
}

export interface User {
    id: number
    email: string
    name: string
    first_name: string
    last_name: string
}

export interface OrderHistoryListItem {
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
    address: string
    address_type: number
    lat: string
    lng: string
    type: number
    note: string
    subtotal: string
    total_price: string
    total_paid: string
    currency: string
    status: number
    status_display: string
    items_count: number
    group_discount: any
    extra_code: number
}