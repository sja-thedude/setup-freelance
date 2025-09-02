import React from 'react';
import Image from "next/image";
import variables from '/public/css/food.module.scss'

const titleTextFirst = variables['title-text-first'];
const titleImage = variables['title-image'];
const titleText = variables['title-text'];
const titleFav = variables['title-fav'];
const descriptionText = variables['description-text'];
const descriptionFood = variables['description-food'];
const btnDark = `btn btn-dark ${variables['btn-dark']}`;
const buttonNew = variables['button-new'];
const priceText = variables['price-text'];
const underLine = variables['underline-title'];

export default function Foods({ category }: any) {
    return (
        <main className="flex min-h-screen flex-col items-center justify-between ">
            <div className='container' id={variables.group}>
                <div className={variables.title}>
                    <div className={titleTextFirst}>
                        <h1 className={underLine}>{category.title}</h1>
                    </div>
                    <div className={titleImage}>
                        <Image
                            alt='sliders'
                            src="/img/sliders.png"
                            width={0}
                            height={0}
                            sizes="200vw"
                            style={{ width: '100%', height: 'auto' }} // optional
                        />
                    </div>
                </div>
                <div className='container'>
                    {category.types.map((item: any, index: any) => (
                        <div key={index} className={variables.container}>
                            <div className={variables.items}>
                                <div className={variables.title}>
                                    <div className={titleText}><h1>{item.name}</h1></div>
                                    <div className={titleFav}>
                                        {
                                            item.fav == true ? (null) : (null)
                                        }
                                    </div>
                                </div>
                                <div className={variables.description}>
                                    <div className={descriptionText}>
                                        {item.img && item.description.length > 137 ? (
                                            <>{item.description.slice(0, 137)}...</>
                                        ) : (
                                            <>
                                                {item.description.length > 157 ? (
                                                    <>{item.description.slice(0, 157)}...</>
                                                ) : (
                                                    <>{item.description}</>
                                                )}
                                            </>
                                        )}
                                    </div>
                                    <div className={descriptionFood}>
                                        {
                                            item.img ? (<Image
                                                alt='food'
                                                src="/img/food.png"
                                                width={0}
                                                height={0}
                                                sizes="150vw"
                                                style={{ width: '115%', height: 'auto' }} // optional
                                            />)
                                                : (null)
                                        }

                                    </div>
                                </div>
                                <div className={variables.price}>
                                    <div className={buttonNew}>
                                        <button type="button" className={btnDark}>NEW</button>
                                    </div>
                                    <div className={priceText}>€{item.price}</div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </main>
    );
};
