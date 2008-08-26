/**
 *
 *
 * @author Emil Sedgh <emilsedgh@gmail.com>
 */

(function() {
	tinymce.PluginManager.requireLangPack('easyUPload');
	tinymce.create('tinymce.plugins.easyUPload', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('easyUPload', function() {
				ed.windowManager.open({
					file : '../../index.php/tinymce/frontend/',
					width : 250 + parseInt(ed.getLang('easyUPload.Width', 0)),
					height : 160 + parseInt(ed.getLang('easyUPload.Height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('easyUPload', {title : 'easyUPload.Description', cmd : 'easyUPload'});
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
	tinymce.PluginManager.add('easyUPload', tinymce.plugins.easyUPload);
})();