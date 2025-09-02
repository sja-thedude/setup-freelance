'use client'
import React, { useState } from 'react';
import variables from '/public/assets/css/food.module.scss'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
// import { colors } from '@mui/material';
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { usePathname } from 'next/navigation'
import { addStepTable, addStepSelfOrdering } from '@/redux/slices/cartSlice'
import style from 'public/assets/css/cart.module.scss';

// const head = variables['head'];
// const backImage = variables['back-image'];
const title = variables['titlingTable'];
const centeredText = variables['centered-text'];
// const moreInfo = variables['moreInfo']

export default function TableOrderHeader({ workspaceId, origin, step }: { workspaceId: any, origin: any, step: any }) {
    const stepTable = useAppSelector((state) => state.cart.stepTable)
    const pathName = usePathname()
    // Get workspace info
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    // var photo = apiDataToken?.data?.gallery ? apiDataToken?.data?.gallery[0].full_path : null;
    const isTableOrdering = pathName.includes('table-ordering');
    // const isSelfOrdering = pathName.includes('self-ordering');
    // Hàm xử lý sự kiện click để mở hoặc đóng popup
    // const togglePopup = () => {
    //     setIsPopupOpen(!isPopupOpen);
    // };

    const dispatch = useAppDispatch()

    const handleStepping = () => {
        if (stepTable > 1) {
            dispatch(addStepTable(stepTable - 1))
        }
    }

    const handleBack = () => {
        if (step > 1) {
            dispatch(addStepSelfOrdering(Number(step) - 1))
        }
    }

    return (
        <div className="header-table-ordering" style={{
            backgroundColor: apiDataToken?.data?.setting_generals?.primary_color
        }}>
            <div className="header-table-ordering-wrapper">
                <div className={`${title} d-flex align-items-center`}>
                    {origin === 'self_ordering' && step != 1 && (
                        <div className={style.backBut} onClick={handleBack}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none">
                                <path d="M19.375 23.25L11.625 15.5L19.375 7.75" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                    )}
                    {origin === 'table_ordering' && stepTable > 1 && (
                        <div className={style.backBut} onClick={handleStepping}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none">
                                <path d="M19.375 23.25L11.625 15.5L19.375 7.75" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                    )}
                    <h1 className={`${centeredText} h1-title`} style={{ left: isTableOrdering && stepTable > 1 ? '48px' : '' }}>
                        {apiDataToken ? apiDataToken?.data?.setting_generals?.title : ''}
                    </h1>
                </div>
            </div>
        </div>
    )
}