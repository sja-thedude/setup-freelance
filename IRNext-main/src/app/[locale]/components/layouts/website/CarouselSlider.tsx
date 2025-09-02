import React, { useState } from 'react';
import 'react-responsive-carousel/lib/styles/carousel.min.css';
import variables from '/public/assets/css/home.module.scss';
import { EffectFade, Autoplay, Pagination, Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import "swiper/css/effect-fade";

const CarouselSlider = ({workspaceDataFinal}: any) => {
    const [slider, setSlider] = useState(false);

    const onChange = () => {
        setTimeout(function (){
            setSlider(true);
            setTimeout(function (){
                setSlider(false);
            },300);
        },0);
    }

    const renderCarouselItems = () => {
        const items = [];
        for (let i = 1; i <= 25; i++) {
            items.push(<div className={`${variables.box}`} key={i}></div>);
        }
        return items;
    };

    return (
        <div className={`${variables.home_carousel_group}`}>
            <Swiper
                className={`${variables.home_carousel}`}
                spaceBetween={0}
                slidesPerView={1}
                modules={[EffectFade, Autoplay, Pagination, Navigation]}
                effect="fade"
                autoplay={{delay:7000}}
                simulateTouch={false}
                onSlideChange={onChange}
            >
                {workspaceDataFinal &&
                    workspaceDataFinal.gallery.map((item: any, index: any) => (
                        <SwiperSlide className={`${variables.item}`} key={index}>
                            <img src={`${item.full_path}`} alt={`${item.file_name}`} />
                        </SwiperSlide>
                    ))}
            </Swiper>

            {slider && <div className={`${variables.boxes}`}>{renderCarouselItems()}</div>}
        </div>
    );
};

export default React.memo(CarouselSlider);
