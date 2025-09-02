import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 10}
        height={props.height || 14}
        viewBox='0 0 10 14'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M4.75 3.294v7.412M2.125 7h5.25'
            stroke={props.stroke || '#B5B268'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
