import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import { StyleSheet } from 'react-native';
import { useEffectOnce } from 'react-use';

import InputComponent, { InputComponentProps } from '@src/components/InputComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { getPhoneAreaCode } from '@src/utils';

import SelectAreaCodeComponent from './SelectAreaCodeComponent';

interface IProps extends InputComponentProps {
    onChangeText: (_text: string) => void;
    error: any,
    alwaysShowBorder?: boolean
}

const PhoneInputComponent: FC<IProps> = ({ onChangeText, alwaysShowBorder, ...rest }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const ref = useRef<any>();

    const [areaCode, setAreaCode] = useState(getPhoneAreaCode(rest.value));

    const phone = useMemo(() => rest.value ? rest.value?.substr(3, rest.value.length - 1) : '', [rest.value]);

    // console.log('phoneALl', rest.value);
    // console.log('phone', phone);
    // console.log('areaCode', areaCode);

    const handleOnChangeText = useCallback((text: string) => {
        if (text) {
            onChangeText(`${areaCode}${text}`);
        } else {
            onChangeText('');
        }
    }, [areaCode, onChangeText]);

    const openPopup = useCallback(() => {
        ref?.current?.showPopup();
    }, []);

    const updateAreaCode = useCallback((code: string) => {
        setAreaCode(code);
        onChangeText(`${code}${phone}`);
    }, [onChangeText, phone]);

    useEffectOnce(() => {
        setTimeout(() => {
            if (phone) {
                onChangeText(`${areaCode}${phone}`);
            } else {
                onChangeText(`${areaCode}`);
            }
        }, 500);
    });

    useEffect(() => {
        setAreaCode(getPhoneAreaCode(rest.value));
    }, [rest.value]);

    return (
        <InputComponent
            {...rest}
            value={phone}
            onChangeText={handleOnChangeText}
            inputContainerStyle={{ borderWidth: alwaysShowBorder ? 1 : rest.error ? 1 : 0 }}
            leftIconPress={openPopup}
            leftIconContainerStyle={styles.phoneInputLeftIcon}
            leftIcon={(
                <SelectAreaCodeComponent
                    ref={ref}
                    error={rest.error}
                    areaCode={areaCode}
                    setAreaCode={updateAreaCode}
                />
            )}
        />
    );
};

export default memo(PhoneInputComponent);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    phoneInputLeftIcon: { backgroundColor: '#E6E6E6', paddingRight: Dimens.W_12 },

    popOverStyle: {
        borderRadius: Dimens.RADIUS_3,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: 'white',
    },
});