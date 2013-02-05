<?php
$path = substr("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 0, -10);
?>

body {
	behavior:url(<?php print $path; ?>csshover2.htc);
}

* html #page, * html #header {
	padding:0 10px 0 10px;
	zoom:1;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/ie_shadow.png', sizingMethod='scale');
}

* html #header {
	padding-top:10px;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/ie_header_shadow.png', sizingMethod='scale');
}

* html #footer {
	padding:0 10px 10px 10px;
	zoom:1;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/ie_footer_shadow.png', sizingMethod='scale');
}

* html .comment.comment_author {
	background-image:none;
	zoom:1;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/transparency/white-90.png', sizingMethod='scale');
}

* html code {
	background-image:none;
	zoom:1;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/code_bg.png', sizingMethod='crop');
}

* html #menu {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/transparency/black-60.png', sizingMethod='scale');
}

* html #menu .menu_container {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/menu/first_menu.png', sizingMethod='crop');
}

* html .menu_end {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/menu/menu_end.png', sizingMethod='crop');
}

* html #menu ul li.current_page_ancestor a, * html #menu ul li:hover a, * html #menu ul li a:hover, * html #menu ul li.current_page_parent a, * html #menu ul li.current_page_item a {
	background-image:none;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/menu/reflect.png', sizingMethod='scale');
}

* html .download {
	background-image:none;
	height:35px;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $path; ?>images/download.png', sizingMethod='crop');
}