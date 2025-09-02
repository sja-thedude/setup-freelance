"use client"

import style from 'public/assets/css/product.module.scss'
import { memo } from 'react'
import Image from 'next/image'
import 'react-responsive-carousel/lib/styles/carousel.min.css'
import Slider from 'react-slick'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'
import useMediaQuery from '@mui/material/useMediaQuery'

type allergenen = {
    id: number,
    icon: string,
    type: number,
    type_display: string
}

function AllergenenList({ allergenens }: { allergenens: any }) {
    const isBigMobile = useMediaQuery('(min-width: 425px)');
    const settings = {
        infinite: false,
        variableWidth: true,
        swipeToSlide: true,
        arrows: false,
        slidesToShow: isBigMobile ? allergenens?.length > 8 ? 8 : allergenens.length : allergenens.length > 4 ? 4 : allergenens.length
    }

    return (
        <div className="allergenens-list">
            <Slider {...settings}>
                {allergenens?.map((allergenen: allergenen, i: number) => (
                    <Image
                        alt={allergenen.type_display}
                        className={style.image}
                        src={allergenen.icon}
                        width={44}
                        height={43}
                        quality={100}
                        priority
                        unoptimized={true}
                        key={'allergenen-' + allergenen.id}
                    />
                ))}
            </Slider>
        </div>
    )
}

export default memo(AllergenenList)