import OrderingLayout from "../components/layouts/ordering";

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <OrderingLayout>
            {children}
        </OrderingLayout>
    )
}