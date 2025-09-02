import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 207}
        height={props.height || 220}
        viewBox='0 0 191 195'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M159.167 170.625v-16.25c0-8.62-3.354-16.886-9.324-22.981-5.97-6.095-14.067-9.519-22.51-9.519H63.667c-8.443 0-16.54 3.424-22.51 9.519-5.97 6.095-9.324 14.361-9.324 22.981v16.25M95.5 89.375c17.581 0 31.833-14.55 31.833-32.5s-14.252-32.5-31.833-32.5c-17.581 0-31.833 14.55-31.833 32.5s14.252 32.5 31.833 32.5Z'
            stroke='#fff'
            strokeWidth={5}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M128.5 167c8.008 0 14.5-6.716 14.5-15 0-8.284-6.492-15-14.5-15s-14.5 6.716-14.5 15c0 8.284 6.492 15 14.5 15Z'
            stroke='#fff'
            strokeWidth={4}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M119.662 165.02 117 183l11-5.921L139 183l-2.662-18'
            stroke='#fff'
            strokeWidth={4}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
