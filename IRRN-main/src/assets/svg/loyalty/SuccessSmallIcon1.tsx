import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
import { memo } from 'react';

const SvgComponent = (props: SvgProps) => (
    <Svg
        width={22}
        height={22}
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
    >
        <Path
            d="M21 10.086v.92a10 10 0 1 1-5.93-9.14"
            stroke={props.stroke || '#91A900'}
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
        <Path
            d="m21 3.006-10 10.01-3-3"
            stroke={props.stroke || '#91A900'}
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
    </Svg>
);

const SuccessSmallIcon = memo(SvgComponent);
export default SuccessSmallIcon;
