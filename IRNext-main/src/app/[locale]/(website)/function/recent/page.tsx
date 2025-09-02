'use client'

import variables from '/public/assets/css/function-page.module.scss'
import Link from 'next/link';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAngleLeft } from '@fortawesome/free-solid-svg-icons';
import { useI18n } from '@/locales/client';
import  OrderHistory  from "@/app/[locale]/components/function/orderHistory";
import Cookies from 'js-cookie';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

const backHeader = variables['backHeader'];
const orderHistorytitle = variables['orderHistory'];
const bnBack = variables['bn-back'];

export default function RecentPage() {
  const trans = useI18n();
  const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
  const apiData = apiDataToken?.data?.setting_generals;
  const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
  const token = Cookies.get('loggedToken');

  return (
    <>
      <div
        className={'d-block justify-content-center'}
        style={{
          minHeight: '100vh',
          height: '100%',
        }}
      >
        <div className={backHeader} style={{ backgroundColor: color ?? 'white', paddingBottom: '15px' }}>
          <Link href="/" style={{ marginTop: 'auto' }}>
            <FontAwesomeIcon
              icon={faAngleLeft}
              className={bnBack} />
          </Link>
          <div className={orderHistorytitle}>
            {trans('order-history')}
          </div>
        </div>
        { token && <OrderHistory/> }
      </div>
    </>
  );
};
