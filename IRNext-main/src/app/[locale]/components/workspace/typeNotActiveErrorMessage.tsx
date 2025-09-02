import { useI18n } from '@/locales/client'
import { useAppSelector } from '@/redux/hooks'

const TypeNotActiveErrorMessage = (props: any) => {
    const trans = useI18n();
    const cls = props.className || 'mt-2';
    const typeNotActiveErrorMessageContent = useAppSelector<any>((state: any) => state.cart.typeNotActiveErrorMessageContent);

    return (
        <div className={`row d-flex cart_messageDesk__zujiH ${cls}`}>
            <div className="col-auto cart_warningDesk__B_lcc">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="21"
                    height="21"
                    viewBox="0 0 21 21"
                    fill="none"
                >
                    <path
                    d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z"
                    stroke="#E03009"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    ></path>
                    <path
                    d="M10.5 7.875V11.375"
                    stroke="#E03009"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    ></path>
                    <path
                    d="M10.5 14.875H10.5088"
                    stroke="#E03009"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    ></path>
                </svg>
                </div>
                <div className="col cart_errorDesk__8JU88">
                <p className="cart_errorDeskText__GqhAu">
                    {typeNotActiveErrorMessageContent != '' ? typeNotActiveErrorMessageContent : trans('cart.type_not_active')}
                </p>
            </div>
        </div>
    );
};

export default TypeNotActiveErrorMessage;