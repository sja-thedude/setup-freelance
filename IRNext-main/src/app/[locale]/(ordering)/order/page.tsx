// components/Header.js
import React from 'react';
import Image from "next/image";
import 'bootstrap/dist/css/bootstrap.css';
import variables from '/public/css/food.module.scss'
import Link from 'next/link';
import Menu from "@/app/[locale]/components/menu/menu";
import Food from "@/app/[locale]/components/food/food";
import { api } from "@/utils/axios";

const centeredText = variables['centered-text'];
const menuBar = variables['menu-bar'];
const navSearch = `nav ${variables['search']}`;
const navItem = `nav-item ${variables['nav-item']}`;
const navLinkCustomActive = `${variables['nav-link']} active ${variables['custom-link']}`;

async function getCoupons() {
    interface Coupon {
        id: number;
        created_at: string;
        updated_at: string;
        code: string;
        promo_name: string;
        workspace_id: number;
        workspace: {
            id: number;
            name: string;
        };
        max_time_all: number;
        max_time_single: number;
        currency: string;
        discount: string;
        expire_time: string;
        discount_type: number;
        percentage: number;
    }
    const token = 'B839ADD642EF4E7D4036EF0A450ACC6EBB308695BCEF79A4DB3C6465FAE5669F';
    try {
        // api get coupons
        const couponsResponse = await api.get('coupons');

        if (!couponsResponse.data) {
        throw new Error('API coupons request failed');
        }

        const infoCoupons = couponsResponse.data.data;

        // api get workspaces by token
        const workspacesResponse = await api.get(`workspaces/token/${token}`);

        if (!workspacesResponse.data) {
        throw new Error('API workspaces request failed');
        }

        const workspaceData = workspacesResponse.data.data;
        const workspaceID = workspaceData.id;

        if (infoCoupons) {
        const filteredData = infoCoupons.data.filter((item: Coupon) => item.workspace_id === workspaceID);

        if (filteredData.length > 0) {
            return filteredData;
        }
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        return null; // Trả về null hoặc xử lý lỗi theo ý muốn của bạn
    }
}

export default async function Page() {
    const menuItems = [
        {id: 1, link: '/page1', text: 'PIZZA'},
        {id: 2, link: '/page2', text: 'PANINI'},
        {id: 3, link: '/page3', text: 'WRAPS'},
        {id: 4, link: '/page3', text: 'DRINKS'},
        {id: 5, link: '/page3', text: 'DESSERTS'}
    ];
    
    const foodItems = [
        {
            id: 1,
            title: 'PIZZA',
            types: [
                {
                    name: 'Margherita',
                    description: 'Classic Italian pizza with tomato, mozzarella, and basil. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 10.99,
                    fav: true,
                    img: "/img/favorite.png"
                },
                {
                    name: 'Pepperoni',
                    description: 'Delicious pizza with pepperoni, cheese, and tomato sauce. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 12.99,
                    fav: true,
                    img: ""
                },
                {
                    name: 'Vegetarian',
                    description: 'Vegetarian pizza loaded with fresh vegetables and cheese. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 11.99,
                    fav: false,
                    img: ""
                },
            ],
        },
        {
            id: 2,
            title: 'PANINI',
            types: [
                {
                    name: 'Caprese Panini',
                    description: 'Fresh mozzarella, tomato, basil, and balsamic glaze on ciabatta bread. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 8.99,
                    fav: true,
                    img: "/img/favorite.png"
                },
                {
                    name: 'Chicken Pesto Panini',
                    description: 'Grilled chicken, pesto sauce, and roasted red peppers on focaccia bread. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 9.99,
                    fav: false,
                },
                // Thêm các loại Panini khác ở đây
            ],
        },
        {
            id: 3,
            title: 'WRAPS',
            types: [
                {
                    name: 'Chicken Caesar Wrap',
                    description: 'Grilled chicken, romaine lettuce, Caesar dressing, and Parmesan cheese in a tortilla. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 7.99,
                    fav: true,
                    img: "/img/favorite.png"
                },
                {
                    name: 'Vegetable Wrap',
                    description: 'Assorted fresh vegetables and hummus wrapped in a spinach tortilla. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 6.99,
                    fav: false,
                },
                // Thêm các loại Wrap khác ở đây
            ],
        },
        {
            id: 4,
            title: 'DRINKS',
            types: [
                {
                    name: 'Coca-Cola',
                    description: 'Refreshing cola drink. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 2.49,
                    fav: true,
                    img: "/img/favorite.png"
                },
                {
                    name: 'Lemonade',
                    description: 'Homemade lemonade with fresh lemons and sugar. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 3.49,
                    fav: false,
                },
                // Thêm các loại đồ uống khác ở đây
            ],
        },
        {
            id: 5,
            title: 'DESSERTS',
            types: [
                {
                    name: 'Tiramisu',
                    description: 'Classic Italian dessert with coffee-soaked ladyfingers and mascarpone cheese. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 5.99,
                    fav: true,
                    img: "/img/favorite.png"
                },
                {
                    name: 'Chocolate Brownie',
                    description: 'Rich and gooey chocolate brownie served with vanilla ice cream. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    price: 4.99,
                    fav: false,
                },
            ],
        },
    ];

    const coupons = await getCoupons();

    return (
        <div>
            <div style={{position: 'fixed', bottom: 0, left: 0, width: '100%'}}>
                <Menu/>
            </div>
            <main className="flex min-h-screen flex-col items-center justify-between ">
                <link rel="stylesheet" href="/css/sf-font.css"/>
                <div className={variables.header}>
                    <h1 className={centeredText}>Pepe In Grani</h1>
                    {coupons && coupons[0].promo_name ? (
                        <><h2 className={variables.coupons}>{coupons[0].promo_name}</h2></>
                    ) : null
                    }
                </div>
                <div className={menuBar}>
                    <nav>
                        <ul className={navSearch}>
                            <li className={navItem}>
                                <Image
                                    alt='search'
                                    className="nav-item-img"
                                    src="/img/search.png"
                                    width={200}
                                    height={200}
                                    layout="responsive"
                                    sizes="(max-width: 375px) 120vw, 200vw"
                                    style={{width: '100%', height: 'auto'}}
                                />
                            </li>
                            {menuItems && menuItems.map((item) => (
                                <li key={item.id}>
                                    <Link href='' legacyBehavior className={navItem}>
                                        <a className={navLinkCustomActive} aria-current="page" href="#">{item.text}</a>
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </nav>
                </div>
                {foodItems.map((category) => (
                    <Food key={category.id} category={category}/>
                ))}
            </main>
        </div>
    );
};
