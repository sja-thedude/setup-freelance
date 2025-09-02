'use client';
import React, { useEffect, useState } from 'react';
import { useI18n } from '@/locales/client';
import { resetPassword } from '@/services/reset_password';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import Image from 'next/image';
import Link from 'next/link';
import { faAngleLeft } from '@fortawesome/free-solid-svg-icons';
import variables from '/public/assets/css/reset-password.module.scss';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import { useRouter } from 'next/navigation'

const introduce = variables['introduce'];
const welcome = variables['welcome'];
const customInput = variables['custom-input'];
const btnDark = variables['btn-dark'];
const messageField = variables['messageField'];
const invalid = variables['invalid'];

function LoadingSpinner() {
  const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
  const apiData = apiDataToken?.data?.setting_generals;
  const trans = useI18n();

  return (
    <div className={`spinner-border my-auto ${variables.centeredDiv}`} style={{ color: apiData ? apiData?.primary_color : 'rgba(0, 0, 0, 0.5)', }} role="status">
      <span className="sr-only">{trans('lang_loading')}...</span>
    </div>
  );
}
export default function ResetPasswordPage() {
  const trans = useI18n();
  const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
  const apiData = apiDataToken?.data?.setting_generals;
  const [message, setMessage] = useState('');
  const [isEmailValid, setIsEmailValid] = useState(true);
  const [isEmailSuccess, setEmailSuccess] = useState(false);
  const [isVisible, setIsVisible] = useState(false);
  const [isLoading, setLoading] = useState(false);
  const [isSubmitClicked, setIsSubmitClicked] = useState(false);
  const router = useRouter();
  const handleSubmitClick = () => {
    setIsSubmitClicked(true);
    if(formik.values.email.length === 0) {
      setIsEmailValid(false);
    }
  }

  const validationSchema = Yup.object().shape({
    email: Yup.string()
      .required(trans('fields-require'))
      .email(trans('invalid-email'))
      .test('is-valid-email', trans('invalid-email'), function(value) {
        const isValidEmail = Yup.string().email().isValidSync(value);
        setIsEmailValid(isValidEmail);
        return isValidEmail;
      }),
  });

  const formik = useFormik({
    initialValues: {
      email: '',
    },
    validationSchema,
    validateOnChange: false,
    validateOnBlur: false,
    onSubmit: async (values) => {
      setIsSubmitClicked(true);
      try {
        setLoading(true);
        const apiData = await resetPassword({ email: values.email });
        if (apiData.success) {
          setMessage(trans('reset-password-success-message'));
          setEmailSuccess(true);
          setIsEmailValid(true);
        } else {
          setIsEmailValid(false);
          setIsVisible(true);
          toast.dismiss();

          toast(trans('email-not-exist'), {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 1500,
            hideProgressBar: true,
            closeOnClick: true,
            closeButton: false,
            transition: Slide,
            className: 'message',
          });
        }
        // Handle API response as needed
      } catch (error) {
        console.error(error);
        setIsEmailValid(false);
        setIsVisible(true);
      }
      finally {
        setLoading(false);
      }

    },
  });

  useEffect(() => {
    if (formik.errors.email && isSubmitClicked) {
      toast.dismiss();
      toast(formik.errors.email, {
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
  }, [formik.errors.email, isSubmitClicked]);

  return (
    <>
      {/* Reset password form */}
      <div
        className={!isEmailSuccess ? 'd-block justify-content-center container' : 'd-none'}
        style={{
          minHeight: '100vh',
          backgroundColor: workspaceId ? apiData ? apiData?.primary_color : 'rgba(0, 0, 0, 0.5)' : '#ABA765',
          height: '100%',
          paddingBottom: '5%',
        }}
      >
        <div>
          <div onClick={() => { router.back() }}>
            <FontAwesomeIcon
              icon={faAngleLeft}
              style={{
                color: 'white',
                height: '20px',
                width: '20px',
                marginTop: '10%',
                marginLeft: '6%',
              }}
            />
          </div>
        </div>
        <div className="row text-center mt-5 ps-2 pe-2">
          <div className={welcome}>{trans('recover-title')}</div>
        </div>
        <div className="row text-center ps-3 pe-3 mt-3 mb-3">
          <h1 className={introduce}> {trans('recover-subtitle')}</h1>
        </div>
        <div className="ps-3 pe-3 text-center">
          <form onSubmit={formik.handleSubmit} className="text-center" noValidate>
            <div className="mx-auto d-flex justify-content-between">
              <input
                id="email"
                type="email"
                className={`${customInput} ${!isEmailValid ? invalid : ""} border-0 form-control mx-auto`}
                onChange={formik.handleChange}
                value={formik.values.email}
                onBlur={formik.handleBlur}
                onKeyUp={() => {setIsEmailValid(true)}}
                name="email"
                required
                placeholder={trans('email-field')}
              />
            </div>
            <div className="mt-4">
              <div className="mx-auto d-flex justify-content-center">
                <button type="submit" name='submit-btn' onClick={handleSubmitClick} className={` ${btnDark} position-relative`}>
                  {isLoading ? <LoadingSpinner /> : trans('reset-password')}
                </button>
              </div>
            </div>
          </form>
        </div>
        {!isLoading && (
          <ToastContainer />
        )}
      </div>
      {/* Success message */}
      {isEmailSuccess && (
        <div className={'d-block justify-content-center pt-5'} style={{ minHeight: '100vh', backgroundColor:workspaceId ? apiData ? apiData?.primary_color : 'rgba(0, 0, 0, 0.5)' : '#ABA765', height: '100%' }}>
          <div className="row text-center pt-5 ps-2 pe-2">
            <h1 className={welcome}>{trans('email-sent')}</h1>
          </div>
          <div className="row text-center ps-3 pe-3 mt-4 mb-3">
            <h1 className={introduce}> {message}</h1>
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
                  <Link href="/user/login" style={{ textDecoration: 'none', color: 'white' }} className={` ${btnDark}`}>
                    {trans('login')}
                  </Link>
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
