import * as React from 'react';
import Svg, {
    Circle, Defs, Pattern, Use, Image,
    SvgProps
} from 'react-native-svg';
const SVGComponent = (props: SvgProps) => (
    <Svg
        width={20}
        height={20}
        viewBox="0 0 20 20"
        fill="none"
        {...props}
    >
        <Circle
            cx={10}
            cy={10}
            r={10}
            fill="url(#a)"
        />
        <Defs>
            <Pattern
                id="a"
                patternContentUnits="objectBoundingBox"
                width={1}
                height={1}
            >
                <Use
                    xlinkHref="#b"
                    transform="translate(-.333)scale(.0013)"
                />
            </Pattern>
            <Image
                id="b"
                width={1280}
                height={768}
                xlinkHref="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABQAAAAMAAgMAAAAGWiJeAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAADFBMVEUAAADdAAD/zgD///9i/kqcAAAAAWJLR0QDEQxM8gAAAAd0SU1FB+EICgkYFjcf36UAAALOSURBVHja7dAxAQAACAMgS1rSlIaYhwdEoAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHmgiAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQgUKFAgAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQgUKFAgAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQi8CRwiAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQgUKFAgAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQgUKFAgAgUKFCgQgQIFCkSgQIECEShQoEAEChQoEIECBQpEoECBAhEoUKBABAoUKBCBAgUKRKBAgQIFIlCgQIEIFChQIAIFChSIQIECBSJQoECBCBQoUCACBQoUiECBAgUiUKBAgQg8sfBY0qNA+DnUAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE3LTA4LTEwVDA5OjI0OjIxKzAwOjAwo+eo0wAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxNy0wOC0xMFQwOToyNDoyMSswMDowMNK6EG8AAAAASUVORK5CYII="
            />
        </Defs>
    </Svg>
);
export default SVGComponent;
