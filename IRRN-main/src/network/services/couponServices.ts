import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const validateCouponCodeService = (params: any) => API.get(Configs.VALIDATE_COUPON_CODE, { params });
export const validateProductCouponService = (params: any) => API.get(Configs.VALIDATE_PRODUCT_COUPON, { params });
