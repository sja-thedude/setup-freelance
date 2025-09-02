import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={42}
        height={40}
        viewBox='0 0 42 40'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M35 11.6666H7C5.067 11.6666 3.5 13.159 3.5 15V31.6666C3.5 33.5076 5.067 35 7 35H35C36.933 35 38.5 33.5076 38.5 31.6666V15C38.5 13.159 36.933 11.6666 35 11.6666Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M28 35V8.33333C28 7.44928 27.6313 6.60143 26.9749 5.97631C26.3185 5.35119 25.4283 5 24.5 5H17.5C16.5717 5 15.6815 5.35119 15.0251 5.97631C14.3687 6.60143 14 7.44928 14 8.33333V35'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={2}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
