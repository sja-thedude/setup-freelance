import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 206}
        height={props.height || 199}
        viewBox='0 0 206 199'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            fillRule='evenodd'
            clipRule='evenodd'
            d='M203.222.76c.58.051 1.144.304 1.579.756a2.487 2.487 0 0 1 .678 2.06 2.5 2.5 0 0 1-.148.58L135.35 196.604a2.502 2.502 0 0 1-4.62.194l-39.597-85.752L2.017 72.923a2.5 2.5 0 0 1 .185-4.667L202.149.899a2.5 2.5 0 0 1 1.073-.14Zm-9.985 8.417L10.006 70.903l82.454 35.272L193.237 9.177ZM96.016 109.692l100.95-97.165-64.241 176.664-36.71-79.499Z'
            fill='#fff'
        />
    </Svg>
);
export default SVGComponent;
