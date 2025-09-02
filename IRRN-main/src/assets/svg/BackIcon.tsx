import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={12}
        height={16}
        viewBox='0 0 12 16'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            transform='matrix(.74607 -.66587 .5332 .846 1.588 10.4)'
            stroke='#fff'
            strokeWidth={3}
            strokeLinecap='round'
            d='M1.5-1.5h9.615'
        />
        <Path
            stroke='#fff'
            strokeWidth={3}
            strokeLinecap='round'
            d='m2.082 8.592 7.922 5.326'
        />
    </Svg>
);
export default SVGComponent;
