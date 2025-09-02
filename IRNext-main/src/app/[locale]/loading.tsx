"use client"

import { useAppSelector } from '@/redux/hooks'

export default function Loading() {
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    // You can add any UI inside Loading, including a Skeleton.
    return (
        <div className="container">
            <div className="row modal-backdrop show justify-content-center align-items-center"
                 style={{left: "auto", background: "#d8d8d8"}}>
                <div style={{opacity: 1, color: color ?? '#B5B268'}} className="spinner-border" role="status"></div>
            </div>
        </div>
    )
}