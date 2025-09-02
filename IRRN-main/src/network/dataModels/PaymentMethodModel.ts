export interface PaymentMethodModel {
    id: number
    type: number
    type_display: string
    api_token?: string
    takeout: boolean
    delivery: boolean
    in_house: boolean
}
