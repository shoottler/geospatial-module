const mix = require('laravel-mix')
mix.setPublicPath('./')
mix.js(`${__dirname}/Resources/assets/src/js/module.js`, 'Resources/assets/dist/js/GeoSpatialModule.js')
  .sass(`${__dirname}/Resources/assets/src/sass/module.scss`, 'Resources/assets/dist/css/GeoSpatialModule.css')
if (mix.inProduction())
  mix.version()
else
  mix.sourceMaps()
