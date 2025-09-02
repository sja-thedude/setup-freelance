import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const           SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 24}
        height={props.height || 24}
        viewBox='0 0 24 24'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            fill='none'
            stroke={props.stroke || '#413E38'}
            strokeWidth={2}
            strokeLinecap="round"
            d='M2,19 L22,19 M2,5 L22,5 M2,12 L22,12'
        />
    </Svg>
);
export default SVGComponent;
