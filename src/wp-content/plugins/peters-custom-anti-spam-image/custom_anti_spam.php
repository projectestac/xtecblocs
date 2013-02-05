<?php
/*
Plugin Name: Peter's Custom Anti-Spam
Plugin URI: http://www.theblog.ca/anti-spam
Description: Stop a lot of spambots from polluting your site by making visitors identify a random word displayed as an image before commenting. You can customize the pool of words to display.
Author: Peter Keung
Version: 3.1.4
Author URI: http://www.theblog.ca/
Change Log:
2010-08-02  Version 3.1.4  Minor code tweak for WordPress 3.0.
2008-12-10  Version 3.1.3  Minor tweak to the automatic JavaScript positioning to account for labels for the comment textarea (thanks Midwestern City Boy!).
2008-12-04  Version 3.1.2  The plugin will now bypass the anti-spam check if a comment is posted from within the WordPress back-end (thanks steveegg!).
2008-11-16  Version 3.1.1  Added a setting for compatibility with various WordPress caching plugins. When the setting is enabled, the anti-spam URL is dynamically pulled via JavaScript (and is thus not cached).
2008-11-05  Version 3.1.0  Implemented a user interface for modifying settings (see Settings > Custom anti-spam) so that almost all settings are now handled through the WordPress back-end interface. Modified JavaScript so that the anti-spam image attaches itself above the comment form for logged in users.
2008-08-02  Version 3.0.7  Added a setting $cas_wpconfig for the absolute path to wp-config.php for WordPress 2.6+ users who have moved either wp-config.php or the wp-content folder from the default locations.
2008-05-31  Version 3.0.6  Fixed the registration form protection for WordPress 2.5. Pre-WordPress 2.5 users who want to use the registration form protection must use Version 3.0.5 of this plugin or lower.
2008-05-24  Version 3.0.5  For efficiency reasons, image and audio generation is now run directly through this file.
2008-05-05  Version 3.0.4  The list of anti-spam words is now in a separate file and manageable through the Manage > Custom anti-spam page.
2008-04-03  Version 3.0.3  Made the folder name of fonts and sounds customizable. The plugin is now tested to be compatible with WordPress MU. See http://www.theblog.ca/anti-spam-mu (thanks Clay!).
2008-03-13  Version 3.0.2  Minor code tweak to be compatible with WordPress 2.5.
2008-02-10  Version 3.0.1  New option to disable random stretching of the wav files. Made the operation to stretch the wav files silent to the server(thanks Angelo!). Blocked indexing of audio file links by search engines (thanks Lucky!).
2008-01-20  Version 3.0.0  Added registration form protection and a corresponding e-mail blacklist. To enable registration form protection, set $cas_reg_protection equal to "true". Also tweaked the audio file generation with wav files to be more robust against spambots.

View all previous change items at http://www.theblog.ca/anti-spam-history
*/

// Leave this as FALSE unless you have moved wp-config.php or the wp-content folder to a non-default location
// If so, change this to be the absolute or relative path to your wp-config.php file
$cas_wpconfig = false;

// -------------------------------------------------
// ALL WORDS AND SETTINGS ARE NOW CUSTOMIZED IN THE WORDPRESS ADMIN PANEL
// LOOK UNDER SETTINGS > CUSTOM ANTI-SPAM
// All language text is found in the "translate/cas_translations.php" file.
// -------------------------------------------------

// If you are calling the plugin file directly, include the file that contains important db functions
if ( ( isset($_GET['antiselect']) || isset($_GET['audioselect']) || isset($_GET['antinew']) ) && file_exists('custom_anti_spam.php')) {
    if ($cas_wpconfig) {
        include_once $cas_wpconfig;
    }
    else {
        include_once '../../../wp-config.php';
    }
}

global $wpdb; // Must be global for WordPress 2.5
global $cas_wordpath; // Must be global for WordPress 2.5
// Folder that the word file(s) and settings file(s) is held in, relative to this file. Include a trailing slash.
$cas_wordpath = 'words/';

// Define the path to the plugin
$cas_folder = str_replace('\\', '/', dirname(__FILE__));
$cas_thisfolder = explode('/', $cas_folder);

global $cas_pluginpath; // Must be global for WordPress 2.5
$cas_pluginpath =  $cas_folder. '/';

// Check for the file with settings
//XTEC ************ MODIFICAT - Modificacions a l'extensió.
//2011.05.26 @fbassas

$cas_settingsfullpath = $cas_pluginpath . $cas_wordpath . $wpdb->base_prefix . 'cas_settings.php';	

//************ ORIGINAL
/*	
$cas_settingsfullpath = $cas_pluginpath . $cas_wordpath . $wpdb->prefix . 'cas_settings.php';
*/
//************ FI

if (file_exists($cas_settingsfullpath) ) {
    include $cas_settingsfullpath;
}

// If for some reason, the settings file does not exist, use these settings
else {

    // Set this to equal TRUE if you want to force registered users to enter the anti-spam word as well.
    $cas_forcereg = true;

    // Set this to equal TRUE if you want to allow trackbacks (but be vulnerable to trackback spam)
    $cas_allowtrack = true;

    // Set this to equal TRUE if you want to send all trackbacks to the moderation queue (only works if the above setting is TRUE)
    $cas_modtrack = false;

    // Set this to equal TRUE if you want to allow pingbacks (but be vulnerable to pingback spam)
    $cas_allowping = true;

    // Set this to equal TRUE if you want to send all pingbacks to the moderation queue (only works if the above setting is TRUE)
    $cas_modping = false;

    $cas_fontlist = array(); // <-- This line should not be changed
    // List as many TrueType font(s) as you like, one per line. Drop your own font files into this plugin's directory.
    // If you are using your own fonts, make sure all fonts used are about the same default size.
    // If you want some fonts to be used more frequently, enter them multiple times.
    // Default freeware fonts from fonts101.com
    $cas_fontlist[] = "04b03.ttf";
    $cas_fontlist[] = "atkinsoutlinemedium-regular.ttf";
    $cas_fontlist[] = "decorative-stylisticblackout-regular.ttf";
    $cas_fontlist[] = "okrienhmk.ttf";
    $cas_fontlist[] = "ttstepha.ttf";
    $cas_fontlist[] = "vtckomixationhand.ttf";

    // Set the anti-spam image width and height.
    // You may need to increase these sizes for longer words and/or bigger fonts.
    $cas_imgwidth = 160;
    $cas_imgheight = 50;

    // Set this to TRUE if you want to use random text colors.
    // If random colors are not selected, blue text will appear on a white background, and white text will appear on black background (as decided in the next option)
    $cas_randomcolors = true;

    // Set the background color for the anti-spam image.
    // Choose either "black" or "white"
    $cas_bgcolorset = 'white';

    // Set the border color for the anti-spam image.
    // Write either major colors (red, green, blue, etc.) or enter the HTML color code (such as #C0C0C0)
    $cas_borderclr = 'black';

    // Set this to TRUE if you prefer PNG graphics (better quality text)
    // Set this to FALSE if you prefer more compatable graphics (PNG crashes IE 4; JPEG does not)
    $cas_UsePngNotJpeg = false;

    // Set this to TRUE if you will be editing your comments file (add this php line wherever you want the anti-spam image inserted in the comments.php file: do_action('secure_image', $post->ID); )
    // Set this to FALSE if you want to use the default JavaScript positioning
    $cas_manualinsert = false;
    
    // Set this to TRUE if you are using a caching plugin that doesn't allow parts of pages to be exempt from caching.
    // When this is set to TRUE, the plugin will use JavaScript to generate the image
    $cas_js_generate = false;

    // Leave this as FALSE unless you are instructed by an error message to "The site administrator needs to manually configure his/her site address in the plugin configuration file!" In that case, enter your site address, which should be the same as the WordPress address from your Options page. Enter it in quotes like the commented line below, withOUT a trailing slash.
    // $cas_siteurl = "http://yoursiteaddress/whatever";
    $cas_siteurl = false;

    // Set this to TRUE to enable wav files of the anti-spam words so that visually enabled users can comment
    $cas_wav = true;

    // Set this to FALSE if you know that your server doesn't have the sox filter or if you do not want to stretch the audio files by a variable rate
    $cas_sox = true;

    // Set this to TRUE to insert anti-spam protection to the registration form
    $cas_reg_protection = false;

    $cas_reg_blacklist = array();
    // Registration blacklist. Enter either e-mail address or domains (like 'theblog.ca') of e-mail addresses you don't want to allow
    // Be careful, because if you enter 'theblog.ca' if an e-mail address contains that string anywhere, it will be blocked
    $cas_reg_blacklist[] = 'exampleaddressthatwontmatch.bc.ca';
}

// Folder that the fonts are held in, relative to this file. Include a trailing slash
$cas_fontpath = 'fonts/';

// Folder that the sounds are held in, relative to this file. Include a trailing slash.
$cas_soundpath = 'sounds/';

global $cas_table; // Must be global for WordPress 2.5
global $cas_tablename; // Must be global for WordPress 2.5
// Name of the table that holds all the anti-spam words
$cas_tablename = 'cas_image';
//XTEC ************ MODIFICAT - Modificacions a l'extensió.
//2011.05.26 @fbassas

$cas_table = $wpdb->base_prefix . $cas_tablename;

//************ ORIGINAL

/*
$cas_table = $wpdb->prefix . $cas_tablename;
*/

//************ FI
global $cas_count; // Must be global for WordPress 2.5
global $cas_countname; // Must be global for WordPress 2.5
// Name of the table that holds the count for the anti-spam words
$cas_countname = 'cas_count';
//XTEC ************ MODIFICAT - Modificacions a l'extensió.
//2011.05.26 @fbassas

$cas_count = $wpdb->base_prefix . $cas_countname;

//************ ORIGINAL

/*
$cas_count = $wpdb->prefix . $cas_countname;
*/

//************ FI

// Include the file that contains the anti-spam words
//XTEC ************ MODIFICAT - Modificacions a l'extensió.
//2011.05.26 @fbassas

$cas_wordfile = $cas_pluginpath . $cas_wordpath . 'wp_cas_words.php';

//************ ORIGINAL

/*
$cas_wordfile = $cas_pluginpath . $cas_wordpath . $wpdb->prefix . 'cas_words.php';
*/

//************ FI
if (file_exists($cas_wordfile)) {
    include $cas_wordfile;
}
else {
    $cas_text = array('dan', 'broad', 'way', 'toast', 'micro', 'wave', 'ikea', 'rent', 'move', 'van', 'connect');
}

// Include the translation text information
include $cas_pluginpath . 'translate/cas_translations.php';

// This makes it so that the invalid audio file pages are not indexed by search engines
$cas_displaytext['not_valid'] = '<html><head><title>' . $cas_displaytext['not_valid'] . '</title><meta name="Robots" content="noindex,nofollow"></head><body>' . $cas_displaytext['not_valid'] . '</body></html>';

// Determine how many words were entered
$cas_textcount = count( $cas_text );
// Copy the first element to a new last element
$cas_text[] = $cas_text[0];
// Set the first element to invalid
$cas_text[0] = "* * * INVALID * * *";

// Output the antispam audio file
if( isset( $_GET['audioselect'] ) )
{
    // Include the wav merging class
    include $cas_pluginpath . $cas_soundpath . 'wav_join.php';

    // Validate the input values
    $cas_audioselect = intval( $_GET['audioselect'] );

    // Get the word from the database
    $cas_antispam = $wpdb->get_var('SELECT word FROM ' . $cas_table . ' WHERE id = ' . $cas_audioselect);
    if (is_null($cas_antispam)) wp_die($cas_displaytext['not_valid']);

    // Get the word
    $petersword = strtolower($cas_antispam);

    // Strip out spaces and hyphens
    $petersword = str_replace(array('-',' '), array('',''), $petersword);

    // Count the number of letters in the word
    $word_count = strlen($petersword);

    // Write the function str_split if it doesn't exist (in PHP 4)
    if (!function_exists('str_split')) {
        function str_split($string) {
            global $word_count;
            $letters = array();
            for ($i = 0; $i < $word_count; ++$i) {
                $letters[$i] = substr($string, $i, 1);
            }
            return $letters;
        }
    }

    // Split the word into an array of individual letters
    $cas_wavs = str_split($petersword);

    // Merge the wav files for this word
    $cas_one_wav = joinwavs($cas_wavs);

    // Create a temporary file for this word
    $cas_audioinput = tempnam($cas_pluginpath, 'cas_');
    $cas_audiooutput = $cas_audioinput . '1.wav';
    $cas_handle=fopen($cas_audioinput, "w");
    fwrite($cas_handle, $cas_one_wav);
    fclose($cas_handle);

    // Clear the variable out since the sound is now in a temporary file
    unset($cas_one_wav);

    if ($cas_sox) {
        // Make use of the sox filter, if available, to make it hard for bots to crack the audio file

        // This will determine a stretch factor with 20 different possibilities
        $stretch_random = sprintf("%02d", rand( 0, 20));

        // Stretch the audio file
        @exec('sox ' . $cas_audioinput . ' ' . $cas_audiooutput . ' stretch 1.' . $stretch_random . ' > /dev/null 2>&1');
    }
    
    // If the sox filter was available, use the stretched file. Otherwise, use the original file
    if (file_exists($cas_audiooutput)) {
        $cas_one_wav = file_get_contents($cas_audiooutput);
    }
    else {
        $cas_one_wav = file_get_contents($cas_audioinput);
    }

    // Spit it the file out to the browser    
    output('output.wav', $cas_one_wav);

    // Delete the files
    if (file_exists($cas_audioinput)) unlink($cas_audioinput);
    if (file_exists($cas_audiooutput)) unlink($cas_audiooutput);

    // Prevent any more display to the page
    die();
}

// Output the antispam image
elseif( isset( $_GET['antiselect'] ) )
{
    // Pick a random font to use
    $cas_font = $cas_pluginpath . $cas_fontpath . $cas_fontlist[ rand( 0, count( $cas_fontlist ) - 1 ) ];

    // Set the default colors for when random text colors are not selected
    if ( $cas_bgcolorset == "white") {
    $cas_textcolor = array( 0, 0, 255 ); // blue text
    $cas_bgcolor = array( 255, 255, 255); // white background
    }
    else {
    $cas_textcolor = array( 255, 255, 255 ); // white text
    $cas_bgcolor = array( 0, 0, 0); // black background
    }

    // If selected, pick a random color for the antispam word text
    if( $cas_randomcolors )
    {
        $cas_rand = rand( 0, 4 );
        switch( $cas_bgcolorset )
            {
                case "white":
                    $cas_textcolorchoice[0] = array ( 0, 0, 255 ); // blue
                    $cas_textcolorchoice[1] = array ( 0, 153, 0 ); // greenish
                    $cas_textcolorchoice[2] = array ( 204, 0, 0 ); // reddish
                    $cas_textcolorchoice[3] = array ( 203, 0, 154 ); // purplish
                    $cas_textcolorchoice[4] = array ( 0, 0, 0 ); // black
                    $cas_textcolor = $cas_textcolorchoice[$cas_rand];
                    break;
                default:
                    $cas_textcolorchoice[0] = array ( 255, 255, 0 ); // yellow
                    $cas_textcolorchoice[1] = array ( 0, 255, 255 ); // blueish
                    $cas_textcolorchoice[2] = array ( 255, 153, 204 ); // pinkish
                    $cas_textcolorchoice[3] = array ( 102, 255, 102 ); // greenish
                    $cas_textcolorchoice[4] = array ( 255, 255, 255 ); // white
                    $cas_textcolor = $cas_textcolorchoice[$cas_rand];
                    break;
            }
    }

    // Validate the input values
    $cas_antiselect = intval( $_GET['antiselect'] );

    // Get the word from the database
    $cas_antispam = $wpdb->get_var('SELECT word FROM ' . $cas_table . ' WHERE id = ' . $cas_antiselect);
    if (is_null($cas_antispam)) $cas_antispam = $cas_displaytext['invalid'];

    // Start building the image
    $cas_image = @imagecreate( $cas_imgwidth, $cas_imgheight ) or die($cas_displaytext['no_gd']);
    $cas_bgcolor = imagecolorallocate( $cas_image, $cas_bgcolor[0], $cas_bgcolor[1], $cas_bgcolor[2] );
    $cas_fontcolor = imagecolorallocate( $cas_image, $cas_textcolor[0], $cas_textcolor[1], $cas_textcolor[2] );

    // Check for freetype lib, if not found default to ugly built in capability using imagechar (Lee's mod)
    // Also check that the chosen TrueType font is available
    if( function_exists( 'imagettftext' ) && file_exists( $cas_font ) )
    {
        $cas_angle = 4; // Degrees to tilt the text
        $cas_offset = 10; // Pixels to offset the text from the border
        $cas_fontsize = 28; // Default font size for the anti-spam image
        $cas_imagebox = imagettfbbox($cas_fontsize, $cas_angle, $cas_font, $cas_antispam);
        $cas_boxwidth = $cas_imagebox[2] - $cas_imagebox[0];
        $cas_boxheight = $cas_imagebox[1] - $cas_imagebox[7];
        
        // if the text width is too big for the image, decrease the font size to a certain extent (best practice is of course not to use really long words!
        while ($cas_boxwidth > $cas_imgwidth - $cas_offset && $cas_fontsize > 12) {
            $cas_fontsize = $cas_fontsize - 3;
            $cas_imagebox = imagettfbbox($cas_fontsize, $cas_angle, $cas_font, $cas_antispam);
            $cas_boxwidth = $cas_imagebox[2] - $cas_imagebox[0];
        }

        // if the text height too big for the image, decrease the font size to a certain extent (best practice is of course not to use really long words!
        while ($cas_boxheight > $cas_imgheight - $cas_offset && $cas_fontsize > 12) {
            $cas_fontsize = $cas_fontsize - 3;
            $cas_imagebox = imagettfbbox($cas_fontsize, $cas_angle, $cas_font, $cas_antispam);
            $cas_boxheight = $cas_imagebox[1] - $cas_imagebox[7];
        }

        // Use png is available, since it produces clearer text images
        if( function_exists( 'imagepng' ) && $cas_UsePngNotJpeg )
        {
            imagettftext( $cas_image, $cas_fontsize, $cas_angle, $cas_offset, $cas_imgheight - $cas_offset, $cas_fontcolor, $cas_font, $cas_antispam );
            header( "Content-type: image/png" );
            imagepng( $cas_image );
        } else {
            imagettftext( $cas_image, $cas_fontsize, $cas_angle, $cas_offset, $cas_imgheight - $cas_offset, $cas_fontcolor, $cas_font, $cas_antispam );
            header( "Content-type: image/jpeg" );
            imagejpeg( $cas_image );
        }
    } else {
        $cas_fontsize = 5; // 1, 2, 3, 4 or 5 (higher numbers correspond to larger font sizes)
        $tmp_len = strlen( $cas_antispam );
        for( $tmp_count = 0; $tmp_count < $tmp_len; $tmp_count++ )
        {
           $tmp_xpos = $tmp_count * imagefontwidth( $cas_fontsize ) + 20;
           $tmp_ypos = 10;
           imagechar( $cas_image, $cas_fontsize, $tmp_xpos, $tmp_ypos, $cas_antispam, $cas_fontcolor );
           $cas_antispam = substr( $cas_antispam, 1);   
        }
        header("Content-Type: image/gif");
        imagegif( $cas_image );
    } // end if
    imagedestroy( $cas_image );
    die();
}

// Generate a new anti-spam image when called by the user
elseif ( isset( $_GET['antinew'] ) && $cas_js_generate ) {

    // Pick a random number
    $cas_antiselect = rand( 1, $cas_textcount ); // 0 is for invalid, so don't select it

    // Generate a new word
    $cas_antiword = $cas_text[$cas_antiselect];

    // Insert a row into the count database to generate an auto_increment number
    $wpdb->query('INSERT INTO ' . $cas_count . ' (id) VALUES (NULL)');

    // Get the id of the inserted count to feed to the word table and image generator
    
    //XTEC ************ MODIFICAT - Bug Fixed: La última fila recuperada era sempre 0.
	//2011.10.05 @fbassas
    
    $cas_rowid = $wpdb->insert_id;
    
    //************ ORIGINAL
    
    /*
    $cas_rowid = $wpdb->get_var('SELECT last_insert_id()');
    */

    //************ FI

    // Put the random word into the database
    $wpdb->query('INSERT INTO ' . $cas_table . ' (id, createtime, word) VALUES (' . $cas_rowid . ', ' . time() . ', \'' . $cas_antiword . '\')');

    // Delete the row from the count table
    $wpdb->query('DELETE FROM ' . $cas_count . ' WHERE id = ' . $cas_rowid);

    // Do some table admin while we can :D
    if (strlen($cas_rowid) == 10) {

        // Delete all rows from the count table
        $wpdb->query('DELETE FROM ' . $cas_count);

        // Reset the table's auto increment if it's getting too huge
        $wpdb->query('ALTER TABLE ' . $cas_count . ' AUTO_INCREMENT=1');

	    //XTEC ************ MODIFICAT - Redueix el timeout d'esborrat de paraules
		//2011.10.20 @fbassas
	    
		// Delete any anti-spam words more than a half day old
        $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 43200');
        	    
	    //************ ORIGINAL
	    
	    /*
	    // Delete any anti-spam words more than a day old
        $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 86400');
	    */
	
	    //************ FI        
        }
    // Output the number to send back to the user display
    die("$cas_rowid");
}


else {

    // Determine the blog url to this script
    $cas_message = "<small>{$cas_displaytext['instructions']}</small>";
    if ( function_exists(get_option) && !$cas_siteurl) {
        $cas_siteurl = get_option('siteurl');
    }
    if ( !$cas_siteurl ) {
        $cas_message = "<h1>{$cas_displaytext['manually_configure']}</h1>";
        }
    $cas_myurl = $cas_siteurl;

    class PeterAntiSpam
    {
        function PeterAntiSpam()
        {
            global $cas_manualinsert;
            add_action( 'secure_image', array( &$this, 'comment_form' ) );    // add image and input field to comment form
            if (! $cas_manualinsert ) {
                add_action( 'comment_form', array( &$this, 'comment_form' ) );    // add image and input field to comment form
            }
            add_filter( 'preprocess_comment', array( &$this, 'comment_post') );    // add post comment post security code check
        }

        function comment_form()
        {
            global $cas_forcereg, $user_ID, $cas_textcount, $cas_text, $cas_myurl, $cas_thisfolder, $cas_imgheight, $cas_imgwidth, $cas_limitcolor, $cas_manualinsert, $cas_borderclr, $cas_message, $cas_displaytext, $cas_table, $cas_count, $cas_wav, $cas_js_generate, $wpdb;
            // If the user is logged in, don't prompt for code
            if( ! $cas_forcereg && intval( $user_ID ) > 0 )
            {
                return( $post_ID );
            }

            if (!$cas_js_generate) {
            
                // Insert a row into the count database to generate an auto_increment number
                $wpdb->query('INSERT INTO ' . $cas_count . ' (id) VALUES (NULL)');

                // Get the id of the inserted count to feed to the word table and image generator
			    //XTEC ************ MODIFICAT - Bug Fixed: La última fila recuperada era sempre 0.
				//2011.10.05 @fbassas
			    
			    $cas_rowid = $wpdb->insert_id;
			    
			    //************ ORIGINAL
			    
			    /*
			    $cas_rowid = $wpdb->get_var('SELECT last_insert_id()');
			    */
			
			    //************ FI

                // Pick a random number
                $cas_antiselect = rand( 1, $cas_textcount ); // 0 is for invalid, so don't select it

                // Put the word corresponding to the random number into the database
                $wpdb->query('INSERT INTO ' . $cas_table . ' (id, createtime, word) VALUES (' . $cas_rowid . ', ' . time() . ', \'' . $cas_text[$cas_antiselect] . '\')');

                // Delete the row from the count table
                $wpdb->query('DELETE FROM ' . $cas_count . ' WHERE id = ' . $cas_rowid);
            }

            // Do some table admin while we can :D
            if (strlen($cas_rowid) == 10) {

                // Delete all rows from the count table
                $wpdb->query('DELETE FROM ' . $cas_count);

                // Reset the table's auto increment if it's getting too huge
                $wpdb->query('ALTER TABLE ' . $cas_count . ' AUTO_INCREMENT=1');

                // Delete any anti-spam words more than a day old
                $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 86400');
            }

            echo( "\t\t\t".'<div style="display:block;" id="secureimgdiv">'."\n\t\t\t\t" );
            echo( "<p><label for=\"securitycode\">{$cas_displaytext['label']}</label><span style=\"color:#FF0000;\">*</span><br />\n\t\t\t\t" );
            echo( $cas_message . "<br />\n\t\t\t\t" );
            echo( '<input type="text" name="securitycode" id="securitycode" size="30" tabindex="4" />'."\n\t\t\t\t" );
            echo( '<input id="cas_match" type="hidden" name="matchthis" value="' );
            if (!$cas_js_generate) {
                echo $cas_rowid;
            }
            echo "\" />\n\t\t\t\t";
            if ($cas_wav) {
                echo( '<a id="cas_link" href="' );
                if (!$cas_js_generate) {
                    echo $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . '/custom_anti_spam.php?audioselect=' . $cas_rowid;
                }
                echo ( '" rel="nofollow" title="' . $cas_displaytext['alt_tag'] . '">' . "\n\t\t\t\t" );
            }
            echo( '<img id="cas_image" src="' );
            if (!$cas_js_generate) {
                echo $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . '/custom_anti_spam.php?antiselect=' . $cas_rowid;
            }
            echo '" ';
            echo( 'alt="' . $cas_displaytext['alt_tag'] . '" ' );
            echo( 'style="border:1px solid ' . $cas_borderclr . ';vertical-align:top;' );
            echo( 'height:' . $cas_imgheight .'px;width:' . $cas_imgwidth . 'px;" />' );
            if ($cas_wav) echo( '</a>');
            echo( "</p>\n\t\t\t" );
            echo( "</div>\n\t\t\t" );
            if ( ! $cas_manualinsert ) {
            echo "<script language='JavaScript' type='text/javascript'>
            <!--
                var commentinput = document.getElementById(\"comment\").parentNode;
                var submitp = commentinput.parentNode;
                var substitution2 = document.getElementById(\"secureimgdiv\");
                submitp.insertBefore(substitution2, commentinput);
            // -->
            </script>\n";
            }
            
            if ($cas_js_generate) {
            // The JavaScript that updates the anti-spam image

                print '<script language="javascript" type="text/javascript">' . "\n";
                print '// Code modified from Peter\'s ajax date script at http://www.theblog.ca/php-unix-timestamp-generator-with-ajax' . "\n";
                print '// The date script modified code from a tutorial here: http://www.webpasties.com/xmlHttpRequest/xmlHttpRequest_tutorial_1.html' . "\n\n";
                print 'var casTime = new Date();';
                print "// Update the code that displays the anti-spam image
                var url = \"" . $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . "/custom_anti_spam.php?antinew&\" + casTime.getTime(); // The server-side script
                function handleHttpResponse() {
                    if (http.readyState == 4) {
                        if (http.responseText.indexOf('invalid') == -1) {
                            results = http.responseText;
                            document.getElementById('cas_match').value = results;
                            document.getElementById('cas_image').src = '" . $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . "/custom_anti_spam.php?antiselect=' + results;";
                if ($cas_wav) {
                print "
                            document.getElementById('cas_link').href = '" . $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . "/custom_anti_spam.php?audioselect=' + results;";
                }
                print "
                            isWorking = false;
                        }
                    }
                }

                var isWorking = false;\n\n";
                print "// Call up the script that generates the new id\n
                function newAntiSpam() {
                    if (!isWorking && http) {
                        http.open(\"GET\", url, true);
                        http.onreadystatechange = handleHttpResponse;
                        isWorking = true;
                        http.send(null);
                    }
                }";
                print "function getHTTPObject() {
                      var xmlhttp;
                      /*@cc_on
                      @if (@_jscript_version >= 5)
                          try { xmlhttp = new ActiveXObject(\"Msxml2.XMLHTTP\"); }
                          catch (e) {
                              try { xmlhttp = new ActiveXObject(\"Microsoft.XMLHTTP\"); }
                              catch (E) { xmlhttp = false; } }
                              @else xmlhttp = false;
                      @end @*/
                      if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
                          try { xmlhttp = new XMLHttpRequest(); }
                          catch (e) { xmlhttp = false; } }
                      return xmlhttp;
                }
                var http = getHTTPObject(); // We create the HTTP Object" . "\n";
                  print "newAntiSpam();\n\n";
                  print "</script>\n";
            }
            return( $post_ID );
        }

        function comment_post( $incoming_comment )
        {
            global $_POST, $cas_text, $cas_textcount, $cas_forcereg, $user_ID, $cas_allowtrack, $cas_allowping, $cas_displaytext, $cas_modping, $cas_modtrack, $cas_table, $wpdb;
            // Validate the form input values
            if( isset( $_POST['securitycode'] ) )
            {
                // Consider only the first 50 characters in the posted word
                $securitycode = substr( strval( $_POST['securitycode'] ), 0, 50 );

                // Remove all spaces and hyphens to give the commenter a break!
                $securitycode = str_replace(' ', '', $securitycode);
                $securitycode = str_replace('-', '', $securitycode);

            } else {
                $securitycode = '';
            }
            if( isset( $_POST['matchthis'] ) )
            {
                $matchnum = intval( $_POST['matchthis'] );
            } else {
                $matchnum = 0;
            }

            // If the user is not logged in check the security code
            if( ( $cas_forcereg || 0 == intval( $user_ID ) ) && !is_admin() )
            {
                $istrackping = $incoming_comment['comment_type'];
                $commentbody = $incoming_comment['comment_content'];
                if ( $istrackping == 'pingback' && $cas_allowping ) {

                    // Send all pingbacks to a moderation queue?
                    if ($cas_modping) add_filter('pre_comment_approved', create_function('$mod_ping', 'return \'0\';'));
                }
                elseif ( $istrackping == 'trackback' && $cas_allowtrack ) {

                    // Send all trackbacks to a moderation queue?
                    if ($cas_modtrack) add_filter('pre_comment_approved', create_function('$mod_track', 'return \'0\';'));
                }
                else
                {
                    if ( $securitycode == '' )
                    {
                        wp_die("<p>{$cas_displaytext['emptyfield']}</p>\n<p>{$cas_displaytext['copyfield']}</p>\n<textarea cols=\"100%\" rows=\"10\" onclick=\"this.select();\" readonly=\"true\">$commentbody</textarea>");
                    }

                    // Get the anti-spam word from the database
                    $matchthis = $wpdb->get_var('SELECT word FROM ' . $cas_table . ' WHERE id = ' . $matchnum);

                    // If this row doesn't exist, say something
                    if (is_null($matchthis)) wp_die("<p>{$cas_displaytext['alreadyused']}</p>\n<p>{$cas_displaytext['copyfield']}</p>\n<textarea cols=\"100%\" rows=\"10\" onclick=\"this.select();\" readonly=\"true\">$commentbody</textarea>");

                    // Remove all spaces and hyphens, since we removed them from what the commenter entered
                    $matchthis = str_replace(' ', '', $matchthis);
                    $matchthis = str_replace('-', '', $matchthis);

                    if ( strtolower( $matchthis ) != strtolower( $securitycode ) )
                    {
                        wp_die("<p>{$cas_displaytext['wrongfield']}</p>\n<p>{$cas_displaytext['copyfield']}</p>\n<textarea cols=\"100%\" rows=\"10\" onclick=\"this.select();\" readonly=\"true\">$commentbody</textarea>");
                    } else {
                        // The word matched, so delete the row for the anti-spam word so that it cannot be used again
                        $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE id = ' . $matchnum);
                        unset( $matchthis );

                        // Do some more table admin while we can :D
                        // Delete any anti-spam words more than a day old
                        $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 86400');
                    }
                }
            }
            return( $incoming_comment );
        }
    }
    new PeterAntiSpam();

// Add some troubleshooting to the Manage tab

// These functions are only needed in the back-end
if (is_admin()) {

function cas_manage() {
    global $cas_siteurl, $cas_pluginpath, $cas_settingsfullpath, $cas_wordfile, $wpdb;
    if ($_POST['submit']) {
        $line = array();
        $cas_words = htmlentities($_POST['cas_words'], ENT_QUOTES);
        // Get rid of all trailing lines
        $cas_words = trim($cas_words);
        // Get rid of all empty lines
        $cas_words = str_replace("\n\r", '', $cas_words);
        // Turn all remaining line returns into commas
        $cas_words = str_replace("\r\n", '\', \'', $cas_words);
        $line[] = '<?php';
        $line[] = '$cas_text = array(\'' . $cas_words . '\');';
        $line[] = '?>';
        $mystring = implode("\n", $line);
        $open = fopen($cas_wordfile, 'w');
        if (fwrite($open, $mystring)) print '<div id="message" class="updated fade"><p><strong>'.__('Word pool updated.').'</strong></p></div>' . "\n";
        fclose($open);
    }
    elseif ($_POST['submit_settings']) {
    
        $line = array();
        $line[] = '<?php';
        
    
        // Take care of dropdown list settings
        $cas_dropdown_settings = array('cas_forcereg', 'cas_allowtrack', 'cas_modtrack', 'cas_allowping', 'cas_modping', 
            'cas_randomcolors', 'cas_UsePngNotJpeg', 'cas_manualinsert', 'cas_wav',
            'cas_sox', 'cas_reg_protection', 'cas_js_generate');
        
        foreach($cas_dropdown_settings as $cas_dropdown_setting) {
            if (intval($_POST[$cas_dropdown_setting]) == 1) {
                $cas_setting_value = 'true';
            }
            else {
                $cas_setting_value = 'false';
            }
            $line[] = '$' . $cas_dropdown_setting . ' = ' . $cas_setting_value . ';';
        }
        
        // Take care of text area settings
        $cas_textarea_settings = array('cas_fontlist', 'cas_reg_blacklist');
        foreach($cas_textarea_settings as $cas_textarea_setting) {
            $cas_list = htmlentities($_POST[$cas_textarea_setting], ENT_QUOTES);
            // Get rid of all trailing lines
            $cas_list = trim($cas_list);
            // Get rid of all empty lines
            $cas_list = str_replace("\n\r", '', $cas_list);
            // Turn all remaining line returns into commas
            $cas_list = str_replace("\r\n", '\', \'', $cas_list);
            $line[] = '$' . $cas_textarea_setting . ' = array(\'' . $cas_list . '\');';
        }
        
        // Take care of number settings
        $cas_number_settings = array('cas_imgwidth', 'cas_imgheight');
        foreach($cas_number_settings as $cas_number_setting) {
            $cas_setting_value = intval($_POST[$cas_number_setting]);
            $line[] = '$' . $cas_number_setting . ' = ' . $cas_setting_value . ';';
        }
        
        // Take care of text field settings
        $cas_textfield_settings = array('cas_bgcolorset', 'cas_borderclr');

        foreach($cas_textfield_settings as $cas_textfield_setting) {
            $cas_setting_value = trim(htmlentities($_POST[$cas_textfield_setting], ENT_QUOTES));
            $line[] = '$' . $cas_textfield_setting . ' = \'' . $cas_setting_value . '\';';
        }
        
        // Take care of text / false settings
        $cas_textfalse_settings = array('cas_siteurl');
        foreach($cas_textfalse_settings as $cas_textfalse_setting) {
            if ($_POST[$cas_textfalse_setting] == '') {
                $cas_setting_value = 'false';
            }
            else {
                $cas_setting_value = "'" . trim(htmlentities($_POST[$cas_textfalse_setting], ENT_QUOTES)) . "'";
            }
            $line[] = '$' . $cas_textfalse_setting . ' = ' . $cas_setting_value . ';';
        }
        
        $line[] = '?>';
        $mystring = implode("\n", $line);
        $open = fopen($cas_settingsfullpath, 'w');
        if (fwrite($open, $mystring)) print '<div id="message" class="updated fade"><p><strong>'.__('Settings updated.').'</strong></p></div>' . "\n";
        fclose($open);
    }
print "<div class=\"wrap\">\n";
print "<h2>Peter's Custom Anti-Spam</h2>\n";

if (file_exists($cas_wordfile) ) {
    include $cas_wordfile;
}
else {
    die('The anti-spam word list does not exist. Make sure the "words" sub-directory is writable, then re-activate the plugin.');
}

?>
<h3>Customize words</h3>
<p>Enter the anti-spam words below, one per line.</p>
<p>If you want some words to be used more often, enter them multiple times.</p>
<p>It is best to use words that are eight letters or less.</p>
<form name="currentwords" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=custom_anti_spam.php">
<p><textarea name="cas_words" cols="60" rows="15"><?php
print implode("\r\n", $cas_text);
?></textarea></p>
<p class="submit"><input name="submit" type="submit" value="Update" /></p>
</form>
<?php
// Check for the file containing the settings list
if (file_exists($cas_settingsfullpath) ) {
    include $cas_settingsfullpath;
}
else {
    global $cas_forcereg, $cas_allowtrack, $cas_modtrack, $cas_allowping,
    $cas_modping, $cas_fontlist, $cas_imgwidth, $cas_imgheight,
    $cas_randomcolors, $cas_bgcolorset, $cas_borderclr, $cas_UsePngNotJpeg,
    $cas_manualinsert, $cas_siteurl, $cas_wav, $cas_sox,
    $cas_reg_protection, $cas_reg_blacklist;
}
?>
<h3>Customize settings</h3>
<form name="currentsettings" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=custom_anti_spam.php">
<table class="widefat">
<tr>
    <td width="40%">
        <p><strong>Force registered users to enter the anti-spam word</strong></p>
    </td>
    <td width="60%">
        <select name="cas_forcereg">
            <option value="0"<?php if (!$cas_forcereg) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_forcereg) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Allow trackbacks (but be vulnerable to trackback spam)</strong></p>
    </td>
    <td>
        <select name="cas_allowtrack">
            <option value="1"<?php if ($cas_allowtrack) print ' selected="selected"'; ?>>Yes</option>
            <option value="0"<?php if (!$cas_allowtrack) print ' selected="selected"'; ?>>No</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Send all trackbacks to the moderation queue (only works if you allow trackbacks)</strong></p>
    </td>
    <td>
        <select name="cas_modtrack">
            <option value="0"<?php if (!$cas_modtrack) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_modtrack) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Allow pingbacks (but be vulnerable to pingback spam)</strong></p>
    </td>
    <td>
        <select name="cas_allowping">
            <option value="1"<?php if ($cas_allowping) print ' selected="selected"'; ?>>Yes</option>
            <option value="0"<?php if (!$cas_allowping) print ' selected="selected"'; ?>>No</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Send all pingbacks to the moderation queue (only works if you allow pingbacks)</strong></p>
    </td>
    <td>
        <select name="cas_modping">
            <option value="0"<?php if (!$cas_modping) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_modping) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Font list</strong></p>
        <p>List as many TrueType font(s) as you like, one per line. Drop your own font files into this plugin's "fonts" sub-directory.</p>
        <p>If you are using your own fonts, make sure all fonts used are about the same default size.</p>
        <p>If you want some fonts to be used more frequently, enter them multiple times.</p>
        <p>Default freeware fonts are from fonts101.com</p>
    </td>
    <td>
        <textarea name="cas_fontlist" cols="35" rows="10"><?php print implode("\r\n", $cas_fontlist); ?></textarea>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Anti-spam image width</strong></p>
    </td>
    <td>
        <input type="text" name="cas_imgwidth" size="4" maxlength="4" value="<?php print $cas_imgwidth; ?>" />
    </td>
</tr>
<tr>
    <td>
        <p><strong>Anti-spam image height</strong></p>
    </td>
    <td>
        <input type="text" name="cas_imgheight" size="4" maxlength="4" value="<?php print $cas_imgheight; ?>" />
    </td>
</tr>
<tr>
    <td>
        <p><strong>Use random text colours</strong></p>
        <p>If random colors are not selected, blue text will appear on a white background, and white text will appear on black background (as decided in the next option).</p>
    </td>
    <td>
        <select name="cas_randomcolors">
            <option value="1"<?php if ($cas_randomcolors) print ' selected="selected"'; ?>>Yes</option>
            <option value="0"<?php if (!$cas_randomcolors) print ' selected="selected"'; ?>>No</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Background colour for the anti-spam image</strong></p>
    </td>
    <td>
        <select name="cas_bgcolorset">
            <option value="white"<?php if ($cas_bgcolorset == 'white') print ' selected="selected"'; ?>>White</option>
            <option value="black"<?php if ($cas_bgcolorset == 'black') print ' selected="selected"'; ?>>Black</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Border colour for the anti-spam image</strong></p>
        <p>Write either major colours (red, green, blue, etc.) or enter the HTML colour code (such as #C0C0C0).</p>
    </td>
    <td>
        <input type="text" name="cas_borderclr" size="10" maxlength="10" value="<?php print $cas_borderclr; ?>"/>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Use PNG graphics</strong></p>
        <p>PNG provides better quality text, while JPEG is more compatible with older browsers.</p>
    </td>
    <td>
        <select name="cas_UsePngNotJpeg">
            <option value="0"<?php if (!$cas_UsePngNotJpeg) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_UsePngNotJpeg) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Manually insert anti-spam code</strong></p>
        <p>By default, the code is automatically inserted using JavaScript.</p>
        <p>If you choose to manually insert it, you can place the code <strong>do_action('secure_image', $post->ID);</strong> into your comments template file.</p>
    </td>
    <td>
        <select name="cas_manualinsert">
            <option value="0"<?php if (!$cas_manualinsert) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_manualinsert) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Use JavaScript to generate anti-spam code</strong></p>
        <p>Most WordPress caching systems don't allow parts of the page to be exempt from caching.</p>
        <p>This setting gets around this problem by dynamically generating the anti-spam content with JavaScript.</p>
    </td>
    <td>
        <select name="cas_js_generate">
            <option value="0"<?php if (!$cas_js_generate) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_js_generate) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Site URL</strong></p>
        <p>Leave this blank unless you get an error message saying <strong>"The site administrator needs to manually configure his/her site address in the plugin configuration file!"</strong>.</p>
        <p>In that case, enter your site address, which should be the same as the WordPress address from your Options page. Enter it withOUT a trailing slash.</p>
    </td>
    <td>
        <input type="text" name="cas_siteurl" size="40" maxlength="255" value="<?php if ($cas_siteurl != get_option('siteurl')) print $cas_siteurl; ?>" />
    </td>
</tr>
<tr>
    <td>
        <p><strong>Enable sound</strong></p>
        <p>Enable wav files of the anti-spam words so that visually enabled users can comment.</p>
    </td>
    <td>
        <select name="cas_wav">
            <option value="1"<?php if ($cas_wav) print ' selected="selected"'; ?>>Yes</option>
            <option value="0"<?php if (!$cas_wav) print ' selected="selected"'; ?>>No</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Use "sox" filter</strong></p>
        <p>The "sox" filter stretches the audio files by a variable rate (so that they are harder to crack by spambots.</p>
        <p>Turn this off if you don't want this feature or if you know that your server does not have this filter.</p>
    </td>
    <td>
        <select name="cas_sox">
            <option value="1"<?php if ($cas_sox) print ' selected="selected"'; ?>>Yes</option>
            <option value="0"<?php if (!$cas_sox) print ' selected="selected"'; ?>>No</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Registration form anti-spam</strong></p>
    </td>
    <td>
        <select name="cas_reg_protection">
            <option value="0"<?php if (!$cas_reg_protection) print ' selected="selected"'; ?>>No</option>
            <option value="1"<?php if ($cas_reg_protection) print ' selected="selected"'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <p><strong>Registration blacklist</strong></p>
        <p>Enter either e-mail address or domains (like "theblog.ca") of e-mail addresses you don't want to allow, one per line.</p>
        <p>Be careful, because if you enter "theblog.ca", and an e-mail address contains that string anywhere, it will be blocked.</p>
        <p>This only works if you enable registration form anti-spam.</p>
    </td>
    <td>
        <textarea name="cas_reg_blacklist" cols="35" rows="10"><?php print implode("\r\n", $cas_reg_blacklist); ?></textarea>
    </td>
</tr>
</table>
<p class="submit"><input name="submit_settings" type="submit" value="Update" /></p>
</form>
<h3>Server diagnosis</h3>
<?php
print "<p>This section will diagnose your server setup to see how it relates to the performance of this plugin.</p>\n";
print "<hr />\n";
print "<p>Can't see the anti-spam image when you're viewing a comment form? Remember to log out of WordPress first -- by default, the image doesn't show to registered users (although you can edit that option in the plugin file).</p>\n";
print "<hr />\n";

print "<p><strong>GD library</strong><br />\n";
if (function_exists(imagejpeg)) {
print "Yay! The GD library is installed. This is the most important thing to be able to use this plugin. You might still need to do some tweaking to the settings of course.</p>\n";
}
else {
print "<font color=\"red\">The GD library is not installed. This plugin will not work without it. Ask your webhost to install this library (or install it if you manage your own server).</font></p>\n";
}

print "<p><strong>FreeType</strong><br />\n";
if (function_exists(imagettftext)) {
print "Yay! The FreeType library is installed. The anti-spam image should display using the uploaded fonts.</p>\n";
}
else {
print "<font color=\"red\">The FreeType library is not installed. The anti-spam image should still display, but with only a plain font. Ask your webhost to install this library (or install it if you manage your own server) if you want to be able to use the uploaded fonts.</font></p>\n";
}

print "<p><strong>get_option</strong><br />\n";
if (function_exists(get_option)) {
print "Yay! The script should be able to automatically figure out your blog address.</p>\n";
}
elseif ($cas_siteurl) {
print "The script cannot automatically figure out your blog address, but you have entered it manually. This should be the correct address to your blog: $cas_siteurl. Otherwise, please make sure you correct the site URL value above (something like <em>http://www.yoursiteaddress.com/whatever</em>).";
}
else {
print "<font color=\"red\">For some reason, the script cannot access your blog address. Make sure you edit the site URL value above (something like <em>http://www.yoursiteaddress.com/whatever</em>).</font></p>\n";
}

print "<hr />\n";
print "<p>Need more help? Post a comment on the <a href=\"http://www.theblog.ca/anti-spam\" title=\"Where all information is posted about the plugin\">Peter's Custom Anti-Spam page</a>.</p>\n";
print "</div>\n";
}

function cas_adminmenu() {
    add_options_page('Custom anti-spam', 'Custom anti-spam', 7, 'custom_anti_spam.php', 'cas_manage');
}
//XTEC ************ ELIMINAT - Modificacions a l'extensió.
//2011.05.26 @fbassas

/*
add_action('admin_menu','cas_adminmenu',1);
*/

//************ FI
} // Close the back-end check
// Install the anti-spam word database table upon plugin activation

function cas_install () {
    global $wpdb, $cas_tablename, $cas_countname, $cas_pluginpath, $cas_wordpath;
    //XTEC ************ MODIFICAT - Modificacions a l'extensió.
    //2011.05.26 @fbassas
    
    $cas_thistable = $wpdb->base_prefix . $cas_tablename;
    $cas_thiscount = $wpdb->base_prefix . $cas_countname;
    
    //************ ORIGINAL
    
    /*
    $cas_thistable = $wpdb->prefix . $cas_tablename;
    $cas_thiscount = $wpdb->prefix . $cas_countname;
    */
    
    //************ FI
    
    // Add the table to hold the anti-spam words
    if($wpdb->get_var("SHOW TABLES LIKE '$cas_thistable'") != $cas_thistable) {
        $sql = "CREATE TABLE " . $cas_thistable . " (
          id int(10) NOT NULL,
          createtime int(10) NOT NULL,
          word VARCHAR(20) NOT NULL,
          UNIQUE KEY id (id)
        );";
          $wpdb->query($sql);
    }

    // Add the table to hold the count for the anti-spam words
    if($wpdb->get_var("SHOW TABLES LIKE '$cas_thiscount'") != $cas_thiscount) {
        $sql = "CREATE TABLE " . $cas_thiscount . " (
          id int(10) NOT NULL AUTO_INCREMENT,
          UNIQUE KEY id (id)
        );";
          $wpdb->query($sql);
    }
    
    // Add the pertinent word file
    //XTEC ************ MODIFICAT - Modificacions a l'extensió.
	//2011.05.26 @fbassas
	
	//************ ORIGINAL
	
	$cas_wordfile = $cas_pluginpath . $cas_wordpath . $wpdb->base_prefix . 'cas_words.php';
    	
	/*
	$cas_wordfile = $cas_pluginpath . $cas_wordpath . $wpdb->prefix . 'cas_words.php';
	*/
	
	//************ FI
    
    if (!file_exists($cas_wordfile)) {
        $line = array();
        $line[] = '<?php';
        $line[] = '$cas_text = array(\'dan\', \'broad\', \'way\', \'toast\', \'micro\', \'wave\', \'ikea\', \'rent\', \'move\', \'van\', \'connect\');';
        $line[] = '?>';
        $mystring = implode("\n", $line);
        $open = fopen($cas_wordfile, 'w');
        fwrite($open, $mystring);
        fclose($open);
    }
}

// Remove the anti-spam word database table upon plugin de-activation

function cas_uninstall () {
    global $wpdb, $cas_tablename, $cas_countname, $cas_pluginpath, $cas_wordpath;
    //XTEC ************ MODIFICAT - Modificacions a l'extensió.
    //2011.05.26 @fbassas
    
    $cas_thistable = $wpdb->base_prefix . $cas_tablename;
    $cas_thiscount = $wpdb->base_prefix . $cas_countname;
    
    $sql = "DROP TABLE " . $cas_thistable;
    $wpdb->query($sql);
    
    $sql = "DROP TABLE " . $cas_thiscount;
    $wpdb->query($sql);
    
    //************ ORIGINAL
    
    /*
    $cas_thistable = $wpdb->prefix . $cas_tablename;
    $cas_thiscount = $wpdb->prefix . $cas_countname;
    
    if($wpdb->get_var("SHOW TABLES LIKE '$cas_thistable'") == $cas_thistable) {
        $sql = "DROP TABLE " . $cas_thistable;
        $wpdb->query($sql);
    }

    if($wpdb->get_var("SHOW TABLES LIKE '$cas_thiscount'") == $cas_thiscount) {
        $sql = "DROP TABLE " . $cas_thiscount;
        $wpdb->query($sql);
    }
    */
    
    //************ FI
}

add_action('activate_' . end($cas_thisfolder) . '/custom_anti_spam.php', 'cas_install');
add_action('deactivate_' . end($cas_thisfolder) . '/custom_anti_spam.php', 'cas_uninstall');


function cas_register_form() {
    global $cas_text, $cas_textcount, $cas_myurl, $cas_thisfolder, $cas_imgheight, $cas_imgwidth, $cas_limitcolor, $cas_borderclr, $cas_message, $cas_displaytext, $cas_table, $cas_count, $cas_wav, $wpdb;

    // Insert a row into the count database to generate an auto_increment number
    $wpdb->query('INSERT INTO ' . $cas_count . ' (id) VALUES (NULL)');

    // Get the id of the inserted count to feed to the word table and image generator
    
    //XTEC ************ MODIFICAT - Bug Fixed: La última fila recuperada era sempre 0.
	//2011.10.05 @fbassas
    
    $cas_rowid = $wpdb->insert_id;
    
    //************ ORIGINAL
    
    /*
    $cas_rowid = $wpdb->get_var('SELECT last_insert_id()');
    */

    //************ FI
    
    // Pick a random number
    $cas_antiselect = rand( 1, $cas_textcount ); // 0 is for invalid, so don't select it

    // Put the word corresponding to the random number into the database
    $wpdb->query('INSERT INTO ' . $cas_table . ' (id, createtime, word) VALUES (' . $cas_rowid . ', ' . time() . ', \'' . $cas_text[$cas_antiselect] . '\')');
    
    // Delete the row from the count table
    $wpdb->query('DELETE FROM ' . $cas_count . ' WHERE id = ' . $cas_rowid);

    // Do some table admin while we can :D
    if (strlen($cas_rowid) == 10) {

        // Delete all rows from the count table
        $wpdb->query('DELETE FROM ' . $cas_count);

        // Reset the table's auto increment if it's getting too huge
        $wpdb->query('ALTER TABLE ' . $cas_count . ' AUTO_INCREMENT=1');

        // Delete any anti-spam words more than a day old
        $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 86400');
    }

    echo( "\t\t\t".'<div style="display:block;" id="secureimgdiv">'."\n\t\t\t\t" );
    echo( "<p><label for=\"securitycode\">{$cas_displaytext['label']}</label><span style=\"color:#FF0000;\">*</span><br />\n\t\t\t\t" );
    echo( $cas_message . "<br />\n\t\t\t\t" );
    echo( '<input type="text" name="securitycode" id="securitycode" size="30" tabindex="4" />'."\n\t\t\t\t" );
    echo( '<input type="hidden" name="matchthis" value="' . $cas_rowid . "\" />\n\t\t\t\t" );
    if ($cas_wav) echo( '<a href="' . $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . '/custom_anti_spam.php?audioselect=' . $cas_rowid . '" title="' . $cas_displaytext['alt_tag'] . '">' );
        echo( '<img src="' . $cas_myurl . '/wp-content/plugins/' . end($cas_thisfolder) . '/custom_anti_spam.php?antiselect=' . $cas_rowid . "\"\n\t\t\t\t" );
        echo( 'alt="' . $cas_displaytext['alt_tag'] . '" ' );
        echo( 'style="border:1px solid ' . $cas_borderclr . ';vertical-align:top;' );
        echo( 'height:' . $cas_imgheight .'px;width:' . $cas_imgwidth . 'px;" />' );
        if ($cas_wav) echo( '</a>');
        echo( "</p>\n\t\t\t" );
        echo( "</div>\n\t\t\t" );
}

function cas_register_post($errors) {
    global $_POST, $cas_text, $cas_textcount, $cas_displaytext, $cas_table, $wpdb, $cas_reg_blacklist;

        // Validate the form input values
        if( isset( $_POST['securitycode'] ) )
        {
            // Consider only the first 50 characters in the posted word
            $securitycode = substr( strval( $_POST['securitycode'] ), 0, 50 );

            // Remove all spaces and hyphens to give the commenter a break!
            $securitycode = str_replace(' ', '', $securitycode);
            $securitycode = str_replace('-', '', $securitycode);

        } else {
            $securitycode = '';
        }
        if( isset( $_POST['matchthis'] ) )
        {
            $matchnum = intval( $_POST['matchthis'] );
        } else {
            $matchnum = 0;
        }

        if ( $securitycode == '' )
        {
            $errors->add('cas_register', $cas_displaytext['emptyfield']);
            return $errors;
        }
        else {
            // Get the anti-spam word from the database
            $matchthis = $wpdb->get_var('SELECT word FROM ' . $cas_table . ' WHERE id = ' . $matchnum);

            // If this row doesn't exist, say something
            if (is_null($matchthis)) {
                $errors->add('cas_register', $cas_displaytext['register_alreadyused']);
                return $errors;
            }

            else {

                // Remove all spaces and hyphens, since we removed them from what the commenter entered
                $matchthis = str_replace(' ', '', $matchthis);
                $matchthis = str_replace('-', '', $matchthis);

                // Check what was entered against what the code should be
                if ( strtolower( $matchthis ) != strtolower( $securitycode ) ) {
                    $errors->add('cas_register', $cas_displaytext['register_wrongfield']);
                    return $errors;
                }

                else {
                    // The word matched, so delete the row for the anti-spam word so that it cannot be used again
                    $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE id = ' . $matchnum);
                    unset( $matchthis );

                    // Do some more table admin while we can :D
                    // Delete any anti-spam words more than a day old
                    $wpdb->query('DELETE FROM ' . $cas_table . ' WHERE ' . time() . ' > createtime + 86400');
                }
            }
        }
        foreach ($cas_reg_blacklist as $cas_blacklist) {
            if (stristr($_POST['user_email'], $cas_blacklist)) {
                $errors->add('cas_register', $cas_displaytext['register_blocked']);
                return $errors;
            }
        }
        return $errors;
    }

// Add registration protection to the appropriate hooks only if it has been enabled in this plugin's settings
if ($cas_reg_protection) {
    add_action('register_form', 'cas_register_form');
    add_action('registration_errors', 'cas_register_post', 1, 1);
    }
}
?>