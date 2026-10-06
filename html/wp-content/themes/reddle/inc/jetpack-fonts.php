<?php

add_filter( 'typekit_add_font_category_rules', function( $category_rules ) {

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body,
		button,
		input,
		select,
		textarea',
		array(
			array( 'property' => 'font', 'value' => '13px Verdana, sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'#main h1,
		#main h2',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'h3',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-style', 'value' => 'italic' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'site-title',
		'#site-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '21px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#site-description',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana, sans-serif' ),
			array( 'property' => 'font-size', 'value' => '11px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.page-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana, serif' ),
			array( 'property' => 'font-size', 'value' => '11px' ),
			array( 'property' => 'font-weight', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'#main .entry-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '21px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-content blockquote:before',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '94px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'#page .image-attachment #content nav a',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana, sans-serif' ),
			array( 'property' => 'font-size', 'value' => '13px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'#content nav a',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia,"Bitstream Charter",serif' ),
			array( 'property' => 'font-size', 'value' => '16px' ),
			array( 'property' => 'font-style', 'value' => 'italic' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.widget-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana, sans-serif' ),
			array( 'property' => 'font-size', 'value' => '11px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'#comments-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana, sans-serif' ),
			array( 'property' => 'font-size', 'value' => '11px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comment .comment-author .fn',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '20px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.children
		#reply-title',
		array(
			array( 'property' => 'font-family', 'value' => 'Georgia, "Bitstream Charter", serif' ),
			array( 'property' => 'font-size', 'value' => '20px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#site-info',
		array(
			array( 'property' => 'font-family', 'value' => 'Verdana,sans-serif' ),
			array( 'property' => 'font-size', 'value' => '12px' ),
		)
	);

	return $category_rules;
} );
