import * as React from 'react';

import Svg, {
    Path,
    SvgProps,
} from 'react-native-svg';

const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 8}
        height={props.height || 12}
        viewBox='0 0 8 12'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            transform='matrix(-.73934 -.67333 -.52549 .8508 6.588 8)'
            stroke={props.stroke || '#413E38'}
            strokeWidth={3}
            strokeLinecap='round'
            d='M1.5-1.5h5.911'
        />
        <Path
            transform='matrix(-.82576 .56403 .42032 .90738 7.588 7)'
            stroke={props.stroke || '#413E38'}
            strokeWidth={3}
            strokeLinecap='round'
            d='M1.5-1.5h4.978'
        />
    </Svg>
);
export default SVGComponent;
