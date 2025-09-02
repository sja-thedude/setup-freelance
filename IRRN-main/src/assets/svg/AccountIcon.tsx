import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={30}
        height={32}
        viewBox='0 0 30 32'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M28.3327 31V27.6667C28.3327 25.8986 27.6303 24.2029 26.3801 22.9526C25.1298 21.7024 23.4341 21 21.666 21H8.33268C6.56457 21 4.86888 21.7024 3.61864 22.9526C2.36839 24.2029 1.66602 25.8986 1.66602 27.6667V31'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M15.0007 14.3333C18.6826 14.3333 21.6673 11.3486 21.6673 7.66667C21.6673 3.98477 18.6826 1 15.0007 1C11.3188 1 8.33398 3.98477 8.33398 7.66667C8.33398 11.3486 11.3188 14.3333 15.0007 14.3333Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
