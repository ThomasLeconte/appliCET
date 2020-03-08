var Encore = require('@symfony/webpack-encore');


const nv = require('./assets/bundles/normandieview/webpack.js');
nv.init(Encore);
nv.conf(Encore);


module.exports = nv.exports(Encore);
