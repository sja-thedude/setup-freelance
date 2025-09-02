import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={32}
        height={35}
        viewBox='0 0 32 35'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M5.3335 28.4375C5.3335 27.4705 5.68469 26.5432 6.30981 25.8595C6.93493 25.1757 7.78277 24.7916 8.66683 24.7916H26.6668'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M8.66683 2.91663H26.6668V32.0833H8.66683C7.78277 32.0833 6.93493 31.6992 6.30981 31.0155C5.68469 30.3317 5.3335 29.4044 5.3335 28.4375V6.56246C5.3335 5.59552 5.68469 4.66819 6.30981 3.98447C6.93493 3.30074 7.78277 2.91663 8.66683 2.91663V2.91663Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
