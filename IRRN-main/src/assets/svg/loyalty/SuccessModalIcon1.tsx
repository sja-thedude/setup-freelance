import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={106}
        height={106}
        viewBox='0 0 106 106'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M103 48.4286V53.0286C102.994 63.8107 99.5025 74.302 93.0466 82.9377C86.5908 91.5735 77.5164 97.891 67.1768 100.948C56.8371 104.005 45.7863 103.638 35.6723 99.9015C25.5584 96.1649 16.9233 89.2591 11.0548 80.2139C5.18633 71.1688 2.39896 60.4689 3.10838 49.7102C3.81781 38.9514 7.98603 28.7102 14.9914 20.514C21.9968 12.3177 31.4639 6.60553 41.9809 4.22935C52.498 1.85317 63.5013 2.9403 73.35 7.32862'
            stroke='#91A900'
            strokeWidth={6}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='M103 13.0293L53 63.0793L38 48.0793'
            stroke='#91A900'
            strokeWidth={6}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
