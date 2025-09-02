export interface RestaurantSettingPreferenceModel {
    id: number
    created_at: string
    updated_at: string
    takeout_min_time: number
    takeout_day_order: number
    delivery_min_time: number
    delivery_day_order: number
    mins_before_notify: number
    use_sms_whatsapp: boolean
    use_email: boolean
    receive_notify: boolean
    sound_notify: boolean
    option_id: any
    holiday_text: string
}
