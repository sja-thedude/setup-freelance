import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 111}
        height={props.height || 101}
        viewBox='0 0 111 101'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M15.693 38.39a45.143 45.143 0 0 0-1.645 12.11c0 25.13 20.583 45.5 45.976 45.5S106 75.63 106 50.5 85.42 5 60.024 5C47.93 4.984 36.32 9.7 27.723 18.121'
            stroke={props.stroke || '#fff'}
            strokeWidth={10}
            strokeMiterlimit={22.93}
            strokeLinejoin='round'
        />
        <Path
            fillRule='evenodd'
            clipRule='evenodd'
            d='M0 49.089h29.434L14.718 30.51 0 49.09Z'
            fill={props.stroke || '#fff'}
        />
        <Path
            d='M38.793 43.803 60.116 70.27l42.71-58.295'
            stroke={props.stroke || '#fff'}
            strokeWidth={10}
            strokeMiterlimit={22.93}
            strokeLinejoin='round'
        />
        <Path
            d='M38.793 43.803 60.116 70.27l42.71-58.295'
            stroke={props.stroke || '#fff'}
            strokeWidth={10}
            strokeMiterlimit={22.93}
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
