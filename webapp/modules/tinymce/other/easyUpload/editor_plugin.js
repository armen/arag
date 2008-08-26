/**
 *
 *
 * @author Emil Sedgh <emilsedgh@gmail.com>
 */

(function() {
	tinymce.PluginManager.requireLangPack('easyUpload');
	tinymce.create('tinymce.plugins.easyUpload', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('easyUpload', function() {
				ed.windowManager.open({
					file : '../../index.php/tinymce/frontend/',
					width : 250 + parseInt(ed.getLang('easyUpload.Width', 0)),
					height : 160 + parseInt(ed.getLang('easyUpload.Height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('easyUpload', {title : 'easyUpload.Description', cmd : 'easyUpload', image : url + '/img/icon.gif'});
		},

		getInfo : function() {
			return {
				longname : 'Easy Upload',
				author : 'Emil Sedgh',
				authorurl : 'emilsedgh@gmail.com',
				version : "1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('easyUpload', tinymce.plugins.easyUpload);
})();