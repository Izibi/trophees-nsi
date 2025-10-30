window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js').default;

window.tinymce = require('tinymce');
require('tinymce/themes/silver');
require('tinymce/icons/default');
require('tinymce/skins/ui/oxide/skin.css');
//require('tinymce/plugins/advlist');
require('tinymce/plugins/code');
require('tinymce/plugins/emoticons');
require('tinymce/plugins/emoticons/js/emojis');
require('tinymce/plugins/link');
require('tinymce/plugins/lists');
require('tinymce/plugins/table');
require('./tinymce/langs/fr.js');

require('bootstrap');
require('./UI/overlay.js');
require('./UI/active-tables.js');
require('./UI/schools-manager.js');
require('./UI/region-selector.js');
require('./UI/project-editor');
require('./UI/teacher-estimate.js');
