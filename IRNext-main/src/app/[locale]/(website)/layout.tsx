import WebsiteLayout from "../components/layouts/website";

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <WebsiteLayout>
            {children}
        </WebsiteLayout>
    )
}