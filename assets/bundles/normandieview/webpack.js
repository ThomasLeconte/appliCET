const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {conf: function(Encore)
{
Encore .addEntry('normandieview-core', ['./assets/bundles/normandieview/webpack-core.js'])
;

},

init: function(Encore)
{

	Encore
	    // directory where compiled assets will be stored
	    .setOutputPath('public/build/')
	    // public path used by the web server to access the output path
	    //.setPublicPath('/NormandieViewBundle/public/build')
	    .setPublicPath('./build')
	    // only needed for CDN's or sub-directory deploy
	    .setManifestKeyPrefix('./build/')

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

	    // enables Sass/SCSS support
	    .enableSassLoader(function(sassOptions) {sassOptions.outputStyle = 'compressed'})
	    
	    // uncomment if you use TypeScript
	    //.enableTypeScriptLoader()

	    // uncomment if you're having problems with a jQuery plugin
	    
	    //.autoProvidejQuery()
	    .autoProvideVariables({
	            $: 'jquery',
	            jQuery: 'jquery',
	            'window.jQuery': 'jquery',
	          //  'window.$': 'jquery'
	    })
	    
	;
	},
exports: function(Encore)
{
	module.exports = Encore.getWebpackConfig();


	module.exports.module.rules = [ { test: /\.jsx?$/,
	    exclude: /(node_modules|bower_components)/,
	    use:
	     [ { loader: 'babel-loader',
	         options:
	          { cacheDirectory: true,
	            presets:
	             [ [ '@babel/preset-env',
	                 { modules: false,
	                   targets: {},
	                   forceAllTransforms: false,
	                   useBuiltIns: 'entry' } ] ],
	            plugins: [ '@babel/plugin-syntax-dynamic-import' ] } } ] },
	  { test: /\.css$/,
	    use:
	     [{loader: MiniCssExtractPlugin.loader, options: {publicPath: '../../'}},
	       { loader: 'css-loader',
	         options: { minimize: true, sourceMap: true, importLoaders: 0 } } ,
	     ] },
	        
	  { test: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/,
	    loader: 'file-loader',
	    options:
	     { name: 'images/[name].[hash:8].[ext]', publicPath: './' } },
	  { test: /\.(woff|woff2|ttf|eot|otf)$/,
	    loader: 'file-loader',
	    options:
	     { name: 'fonts/[name].[hash:8].[ext]', publicPath: './' } },
	  { test: /\.s[ac]ss$/,
	    use:
	     [ MiniCssExtractPlugin.loader,
	       { loader: 'css-loader',
	         options: { minimize: true, sourceMap: true, importLoaders: 0 } },
	       { loader: 'sass-loader', options: { sourceMap: true, outputStyle: 'compressed' } } ,
	       ] } ];
	return module.exports;
}

}
