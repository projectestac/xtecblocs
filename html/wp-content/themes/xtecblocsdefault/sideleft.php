<div class="sidebox">
    <span class="sideboxright">&nbsp;</span>
    <span class="sideboxleft">&nbsp;</span>
    <h3 class="noticies">Cerca</h3>
    <div class="sidecontent">
        <form method="get" action="https://www.google.com/search" target="_blank">
            <input type="hidden" name="as_sitesearch" value="<?php echo esc_attr(DOMAIN_CURRENT_SITE); ?>"/>
            <input type="text" id="paraulaCerca" name="q" style="width:117px;float:left;"/>
            <button type="submit" id="botoCerca">Cerca</button>
        </form>
    </div>
</div>
<div class="sidebox">
    <span class="sideboxright"></span>
    <span class="sideboxleft"></span>
    <h3 class="noticies">Notícies</h3>
    <div class="sidecontent">
        <ul>
        <?php foreach (get_posts(['numberposts' => 5]) as $news_post) : ?>
        <li><a href="index.php?id=<?php echo $news_post->ID; ?>"><?php echo get_the_title($news_post); ?></a></li>
        <?php endforeach; ?>
        </ul>
        <ul class="cloudtags">
            <li class="mes"><a href="<?php echo esc_url(home_url('/index.php?a=newsList')); ?>">Més...</a></li>
        </ul>
    </div>
</div>


<div class="sidebox">
    <span class="sideboxright">&nbsp;</span>
    <span class="sideboxleft">&nbsp;</span>
    <h3 class="noticies">Descriptors</h3>
    <div class="sidecontent">
        <ul class="cloudtags">
        <?php $cloudArray = xtec_descriptors_get_descriptors_cloud(25, 12, 25);
        foreach ($cloudArray as $cloud) {
            $tag = htmlspecialchars_decode($cloud['tag'], ENT_QUOTES);
            $url = home_url('/index.php?a=list&desc=' . rawurlencode($tag));
            ?>
            <li><a style="font-size:<?php echo $cloud['size'];?>px; color:#1E4588;" class="tag_cloud"
                href="<?php echo esc_url($url); ?>">
                <?php echo $cloud['tag'];?>
            </a></li>
        <?php } ?>
        <li class="mes"><a href="<?php echo esc_url(home_url('/index.php?a=allDescriptors')); ?>">Més...</a></li>
        </ul>
        <!-- 
        end of els descriptors 
        -->
    </div>
</div>
