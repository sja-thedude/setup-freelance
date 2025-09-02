
export interface Timeslot {
    id: number
    type: number
    type_display: string
    active: boolean
    time: string
    max_order: number
    max_price: string
    date: string
    day_number: number
    current_order: number
    current_price: number
}

export interface TimeSlotModel {
    id: number
    type: number
    type_display: string
    order_per_slot: number
    max_price_per_slot: string
    interval_slot: number
    max_mode: boolean
    max_time: string
    max_before: number
    max_days: number[]
    timeslots: Timeslot[]
}
