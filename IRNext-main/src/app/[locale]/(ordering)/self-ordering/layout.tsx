export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <div className="container">
            {children}
        </div>
    )
}