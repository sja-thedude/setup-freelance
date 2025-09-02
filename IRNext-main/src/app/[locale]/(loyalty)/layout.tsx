import LoyaltyLayout from "../components/layouts/loyalty";

export default function Layout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string }
}) {
    return (
        <LoyaltyLayout>
            {children}
        </LoyaltyLayout>
    );
}