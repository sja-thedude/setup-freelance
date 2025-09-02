import PortalLayout from "../components/layouts/portal"

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <PortalLayout>
            <div className="container res-mobile" style={{ background: "#F8F8F8", minHeight: "100vh" }}>
                {children}
            </div>
            <div className="container res-desktop" style={{ background: "#F5F5F5", minHeight: "100vh", overflowX: "hidden" }}>
                {children}
            </div>
        </PortalLayout>
    )
}