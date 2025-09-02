import * as React from 'react';
import Svg, {
    SvgProps,
    Path,
    Defs,
    LinearGradient,
    Stop,
} from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 113}
        height={props.height || 110}
        viewBox='0 0 113 110'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M102.76 10 10 100'
            stroke='url(#a)'
            strokeWidth={20}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Path
            d='m10 10 92.76 90'
            stroke='url(#b)'
            strokeWidth={20}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
        <Defs>
            <LinearGradient
                id='a'
                x1={56.38}
                y1={10}
                x2={56.38}
                y2={100}
                gradientUnits='userSpaceOnUse'
            >
                <Stop stopColor='#EE0C0C' />
                <Stop
                    offset={1}
                    stopColor='#911515'
                />
            </LinearGradient>
            <LinearGradient
                id='b'
                x1={56.38}
                y1={10}
                x2={56.38}
                y2={100}
                gradientUnits='userSpaceOnUse'
            >
                <Stop stopColor='#EE0C0C' />
                <Stop
                    offset={1}
                    stopColor='#911515'
                />
            </LinearGradient>
        </Defs>
    </Svg>
);
export default SVGComponent;
