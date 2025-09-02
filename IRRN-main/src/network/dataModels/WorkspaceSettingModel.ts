
export interface Meta {
    id: number
    active: boolean
    order: number
    workspace_app_id: number
    default: boolean
    key?: string
    name: string
    type: number
    title?: string
    description?: string
    content: any
    icon: any
    url?: string
    meta_data: any
}

export interface WorkspaceSettingModel {
    id: number
    theme: number
    meta: Meta[]
}
