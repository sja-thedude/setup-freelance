import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 207}
        height={props.height || 220}
        viewBox='0 0 207 220'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M8.625 55v146.667L69 165l69 36.667L198.375 165V18.333L138 55 69 18.333 8.625 55ZM69 18.333V165m69-110v146.667'
            stroke='#fff'
            strokeWidth={5}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M192 71.5c0 17.5-20.5 32.5-20.5 32.5S151 89 151 71.5c0-5.967 2.16-11.69 6.004-15.91 3.845-4.22 9.059-6.59 14.496-6.59 5.437 0 10.651 2.37 14.496 6.59C189.84 59.81 192 65.533 192 71.5Z'
            stroke='#fff'
            strokeWidth={4}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M171.5 79c3.59 0 6.5-3.358 6.5-7.5 0-4.142-2.91-7.5-6.5-7.5s-6.5 3.358-6.5 7.5c0 4.142 2.91 7.5 6.5 7.5ZM58 132.5C58 150 37.5 165 37.5 165S17 150 17 132.5c0-5.967 2.16-11.69 6.004-15.91 3.845-4.219 9.06-6.59 14.496-6.59 5.437 0 10.651 2.371 14.496 6.59C55.84 120.81 58 126.533 58 132.5Z'
            stroke='#fff'
            strokeWidth={4}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M37.5 140c3.59 0 6.5-3.358 6.5-7.5 0-4.142-2.91-7.5-6.5-7.5s-6.5 3.358-6.5 7.5c0 4.142 2.91 7.5 6.5 7.5Z'
            stroke='#fff'
            strokeWidth={4}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
