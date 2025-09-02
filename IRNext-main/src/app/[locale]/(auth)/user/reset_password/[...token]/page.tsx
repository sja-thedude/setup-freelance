'use client';

import React from 'react';
import PasswordForm from '@/app/[locale]/components/reset-password/resetPasswordform'
import { api } from "@/utils/axios";
import HomeComponent from "../../../../components/workspace/home";

export default async function Confirm({
  params
}: {
  params: { token: string, email: string }
}) {
    let token = params.token[0];
    let emailUser = params.token[1];

  if(token && emailUser) {
    try {
      const emailEncode = new URLSearchParams({email: emailUser ? emailUser : ''}).toString();
      const isConfirmed1 = await api.get(`password/reset/${token}/verify?${emailEncode}`);
      const data = {
        token: token,
        email: emailUser
      }
      return (
        <>
          {
            window.innerWidth >= 1280 ? (
                <div className='d-lg-block d-md-none'>
                    <HomeComponent isRegisterConfirm={false} isResetPasswordConfirm={true} dataResetPassword={data}></HomeComponent>
                </div>
            ) : (
              <PasswordForm token={token}
                      emailUser={emailUser}/>
            )
          }
        </>
      );
    } catch (error) {
      // console.log(error);
    }
  }

}
