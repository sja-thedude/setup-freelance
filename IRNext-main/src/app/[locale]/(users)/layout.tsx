import ProfileLayout from "../components/layouts/profile";

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <ProfileLayout>
            {children}
        </ProfileLayout>
    )
}