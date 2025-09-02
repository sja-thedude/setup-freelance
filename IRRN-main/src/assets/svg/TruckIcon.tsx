import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 16}
        height={props.height || 17}
        viewBox='0 0 16 17'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M10.667 2.5h-10v8.667h10V2.5Zm0 3.333h2.666l2 2v3.334h-4.666V5.833Z'
            stroke={props.stroke || '#fff'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M3.667 14.5a1.667 1.667 0 1 0 0-3.333 1.667 1.667 0 0 0 0 3.333Zm8.666 0a1.667 1.667 0 1 0 0-3.333 1.667 1.667 0 0 0 0 3.333Z'
            stroke={props.stroke || '#fff'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
