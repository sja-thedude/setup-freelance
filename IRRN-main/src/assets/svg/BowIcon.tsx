import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 11}
        height={props.height || 16}
        viewBox='0 0 11 16'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M5.375 9.75C7.79125 9.75 9.75 7.79125 9.75 5.375C9.75 2.95875 7.79125 1 5.375 1C2.95875 1 1 2.95875 1 5.375C1 7.79125 2.95875 9.75 5.375 9.75Z'
            stroke={props.stroke || '#B5B268'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M3.00625 9.0563L2.25 14.75L5.375 12.875L8.5 14.75L7.74375 9.05005'
            stroke={props.stroke || '#B5B268'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
