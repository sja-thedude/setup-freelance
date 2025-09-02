import * as React from 'react';
import Svg, {
    SvgProps,
    Circle,
    Path,
    Defs,
    LinearGradient,
    Stop,
} from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 52}
        height={props.height || 52}
        viewBox='0 0 52 52'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Circle
            cx={26}
            cy={26}
            r={26}
            fill='#fff'
        />
        <Path
            d='M22.664 46C13.222 44.328 6 36.055 6 26.112 6 15.05 15 6 26 6c11.002 0 20 9.05 20 20.112C46 36.055 38.778 44.325 29.333 46l-1.11-.89h-4.445l-1.114.89Z'
            fill='url(#a)'
        />
        <Path
            d='m33.904 32.653.876-5.517h-5.264v-3.862c0-1.545.548-2.759 2.96-2.759H35V15.44c-1.427-.22-2.96-.44-4.389-.44-4.497 0-7.678 2.76-7.678 7.724v4.414H18v5.516h4.933V46.67a16.52 16.52 0 0 0 6.58 0V32.657l4.39-.004Z'
            fill='#fff'
        />
        <Defs>
            <LinearGradient
                id='a'
                x1={26.024}
                y1={44.456}
                x2={26.024}
                y2={5.623}
                gradientUnits='userSpaceOnUse'
            >
                <Stop stopColor='#0062E0' />
                <Stop
                    offset={1}
                    stopColor='#19AFFF'
                />
            </LinearGradient>
        </Defs>
    </Svg>
);
export default SVGComponent;
