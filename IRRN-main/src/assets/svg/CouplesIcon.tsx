import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 11}
        height={props.height || 9}
        viewBox='0 0 11 9'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M8 8.5v-.667c0-.353-.184-.692-.513-.942C7.16 6.64 6.714 6.5 6.25 6.5h-3.5c-.464 0-.91.14-1.237.39-.329.25-.513.59-.513.943V8.5m3.5-5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm5.5 5v-.681a1.38 1.38 0 0 0-.28-.834A1.332 1.332 0 0 0 9 6.5m-2-6c.286.085.54.279.72.55.182.272.28.606.28.95s-.098.678-.28.95A1.333 1.333 0 0 1 7 3.5'
            stroke={props.stroke || '#fff'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
