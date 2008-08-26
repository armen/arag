/**
 *
 *
 * @author Emil Sedgh <emilsedgh@gmail.com>
 */

(function() {
	tinymce.PluginManager.requireLangPack('EasyUpload');
	tinymce.create('tinymce.plugins.EasyUpload', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('EasyUpload', function() {
				ed.windowManager.open({
					file : '../../index.php/tinymce/frontend/',
					width : 250 + parseInt(ed.getLang('EasyUpload.Width', 0)),
					height : 160 + parseInt(ed.getLang('EasyUpload.Height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('EasyUpload', {title : 'EasyUpload.Description', cmd : 'EasyUpload'});
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
	tinymce.PluginManager.add('EasyUpload', tinymce.plugins.EasyUpload);
})();