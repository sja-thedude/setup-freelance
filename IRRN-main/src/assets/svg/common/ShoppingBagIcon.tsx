import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
import { memo } from 'react';

const SvgComponent = (props: SvgProps) => (
    <Svg
        width={21}
        height={21}
        viewBox="0 0 21 21"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}>
        <Path
            d="M5.42529 1.42358L2.80029 4.92358V17.1736C2.80029 17.6377 2.98467 18.0828 3.31286 18.411C3.64104 18.7392 4.08616 18.9236 4.55029 18.9236H16.8003C17.2644 18.9236 17.7095 18.7392 18.0377 18.411C18.3659 18.0828 18.5503 17.6377 18.5503 17.1736V4.92358L15.9253 1.42358H5.42529Z"
            stroke="white"
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
        <Path
            d="M2.80029 4.30078H18.5503"
            stroke="white"
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
        <Path
            d="M14 7.97217C14 8.90043 13.6313 9.79066 12.9749 10.447C12.3185 11.1034 11.4283 11.4722 10.5 11.4722C9.57174 11.4722 8.6815 11.1034 8.02513 10.447C7.36875 9.79066 7 8.90043 7 7.97217"
            stroke="white"
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
    </Svg>
);

const ShoppingBagIcon = memo(SvgComponent);
export default ShoppingBagIcon;
