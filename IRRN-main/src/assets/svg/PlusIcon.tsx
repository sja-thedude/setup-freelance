import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 16}
        height={props.height || 16}
        viewBox='0 0 16 16'
        xmlns='http://www.w3.org/2000/svg'
        fill='currentColor'
        className='bi bi-plus'
        {...props}
    >
        <Path
            fill={props.fill}
            strokeWidth={0}
            d='M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z'
        />
    </Svg>
);
export default SVGComponent;
