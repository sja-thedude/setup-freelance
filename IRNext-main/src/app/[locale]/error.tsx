'use client'

import { useI18n } from '@/locales/client'

export default function Error({
    error,
    reset,
}: {
    error: Error
    reset: () => void
}) {
    const trans = useI18n();

    if(error) {
        console.log(error);
    }

    return (
        <div>
            <h2>{trans('lang_something_went_wrong')}!</h2>
            <button onClick={() => reset()}>{trans('lang_try_again')}</button>
        </div>
    )
}