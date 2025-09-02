'use client'

import style from 'assets/assets/css/profile.module.scss'
import Image from "next/image";
import React from 'react';
import { useSelector } from "react-redux";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { logout } from "@/redux/slices/authSlice";
import { useI18n } from '@/locales/client';
import Link from 'next/link';
import Cookies from 'js-cookie';
import { useAppSelector, useAppDispatch } from '@/redux/hooks';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import MenuPortal from "@/app/[locale]/components/menu/menu-portal";
import Navbar from "@/app/[locale]/components/layouts/profile/navbar";
import {handleLogoutToken} from "@/utils/axiosRefreshToken";
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';

export default function Profile() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const dispatch = useAppDispatch();
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = '#B5B268';
    const trans = useI18n();
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    var avatar = apiSliceProfile?.data?.photo ?? '/img/avatar.png';

    const handleLogoutClick = async () => {
        dispatch(logout());
        // Remove cookie 'loggedToken'
        handleLogoutToken();
        window.location.reload();
    };

    return (
        <>
            <div style={{ background: "#F8F8F8", minHeight: "100vh" }}>
                <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%' ,zIndex: 100 }}>
                    <MenuPortal />
                </div>
                <Navbar content={ trans('profile')} background={ '#B5B268' }/>
                <div className={ style['menu-profile'] }>
                    {
                        tokenLoggedInCookie ? (
                            <div className={ style['avatar']}>
                                <Image
                                    alt=''
                                    src={ avatar ? avatar : '/assets/images/avatar.png' }
                                    width={100}
                                    height={100}
                                    sizes="100vw"
                                    style={{ borderRadius: '50%' }}
                                />
                            </div>) : (<div/>)
                    }
                    <div className={style['profile-info']}>
                        {
                            tokenLoggedInCookie ? (
                                <>
                                    <hr style={{color: '#EFEFEF'}}/>
                                    <Link className={style['portal-profile-info-item']}
                                          style={{ textDecoration : 'none' }}
                                          href={'/profile/edit'}
                                    >
                                        { trans('change-profile') }
                                    </Link>
                                </>) : (
                                <>
                                    <div className={'mt-4'}>
                                        <Link className={style['portal-profile-info-item']}
                                              style={{ textDecoration : 'none' }}
                                              href={'/user/portal/login'}
                                        >
                                            { trans('login-or-register') }
                                        </Link>
                                    </div>
                                </>
                            )
                        }
                        <hr style={{color: '#EFEFEF'}}/>
                        <Link className={style['portal-profile-info-item']}
                              style={{ textDecoration : 'none' }}
                              href={'https://b2b.itsready.be/'}>
                            { trans('more-information') }
                        </Link>
                        <hr style={{color: '#EFEFEF'}}/>
                        <Link className={style['portal-profile-info-item']}
                              style={{ textDecoration : 'none' }}
                              href={TERMS_CONDITIONS_LINK}
                              target="_blank"
                        >
                            { trans('terms-and-conditions') }
                        </Link>
                        <hr style={{color: '#EFEFEF'}}/>
                        <Link className={style['portal-profile-info-item']}
                              style={{ textDecoration : 'none' }}
                              href={PRIVACY_POLICY_LINK}
                              target="_blank"
                            >
                            { trans('privacy-policy-portal') }
                        </Link>
                        <hr style={{color: '#EFEFEF'}}/>
                        <div className={style['portal-profile-info-item']}>
                            { trans('follow-on-facebook') }
                        </div>
                        <hr style={{color: '#EFEFEF'}}/>
                        <Link className={style['portal-profile-info-item']}
                              style={{ textDecoration : 'none' }}
                              href={'/language'}
                        >
                            { trans('language') }
                        </Link>
                        <hr style={{color: '#EFEFEF'}}/>
                        {
                            tokenLoggedInCookie ? (
                                <>
                                    <div className={style['portal-profile-info-item']} style={{ color: '#D94B2C'}} data-bs-toggle="modal"
                                         data-bs-target="#exampleModal">
                                        { trans('logout') }
                                    </div>
                                    <hr style={{color: '#EFEFEF'}}/>

                                </>
                            ) : (
                                <div/>
                            )
                        }
                    </div>
                </div>
                <div className="d-flex">
                    <div
                        className="modal"
                        id="exampleModal"
                    >
                        <div className="modal-dialog">
                            <div className={`modal-content ${style['modal-content-login']}`}>
                                <div className="modal-body" >
                                    <div className={style['btn-confirm-logout']}>
                                        { trans('confirm-logout') }
                                    </div>
                                    <div className={style['btn-yes-logout']} onClick={handleLogoutClick}>
                                        { trans('yes-logout') }
                                    </div>
                                    <div
                                        data-type="button"
                                        className={style['btn-no-logout']}
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                        style={{ color: color }}
                                    > { trans('no-cancel') } </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}