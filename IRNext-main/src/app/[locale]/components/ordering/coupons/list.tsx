"use client"

import style from 'public/assets/css/cart.module.scss'
import { memo , useState , useEffect } from 'react'
import 'react-responsive-carousel/lib/styles/carousel.min.css'
import Slider from 'react-slick'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'
import CouponPopup from '@/app/[locale]/components/layouts/popup/coupon';

function CouponList({ coupons, color }: { coupons: any, color: string }) {
    const couping = style['couping'];
    const couName = style['cou-name'];
    const couCode = style['cou-code'];
    const couponsList = style['coupons-list'];
    const doting = style['doting'];
    const [openPopups, setOpenPopups] = useState<{ [key: number]: boolean }>({});
    const toggleCouponPopup = (couponIndex: number) => {
        const updatedOpenPopups: { [key: number]: boolean } = { ...openPopups };
        updatedOpenPopups[couponIndex] = !updatedOpenPopups[couponIndex];
        setOpenPopups(updatedOpenPopups);
    };

    const settingCoupon = {
        infinite: false,
        dots: true,
        appendDots: (dots: any) => (
            <div style={{ textAlign: "center", width: "90%", position: 'relative', bottom: '0', color: '#FFFFFF', marginTop: "-20px", height: "0px" }}>
                <ul style={{ listStyle: "none", padding: 0, margin: 0, height: '0px' }}>
                    {dots.map((dot: any, index: any) => (
                        <li id={`${doting}`} key={index} style={{ display: "inline-block", margin: "0 -2px" }}>
                            <ul>
                                {dot}
                            </ul>
                        </li>
                    ))}
                </ul>
            </div>
        ),
    }

    // Set color for  background  if scroll
    const [scrolled, setScrolled] = useState(false);
    useEffect(() => {
        window.addEventListener('scroll', handleScroll);
        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    const handleScroll = () => {
        if (window.scrollY > 0) {
            setScrolled(true);
        } else {
            setScrolled(false);
        }
    };

    return (
        <>
            <div className={`${couping}`}  style={{ top: scrolled ? '60px' : '132px' }}>
                {coupons && coupons.length > 0 && (
                    <div className={`${couponsList} row d-flex`} style={{ backgroundColor: color ? color : 'white' }}>
                        <Slider {...settingCoupon} arrows={false}>
                            {coupons.map((coupon: any, index: any) => (
                                <div key={index} className={`${style.couponContainer} d-flex`} onClick={() => toggleCouponPopup(index)}>
                                    {/* Hiển thị Popup nếu trạng thái là mở */}
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
            </div>
            {
                coupons && coupons.length > 0 && (
                    coupons.map((coupon: any, index: any) => {
                        if (openPopups[index]) {
                            return (
                                <CouponPopup
                                    key={index}
                                    color={color ? color : "black"}
                                    coupon={coupon} 
                                    toggleCouponPopup={() => toggleCouponPopup(index)}
                                />
                            );
                        } else {
                            return null;
                        }
                    })
                )
            }
        </>
    )
}

export default memo(CouponList)