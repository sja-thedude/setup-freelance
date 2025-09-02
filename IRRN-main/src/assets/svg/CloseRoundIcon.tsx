import * as React from 'react';

import Svg, {
    ClipPath,
    Defs,
    G,
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={13}
        height={13}
        viewBox='0 0 13 13'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <G
            clipPath='url(#a)'
            stroke={props.stroke || '#B4B4B4'}
            strokeLinecap='round'
            strokeLinejoin='round'
        >
            <Path d='M6.717 12.133a5.417 5.417 0 1 0 0-10.833 5.417 5.417 0 0 0 0 10.833Zm1.625-7.042-3.25 3.25m0-3.25 3.25 3.25' />
        </G>
        <Defs>
            <ClipPath id='a'>
                <Path
                    fill='#fff'
                    d='M0 0h13v13H0z'
                />
            </ClipPath>
        </Defs>
    </Svg>
);
export default SVGComponent;
