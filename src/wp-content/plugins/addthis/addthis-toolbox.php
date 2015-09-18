<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

define('AT_API_URL', 'http://adt00:8080/live/red_lojson');

/**
 * Class for output addthis tool box
 */
class Addthis_ToolBox
{

    const AT_ABOVE_POST_HOME = "at-above-post-homepage";
    const AT_BELOW_POST_HOME = "at-below-post-homepage";
    const AT_ABOVE_POST_PAGE = "at-above-post-page";
    const AT_BELOW_POST_PAGE = "at-below-post-page";
    const AT_ABOVE_POST = "at-above-post";
    const AT_BELOW_POST = "at-below-post";
    const AT_ABOVE_POST_CAT_PAGE = "at-above-post-cat-page";
    const AT_BELOW_POST_CAT_PAGE = "at-below-post-cat-page";
    const AT_ABOVE_POST_ARCH_PAGE = "at-above-post-arch-page";
    const AT_BELOW_POST_ARCH_PAGE = "at-below-post-arch-page";
    const AT_CONTENT_BELOW_POST_HOME = "at-below-post-homepage-recommended";
    const AT_CONTENT_BELOW_POST_PAGE = "at-below-post-page-recommended";
    const AT_CONTENT_BELOW_POST = "at-below-post-recommended";
    const AT_CONTENT_BELOW_CAT_PAGE = "at-below-post-cat-page-recommended";
    const AT_CONTENT_BELOW_ARCH_PAGE = "at-below-post-arch-page-recommended";
    const AT_CONTENT_ABOVE_POST_HOME = "at-above-post-homepage-recommended";
    const AT_CONTENT_ABOVE_POST_PAGE = "at-above-post-page-recommended";
    const AT_CONTENT_ABOVE_POST = "at-above-post-recommended";
    const AT_CONTENT_ABOVE_CAT_PAGE = "at-above-post-cat-page-recommended";
    const AT_CONTENT_ABOVE_ARCH_PAGE = "at-above-post-arch-page-recommended";
    protected $addThisConfigs;
    protected $cmsConnector;

    /**
     * Initializes the widget class.
     * */
    public function __construct($addThisConfigs, $cmsConnector)
    {
        $this->addThisConfigs = $addThisConfigs;
        $this->cmsConnector = $cmsConnector;

        add_filter('the_content', array($this, 'addWidget'));
        add_filter('get_the_excerpt', array($this, 'addWidgetForExcerpt'));
    }

    /**
     * Adds toolbox to excerpts wp pages -- can't use addWidget straight out
     * because when the filter on get_the_excerpt is added, it doesn't yet know
     * if it has an excerpt or not
     *
     * @param string $content Page contents
     * @return string Page content with our sharing button HTML added
     */
    public function addWidgetForExcerpt($content){
        if (has_excerpt() || !is_single()) {
            $content = $this->addWidget($content);
        }
        return $content;
    }

    /**
     * Adds toolbox to wp pages
     *
     * @param string $content Page contents
     *
     * @return string
     */
    public function addWidget($content)
    {
        $configs = $this->addThisConfigs->getConfigs();

        if ($this->addThisConfigs->getProfileId() && !is_404() && !is_feed()) {
            global $post;
            $postid = $post->ID;
            $at_flag = get_post_meta( $postid, '_at_widget', TRUE );
            if (!$configs['addthis_per_post_enabled']) {
                $at_flag = '1';
            }

            if (is_home() || is_front_page()) {
                if($at_flag == '' || $at_flag == '1'){
                    $content  = self::_buildDiv(self::AT_ABOVE_POST_HOME) .
                                self::_buildDiv(self::AT_CONTENT_ABOVE_POST_HOME) .
                                $content;
                    $content .= self::_buildDiv(self::AT_BELOW_POST_HOME);
                    $content .= self::_buildDiv(self::AT_CONTENT_BELOW_POST_HOME);
                }
            } else if (is_page()) {
                if($at_flag == '' || $at_flag == '1'){
                    $content  = self::_buildDiv(self::AT_ABOVE_POST_PAGE) .
                                self::_buildDiv(self::AT_CONTENT_ABOVE_POST_PAGE) .
                                $content;
                    $content .= self::_buildDiv(self::AT_BELOW_POST_PAGE);
                    $content .= self::_buildDiv(self::AT_CONTENT_BELOW_POST_PAGE);
                }
            } else if (is_single()) {
                if($at_flag == '' || $at_flag == '1'){
                    $content  = self::_buildDiv(self::AT_ABOVE_POST) .
                                self::_buildDiv(self::AT_CONTENT_ABOVE_POST, false) .
                                $content;
                    $content .= self::_buildDiv(self::AT_BELOW_POST);
                    $content .= self::_buildDiv(self::AT_CONTENT_BELOW_POST, false);
                }
            }  else if (is_category()) {
                if($at_flag == '' || $at_flag == '1'){
                    $content  = self::_buildDiv(self::AT_ABOVE_POST_CAT_PAGE) .
                                self::_buildDiv(self::AT_CONTENT_ABOVE_CAT_PAGE) .
                                $content;
                    $content .= self::_buildDiv(self::AT_BELOW_POST_CAT_PAGE);
                    $content .= self::_buildDiv(self::AT_CONTENT_BELOW_CAT_PAGE);
                }
            }  else if (is_archive()) {
                if($at_flag == '' || $at_flag == '1'){
                    $content  = self::_buildDiv(self::AT_ABOVE_POST_ARCH_PAGE) .
                                self::_buildDiv(self::AT_CONTENT_ABOVE_ARCH_PAGE) .
                                $content;
                    $content .= self::_buildDiv(self::AT_BELOW_POST_ARCH_PAGE);
                    $content .= self::_buildDiv(self::AT_CONTENT_BELOW_ARCH_PAGE);
                }
            }
        }

        return $content;
    }

    /**
     * Build toolbox div
     *
     * @param string $class Class name
     *
     * @return string
     */
    private static function _buildDiv($class, $inline_data = true)
    {
        $title = get_the_title();
        $url   = get_permalink();
        if($inline_data == true){
            return "<div class='".$class." addthis_default_style addthis_toolbox at-wordpress-hide'".
                       " data-title='".$title."' data-url='".$url."'>".
                    "</div>";
        } else {
             return "<div class='".$class." addthis_default_style addthis_toolbox at-wordpress-hide'></div>";
        }
    }

    /**
     * Get user's activated tools in addthis
     *
     * @return array
     */
    public static function getUserTools()
    {
        $curl = curl_init();
        $url  = AT_API_URL . '?pub='. $this->addThisConfigs->getProfileId();
        $url .= '&dp=' . Addthis_Wordpress::getDomain();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        $activatedTools = null;

        if ($response) {
            foreach ($response as $key => $value) {
                if ($key == 'pc') {
                    $activatedTools = $value;
                    break;
                }
            }
        }
        return $activatedTools ? explode(',', $activatedTools) : array();
    }
}
