export interface Workspace {
    id: number
    name: string
}

export interface GroupModel {
    id: number
    created_at: string
    updated_at: string
    workspace_id: number
    workspace: Workspace
    name: string
    company_name: any
    company_street: string
    company_number: string
    company_vat_number: any
    company_city: string
    company_postcode: string
    address_display: string
    payment_mollie: number
    payment_payconiq: number
    payment_cash: number
    payment_factuur: number
    close_time: string
    receive_time: string
    type: number
    type_display: string
    contact_email: string
    contact_name: string
    contact_surname: string
    contact_gsm: string
    active: number
    is_product_limit: number
    discount_type: number
    discount: number
    percentage: number
}

