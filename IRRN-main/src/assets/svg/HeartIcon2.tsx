import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={17}
        height={15}
        viewBox='0 0 17 15'
        fill='none'
        xmlns='http://www.w3.org/2000/svg'
        {...props}
    >
        <Path
            d='M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z'
            stroke={props.stroke || '#F6B545'}
            strokeWidth={props.strokeWidth || 1}
            fill={props.fill || '#F6B545'}
            strokeLinecap='round'
            strokeLinejoin='round'
        />
    </Svg>
);
export default SVGComponent;
