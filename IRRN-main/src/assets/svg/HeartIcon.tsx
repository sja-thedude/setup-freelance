import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={props.width || 24}
        height={props.height || 24}
        viewBox='0 0 24 24'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M17.8434 6.15664C17.4768 5.78995 17.0417 5.49907 16.5627 5.30061C16.0837 5.10215 15.5704 5 15.0519 5C14.5335 5 14.0201 5.10215 13.5411 5.30061C13.0621 5.49907 12.627 5.78995 12.2605 6.15664L11.4998 6.91729L10.7392 6.15664C9.99882 5.4163 8.9947 5.00038 7.94771 5.00038C6.90071 5.00038 5.89659 5.4163 5.15626 6.15664C4.41592 6.89698 4 7.90109 4 8.94809C4 9.99509 4.41592 10.9992 5.15626 11.7395L5.91691 12.5002L11.4998 18.0831L17.0827 12.5002L17.8434 11.7395C18.21 11.373 18.5009 10.9379 18.6994 10.4589C18.8979 9.97992 19 9.46654 19 8.94809C19 8.42964 18.8979 7.91626 18.6994 7.43729C18.5009 6.95833 18.21 6.52316 17.8434 6.15664Z'
            fill={props.fill || '#B5B268'}
            stroke={props.stroke || '#B5B268'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
