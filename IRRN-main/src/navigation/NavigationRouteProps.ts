/* eslint-disable @typescript-eslint/indent */
import type { RouteProp } from '@react-navigation/native';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';

export type RootStackParamList = {
    [key: string]: any;
    ConfirmRegisterScreen: {
        token: string;
    };
    ResetPasswordScreen: {
        token: string;
        email: string;
    };
    SelectAddressScreen: {
        onSelectAddress: Function;
    };
    RestaurantDetailScreen: {
        showFavorite?: boolean,
        inHomeStack?: boolean,
        prevScreen?: string,
    };
    DetailLoyaltyRestaurantScreen: { title?: string; id: number | string };
    ProductDetailScreen: { id: number, restaurantId: number, callback?: Function };
    SelectOrderTypeScreen: { product: ProductInCart, defaultPopup?: number, isInCart: boolean, callback?: Function  };
    LoginScreen: { callback?: Function, fromCart?: boolean };
    OrderSuccessScreen: { orderId?: number };
    WebViewScreen: { url: string, title: string };
    TemplateJobRegisterScreen: { data: any };
    EditProfileScreen: { fromCart?: boolean };
};

export type ConfirmRegisterScreenProps = RouteProp<
    RootStackParamList,
    'ConfirmRegisterScreen'
>;
export type ResetPasswordScreenProps = RouteProp<
    RootStackParamList,
    'ResetPasswordScreen'
>;
export type SelectAddressScreenProps = RouteProp<
    RootStackParamList,
    'SelectAddressScreen'
>;
export type RestaurantDetailScreenProps = RouteProp<
    RootStackParamList,
    'RestaurantDetailScreen'
>;
export type DetailLoyaltyRestaurantScreenProps = RouteProp<
    RootStackParamList,
    'DetailLoyaltyRestaurantScreen'
>;
export type ProductDetailScreenProps = RouteProp<
    RootStackParamList,
    'ProductDetailScreen'
>;
export type SelectOrderTypeScreenProps = RouteProp<
    RootStackParamList,
    'SelectOrderTypeScreen'
>;
export type LoginScreenScreenProps = RouteProp<
    RootStackParamList,
    'LoginScreen'
>;
export type OrderSuccessScreenProps = RouteProp<
    RootStackParamList,
    'OrderSuccessScreen'
>;
export type WebViewScreenProps = RouteProp<
    RootStackParamList,
    'WebViewScreen'
>;
export type TemplateJobRegisterScreenProps = RouteProp<
    RootStackParamList,
    'TemplateJobRegisterScreen'
>;

export type EditProfileScreenProps = RouteProp<
    RootStackParamList,
    'EditProfileScreen'
>;
