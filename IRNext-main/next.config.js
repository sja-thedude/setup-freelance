/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: false,
    images: {
        remotePatterns: [
            {
                hostname: "**",
            }
        ],
    },
    webpack: (config, { isServer }) => {
        if (!isServer) {
            const timestamp = Date.now(); // Generate unique timestamp
            config.output.filename = `static/chunks/[name].[contenthash].${timestamp}.js`;
            config.output.chunkFilename = `static/chunks/[name].[contenthash].${timestamp}.js`;                    
        }
        return config;
    },
}

module.exports = nextConfig
