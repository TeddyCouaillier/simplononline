var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/base', './assets/js/base.js')
    .addEntry('js/ajax', './assets/js/ajax.js')
    .addEntry('js/libraries/bootstrap-select', './assets/js/libraries/bootstrap-select.min.js')
    // .addEntry('editor/js/tui-editor-Editor-full','./assets/Editor/js/tui-editor-Editor-full.min.js')
    // .addEntry('editor/js/tui-editor-extColorSyntax.js','./assets/Editor/js/tui-editor-extColorSyntax.min.js')
    // .addEntry('editor/js/tui-editor-extScrollSync.js','./assets/Editor/js/tui-editor-extScrollSync.min.js')

    .addStyleEntry('css/main/base','./assets/css/template.scss')
    .addStyleEntry('css/user/main','./assets/css/user/main.scss')
    .addStyleEntry('css/project/main','./assets/css/project/main.scss')
    .addStyleEntry('css/home','./assets/css/home.scss')
    .addStyleEntry('css/file','./assets/css/file.scss')
    .addStyleEntry('css/other','./assets/css/other.scss')
    .addStyleEntry('css/mq_home','./assets/css/mq_home.css')
    .addStyleEntry('css/training','./assets/css/training.scss')
    .addStyleEntry('css/calendar','./assets/css/calendar.css')
    .addStyleEntry('css/libraries/bootstrap-select','./assets/css/libraries/bootstrap-select.min.css')
    .addStyleEntry('css/libraries/markdown','./assets/css/libraries/markdown.css')
    .addStyleEntry('editor/css/codemirror','./assets/Editor/css/codemirror.css')
    .addStyleEntry('editor/css/github','./assets/Editor/css/github-markdown.min.css')
    .addStyleEntry('editor/css/tui-editor','./assets/Editor/css/tui-editor.css')
    .addStyleEntry('editor/css/tui-editor-contents','./assets/Editor/css/tui-editor-contents.css')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })
;

module.exports = Encore.getWebpackConfig();
