import type { Metadata } from 'next';
import { Providers } from "@/redux/provider";
import ClientLayout from './clientLayout';
import { headers } from 'next/headers';
import WorkspaceLayout from './workspaceLayout';

export const metadata: Metadata = {
    title: "It's Ready",
    description: "It's Ready is restaurant platform",
    viewport: "width=device-width, initial-scale=1, maximum-scale=1",
};

export default async function RootLayout({
    children,
    params
}: {
    children: React.ReactNode
    params: { locale: string, workspaceId: any, workspaceToken: any, workspaceColor: any }
}) {
    params.workspaceId = headers().get('x-next-workspace')
    params.workspaceToken = headers().get('x-next-workspace-token')
    params.workspaceColor = headers().get('x-next-workspace-color')

    return (
        <html lang={params.locale}>
            <body tabIndex={-1}>
                <ClientLayout params={params}>
                    <Providers>
                        <WorkspaceLayout 
                            workspaceId={params.workspaceId} 
                            workspaceToken={params.workspaceToken}
                            workspaceColor={params.workspaceColor}
                            language={params.locale}>
                            <>
                                {children}
                            </>
                        </WorkspaceLayout>                        
                    </Providers>
                </ClientLayout>                
            </body>
        </html>
    );
}