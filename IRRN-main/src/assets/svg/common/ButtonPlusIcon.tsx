import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
import { memo } from 'react';

const SvgComponent = (props: SvgProps) => (
    <Svg
        width={17}
        height={18}
        viewBox="0 0 17 18"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
    >
        <Path
            d="M8.11523 1.5144V16.3914"
            stroke={props.stroke || '#B5B268'}
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
        <Path
            d="M1.04834 8.95288H15.183"
            stroke={props.stroke || '#B5B268'}
            strokeWidth={2}
            strokeLinecap="round"
            strokeLinejoin="round"
        />
    </Svg>
);

const ButtonPlusIcon = memo(SvgComponent);
export default ButtonPlusIcon;
