import * as React from 'react';
import Svg, {
    SvgProps, Circle, Path
} from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 20}
        height={props.height || 20}
        viewBox='0 0 20 20'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Circle
            cx={10}
            cy={10}
            r={9.5}
            stroke={props.stroke || '#fff'}
        />
        <Path
            d='M10.875 7.244c-.308 0-.57-.107-.784-.322a1.067 1.067 0 0 1-.322-.784c0-.308.107-.57.322-.784.215-.224.476-.336.784-.336.308 0 .57.112.784.336.224.215.336.476.336.784 0 .308-.112.57-.336.784a1.067 1.067 0 0 1-.784.322Zm-.952 7.84c-.448 0-.812-.14-1.092-.42-.27-.28-.406-.7-.406-1.26 0-.233.037-.537.112-.91L9.49 8h2.016l-1.008 4.76c-.037.14-.056.29-.056.448 0 .187.042.322.126.406.093.075.243.112.448.112.168 0 .317-.028.448-.084-.037.467-.205.826-.504 1.078a1.56 1.56 0 0 1-1.036.364Z'
            fill={props.fill || '#fff'}
        />
    </Svg>
);
export default SVGComponent;
