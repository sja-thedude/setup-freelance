import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
import { memo } from 'react';

const SvgComponent = (props: SvgProps) => (
    <Svg
        width={16}
        height={3}
        viewBox="0 0 16 3"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
    >
        <Path
            d="M1.48096 1.80298H14.6733"
            stroke={props.stroke || '#B5B268'}
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
    </Svg>
);

const MinusIcon = memo(SvgComponent);
export default MinusIcon;
