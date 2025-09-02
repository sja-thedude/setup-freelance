import variables from '/public/assets/css/footer.module.scss'
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';

export default function Footer({trans}: any){
    const currentYear = new Date().getFullYear();
    return(
        <>
            <div className={`${variables.footer}`}>
                {currentYear} © &nbsp;
                <a href={'https://b2b.itsready.be/'}
                   target="_blank">
                    {trans('its-ready')}
                </a>
                &nbsp; - &nbsp;
                <a style={{textTransform: 'lowercase'}} href={TERMS_CONDITIONS_LINK}
                   target="_blank">
                    {trans('terms-and-conditions')}
                </a>
                &nbsp; - &nbsp;
                <a style={{textTransform: 'lowercase'}} href={PRIVACY_POLICY_LINK}
                   target="_blank">
                    {trans('privacy-policy')}
                </a>
            </div>
        </>
    )
}
