'use client'
import { useRouter , usePathname , useSearchParams } from 'next/navigation'
export default function BackButton({id}:{id: any}) {
    const router = useRouter()
    const searchParams = useSearchParams()
    const pathname = usePathname()
    const handleClick = () => {
        if(id){
            localStorage.setItem('prevItem', id);
        }
        if(searchParams.get('from')=='productSuggestion'){
            if(pathname.includes('category')){
                router.push(`/category/cart`)
            }else if(pathname.includes('table-ordering')){
                router.push(`/table-ordering/cart`)
            }
        } else {
            if(pathname.includes('category')){
                router.push(`/category/products`)
            } else if(pathname.includes('table-ordering')){
                router.push(`/table-ordering/products`)
            } else if(pathname.includes('self-ordering')){
                router.push(`/self-ordering/products`)
            }
        }
    }
    
    return (
        <>
            <div onClick={handleClick} >
                <svg width="57" height="56" viewBox="0 0 57 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_d_297_3117)">
                        <path d="M45.7598 22.5168C45.7598 31.601 38.0797 39.0335 28.5198 39.0335C18.9599 39.0335 11.2798 31.601 11.2798 22.5168C11.2798 13.4325 18.9599 6 28.5198 6C38.0797 6 45.7598 13.4325 45.7598 22.5168Z" stroke="#413E38" strokeWidth="2" />
                    </g>
                    <path d="M34.5995 16.6777L22.4395 28.3555" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M22.4395 16.6777L34.5995 28.3555" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    <defs>
                        <filter id="filter0_d_297_3117" x="0.279785" y="0" width="56.48" height="55.0334" filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                            <feFlood floodOpacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="5" />
                            <feGaussianBlur stdDeviation="5" />
                            <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.15 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_297_3117" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_297_3117" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>
        </>
    );
};
