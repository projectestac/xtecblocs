<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/docs/define-meta-boxes
 */

/********************* META BOX DEFINITIONS ***********************/

/**
 * Prefix of meta keys (optional)
 * Use underscore (_) at the beginning to make keys hidden
 * Alt.: You also can make prefix empty to disable it
 */
// Better has an underscore as last sign
$prefix = 'Delicacy_';

global $meta_boxes;

$meta_boxes = array();

// 1st meta box
$meta_boxes[] = array(
	// Meta box id, UNIQUE per meta box
	'id' => 'Delicacy',

	// Meta box title - Will appear at the drag and drop handle bar
	'title' => __('Culinary recipe', 'delicacy'),

	// Post types, accept custom post types as well - DEFAULT is array('post'); (optional)
	'pages' => array( 'post'),

	// Where the meta box appear: normal (default), advanced, side; optional
	'context' => 'normal',

	// Order of meta box: high (default), low; optional
	'priority' => 'high',

	// List of meta fields
	'fields' => array(
		//SERVINGS - TEXT
		array(
			'name' => __('Servings', 'delicacy'),
			'desc' => __('Enter the number of servings eg. "4"', 'delicacy'),
			'id' => $prefix . 'servings',
			'type' => 'text',
		),
		//PREP TIME - TEXT
		array(
			'name' => __('Prep time', 'delicacy'),
			'desc' => __('How long does it take to prep (without cooking time, eg. 20 min.)', 'delicacy'),
			'id' => $prefix . 'prep_time',
			'type' => 'text',
			'std' => '',
		),
		//COOKING TIME - TEXT
		array(
			'name' => __('Cooking time', 'delicacy'),
			'desc' => __('Toal time of cooking, baking etc.', 'delicacy'),
			'id' => $prefix . 'cook_time',
			'type' => 'text',
			'std' => '',
		),
		//DIFFICULTY - SELECT
		array(
			'name' => __('Difficulty', 'delicacy'),
			'id' => $prefix . 'difficulty',
			'type' => 'select',
			'options' => array(
				1 => __('easy', 'delicacy'),
				2 => __('medium', 'delicacy'),
				3 => __('hard', 'delicacy')
			),
			'std' => '',
		),
		//INGREDIENTS - TEXTAREA
		array(
			'name' => __('Ingredients', 'delicacy'),
			'desc' => __('Enter all the ingredients. IMPORTANT: Seperate the ingredients with a single line break.', 'delicacy'),
			'id' => $prefix . 'ingredients',
			'type' => 'textarea',
			'std' => '',
		),
		//DIRECTIONS - WYSIWYG
		array(
			'name' => __('Directions', 'delicacy'),
			'id' => $prefix . 'recipe',
			'type' => 'wysiwyg',
			'std' => '',
		)
	)
);


/********************* META BOX REGISTERING ***********************/

/**
 * Register meta boxes
 *
 * @return void
 */
function Delicacy_register_meta_boxes()
{
	global $meta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( class_exists( 'RW_Meta_Box' ) )
	{
		foreach ( $meta_boxes as $meta_box )
		{
			new RW_Meta_Box( $meta_box );
		}
	}
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action( 'admin_init', 'Delicacy_register_meta_boxes' );