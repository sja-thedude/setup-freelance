import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 24}
        height={props.height || 18}
        viewBox='0 0 24 18'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M1 9s4-8 11-8 11 8 11 8-4 8-11 8S1 9 1 9Z'
            stroke={props.stroke || '#000'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'
            stroke={props.stroke || '#000'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
