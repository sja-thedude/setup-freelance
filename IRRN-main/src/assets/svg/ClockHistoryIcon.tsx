import * as React from 'react';

import Svg, {
    ClipPath,
    Defs,
    G,
    Path,
    Rect,
    SvgProps,
} from 'react-native-svg';

import useThemeColors from '@src/themes/useThemeColors';

const SVGComponent = (props: SvgProps) => {
    const { themeColors } = useThemeColors();
    return (
        <Svg
            width={props.width || 24}
            height={props.width || 24}
            viewBox='0 0 24 24'
            fill='none'
            xmlns='http://www.w3.org/2000/svg'
            {...props}
        >
            <G clipPath='url(#clip0_15_1481)'>
                <Path
                    d='M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z'
                    stroke={props.stroke || '#fff'}
                    strokeWidth={2}
                    strokeLinecap='round'
                    strokeLinejoin='round'
                />
                <Path
                    d='M12 6V12L16 14'
                    stroke={props.stroke || '#fff'}
                    strokeWidth={2}
                    strokeLinecap='round'
                    strokeLinejoin='round'
                />
                <Rect
                    x={-1}
                    y={9}
                    width={7}
                    height={6}
                    fill={themeColors.color_primary}
                />
                <Path
                    d='M1.92225 11.2306L0.142104 5.63749L6.93564 8.17808L1.92225 11.2306Z'
                    fill={props.fill || '#fff'}
                />
            </G>
            <Defs>
                <ClipPath id='clip0_15_1481'>
                    <Rect
                        width={24}
                        height={24}
                        fill={props.fill || '#fff'}
                    />
                </ClipPath>
            </Defs>
        </Svg>
    );
};
export default SVGComponent;
