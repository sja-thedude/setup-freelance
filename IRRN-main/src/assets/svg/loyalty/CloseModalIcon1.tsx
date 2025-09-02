import * as React from 'react';

import Svg, {
    Defs,
    G,
    Path,
    SvgProps,
} from 'react-native-svg';

/* SVGR has dropped some elements not supported by react-native-svg: filter */
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={57}
        height={56}
        viewBox='0 0 57 56'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <G filter='url(#a)'>
            <Path
                d='M45.76 22.517c0 9.084-7.68 16.516-17.24 16.516-9.56 0-17.24-7.432-17.24-16.516C11.28 13.432 18.96 6 28.52 6c9.56 0 17.24 7.432 17.24 16.517Z'
                stroke={props.stroke || '#413E38'}
                strokeWidth={2}
            />
        </G>
        <Path
            d='M34.6 16.678 22.44 28.355m0-11.677L34.6 28.355'
            stroke={props.stroke || '#413E38'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Defs></Defs>
    </Svg>
);
export default SVGComponent;
