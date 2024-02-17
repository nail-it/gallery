
// any CSS you require will output into a single css file (app.css in this case)
require('./css/style.css');
require('./css/bootstrap.min.css');

const $ = require('jquery');
global.$ = global.jQuery = $;

require('./js/admin.js');