import * as React from 'react';

import Svg, {
    Circle,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 20}
        height={props.height || 20}
        viewBox='0 0 20 20'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
        fill='none'
    >
        <Circle
            cx={10}
            cy={10}
            r={9}
            stroke={props.stroke || '#B5B268'}
            strokeWidth={2}
        />
        <Circle
            cx={10}
            cy={10}
            r={5}
            fill={props.fill || '#B5B268'}
        />
    </Svg>
);
export default SVGComponent;
