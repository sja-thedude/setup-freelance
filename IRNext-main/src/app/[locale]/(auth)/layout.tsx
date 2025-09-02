import AuthLayout from "../components/layouts/auth";

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <AuthLayout>
            <div style={{ maxWidth: '100%' }}>
                {children}
            </div>
        </AuthLayout>
    )
}