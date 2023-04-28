const mix = require('laravel-mix');
mix.webpackConfig({
  optimization: {
    providedExports: false,
    sideEffects: false,
    usedExports: false
  }
});
mix.js('src/LittleBigTable.js', 'dist/LittleBigTable.min.js')
mix.disableNotifications();