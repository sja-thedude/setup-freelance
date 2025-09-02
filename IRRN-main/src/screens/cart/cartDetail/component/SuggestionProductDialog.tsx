import React, { useCallback } from 'react';

import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { ProductSuggestionModel, } from '@src/network/dataModels/ProductSuggestionModel';

import SuggestionProductItem from './SuggestionProductItem';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    suggestionProducts?: Array<ProductSuggestionModel>,
    checkOpeningHour: Function,
}

const SuggestionProductDialog = ({ isShow, hideModal, suggestionProducts, checkOpeningHour }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleDismissModal = useCallback(() => {
        hideModal();
        InteractionManager.runAfterInteractions(() => {
            checkOpeningHour(true);
        });
    }, [checkOpeningHour, hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
            onSwipeComplete={handleDismissModal}
            onBackdropPress={handleDismissModal}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_this_fits_nicely')}
            </TextComponent>

            <ScrollViewComponent
                style={styles.scrollView}
            >
                <View style={styles.listContainer}>
                    {suggestionProducts?.map((product, index) => (
                        <SuggestionProductItem
                            key={index}
                            item={product}
                            hideModal={hideModal}
                        />
                    ))}
                </View>
            </ScrollViewComponent>

            <ButtonComponent
                title={t('Verder met bestellen')}
                style={styles.textAButton}
                onPress={handleDismissModal}
            />
        </DialogComponent>
    );
};

export default SuggestionProductDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    listContainer: { paddingTop: Dimens.H_16, paddingHorizontal: Dimens.W_8 },
    scrollView: {
        maxHeight: Dimens.SCREEN_HEIGHT / 3,
        marginHorizontal: -Dimens.W_8,
    },
    textAButton: {
        width: '60%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textBButton: {
        width: '60%',
        alignSelf: 'center',
        backgroundColor: 'transparent',
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
        marginBottom: Dimens.W_8,
    },
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginTop: Dimens.H_16,
    },
});