import ProductDetailContainer from '@/app/[locale]/components/container/productDetailContainer'
import style from 'public/assets/css/product.module.scss'
import { api } from "@/utils/axios";
import LabelList from '@/app/[locale]/components/ordering/labels/list';
import AllergenenList from '@/app/[locale]/components/ordering/allergenens/list';
import BackButton from '@/app/[locale]/components/layouts/ordering/backButton'
import { getWorkspaceById } from '@/services/workspace'
import { headers } from 'next/headers'
import Cookies from "js-cookie";

export default async function Page({
    params
}: {
    params: { id: number }
}) {
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const workspaceId = headers().get('x-next-workspace')
    const workspace = await getWorkspaceById({ id: workspaceId });
    const color = workspace?.setting_generals?.primary_color
    let product = await api.get(`products/${params.id}`)
    let productOptions = await api.get(`products/${params.id}/options?limit=100&page=1`, {
        headers: {
            'Content-Language': language
        }
    })
    product = product?.data
    productOptions = productOptions?.data

    return (
        <>
            <div className="row overflow-hidden">
                <div className="col-sm-12 col-xs-12">
                    <div className={style['product-image']} style={product.data && product.data.photo != null ? { backgroundImage: `url(${product.data.photo})`, height: '175px' , backgroundPosition: 'center' } : { backgroundColor: color, minHeight: '56px' }}>
                        <div className={`ps-3 pe-3 ${style.labels}`}>
                            <LabelList labels={product?.data?.labels} color={color} />
                        </div>
                    </div>
                </div>
            </div>
            <div className={`${style['product-detail']} overflow-hidden`}>
                <div className="row ps-2 pe-2">
                    <div className="col-sm-12 col-xs-12">
                        <div className="float-start">
                            <h1 className={`${style['product-title']} mt-3`}>
                                {product?.data?.name}
                            </h1>
                        </div>
                    </div>
                </div>
                <div className="row ps-2 pe-2">
                    <div className="col-sm-12 col-xs-12">
                        <p className={style['product-description']}>{product.data.description}</p>
                    </div>
                </div>
                <div className="row ps-2 pe-2">
                    <div className="col-sm-12 col-xs-12">
                        <div className={style.allergenens}>
                            <AllergenenList allergenens={product?.data?.allergenens} />
                        </div>
                    </div>
                </div>
                <ProductDetailContainer color={color}
                    product={product}
                    productOptions={productOptions}
                    cartType="self_ordering" />
                <div className={`${style['close-page']} row text-center ps-2 pe-2`}>
                    <div className="col-sm-12">
                        <BackButton id={params.id} />
                    </div>
                </div>
            </div>
        </>
    );
}