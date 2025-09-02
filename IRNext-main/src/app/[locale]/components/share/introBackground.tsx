import variables from '/public/assets/css/intro-table.module.scss'
const halfCircleTop = variables['half-circle-top'];
const halfCircleBottom = variables['half-circle-bottom'];
const introTop = variables['intro-top'];
const introBottom = variables['intro-bottom'];

export default function IntroBackground(props: any) {    
    const {position, color, rgbColor} = props;

    return (
        <>
            {position === 'top' ? (
                <div className={`row justify-content-end intro-image-custom ${variables.introImageCustom}`}>
                    <div className={`col-6 ${halfCircleTop} ${variables.item}`} style={{
                        backgroundColor: rgbColor ? `rgba(${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}, 0.6)` : 'none',
                        boxShadow: 'none'
                    }}>
                        <div className={`${introTop}`} style={{background: color}}></div>
                    </div>
                </div>
            ) : (
                <div className={`row mt-2 ${variables.introImageCustomBottom}`}>
                    <div className={`col-6 ${halfCircleBottom} ${variables.item} align-self-end`} style={{
                        backgroundColor: rgbColor ? `rgba(${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}, 0.6)` : 'none',
                        boxShadow: 'none'
                    }}>
                        <div className={`${introBottom}`} style={{background: color}}></div>
                    </div>
                </div>
            )}
        </>        
    );
}