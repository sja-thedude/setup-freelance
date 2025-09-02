import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 19}
        height={props.height || 21}
        viewBox='0 0 19 21'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M18 15.786h-6.611m-3.778 0H1M18 5.214H9.5m-3.778 0H1M11.389 19.75v-7.928M5.722 9.179V1.25'
            stroke={props.stroke || '#fff'}
            strokeWidth={props.strokeWidth || 1.5}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
