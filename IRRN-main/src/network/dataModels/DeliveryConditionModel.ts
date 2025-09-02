
export interface Workspace {
    id: number
    name: string
}

export interface DeliveryConditionModel {
    id: number
    area_start: number
    area_end: number
    price_min: string
    price: string
    free: string
    workspace_id: number
    workspace: Workspace
}
