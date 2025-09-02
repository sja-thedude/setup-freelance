import * as React from 'react';
import Svg, {
    SvgProps,
    Circle,
    G,
    Path,
    Defs,
    ClipPath,
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
        <G clipPath='url(#a)'>
            <Path
                d='M45.576 26.45c0-1.317-.117-2.567-.317-3.783H26.426v7.516h10.783c-.483 2.467-1.9 4.55-4 5.967v5h6.433c3.767-3.483 5.934-8.617 5.934-14.7Z'
                fill='#4285F4'
            />
            <Path
                d='M26.424 46c5.4 0 9.917-1.8 13.217-4.85l-6.433-5c-1.8 1.2-4.084 1.933-6.784 1.933-5.216 0-9.633-3.516-11.216-8.266H8.574v5.15C11.858 41.5 18.608 46 26.424 46Z'
                fill='#34A853'
            />
            <Path
                d='M15.21 29.817A11.605 11.605 0 0 1 14.575 26c0-1.333.233-2.617.633-3.817v-5.15H8.576a19.768 19.768 0 0 0 0 17.934l6.633-5.15Z'
                fill='#FBBC05'
            />
            <Path
                d='M26.424 13.917c2.95 0 5.584 1.016 7.667 3l5.7-5.7C36.34 7.983 31.824 6 26.424 6c-7.816 0-14.566 4.5-17.85 11.033l6.634 5.15c1.583-4.75 6-8.266 11.216-8.266Z'
                fill='#EA4335'
            />
        </G>
        <Defs>
            <ClipPath id='a'>
                <Path
                    fill='#fff'
                    d='M6 6h40v40H6z'
                />
            </ClipPath>
        </Defs>
    </Svg>
);
export default SVGComponent;
