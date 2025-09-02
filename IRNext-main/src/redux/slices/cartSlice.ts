import { createSlice, PayloadAction } from '@reduxjs/toolkit'

type cartItemType = {
    productId: number,
    product: any,
    productTotal: number,
    productOptions: any,
    basePrice: number,
    optionItemsStore: any
}

const initData: Array<cartItemType> = []
const initDataInfoTable: any = null
const initDataInfoSelfOrder: any = null
const initDeliveryAddress: any = null
const initRootCartItemTmp: any = null
const initialState = {
    // table ordering
    data: initData,
    dataInfoTable: initDataInfoTable,
    stepTable: 1,
    stepCategory: 1,
    cartNote: '',
    addToCartSuccess: false,
    rootCartTotalDiscountTable: 0,
    // self ordering
    selfOrderingData: initData,
    selfOrderingCartNote: '',
    dataInfoSelfOrder: initDataInfoSelfOrder,
    rootCartTotalDiscountSelf: 0,
    stepSelfOrdering: 1,
    stepReversed: false,
    // user website
    stepRoot: 1,
    rootData: initData,
    rootAddToCartSuccess: false,
    rootCartItemTmp: initRootCartItemTmp,
    rootCartTmp: initData,
    rootCartHistory: initData,
    rootCartTotalPrice: 0,
    rootCartTotalDiscount: 0,
    typeFlag: false,
    type: 0, // 1: takeout, 2: delivery, 3: group ordering
    typeBeforeChange: null,
    coupon: null,
    couponTable: null,
    couponSelf: null,
    rootCartInvalidProductIds: null,
    rootCartRedeemId: null,
    rootCartDatetime: null,
    rootCartNote: '',
    rootCartValidCouponProductIds: null,
    rootCartValidCouponProductIdsTable: null,
    rootCartValidCouponProductIdsSelf: null,
    rootCartDeliveryConditions: null,
    rootCartDeliveryAddress: initDeliveryAddress,
    rootCartDeliveryOpen: false,
    changeOrderTypeDesktop: false,
    paymentMethod: null,
    openRegisterPortal: false,
    groupOrderSelected: null,
    groupOrderSelectedNow: null,
    openDeskTopLogin: false,
    formGroupOpen: true,
    readyDelivery: false,
    closeDetail: false,
    openEditProfileSuccess: false,
    isReadyTakeOut: false,
    maxHeightPhoto: 0,
    maxHeightNonePhoto: 0,
    maxHeightPhotoLoaded: false,
    maxHeightNonePhotoLoaded: false,
    showCartItem: false,
    // cart limit time to payment: 5 minutes
    cartLimitTimeToPayment: false,
    typeNotActiveErrorMessage: false,
    typeNotActiveErrorMessageContent: '',
    isShowRedeemGlobal: false,
    totalPriceNeedToPay: 0,
}

// Config slice
export const cartSlice = createSlice({
    name: 'cart',
    initialState,
    reducers: {
        // table ordering
        addToCart: (state, action: PayloadAction<cartItemType>) => {
            if (action.payload?.product) {
                if (!state.data) {
                    state.data = []
                }
                state.data = [...state.data, action.payload]
            }
        },
        changeInCart: (state, action: PayloadAction<any>) => {
            state.data = action.payload
        },
        toggleAddToCartSuccess: (state) => {
            state.addToCartSuccess = !state.addToCartSuccess
        },
        cartNote: (state, action: PayloadAction<any>) => {
            state.cartNote = action.payload
        },
        addInfoTable: (state, action: PayloadAction<any>) => {
            state.dataInfoTable = action.payload
        },
        addInfoSelfOrder: (state, action: PayloadAction<any>) => {
            state.dataInfoSelfOrder = action.payload
        },
        addStepTable: (state, action: PayloadAction<any>) => {
            state.stepTable = action.payload
        },
        addStepRoot: (state, action: PayloadAction<any>) => {
            state.stepRoot = action.payload
        },
        openRegisterPortal: (state, action: PayloadAction<any>) => {
            state.openRegisterPortal = action.payload
        },
        // self ordering
        selfOrderingAddToCart: (state, action: PayloadAction<cartItemType>) => {
            if (action.payload?.product) {
                if (!state.selfOrderingData) {
                    state.selfOrderingData = []
                }
                state.selfOrderingData = [...state.selfOrderingData, action.payload]
            }
        },
        selfOrderingChangeInCart: (state, action: PayloadAction<any>) => {
            state.selfOrderingData = action.payload
        },
        selfOrderingCartNote: (state, action: PayloadAction<any>) => {
            state.selfOrderingCartNote = action.payload
        },
        addStepSelfOrdering: (state, action: PayloadAction<any>) => {
            state.stepSelfOrdering = action.payload
        },
        markStepReversed: (state, action: PayloadAction<any>) => {
            state.stepReversed = action.payload
        },
        addStepCategory: (state, action: PayloadAction<any>) => {
            state.stepCategory = action.payload
        },
        // user website
        rootAddToCart: (state, action: PayloadAction<cartItemType>) => {
            if (action.payload?.product) {
                if (!state.rootData) {
                    state.rootData = []
                }
                state.rootData = [...state.rootData, action.payload]
            }
        },
        rootChangeInCart: (state, action: PayloadAction<any>) => {
            state.rootData = action.payload
        },
        rootToggleAddToCartSuccess: (state) => {
            state.rootAddToCartSuccess = !state.rootAddToCartSuccess
        },
        changeType: (state, action: PayloadAction<any>) => {
            state.type = action.payload
        },
        changeTypeFlag: (state, action: PayloadAction<any>) => {
            state.typeFlag = action.payload
        },
        changeRootCartItemTmp: (state, action: PayloadAction<any>) => {
            state.rootCartItemTmp = action.payload
        },
        changeRootCartTmp: (state, action: PayloadAction<any>) => {
            state.rootCartTmp = action.payload
        },
        changeRootCartHistory: (state, action: PayloadAction<any>) => {
            state.rootCartHistory = action.payload
        },
        changeRootCartTotalPrice: (state, action: PayloadAction<any>) => {
            state.rootCartTotalPrice = action.payload
        },
        rootCartTotalDiscount: (state, action: PayloadAction<any>) => {
            state.rootCartTotalDiscount = action.payload
        },
        rootCartTotalDiscountTable: (state, action: PayloadAction<any>) => {
            state.rootCartTotalDiscountTable = action.payload
        },
        rootCartTotalDiscountSelf: (state, action: PayloadAction<any>) => {
            state.rootCartTotalDiscountSelf = action.payload
        },
        changeRootInvalidProductIds: (state, action: PayloadAction<any>) => {
            state.rootCartInvalidProductIds = action.payload
        },
        rootCartDeliveryAddress: (state, action: PayloadAction<any>) => {
            state.rootCartDeliveryAddress = action.payload
        },
        rootCartDeliveryConditions: (state, action: PayloadAction<any>) => {
            state.rootCartDeliveryConditions = action.payload
        },
        rootCartDeliveryOpen: (state, action: PayloadAction<any>) => {
            state.rootCartDeliveryOpen = action.payload
        },
        rootCartDatetime: (state, action: PayloadAction<any>) => {
            state.rootCartDatetime = action.payload
        },
        rootCartNote: (state, action: PayloadAction<any>) => {
            state.rootCartNote = action.payload
        },
        rootCartValidCouponProductIds: (state, action: PayloadAction<any>) => {
            state.rootCartValidCouponProductIds = action.payload
        },
        rootCartValidCouponProductIdsTable: (state, action: PayloadAction<any>) => {
            state.rootCartValidCouponProductIdsTable = action.payload
        },
        rootCartValidCouponProductIdsSelf: (state, action: PayloadAction<any>) => {
            state.rootCartValidCouponProductIdsSelf = action.payload
        },
        resetRootCart: (state) => {
            state.rootData = []
        },
        rootCartRedeemId: (state, action: PayloadAction<any>) => {
            state.rootCartRedeemId = action.payload
        },
        manualChangeOrderTypeDesktop: (state, action: PayloadAction<any>) => {
            state.changeOrderTypeDesktop = action.payload
        },
        handleTypeBeforeChange: (state, action: PayloadAction<any>) => {
            state.typeBeforeChange = action.payload
        },
        handleShowCartItem: (state, action: PayloadAction<any>) => {
            state.showCartItem = action.payload
        },

        /**
         * Add coupon to cart
         *
         * @param state
         * @param action
         */
        addCouponToCart: (state, action: PayloadAction<any>) => {
            state.coupon = action.payload;
        },

        /**
        * Add coupon to cart
        *
        * @param state
        * @param action
        */
        addCouponToCartTable: (state, action: PayloadAction<any>) => {
            state.couponTable = action.payload;
        },

        /**
        * Add coupon to cart
        *
        * @param state
        * @param action
        */
        addCouponToCartSelf: (state, action: PayloadAction<any>) => {
            state.couponSelf = action.payload;
        },

                /**
        * Add coupon to cart
        *
        * @param state
        * @param action
        */
        addGroupOrderSelected: (state, action: PayloadAction<any>) => {
            state.groupOrderSelected = action.payload;
        },

        /**
        * Add coupon to cart
        *
        * @param state
        * @param action
        */
        addGroupOrderSelectedNow: (state, action: PayloadAction<any>) => {
            state.groupOrderSelectedNow = action.payload;
        },

        addOpenLoginDesktop: (state, action: PayloadAction<any>) => {
            state.openDeskTopLogin = action.payload;
        },

        addFormGroupOpen: (state, action: PayloadAction<any>) => {
            state.formGroupOpen = action.payload;
        },

        addReadyDelivery: (state, action: PayloadAction<any>) => {
            state.readyDelivery = action.payload;
        },

        addCloseDetail: (state, action: PayloadAction<any>) => {
            state.closeDetail = action.payload;
        },
        addOpenEditProfileSuccess: (state, action: PayloadAction<any>) => {
            state.openEditProfileSuccess = action.payload;
        },
        addIsReadyTakeOut: (state, action: PayloadAction<any>) => {
            state.isReadyTakeOut = action.payload;
        },
        addMaxHeightPhoto: (state, action: PayloadAction<any>) => {
            state.maxHeightPhoto = action.payload;
        },
        addMaxHeightPhotoLoaded: (state, action: PayloadAction<any>) => {
            state.maxHeightPhotoLoaded = action.payload;
        },
        addMaxHeightNonePhotoLoaded: (state, action: PayloadAction<any>) => {
            state.maxHeightNonePhotoLoaded = action.payload;
        },
        addMaxHeightNonePhoto: (state, action: PayloadAction<any>) => {
            state.maxHeightNonePhoto = action.payload;
        },
        /**
         * Add payment method to cart
         *
         * @param state
         * @param action
        */
        addPaymentMethodToCart: (state, action: PayloadAction<any>) => {
            state.paymentMethod = action.payload;
        },

        /**
         * Remove coupon from cart
         */
        removeCouponFromCart: (state) => {
            state.coupon = null;
        },
        changeCartLimitTimeToPayment: (state, action: PayloadAction<any>) => {
            state.cartLimitTimeToPayment = action.payload;
        },
        changeTypeNotActiveErrorMessage: (state, action: PayloadAction<any>) => {
            state.typeNotActiveErrorMessage = action.payload;
        },
        changeTypeNotActiveErrorMessageContent: (state, action: PayloadAction<any>) => {
            state.typeNotActiveErrorMessageContent = action.payload;
        },
        changeIsShowRedeemGlobal: (state, action: PayloadAction<any>) => {
            state.isShowRedeemGlobal = action.payload;
        },
        addCartTotalPriceNeedToPay: (state, action: PayloadAction<any>) => {
            state.totalPriceNeedToPay = action.payload;
        },
    }
})

// Export actions
export const {
    // table ordering
    addToCart,
    changeInCart,
    toggleAddToCartSuccess,
    cartNote,
    addInfoTable,
    addStepTable,
    addStepRoot,
    openRegisterPortal,
    addStepCategory,
    // self ordering
    selfOrderingAddToCart,
    selfOrderingChangeInCart,
    selfOrderingCartNote,
    addInfoSelfOrder,
    addStepSelfOrdering,
    markStepReversed,
    // user website
    rootAddToCart,
    rootChangeInCart,
    rootToggleAddToCartSuccess,
    changeType,
    changeTypeFlag,
    changeRootCartItemTmp,
    changeRootCartTmp,
    changeRootCartHistory,
    changeRootCartTotalPrice,
    removeCouponFromCart,
    changeRootInvalidProductIds,
    rootCartDeliveryAddress,
    addCouponToCart,
    addCouponToCartTable,
    addCouponToCartSelf,
    addGroupOrderSelected,
    addGroupOrderSelectedNow,
    resetRootCart,
    rootCartNote,
    rootCartValidCouponProductIds,
    rootCartValidCouponProductIdsTable,
    rootCartValidCouponProductIdsSelf,
    rootCartDeliveryConditions,
    rootCartTotalDiscount,
    rootCartTotalDiscountTable,
    rootCartTotalDiscountSelf,
    rootCartRedeemId,
    rootCartDeliveryOpen,
    rootCartDatetime,
    manualChangeOrderTypeDesktop,
    handleTypeBeforeChange,
    addPaymentMethodToCart,
    addOpenLoginDesktop,
    addFormGroupOpen,
    addReadyDelivery,
    addCloseDetail,
    addOpenEditProfileSuccess,
    addIsReadyTakeOut,
    addMaxHeightPhoto,
    addMaxHeightNonePhoto,
    addMaxHeightPhotoLoaded,
    addMaxHeightNonePhotoLoaded,
    changeCartLimitTimeToPayment,
    changeTypeNotActiveErrorMessage,
    changeTypeNotActiveErrorMessageContent,
    handleShowCartItem,
    changeIsShowRedeemGlobal,
    addCartTotalPriceNeedToPay
} = cartSlice.actions

// Select state currentUser from slice
export const getCart = (state: any) => state.cart

// Export reducer
export default cartSlice.reducer
