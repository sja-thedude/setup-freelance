'use client'

import style from 'public/assets/css/profile.module.scss'
import Menu from "../../../components/menu/menu-plus";
import Navbar from "../../../components/layouts/profile/navbar";
import Image from "next/image";
import React, { useEffect } from 'react';
import { useSelector } from "react-redux";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { logout } from "@/redux/slices/authSlice";
import { useI18n } from '@/locales/client';
import Link from 'next/link';
import Cookies from 'js-cookie';
import {useRouter} from "next/navigation";
import { addStepRoot} from '@/redux/slices/cartSlice';
import { useAppSelector, useAppDispatch } from '@/redux/hooks';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import MenuPortal from "@/app/[locale]/components/menu/menu-portal";
import {handleLogoutToken} from "@/utils/axiosRefreshToken";
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { addOpenEditProfileSuccess } from '@/redux/slices/cartSlice'
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';

export default function Profile() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const dispatch = useAppDispatch();
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId});
    const openEditProfileSuccess = useAppSelector<any>((state: any) => state.cart.openEditProfileSuccess);
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    var avatar = apiSliceProfile?.data?.photo ?? '/img/avatar.png';
    const router = useRouter();
    const language = Cookies.get('Next-Locale');
    const activeLanguages = useAppSelector((state) => state.auth.activeLanguages)

    const handleLogoutClick = async () => {
        dispatch(logout());
        dispatch(addStepRoot(1))
        // Remove cookie 'loggedToken'
        handleLogoutToken();
        router.push('/')
    };
    const handleOpenNewTab = (event:any) => {
        event.preventDefault();
        const url = event.currentTarget.getAttribute('href');
        window.open(url, '_blank');
    };

    useEffect(() => {
        if(openEditProfileSuccess){
            toast.dismiss();
            toast(trans('profile-edit-success'), {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
            dispatch(addOpenEditProfileSuccess(false))
        }
    }, [openEditProfileSuccess]);

    const switchLanguage = (language: any) => {
        return (
            <>
                <hr style={{color: '#EFEFEF'}}/>
                <Link className={style['profile-info-item']} style={{ textDecoration : 'none' }} href={'/' + language + '/language'}>  
                    {trans('language')}
                </Link>                 
            </>
        );
    }

    return (
        <>
            {
                workspaceId ? (
                    <>
                        <div>
                            <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%' ,zIndex: 100 }}>
                                {
                                    workspaceId ? (
                                        <Menu />
                                    ) : (
                                        <MenuPortal />
                                    )
                                }
                            </div>
                            <Navbar content={ trans('profile')} background={ color }/>
                            <div className={ style['menu-profile'] } style={{backgroundColor: '#F8F8F8' , minHeight: 'calc(100vh - 141px)'}}>
                                { tokenLoggedInCookie && (
                                    <div className={ style['avatar']}>
                                        <Image
                                            alt=''
                                            src={ avatar ? avatar : '/assets/images/avatar.png' }
                                            width={100}
                                            height={100}
                                            sizes="100vw"
                                            style={{ borderRadius: '50%' }}
                                        />
                                    </div>
                                )}

                                {
                                    workspaceId ? (
                                        <>
                                            <div className={style['profile-info']}>
                                                {
                                                    tokenLoggedInCookie ? (
                                                        <>
                                                            <hr style={{ color: '#EFEFEF' }}></hr>
                                                            <Link className={style['profile-info-item']}
                                                                  style={{ textDecoration : 'none' }}
                                                                  href={'/profile/edit'}
                                                            >
                                                                { trans('change-profile') }
                                                            </Link>
                                                        </>) : (
                                                        <>
                                                            <div className={'mt-3'}>
                                                                <Link className={style['profile-info-item']}
                                                                      style={{ textDecoration : 'none' }}
                                                                      href={'/user/login?account=true'}
                                                                >
                                                                    { trans('login-or-register') }
                                                                </Link>
                                                            </div>
                                                        </>
                                                    )
                                                }


                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={'https://b2b.itsready.be/'}
                                                      onClick={handleOpenNewTab}
                                                      >
                                                    { trans('more-information') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={TERMS_CONDITIONS_LINK}
                                                      onClick={handleOpenNewTab}
                                                >
                                                    { trans('terms-and-conditions') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={PRIVACY_POLICY_LINK}
                                                      onClick={handleOpenNewTab}
                                                      >  
                                                    { trans('privacy-policy') }
                                                </Link>

                                                { (!workspaceId || activeLanguages?.length > 1) && switchLanguage(language)}

                                                <hr style={{color: '#EFEFEF'}}/>
                                                {tokenLoggedInCookie && (
                                                    <>
                                                        <div className={style['profile-info-item']} style={{ color: '#D94B2C'}} data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal">
                                                            { trans('logout') }
                                                        </div>
                                                        <hr style={{ color: '#EFEFEF' }}></hr>

                                                    </>
                                                )}
                                            </div>
                                        </>
                                    ) : (
                                        <>
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
                                                        </>
                                                    ) : (
                                                        <>
                                                            <div className={'mt-4'}>
                                                                <Link className={style['portal-profile-info-item']}
                                                                      style={{ textDecoration : 'none' }}
                                                                      href={'/user/login'}
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
                                                      target="_blank">
                                                    { trans('terms-and-conditions') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['portal-profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={PRIVACY_POLICY_LINK}
                                                      target="_blank">
                                                    { trans('privacy-policy-portal') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <div className={style['portal-profile-info-item']}>
                                                    { trans('follow-on-facebook') }
                                                </div>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['portal-profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={'/language'}>
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
                                        </>
                                    )
                                }
                            </div>
                            <div className="d-flex">
                                <div className="modal" id="exampleModal">
                                    <div className="modal-dialog">
                                        <div className={`modal-content ${style['modal-content-login']}`}>
                                            <div className="modal-body" >
                                                <div className={style['btn-confirm-logout']}>
                                                    { trans('confirm-logout') }
                                                </div>
                                                <div className={style['btn-yes-logout']} data-type="button" data-bs-dismiss="modal"
                                                     aria-label="Close" onClick={handleLogoutClick}>
                                                    { trans('yes-logout') }
                                                </div>
                                                <div
                                                    data-type="button"
                                                    className={style['btn-no-logout']}
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"
                                                    style={{ color: color}}
                                                > { trans('no-cancel') } </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ToastContainer />
                    </>
                ) : (
                    <>
                        <div style={{ background: "#F8F8F8", minHeight: "100vh" }}>
                            <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%' ,zIndex: 100 }}>
                                {
                                    workspaceId ? (
                                        <Menu />
                                    ) : (
                                        <MenuPortal />
                                    )
                                }
                            </div>
                            <Navbar content={ trans('profile')} background={ color }/>
                            <div className={ style['menu-profile'] }>
                                { tokenLoggedInCookie && (
                                    <div className={ style['avatar']}>
                                        <Image
                                            alt=''
                                            src={ avatar ? avatar : '/assets/images/avatar.png' }
                                            width={100}
                                            height={100}
                                            sizes="100vw"
                                            style={{ borderRadius: '50%' }}
                                        />
                                    </div>
                                )}

                                {
                                    workspaceId ? (
                                        <>
                                            <div className={style['profile-info']}>
                                                {
                                                    tokenLoggedInCookie ? (
                                                        <>
                                                            <hr style={{ color: '#EFEFEF' }}></hr>
                                                            <Link className={style['profile-info-item']}
                                                                  style={{ textDecoration : 'none' }}
                                                                  href={'/profile/edit'}
                                                            >
                                                                { trans('change-profile') }
                                                            </Link>
                                                        </>) : (
                                                        <>
                                                            <div className={'mt-3'}>
                                                                <Link className={style['profile-info-item']}
                                                                      style={{ textDecoration : 'none' }}
                                                                      href={'/user/login'}
                                                                >
                                                                    { trans('login-or-register') }
                                                                </Link>
                                                            </div>
                                                        </>
                                                    )
                                                }

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={'https://b2b.itsready.be/'}>
                                                    { trans('more-information') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={TERMS_CONDITIONS_LINK}
                                                      target="_blank">
                                                    { trans('terms-and-conditions') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={PRIVACY_POLICY_LINK}
                                                      target="_blank">
                                                    { trans('privacy-policy') }
                                                </Link>

                                                { (!workspaceId || activeLanguages?.length > 1) && switchLanguage(language)}
                                                
                                                <hr style={{color: '#EFEFEF'}}/>
                                                {
                                                    tokenLoggedInCookie && (
                                                        <>
                                                            <div className={style['profile-info-item']} style={{ color: '#D94B2C'}} data-bs-toggle="modal"
                                                                 data-bs-target="#exampleModal">
                                                                { trans('logout') }
                                                            </div>
                                                            <hr style={{ color: '#EFEFEF' }}></hr>

                                                        </>
                                                    )
                                                }
                                            </div>
                                        </>
                                    ) : (
                                        <>
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
                                                        </>
                                                    ) : (
                                                        <>
                                                            <div className={'mt-4'}>
                                                                <Link className={style['portal-profile-info-item']}
                                                                      style={{ textDecoration : 'none' }}
                                                                      href={'/user/login'}
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
                                                      target="_blank">
                                                    { trans('terms-and-conditions') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['portal-profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={PRIVACY_POLICY_LINK}
                                                      target="_blank">
                                                    { trans('privacy-policy-portal') }
                                                </Link>

                                                <hr style={{color: '#EFEFEF'}}/>
                                                <Link className={style['portal-profile-info-item']}
                                                      style={{ textDecoration : 'none' }}
                                                      href={'/language'}>
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
                                        </>
                                    )
                                }
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
                                                <div className={style['btn-yes-logout']} data-type="button" data-bs-dismiss="modal"
                                                     aria-label="Close" onClick={handleLogoutClick}>
                                                    { trans('yes-logout') }
                                                </div>
                                                <div
                                                    data-type="button"
                                                    className={style['btn-no-logout']}
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"
                                                    style={{ color: color}}
                                                > { trans('no-cancel') } </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                    </>
                )
            }
        </>
    );
}