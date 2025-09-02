'use client'

import { useI18n } from '@/locales/client'

const NotFound: React.FC = () => {
    const trans = useI18n();

    return (
        <div>
            <h1>404 - {trans('lang_not_found')}</h1>
        </div>
    );
};

export default NotFound;
