"use client"

import { memo, useState, useEffect } from 'react'
import style from 'public/assets/css/cart.module.scss'

function Loyalty({ color }: { color: any }) {
    const [isWhiteBackground, setIsWhiteBackground] = useState(false);

    useEffect(() => {
      const intervalId = setInterval(() => {
        // Đảo ngược trạng thái của biến cờ để thay đổi màu nền và màu icon
        setIsWhiteBackground((prev) => !prev);
      }, 1000);
  
      return () => clearInterval(intervalId);
    }, []); 
    const backgroundColorConfig = isWhiteBackground ? 'white' : color;
    const svgColor = isWhiteBackground ? color : 'white';
    const border = isWhiteBackground ? `2px solid ${color}` : 'none';

    return (
        <>
            <div className={`${style.boxDesk} d-flex`} style={{ background: backgroundColorConfig, boxShadow: `${backgroundColorConfig} 0px 0px 10px 0px`, border }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none" style={{ color: svgColor }}>
                    <g clipPath="url(#clip0_6525_19300)">
                        <path d="M15 18.75C19.8325 18.75 23.75 14.8325 23.75 10C23.75 5.16751 19.8325 1.25 15 1.25C10.1675 1.25 6.25 5.16751 6.25 10C6.25 14.8325 10.1675 18.75 15 18.75Z" stroke={svgColor} strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M10.2625 17.3621L8.75 28.7496L15 24.9996L21.25 28.7496L19.7375 17.3496" stroke={svgColor} strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                    </g>
                    <defs>
                        <clipPath id="clip0_6525_19300">
                            <rect width="30" height="30" fill={svgColor} />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </>
    )
}

export default memo(Loyalty)