import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 17}
        height={props.height || 17}
        viewBox='0 0 18 17'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M16 8.5v5.833A1.666 1.666 0 0 1 14.333 16H2.667A1.667 1.667 0 0 1 1 14.333V2.667A1.667 1.667 0 0 1 2.667 1h9.166'
            stroke={props.stroke || '#fff'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='m6 6.833 2.5 2.5L16.833 1'
            stroke={props.stroke || '#fff'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
