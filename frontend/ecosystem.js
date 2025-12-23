module.exports = {
    apps: [
        {
            name: "YOUR_APP_NAME-preprod",
            script: "npm",
            args: "run start",
            watch: true,
            env: {
                NODE_ENV: "development",
            },
        },
    ],
};
