import * as React from 'react';
import Svg, { SvgProps, Circle } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 20}
        height={props.height || 20}
        viewBox='0 0 20 20'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Circle
            cx={10}
            cy={10}
            r={9}
            stroke={props.stroke || '#717171'}
            strokeWidth={2}
        />
    </Svg>
);
export default SVGComponent;
