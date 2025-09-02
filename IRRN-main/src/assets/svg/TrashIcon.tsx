import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 13}
        height={props.height || 13}
        viewBox='0 0 13 13'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M1.625 3.25h9.75m-7.042 0V2.167a1.083 1.083 0 0 1 1.084-1.083h2.167a1.083 1.083 0 0 1 1.083 1.083V3.25m1.625 0v7.584a1.083 1.083 0 0 1-1.083 1.083H3.792a1.083 1.083 0 0 1-1.083-1.083V3.25h7.583ZM5.417 5.958v3.25m2.166-3.25v3.25'
            stroke={props.stroke || '#B5B268'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
