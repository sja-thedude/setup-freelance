export default function LoyaltyLayout({children}: {children: React.ReactNode}) {
    return (
        <>
            <main>
                <div className="container" style={{ background: "F8F8F8" }}>
                    {children}
                </div>
            </main>
        </>
    )
}