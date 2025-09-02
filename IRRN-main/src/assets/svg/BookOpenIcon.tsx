import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={36}
        height={31}
        viewBox='0 0 36 31'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M3 3.875H12C13.5913 3.875 15.1174 4.41934 16.2426 5.38828C17.3679 6.35722 18 7.67138 18 9.04167V27.125C18 26.0973 17.5259 25.1117 16.682 24.385C15.8381 23.6583 14.6935 23.25 13.5 23.25H3V3.875Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M33 3.875H24C22.4087 3.875 20.8826 4.41934 19.7574 5.38828C18.6321 6.35722 18 7.67138 18 9.04167V27.125C18 26.0973 18.4741 25.1117 19.318 24.385C20.1619 23.6583 21.3065 23.25 22.5 23.25H33V3.875Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
