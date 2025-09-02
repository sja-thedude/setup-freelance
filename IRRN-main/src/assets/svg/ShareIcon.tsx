import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={35}
        height={32}
        viewBox='0 0 35 32'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M5.83301 16V26.6667C5.83301 27.3739 6.1403 28.0522 6.68728 28.5523C7.23426 29.0524 7.97613 29.3333 8.74967 29.3333H26.2497C27.0232 29.3333 27.7651 29.0524 28.3121 28.5523C28.859 28.0522 29.1663 27.3739 29.1663 26.6667V16'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M23.3337 7.99996L17.5003 2.66663L11.667 7.99996'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M17.5 2.66663V20'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
