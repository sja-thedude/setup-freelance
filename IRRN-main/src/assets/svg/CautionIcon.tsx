import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 15}
        height={props.height || 15}
        viewBox='0 0 15 15'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M7.5 13.913c3.59 0 6.5-2.89 6.5-6.456C14 3.89 11.09 1 7.5 1S1 3.89 1 7.457c0 3.566 2.91 6.456 6.5 6.456Zm0-10.43V8.45m0 2.483v.199'
            stroke={props.stroke || '#D94B2C'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
