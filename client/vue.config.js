const app = require('./src/utils/app')

module.exports = {
  runtimeCompiler: true,
  productionSourceMap: false,

  publicPath: process.env.NODE_ENV === 'production'
      ? './dist'
      : '/',

  outputDir: '../server/public/dist',
  indexPath: '../../resources/views/welcome.blade.php',
  chainWebpack: config => {
    config
        .plugin('html')
        .tap(args => {
          args[0].title = app.name
          return args
        })
  }
};