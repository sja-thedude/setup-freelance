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
            d='M1.45801 7.99996V29.3333L11.6663 24L23.333 29.3333L33.5413 24V2.66663L23.333 7.99996L11.6663 2.66663L1.45801 7.99996Z'
            stroke={props.stroke || '#F6B545'}

            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M11.667 2.66663V24'
            stroke={props.stroke || '#F6B545'}

            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M23.333 8V29.3333'
            stroke={props.stroke || '#F6B545'}

            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
