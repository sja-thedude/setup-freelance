import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 18}
        height={props.height || 22}
        viewBox='0 0 18 22'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M17 9.182C17 15.546 9 21 9 21S1 15.546 1 9.182a8.28 8.28 0 0 1 2.343-5.786A7.91 7.91 0 0 1 9 1c2.122 0 4.157.862 5.657 2.396A8.277 8.277 0 0 1 17 9.182Z'
            stroke={props.stroke || '#413E38'}
            strokeWidth={props.strokeWidth || 1.5}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M9 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'
            stroke={props.stroke || '#413E38'}
            strokeWidth={props.strokeWidth || 1.5}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
