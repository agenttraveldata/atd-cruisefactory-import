const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const config = {
    entry: {
        main: './assets/app.js',
        'admin/atd-cfi': './assets/admin/atd-cfi.js'
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader",
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].css"
        }),
    ],
};

module.exports = config;