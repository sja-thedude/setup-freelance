import { User } from './RestaurantRecentItemModel';

export interface Gallery {
    id: number;
    created_at: string;
    updated_at: string;
    file_name: string;
    file_type: string;
    file_size: string;
    file_path: string;
    full_path: string;
    active: number;
    order?: number;
}

export interface LastRedeemHistory {
    id: number;
    created_at: string;
    updated_at: string;
    loyalty_id: number;
    reward_level_id: number;
}

export interface Reward {
    id: number;
    title: string;
    description: string;
    type: number;
    type_display: string;
    score: number;
    reward: string;
    expire_date: string;
    repeat: number;
    photo?: string;
    discount_type: number;
    percentage?: number;
    is_redeem: boolean;
    is_used: boolean;
    last_redeem_history?: LastRedeemHistory;
}

export interface Workspace {
    id: number;
    name: string;
    title: string;
    photo: string;
    gallery: Gallery[];
}

export interface Loyalty {
    id: number;
    created_at: string;
    updated_at: string;
    workspace_id: number;
    workspace: Workspace;
    user_id: number;
    user: User;
    point: number;
    reward_level_id: number;
    reward?: Reward;
    highest_point: number;
    rewards: Reward[];
}
