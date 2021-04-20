const Encore = require('@symfony/webpack-encore')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore.setOutputPath('public/build/')
  .setPublicPath('/build')

  //js
  .addEntry('app', './assets/app.js')

  //scss
  .addStyleEntry('profil_show', './assets/styles/profil_show.scss')
  .addStyleEntry('form', './assets/styles/form.scss')
  .addStyleEntry('nav', './assets/styles/nav.scss')
  .addStyleEntry('accueil', './assets/styles/accueil.scss')
  .addStyleEntry('login', './assets/styles/login.scss')
  .addStyleEntry('sortie', './assets/styles/sortie.scss')

  .enableStimulusBridge('./assets/controllers.json')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .configureBabel((config) => {
    config.plugins.push('@babel/plugin-proposal-class-properties')
  })

  // enables @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage'
    config.corejs = 3
  })

  // enables Sass/SCSS support
  .enableSassLoader()

  // uncomment if you use TypeScript

// uncomment if you use React
//.enableReactPreset()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()
const config = Encore.getWebpackConfig()
config.watchOptions = {
  poll: true,
}

module.exports = Encore.getWebpackConfig()
