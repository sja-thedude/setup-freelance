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
        viewBox='0 0 51 51'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Circle
            cx={25.5}
            cy={25.5}
            r={25.5}
            fill='#fff'
        />
        <G clipPath='url(#a)'>
            <Path
                d='M40.524 18.636c-.232.18-4.328 2.488-4.328 7.62 0 5.936 5.212 8.036 5.368 8.088-.024.128-.828 2.876-2.748 5.676-1.712 2.464-3.5 4.924-6.22 4.924-2.72 0-3.42-1.58-6.56-1.58-3.06 0-4.148 1.632-6.636 1.632-2.488 0-4.224-2.28-6.22-5.08C10.868 36.628 9 31.52 9 26.672c0-7.776 5.056-11.9 10.032-11.9 2.644 0 4.848 1.736 6.508 1.736 1.58 0 4.044-1.84 7.052-1.84 1.14 0 5.236.104 7.932 3.968Zm-9.36-7.26c1.244-1.476 2.124-3.524 2.124-5.572A3.84 3.84 0 0 0 33.212 5c-2.024.076-4.432 1.348-5.884 3.032-1.14 1.296-2.204 3.344-2.204 5.42 0 .312.052.624.076.724.128.024.336.052.544.052 1.816 0 4.1-1.216 5.42-2.852Z'
                fill='#000'
            />
        </G>
        <Defs>
            <ClipPath id='a'>
                <Path
                    fill='#fff'
                    d='M9 5h32.56v40H9z'
                />
            </ClipPath>
        </Defs>
    </Svg>
);
export default SVGComponent;
