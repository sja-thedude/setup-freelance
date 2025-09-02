"use client"

import style from 'public/assets/css/product.module.scss'
import { memo } from 'react'
import Image from "next/image"
import 'react-responsive-carousel/lib/styles/carousel.min.css'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

type allergenen = {
    id: number,
    icon: string,
    type: number,
    type_display: string
}

function AllergenenListDesk({ allergenens , photo }: { allergenens: any , photo: any }) {
    return (
        <div className="allergenens-list d-flex" style={{justifyContent: 'flex-end', flexWrap: 'wrap'}}>
                {allergenens?.map((allergenen: allergenen, i: number) => (
                    <Image
                        alt={allergenen.type_display}
                        className={style.image}
                        src={allergenen.icon}
                        width={49}
                        height={49}
                        quality={100}
                        priority
                        unoptimized={true}
                        key={'allergenen-' + allergenen.id}
                    />
                ))}
        </div>
    )
}

export default memo(AllergenenListDesk)