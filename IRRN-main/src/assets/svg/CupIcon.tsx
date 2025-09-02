import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={32}
        height={32}
        viewBox='0 0 32 32'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M24 10.6666H25.3333C26.7478 10.6666 28.1044 11.2285 29.1046 12.2287C30.1048 13.2289 30.6667 14.5855 30.6667 16C30.6667 17.4144 30.1048 18.771 29.1046 19.7712C28.1044 20.7714 26.7478 21.3333 25.3333 21.3333H24'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M2.66699 10.6666H24.0003V22.6666C24.0003 24.0811 23.4384 25.4377 22.4382 26.4379C21.438 27.4381 20.0815 28 18.667 28H8.00033C6.58584 28 5.22928 27.4381 4.22909 26.4379C3.2289 25.4377 2.66699 24.0811 2.66699 22.6666V10.6666Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M8 1.33337V5.33337'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M13.333 1.33337V5.33337'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M18.667 1.33337V5.33337'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
