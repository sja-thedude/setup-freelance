"use client"

import style from 'public/assets/css/cart.module.scss'
import { memo, useState } from 'react'
import 'react-responsive-carousel/lib/styles/carousel.min.css'
import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import CouponPopup from '@/app/[locale]/components/layouts/popup/coupon';
import { useAppSelector } from '@/redux/hooks'
import { usePathname } from 'next/navigation'

function CouponList({ coupons, color }: { coupons: any, color: string }) {
    const couping = style['coupingTable'];
    const couName = style['cou-name'];
    const couCode = style['cou-code'];
    const couponsList = style['coupons-list'];
    const doting = style['doting'];
    const stepTable = useAppSelector((state) => state.cart.stepTable)
    const pathName = usePathname()
    const isTableOrdering = pathName.includes('table-ordering');
    const [openPopups, setOpenPopups] = useState<{ [key: number]: boolean }>({});

    const toggleCouponPopup = (couponIndex: number) => {
        const updatedOpenPopups: { [key: number]: boolean } = { ...openPopups };
        updatedOpenPopups[couponIndex] = !updatedOpenPopups[couponIndex];
        setOpenPopups(updatedOpenPopups);
    };

    const settingCoupon = {
        className: "product-coupon-mobile-container",
        infinite: true,
        dots: true,
        autoplay: true,
        appendDots: (dots: any) => (
            <div>
                <ul className="coupon-slide-dot-mobile d-flex">
                    {dots.map((dot: any, index: any) => (
                        <div key={index}>
                            {dot}
                        </div>
                    ))}
                </ul>
            </div>
        ),
    }

    return (
        <>
            {!isTableOrdering ? (
                <>
                    <div className={`${couping}`}>
                        {coupons && coupons.length > 0 && (
                            <div className={`${couponsList} row d-flex`} style={{ backgroundColor: color ? color : 'white' }}>
                                <Slider {...settingCoupon} arrows={false}>
                                        {coupons.map((coupon: any, index: any) => (
                                            <div key={index} className={`${style.couponContainer} d-flex`} onClick={() => toggleCouponPopup(index)}>
                                                <div className={`${couName} d-flex`}>
                                                    <h2 className={`${style.coupons} ms-2`}>
                                                        {coupon.promo_name}
                                                    </h2>
                                                </div>
                                                <div className={`${couCode} d-flex`}>
                                                    <h2 className={`${style.code} me-2 text-uppercase`}>
                                                        {coupon.code}
                                                    </h2>
                                                </div>
                                            </div>
                                        ))}
                                    </Slider>
                            </div>
                        )}
                        {coupons && coupons.length > 0 && (
                            coupons.map((coupon: any, index: any) => (
                                openPopups[index] && (
                                    <CouponPopup
                                        key={index}
                                        color={color ? color : "black"}
                                        coupon={coupon}
                                        toggleCouponPopup={() => toggleCouponPopup(index)}
                                    />
                                )
                            ))
                        )}
                    </div>
                </>
            ) : (
                stepTable === 1 && (
                    <>
                        <div className={`${couping}`}>
                            {coupons && coupons.length > 0 && (
                                <div className={`${couponsList} row d-flex`} style={{ backgroundColor: color ? color : 'white' }}>
                                    <Slider {...settingCoupon} arrows={false}>
                                        {coupons.map((coupon: any, index: any) => (
                                            <div key={index} className={`${style.couponContainer} d-flex`} onClick={() => toggleCouponPopup(index)}>
                                                <div className={`${couName} d-flex`}>
                                                    <h2 className={`${style.coupons} ms-2`}>
                                                        {coupon.promo_name}
                                                    </h2>
                                                </div>
                                                <div className={`${couCode} d-flex`}>
                                                    <h2 className={`${style.code} me-2 text-uppercase`}>
                                                        {coupon.code}
                                                    </h2>
                                                </div>
                                            </div>
                                        ))}
                                    </Slider>
                                </div>
                            )}
                            {coupons && coupons.length > 0 && (
                                coupons.map((coupon: any, index: any) => (
                                    openPopups[index] && (
                                        <CouponPopup
                                            key={index}
                                            color={color ? color : "black"}
                                            coupon={coupon}
                                            toggleCouponPopup={() => toggleCouponPopup(index)}
                                        />
                                    )
                                ))
                            )}
                        </div>
                    </>
                )
            )}
        </>
    );
}

export default memo(CouponList)