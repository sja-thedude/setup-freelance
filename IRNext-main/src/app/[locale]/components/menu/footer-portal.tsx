import style from "public/assets/css/portal.module.scss";
import Image from "next/image";
import React, {useEffect, useState} from "react";
import { api } from "@/utils/axios";
import Link from 'next/link';
import * as config from "@/config/constants";
import Cookies from "js-cookie";
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';

export default function FooterPortal({trans, lang , from}: any){

    const currentYear = new Date().getFullYear();
    const [workspaces, setWorkspaces] = useState<any>(null);

    useEffect(() => {
        const restaurantData = async () => {
            const res = await api.get(`workspaces?sort_by=desc&limit=7&page=1`, {
                headers: {
                    'Timezone': Cookies.get('timezone') || 'Asia/Ho_Chi_Minh',
                }
            });

            const data = res?.data?.data?.data;
            setWorkspaces(data);
        }

        restaurantData();
    }, []);

    return(
        <>
            <div className={`${style['footer']}`} style={{border: from === 'notfound' ? 'none' : ''}}>
                <div className="row justify-content-between">
                    <div className={`${style['group-items']} text-center res-desktop col-lg-4 col-md-12 col-12`}>
                        <Image src="/img/footer-logo.png" alt="" width={230} height={160} />
                        <div className={`${style['copyright']}`}>{currentYear} © IT’S READY</div>
                    </div>

                    <div className={`${style['group-items']} col-lg-4 col-md-12 col-12`}>
                        <div className={`${style['footer-title']}`}>
                            {trans('portal.navigation')}
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href="/">{trans('home')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href="/search">{trans('portal.find-dealer')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href="https://b2b.itsready.be/" target="_blank">{trans('portal.traders-website')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href="/contacts">{trans('contact-us')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href={TERMS_CONDITIONS_LINK} target="_blank">{trans('terms-and-conditions')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href={PRIVACY_POLICY_LINK} target="_blank">{trans('privacy-policy')}</Link>
                        </div>
                        <div className={`${style['footer-item']}`}>
                            <Link href="https://b2b.itsready.be/cookies" target="_blank">{trans('portal.cookie-policy')}</Link>
                        </div>
                    </div>

                    <div className={`${style['group-items']} col-lg-4 col-md-12 col-12`}>
                        <div className={`${style['footer-title']}`}>
                            {trans('portal.new-traders')}
                        </div>
                        {workspaces && workspaces.map((workspace: any, index: number) =>
                            <div key={index} className={`${style['footer-item']}`}>
                                <Link href={`https://${workspace?.slug}.${config.WEBSITE_DOMAIN}/${lang}/category/products?portal=1&origin=home`}>{workspace.name}</Link>
                            </div>
                        )}
                    </div>

                    <div className={`${style['group-items']} mb-0 res-mobile col-lg-4 col-md-12 col-12`}>
                        <Image src="/img/footer-logo.png" alt="" width={230} height={160} />
                        <div className={`${style['copyright']}`}>{currentYear} © IT’S READY</div>
                    </div>
                </div>
            </div>
        </>
    )
}
