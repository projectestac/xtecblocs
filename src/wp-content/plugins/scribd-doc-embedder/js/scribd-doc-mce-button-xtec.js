(function() {
tinymce.PluginManager.add('scribd_doc_mce_button', function( editor, url ) {
		editor.addButton('scribd_doc_mce_button', {
			title: 'Incrusta un document Scribd',
			text: false,
			icon: 'scribd-doc-mce-icon',
			type: 'button',
         onclick : function() {
            editor.windowManager.alert("Aquesta funció ha quedat integrada a WordPress. Podeu inserir directament els URL dels documents a l'editor.");
         },
		});
	});
})();
