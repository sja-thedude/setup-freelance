import Link from 'next/link';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons';

export default function Header({apiColor}:any) {
    return (
        <div id="job_header" className="container-fluid" style={{ backgroundColor: apiColor }}>
            <h1>
                <Link href="/">
                    <FontAwesomeIcon
                        icon={faChevronLeft}
                        className={'bn-back'} />
                </Link>
                <span className={'header-title'}>Jobs</span>
            </h1>
        </div>
    );
}