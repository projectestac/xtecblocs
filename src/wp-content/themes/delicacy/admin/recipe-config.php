<?php // RECIPE RICH SNIPPETS

$prefix = 'Delicacy_';
/*-----------------------------------------------------------------------------------*/
/*	Prepare Meta Boxes
/*-----------------------------------------------------------------------------------*/
new Delicacy_Meta_Box(array(
	'id' => 'Delicacy',							// meta box id, unique per meta box
	'title' => __('Culinary recipe', 'delicacy'),	// meta box title
	'pages' => array('post',),					// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',						// order of meta box: high (default), low; optional
	'fields' => array(							// list of meta fields
		//TITLE - TEXT
		array(
			'name' => __('Recipe title', 'delicacy'),
			'id' => $prefix . 'recipe_title',
			'type' => 'text',
		),
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
			'desc' => __('How long does it take to prep (without cooking time)? IMPORTANT! Input amount of minutes only! For time longer than 1 hour, for example 1h30m enter 90', 'delicacy'),
			'id' => $prefix . 'prep_time',
			'type' => 'text',
			'std' => '',
		),

		//COOKING TIME - TEXT
		array(
			'name' => __('Cooking time', 'delicacy'),
			'desc' => __('Toal time of cooking, baking etc. IMPORTANT! Input amount of minutes only!', 'delicacy'),
			'id' => $prefix . 'cook_time',
			'type' => 'text',
			'std' => '',
		),

		//RECIPE TYPE - TEXT
		array(
			'name' => __('Recipe type', 'delicacy'),
			'desc' => __('The type of dish, for example: appetizer, entree, dessert ...', 'delicacy'),
			'id' => $prefix . 'type',
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
			'type' => 'textarea',
			'std' => '',
		)
	)
));
/*-----------------------------------------------------------------------------------*/
/*	Recipe display
/*-----------------------------------------------------------------------------------*/

function delicacy_list_items($type = 'ingredients') {
 
    global $post;

    if (get_post_meta($post->ID, 'Delicacy_'. $type, true)) {
        $get_items = trim(get_post_meta($post->ID, 'Delicacy_'. $type, true));
        $items = explode("\n", $get_items);
        $list = '';
    }
    else {
        return;
    }
    if ($type=='ingredients') {
        $list .= '<ul class="ingredient-list">';
        foreach ($items as $item) {
            $list .= '<li><span itemprop="ingredients">' . trim($item) . '</span></li>';
        }
        $list .= '</ul>';
    }
    elseif ($type=='recipe') {
        $list .= '<ol itemprop="recipeInstructions">';
        foreach ($items as $item) {
            $list .= '<li>' . trim($item) . '</li>';
        }
        $list .= '</ol>';
    }
    else {
        $list .= 'Invalid list type.';
    }
    return $list;
}

function Delicacy_time($type = 'prep', $format = null) {
 
    global $post;
    $t = get_post_meta($post->ID,'Delicacy_'.$type.'_time',true);
    $hours = 0;
    $split = preg_split('/[^\d]/',$t);
    
    $total_minutes = $split[0];
    
    $time = '';
    if ($total_minutes >= 60) {
        $hours = floor($total_minutes / 60);
        $minutes = $total_minutes - ($hours * 60);
    }
    else {
        $minutes = $total_minutes;
    }
    
    if ($format == 'iso') {
        if ($hours > 0) {
            $time = 'PT'.$hours.'H';
            if($minutes > 0) {
                $time .= $minutes.'M';
            }
        }
        else {
            $time = 'PT'.$minutes.'M';
        }
    }
    else {
        if ($hours > 0) {
            if ($hours == 1) {
                $time = $hours.' h ';
            }
            else {
                $time = $hours.' h ';
            }
            if ($minutes > 0) {
                $time .= $minutes.' min';
            }
        }
        else {
            $time = $minutes.' min';
        }
    }
    return $time;
}
function delicacy_total_time($format = null) {
 
    global $post;
    $prep_minutes = get_post_meta($post->ID,'Delicacy_prep_time',true);
    $cook_minutes = get_post_meta($post->ID,'Delicacy_cook_time',true);
    $total_minutes = $prep_minutes + $cook_minutes;
    $hours = 0;
    $minutes = 0;
 
    if ($total_minutes >= 60) {
        $hours = floor($total_minutes / 60);
        $minutes = $total_minutes - ($hours * 60);
    }
    else {
        $minutes = $total_minutes;
    }
    $total_time = '';
    if ($format == 'iso') {
        if ($hours > 0 ) {
            $total_time = 'PT'.$hours.'H';
            if ($minutes > 0) {
                $total_time .= $minutes.'M';
            }
        }
        else {
            $total_time = 'PT'.$minutes.'M';
        }
    }
    else {
        if ($hours > 0 ) {
            if ($hours == 1) {
                $total_time = $hours.' h ';
            }
            else {
                $total_time = $hours.' h ';
            }
            if ($minutes > 0) {
                $total_time .= $minutes.' min';
            }
        }
        else {
            $total_time = $minutes.' min';
        }
    }
    return $total_time;
}

function delicacy_display_recipe($content) {
 
    global $post;
    $recipe = '';
    $diff = array (
        1 => __('easy', 'delicacy'),
        2 => __('medium', 'delicacy'),
        3 => __('hard', 'delicacy'),
    );
    $image = 0;
    if(wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'nivo-thumb')) { 
      $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID));
    } 
    
    if ( is_singular( 'post' ) && get_post_meta($post->ID,'Delicacy_ingredients',true)) {
        $recipe .= '<div class="recipe">';
            $recipe .= '<div itemscope itemtype="http://schema.org/Recipe" >';
                if(get_post_meta($post->ID,'Delicacy_recipe_title',true)):
                $recipe .= '<h2 itemprop="name">'. get_post_meta($post->ID,'Delicacy_recipe_title',true) .'</h2>';
				else :
                $recipe .= '<h2 itemprop="name">'. $post->post_title .'</h2>';
                endif;
                if($image[0]):
                $recipe .= '<img itemprop="image" src="'. $image[0] .'" class="photo" />';
                endif;
                $recipe .= '<div class="info">';
                $recipe .= '<ul>';
                $recipe .= '<li class="mcr_meta"><b>'. __('Prep time','delicacy') .':</b> <time datetime="'. delicacy_time('prep','iso') .'" itemprop="prepTime">'. delicacy_time('prep') .'</time></li>';
                $recipe .= '<li class="mcr_meta"><b>'. __('Cook time','delicacy') .':</b> <time datetime="'. delicacy_time('cook','iso') .'" itemprop="cookTime">'. delicacy_time('cook') .'</time></li>';
                $recipe .= '<li class="mcr_meta"><b>'. __('Total time','delicacy') .':</b> <time datetime="'. delicacy_total_time('iso') .'" itemprop="totalTime">'. delicacy_total_time() .'</time></li>';
                if(get_post_meta($post->ID,'Delicacy_servings',true)):
                    $recipe .= '<li class="mcr_meta"><b>'. __('Yield','delicacy') .':</b> <span itemprop="recipeYield">'. get_post_meta($post->ID,'Delicacy_servings',true) .'</span></li>';
                endif;
                if(get_post_meta($post->ID,'Delicacy_difficulty',true)):                
                    $recipe .= '<li class="mcr_meta"><b>'. __('Difficulty','delicacy') .':</b> <span>'. $diff[get_post_meta($post->ID,'Delicacy_difficulty',true)] .'</span></li>';
                endif;
                if(get_post_meta($post->ID,'Delicacy_type',true)):                
                    $recipe .= '<li class="mcr_meta"><b>'. __('Recipe type','delicacy') .':</b> <span itemprop="recipeType">'. get_post_meta($post->ID,'Delicacy_type',true) .'</span></li>';
                endif;
                $recipe .= '</ul>';
                $recipe .= '</div>';
                $recipe .= '<div class="ingredients">';
                $recipe .= '<h3>' .__('Ingredients','delicacy') .':</h3>';
                $recipe .= delicacy_list_items('ingredients');
                $recipe .= '</div>';
                $recipe .= '<div class="clear"></div>';
                $recipe .= '<h3>' .__('Directions','delicacy'). ':</h3> '. delicacy_list_items('recipe');
                $recipe .= '<span class="mcr_meta">'. __('Published on','delicacy') .' <time itemprop="datePublished" datetime="'. get_the_date('Y-m-d') .'">'. get_the_date('F j, Y') .'</time></span>';
                $recipe .= '<span class="mcr_meta">'. __('by','delicacy') .' <span itemprop="author">'. get_the_author() .'</span></span>';
            $recipe .= '</div>';
        $recipe .= '</div>';
    }
 
    return $content . $recipe;
}
add_filter('the_content', 'delicacy_display_recipe', 1);
