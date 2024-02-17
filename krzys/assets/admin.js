
// any CSS you require will output into a single css file (app.css in this case)
require('./bundles/nailitgallery/css/style.css');
require('./bundles/nailitgallery/css/bootstrap.min.css');

const $ = require('jquery');
global.$ = global.jQuery = $;

require('./bundles/nailitgallery/js/functions.js');


require('./bundles/nailitgallery/js/admin.js');