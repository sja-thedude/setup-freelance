import React, { FC } from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';

import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { IS_IOS } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';

import LoginWithAppleButton from './LoginWithAppleButton';
import LoginWithFaceBookButton from './LoginWithFaceBookButton';
import LoginWithGoogleButton from './LoginWithGoogleButton';

interface SocialComponentProps {
    fromCart?: boolean,
    isRegister: boolean,
    callback?: Function
}

const SocialComponent: FC<SocialComponentProps> = ({ fromCart, isRegister, callback }) => {
    const { t } = useTranslation();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <View style={styles.mainContainer}>
            <View style={styles.offLineContainer}>
                <View
                    style={styles.line}
                />
                <TextComponent style={styles.textOF}>
                    {t('text_register_of')}
                </TextComponent>
                <View
                    style={styles.line}
                />
            </View>

            <TextComponent style={styles.textLoginWith}>
                {t(isRegister ? 'text_register_registreren_met' : 'text_register_inloggen_met')}
            </TextComponent>

            <View style={styles.socialIconContainer}>
                <LoginWithGoogleButton
                    fromCart={fromCart}
                    callback={callback}
                    isRegister={isRegister}
                />
                <LoginWithFaceBookButton
                    fromCart={fromCart}
                    callback={callback}
                    isRegister={isRegister}
                />
                {IS_IOS && (
                    <LoginWithAppleButton
                        fromCart={fromCart}
                        callback={callback}
                        isRegister={isRegister}
                    />
                )}
            </View>
        </View>
    );
};

export default SocialComponent;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    mainContainer: { justifyContent: 'center', alignItems: 'center' },
    offLineContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_16,
        width: '100%',
    },
    socialIconContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-evenly',
        marginTop: Dimens.H_16,
        width: '100%',
        marginBottom: Dimens.H_16,
    },
    textLoginWith: {
        fontSize: Dimens.FONT_16,
        fontWeight: '600',
        color: Colors.COLOR_WHITE,
        marginTop: Dimens.H_10,
    },
    line: {
        backgroundColor: Colors.COLOR_WHITE,
        flex: 1,
        height: Dimens.H_3,
        borderRadius: Dimens.RADIUS_24,
    },
    textOF: {
        fontSize: Dimens.FONT_16,
        fontWeight: '600',
        color: Colors.COLOR_WHITE,
        marginHorizontal: Dimens.W_10,
    },
});