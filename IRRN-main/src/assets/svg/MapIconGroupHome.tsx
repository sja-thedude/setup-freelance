import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={117}
        height={124}
        viewBox='0 0 117 124'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M4.875 31v82.667L39 93l39 20.667L112.125 93V10.333L78 31 39 10.333 4.875 31ZM39 10.333V93m39-62v82.667'
            stroke='#fff'
            strokeWidth={props.strokeWidth || 3}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M108.522 40.3c0 9.864-11.587 18.318-11.587 18.318S85.348 50.164 85.348 40.3c0-3.363 1.22-6.59 3.394-8.967 2.173-2.379 5.12-3.715 8.193-3.715 3.073 0 6.02 1.336 8.193 3.715 2.173 2.378 3.394 5.604 3.394 8.967Z'
            stroke='#fff'
            strokeWidth={props.strokeWidth || 3}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M96.935 44.527c2.029 0 3.674-1.892 3.674-4.227 0-2.335-1.645-4.227-3.674-4.227-2.03 0-3.674 1.892-3.674 4.227 0 2.335 1.645 4.227 3.674 4.227ZM32.783 74.682C32.783 84.546 21.196 93 21.196 93S9.609 84.546 9.609 74.682c0-3.364 1.22-6.59 3.393-8.968S18.122 62 21.196 62c3.073 0 6.02 1.336 8.193 3.714 2.173 2.379 3.394 5.604 3.394 8.968Z'
            stroke='#fff'
            strokeWidth={props.strokeWidth || 3}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M21.196 78.91c2.029 0 3.674-1.894 3.674-4.228 0-2.335-1.645-4.227-3.674-4.227-2.03 0-3.674 1.892-3.674 4.227 0 2.334 1.645 4.227 3.674 4.227Z'
            stroke='#fff'
            strokeWidth={props.strokeWidth || 3}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M24.87 72.709c17.521-8.455 24.388 13.319 33.065 10.145 11.292-4.13-4.934-30.33-1.13-41.709 4.521-13.527 35.608 0 35.608 0'
            stroke='#fff'
            strokeWidth={props.strokeWidth || 3}
            strokeDasharray='10 10'
        />
    </Svg>
);
export default SVGComponent;
