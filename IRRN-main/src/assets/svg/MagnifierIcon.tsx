import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 24}
        height={props.height || 24}
        viewBox='0 0 25 24'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M11.673 19c4.333 0 7.846-3.582 7.846-8s-3.513-8-7.846-8-7.846 3.582-7.846 8 3.513 8 7.846 8Zm9.807 2-4.265-4.35'
            stroke={props.stroke || '#B5B268'}
            strokeWidth={props.strokeWidth || 2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
