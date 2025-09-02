import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={33}
        height={32}
        viewBox='0 0 33 32'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M17.2256 8.27271V17L22.9674 19.9091'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            fillRule='evenodd'
            clipRule='evenodd'
            d='M5.95687 8.27271C8.32986 4.49859 12.4946 2 17.2257 2C24.5889 2 30.5803 8.05201 30.5803 15.5455C30.5803 23.0389 24.5889 29.0909 17.2257 29.0909C10.3455 29.0909 4.66294 23.8067 3.94713 17H1.9375C2.66153 24.8934 9.219 31.0909 17.2257 31.0909C25.7182 31.0909 32.5803 24.1185 32.5803 15.5455C32.5803 6.97243 25.7182 0 17.2257 0C11.3344 0 6.2277 3.35533 3.6521 8.27271H5.95687Z'
            fill={props.stroke || '#F6B545'}
            strokeWidth={0}
        />
        <Path
            d='M4.04259 12.4666L2.48649 5.8707L9.48809 8.5239L4.04259 12.4666Z'
            fill={props.stroke || '#F6B545'}
            strokeWidth={0}
        />
    </Svg>
);
export default SVGComponent;
