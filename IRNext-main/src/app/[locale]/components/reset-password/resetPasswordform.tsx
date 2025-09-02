"use client";
import { useState, memo, useEffect } from "react";
import { useI18n } from "@/locales/client";
import { confirmNewPassword } from '@/services/confirm_new_password';
import _ from "lodash";
import Image from 'next/image';
import Link from 'next/link';
import { useFormik } from "formik";
import * as Yup from "yup";
import variables from "/public/assets/css/reset-password.module.scss";
import '@/app/[locale]/components/reset-password/custom.scss';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

const introduce = variables["introduce"];
const welcome = variables["welcome"];
const customInput = variables["custom-input-1"];
const btnDark = variables["btn-dark"];
const invalid = variables["invalid"];

function PasswordForm({
  token,
  emailUser,
}: {
  token: string;
  emailUser: string;
}) {
  const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
  const apiData = apiDataToken?.data?.setting_generals;
  const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
  const trans = useI18n();
  const [dataToken, setDataToken] = useState("");
  const [dataEmail, setDataEmail] = useState("");
  const [isSubmitClicked, setIsSubmitClicked] = useState(false);
  const [isVisible, setIsVisible] = useState(false);
  const [isPasswordVisible, setPasswordVisibility] = useState(false);
  const [isRePasswordVisible, setRePasswordVisibility] = useState(false);
  const [passwordChanged, setPasswordChanged] = useState(false);
  const [isPasswordValid, setPasswordValid] = useState(true);
  const [isRePasswordValid, setRePasswordValid] = useState(true);

  const togglePasswordVisibility = () => {
    setPasswordVisibility(!isPasswordVisible);
  };
  const reTogglePasswordVisibility = () => {
    setRePasswordVisibility(!isRePasswordVisible);
  };

  const handleSubmitClick = () => {
    setIsSubmitClicked(true);
    if(formik.values.password === '') {
      setPasswordValid(false);
    }
    if(formik.values.repeatPassword === '') {
      setRePasswordValid(false);
    }
    if(formik.values.password !== '' && formik.values.repeatPassword !== '') {
      if(formik.values.password.length < 6) {
        setPasswordValid(false);
      }

      if(formik.values.repeatPassword.length < 6) {
        setRePasswordValid(false);
      }

      if(formik.values.password !== formik.values.repeatPassword) {
        setPasswordValid(false);
        setRePasswordValid(false);
      }

      if(formik.values.password === formik.values.repeatPassword && formik.values.password.length >= 6 && formik.values.repeatPassword.length >= 6) {
        setPasswordValid(true);
        setRePasswordValid(true);
      }
    }
  }
  useEffect(() => {
    const decodedEmail = decodeURIComponent(emailUser.replace(/\+/g, '%2B'));
    setDataToken(token);
    setDataEmail(decodedEmail);
  }, [token, emailUser]);

  const validationSchema = Yup.object().shape({
    password: Yup.string()
      .required(trans("new-password-required"))
      .min(6, trans("password-min-length")),
    repeatPassword: Yup.string()
      .required(trans("repeat-password-required"))
      .oneOf([Yup.ref("password")], trans("password-not-match"))
      .min(6, trans("password-min-length")),
  });

  const formik = useFormik({
    initialValues: {
      password: "",
      repeatPassword: "",
    },
    validationSchema,
    validateOnChange: false,
    validateOnBlur: false,
    onSubmit: async (values) => {
      setIsSubmitClicked(true);
      const apiData = await confirmNewPassword({
        token: dataToken,
        email: dataEmail,
        password: values.password,
        password_confirmation: values.repeatPassword,
      });

      if (apiData.success) {
        setIsVisible(false);
        setPasswordChanged(true); // Set passwordChanged to true if password change was successful
      } else {
        setIsVisible(true);
        setPasswordChanged(false);
      }
    },
  });

  useEffect(() => {
    if (formik.errors.password && formik.errors.repeatPassword && isSubmitClicked) {
      toast.dismiss();
      toast(formik.errors.password, {
        position: toast.POSITION.BOTTOM_CENTER,
        autoClose: 1500,
        hideProgressBar: true,
        closeOnClick: true,
        closeButton: false,
        transition: Slide,
        className: 'message',
      });
      setIsSubmitClicked(false);

    } else if (formik.errors.repeatPassword && !formik.errors.password && isSubmitClicked) {
      toast.dismiss();
      toast(formik.errors.repeatPassword, {
        position: toast.POSITION.BOTTOM_CENTER,
        autoClose: 1500,
        hideProgressBar: true,
        closeOnClick: true,
        closeButton: false,
        transition: Slide,
        className: 'message',
      });
      setIsSubmitClicked(false);
    }
    else if (!formik.errors.repeatPassword && formik.errors.password && isSubmitClicked) {
      toast.dismiss();
      toast(formik.errors.password, {
        position: toast.POSITION.BOTTOM_CENTER,
        autoClose: 1500,
        hideProgressBar: true,
        closeOnClick: true,
        closeButton: false,
        transition: Slide,
        className: 'message',
      });
      setIsSubmitClicked(false);
    }
  }, [formik.errors, isSubmitClicked]);
  
  return (
    <>
      <div
        className={!passwordChanged ? 'd-block justify-content-center pt-5 container' : 'd-none'}
        style={{
          minHeight: "100vh",
          backgroundColor: color ?? 'rgba(0, 0, 0, 0.5)',
          height: "100%",
        }}
      >
        <div className="row text-center ps-2 pe-2">
          <div className={welcome}>{trans("setup-password-title")}</div>
        </div>
        <div className="ps-3 pe-3 text-center mt-5">
          <form onSubmit={formik.handleSubmit} className="text-center" noValidate>
            <div className="text-center mx-auto">
              <div className={`${customInput} input-group mx-auto`}>
                <input
                  type={isPasswordVisible ? "text" : "password"}
                  className={`${customInput} ${(formik.errors.password || formik.errors.repeatPassword === trans("password-not-match")) && formik.touched.password && !isPasswordValid
                    ? invalid
                    : ""
                    } form-control border-0`}
                  id="password"
                  placeholder={trans("new-password")}
                  style={{
                    height: "50px",
                    width: "100%",
                    flex: 1,
                    marginRight: "0",
                    borderRight: "none",
                  }}
                  required
                  onChange={formik.handleChange}
                  onKeyUp={() => { setPasswordValid(true) }}
                  onBlur={formik.handleBlur}
                  value={formik.values.password}
                  name="password"
                />
                <button
                  className={`${variables.eye} ${(formik.errors.password || formik.errors.repeatPassword === trans("password-not-match")) && formik.touched.password && !isPasswordValid ? invalid : ''} btn`}
                  type="button"
                  onClick={togglePasswordVisibility}
                  style={{
                    backgroundColor: isPasswordValid ? "white" : '',
                    borderLeft: isPasswordValid ? "none" : '',
                    minWidth: isPasswordValid ? "40px" : '',
                  }}
                >
                  {isPasswordVisible ? (
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 12.5C1 12.5 5 4.83334 12 4.83334C19 4.83334 23 12.5 23 12.5C23 12.5 19 20.1667 12 20.1667C5 20.1667 1 12.5 1 12.5Z" stroke={isPasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M12 15.375C13.6569 15.375 15 14.0878 15 12.5C15 10.9122 13.6569 9.625 12 9.625C10.3431 9.625 9 10.9122 9 12.5C9 14.0878 10.3431 15.375 12 15.375Z" stroke={isPasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke={isPasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  ) : (
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 15C1 15 5 7 12 7C19 7 23 15 23 15C23 15 19 23 12 23C5 23 1 15 1 15Z" stroke={isPasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M12 18C13.6569 18 15 16.6569 15 15C15 13.3431 13.6569 12 12 12C10.3431 12 9 13.3431 9 15C9 16.6569 10.3431 18 12 18Z" stroke={isPasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  )}
                </button>
              </div>

              <div className={`${customInput} input-group mx-auto mt-3`}>
                <input
                  type={isRePasswordVisible ? "text" : "password"}
                  className={`${customInput} ${formik.errors.repeatPassword &&
                    formik.touched.repeatPassword && !isRePasswordValid
                    ? invalid
                    : ""
                    } form-control border-0`}
                  id="repeatPassword"
                  placeholder={trans("repeat-password")}
                  style={{
                    height: "50px",
                    width: "100%",
                    flex: 1,
                    marginRight: "0",
                    borderRight: "none",
                  }}
                  required
                  onChange={formik.handleChange}
                  onKeyUp={() => { setRePasswordValid(true) }}
                  onBlur={formik.handleBlur}
                  value={formik.values.repeatPassword}
                  name="repeatPassword"
                />
                <button
                  className={`${variables.eye} ${formik.errors.repeatPassword && formik.touched.repeatPassword && !isRePasswordValid ? invalid : ''} btn`}
                  type="button"
                  onClick={reTogglePasswordVisibility}
                  style={{
                    backgroundColor: isRePasswordValid ? "white" : '',
                    borderLeft: isRePasswordValid ? "none" : '',
                    minWidth: isRePasswordValid ? "40px" : '',
                  }}
                >
                  {isRePasswordVisible ? (
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 12.5C1 12.5 5 4.83334 12 4.83334C19 4.83334 23 12.5 23 12.5C23 12.5 19 20.1667 12 20.1667C5 20.1667 1 12.5 1 12.5Z" stroke={isRePasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M12 15.375C13.6569 15.375 15 14.0878 15 12.5C15 10.9122 13.6569 9.625 12 9.625C10.3431 9.625 9 10.9122 9 12.5C9 14.0878 10.3431 15.375 12 15.375Z" stroke={isRePasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke={isRePasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  ) : (
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 15C1 15 5 7 12 7C19 7 23 15 23 15C23 15 19 23 12 23C5 23 1 15 1 15Z" stroke={isRePasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M12 18C13.6569 18 15 16.6569 15 15C15 13.3431 13.6569 12 12 12C10.3431 12 9 13.3431 9 15C9 16.6569 10.3431 18 12 18Z" stroke={isRePasswordValid ? 'black' : 'red'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  )}
                </button>
              </div>
            </div>

            <div className="mt-4">
              <div
                className="mx-auto d-flex justify-content-center"
                style={{ width: "53%" }}
              >
                <button type="submit" onClick={handleSubmitClick} className={`${btnDark}`}>
                  {trans("save-password")}
                </button>
              </div>
            </div>
          </form>
        </div>
        <ToastContainer />
      </div>
      <div className={passwordChanged ? 'd-block justify-content-center pt-5' : 'd-none'} style={{ minHeight: '100vh', backgroundColor: color ?? 'rgba(0, 0, 0, 0.5)', height: '100%' }}>
        <div className="row text-center pt-5 ps-2 pe-2">
          <div className={welcome}>{trans('password-changed')}</div>
        </div>
        <div className="row text-center ps-5 pe-5 mt-4 mb-3">
          <div className={introduce}> {trans('new-password-success')}</div>
        </div>
        <div className="text-center">
          <Image
            alt="intro"
            src={'/img/check-tick.svg'}
            width={120}
            height={120}
            sizes="100vw"
            style={{ borderRadius: '50%' }}
          />
        </div>
        <div>
          <div className="mt-4">
            <div className="mx-auto w-25 d-flex flex-column align-items-center justify-content-center">
              <button className={`${btnDark}`}>
                <Link href="/user/login" style={{ textDecoration: 'none', color: 'white' }} className={` ${btnDark}`}>{trans('login')}</Link>
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default memo(PasswordForm);
