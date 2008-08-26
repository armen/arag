/**
 * $Id: editor_plugin_src.js 520 2008-01-07 16:30:32Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.EasyUpload', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('EasyUpload', function() {
				ed.windowManager.open({
					file : url + '/EasyUpload.htm',
					width : 250 + parseInt(ed.getLang('EasyUpload.delta_width', 0)),
					height : 160 + parseInt(ed.getLang('EasyUpload.delta_height', 0)),
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
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('EasyUpload', tinymce.plugins.E);
})();