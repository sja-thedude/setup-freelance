import React from "react";
import style from "../../../../../../public/assets/css/profile.module.scss";

const Navbar = ({content, background} : {content : any, background : any}) => {
    return (
        <div className={`${style['navbar']} ps-0`} style={{ backgroundColor: background }}>
            <div className={style['profile-text']}>{content}</div>
        </div>
    );
}

export default Navbar;