(function() {
tinymce.PluginManager.add('scribd_doc_mce_button', function( editor, url ) {
		editor.addButton('scribd_doc_mce_button', {
			title: 'Scribd Doc Embedder',
			text: false,
			icon: 'scribd-doc-mce-icon',
			type: 'menubutton',
			menu: [
						{
							text: 'Embed Scribd Document',
							onclick: function() {
								editor.windowManager.open( {
									title: 'Insert Scribd Document',
									body: [
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;">Do not include "key-" or "pub-" with Document Key or Publisher ID.</p>'
										},	
										{
											type: 'textbox',
											name: 'scribddocid',
											label: 'Doc Key',
											value: ''
										},
										{
											type: 'textbox',
											name: 'scribdkey',
											label: 'Publisher ID',
											value: ''
										},
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;margin-top:10px;">Leave width & height blank for reader to size within container.</p>'
										},	
										{
											type: 'textbox',
											name: 'scribdwidth',
											label: 'Viewer Width',
											value: ''
										},
										{
											type: 'textbox',
											name: 'scribdheight',
											label: 'Viewer Height',
											value: ''
										},
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;margin-top:10px;">Leave page blank to start on page 1.</p>'
										},	
										{
											type: 'textbox',
											name: 'scribdpage',
											label: 'Start Page',
											value: ''
										},
										{
											type: 'listbox',
											name: 'scribdmode',
											label: 'Player Mode',
											'values': [
												{text: 'Default', value: ''},
												{text: 'List', value: 'list'},
												{text: 'Slideshow', value: 'slideshow'}
											]
										},
										{
											type: 'listbox',
											name: 'scribdshare',
											label: 'Share Button',
											'values': [
												{text: 'Default', value: ''},
												{text: 'Display', value: 'true'},
												{text: 'Hide', value: 'false'}
											]
										},
										{
											type: 'listbox',
											name: 'scribdseamless',
											label: 'Seamless',
											'values': [
												{text: 'Default', value: ''},
												{text: 'Yes', value: 'true'},
												{text: 'No', value: 'false'}
											]
										}
									],
									onsubmit: function( e ) {
										editor.insertContent( '[scribd-doc doc="' + e.data.scribddocid + '" key="' + e.data.scribdkey + '"' );
										if (e.data.scribdwidth) {
											editor.insertContent( ' width="' + e.data.scribdwidth + '"');
										}
										if (e.data.scribdheight) {
											editor.insertContent( ' height="' + e.data.scribdheight + '"');
										}
										if (e.data.scribdpage) {
											editor.insertContent( ' page="' + e.data.scribdpage + '"');
										}
										if (e.data.scribdmode) {
											editor.insertContent( ' mode="' + e.data.scribdmode + '"');
										}
										if (e.data.scribdshare) {
											editor.insertContent( ' share="' + e.data.scribdshare + '"');
										}
										if (e.data.scribdseamless) {
											editor.insertContent( ' seamless="' + e.data.scribdseamless + '"');
										}
										editor.insertContent( ' ]');

									}
								});
							}
						},
{
							text: 'Embed Scribd Reader from URL',
							onclick: function() {
								editor.windowManager.open( {
									title: 'Insert Scribd Doc from URL',
									body: [
										{
											type: 'textbox',
											name: 'scribdpubid',
											label: 'Publisher ID',
											value: ''
										},
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;">Include the full URL with "http://".</p>'
										},	
										{
											type: 'textbox',
											name: 'scribdurl',
											label: 'URL',
											value: ''
										},
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;margin-top:10px;">Leave width & height blank for reader to size within container.</p>'
										},	
										{
											type: 'textbox',
											name: 'scribdwidth',
											label: 'Viewer Width',
											value: ''
										},
										{
											type: 'textbox',
											name: 'scribdheight',
											label: 'Viewer Height',
											value: ''
										},
										{
											type: 'container',
											html: '<p style="font-size:12px;text-align:right;margin-top:10px;">Leave page blank to start on page 1.</p>'
										},	
										{
											type: 'textbox',
											name: 'scribdpage',
											label: 'Start Page',
											value: ''
										},
										{
											type: 'listbox',
											name: 'scribdmode',
											label: 'Player Mode',
											'values': [
												{text: 'Default', value: ''},
												{text: 'List', value: 'list'},
												{text: 'Slideshow', value: 'slideshow'}
											]
										},
										{
											type: 'listbox',
											name: 'scribdshare',
											label: 'Share Button',
											'values': [
												{text: 'Default', value: ''},
												{text: 'Display', value: 'true'},
												{text: 'Hide', value: 'false'}
											]
										}
									],
									onsubmit: function( e ) {
										editor.insertContent( '[scribd-url pubid="' + e.data.scribdpubid + '" url="' + e.data.scribdurl + '"' );
										if (e.data.scribdwidth) {
											editor.insertContent( ' width="' + e.data.scribdwidth + '"');
										}
										if (e.data.scribdheight) {
											editor.insertContent( ' height="' + e.data.scribdheight + '"');
										}
										if (e.data.scribdpage) {
											editor.insertContent( ' page="' + e.data.scribdpage + '"');
										}
										if (e.data.scribdmode) {
											editor.insertContent( ' mode="' + e.data.scribdmode + '"');
										}
										if (e.data.scribdshare) {
											editor.insertContent( ' share="' + e.data.scribdshare + '"');
										}
										editor.insertContent( ' ]');

									}
								});
							}
						}

				
			]
		});
	});
})();
