import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 24}
        height={props.height || 25}
        viewBox='0 0 24 25'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M1 12.5s4-7.667 11-7.667S23 12.5 23 12.5s-4 7.667-11 7.667S1 12.5 1 12.5Z'
            stroke={props.stroke || '#000'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M12 15.375c1.657 0 3-1.287 3-2.875s-1.343-2.875-3-2.875-3 1.287-3 2.875 1.343 2.875 3 2.875ZM5.378 1.318l13.94 22.304'
            stroke={props.stroke || '#000'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
