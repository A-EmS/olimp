module.exports = {
    runtimeCompiler: true,
    productionSourceMap: false,

    publicPath: process.env.NODE_ENV === 'production'
        ? '/vuejs/dist/'
        : '/vuejs/dist/',

    devServer: {
        host: 'olimp.loc'
    }
}
