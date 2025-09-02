export interface Workspace {
    id: number
    name: string
}

export interface Item {
    id: number
    deleted_at: any
    name: string
    price: string
    currency: string
    available: boolean
    master: boolean
    order: number
}

export interface ProductOptionModel {
    id: number
    deleted_at: any
    workspace_id: number
    workspace: Workspace
    name: string
    min: number
    max: number
    type: number
    type_display: string
    is_ingredient_deletion: boolean
    order: number
    items: Item[]
    optionOrder: number
    index: number
    isWarning: boolean
}
