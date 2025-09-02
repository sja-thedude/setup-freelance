import { useState, useEffect } from "react";

const useQueryEditProfileParam = () => {
    const query = new URLSearchParams(window.location.search);
    const [editProfile, setEditProfile] = useState(false);

    useEffect(() => {
        const isEditProfile = query.get('editProfile');
        const editProfileParam = window.location.href.split("editProfile=");

        if((isEditProfile && isEditProfile === 'true') || (editProfileParam.length > 1 && editProfileParam[1] === 'true')) {
            setEditProfile(true);
        } else {
            setEditProfile(false);
        }
    }, [
        query.get('editProfile'),
        window.location.href
    ]);

    return editProfile;
};

export default useQueryEditProfileParam;