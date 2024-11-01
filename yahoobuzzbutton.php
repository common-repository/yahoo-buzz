<?php 

/*

Plugin Name: yahoo Buzz

Plugin URI: http://www.digcms.com/wp-plugins/

Description: Adds a button which allows you to share post on yahoo buzz. 

License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html

Version: 1.0

Author: digcms.com

Author URI: http://www.digcms.com

*/

if ( !defined('yahooBUZZ_URL') ) {

	define('yahooBUZZ_URL',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');

} else {

	define('yahooBUZZ_URL',WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');

}

function get_the_excerpt_here($post_id)
{
  global $wpdb;
  $query = "SELECT post_excerpt FROM wp_posts WHERE ID = " . $post_id . " LIMIT 1";
  $result = $wpdb->get_results($query, ARRAY_A);
  return $result[0]['post_excerpt'];
}


function yahoo_buzz_button($content) {
	global $post;
    $url = '';
    if (get_post_status($post->ID) == 'publish') {
    $url = get_permalink();
	$title = get_the_title($post->ID);
	$excerpt = get_the_excerpt_here($post->ID);

	$category = get_the_category($post->ID); 
	$first_cat = $category[0]->cat_name;

    }	

	if (get_option('yahoobuzz_where')=='manual' && get_option('yahoobuzz_style')!=''){
$button = '<div id="buzz_share_1" style="'.get_option('yahoobuzz_style').'">
<script type="text/javascript">
    yahooBuzzArticleHeadline = "'.$title.'";
    yahooBuzzArticleSummary = "'.$excerpt.'";
    yahooBuzzArticleCategory = "'.$first_cat.'";
    yahooBuzzArticleType = "text";
    yahooBuzzArticleId = window.location.href;
</script>
	<script type="text/javascript"
	src="http://d.yimg.com/ds/badge2.in.js"
	badgetype="square">
	<?php the_permalink() ?>	
</script></div>';
	} else {
	$button = '<div id="buzz_share_1" style="float: right; margin-left:10px; margin-right: 0px">
<script type="text/javascript">
    yahooBuzzArticleHeadline = "'.$title.'";
    yahooBuzzArticleSummary = "'.$excerpt.'";
    yahooBuzzArticleCategory = "'.$first_cat.'";
    yahooBuzzArticleType = "text";
    yahooBuzzArticleId = window.location.href;
</script>
	<script type="text/javascript"
	src="http://d.yimg.com/ds/badge2.in.js"
	badgetype="square">
	<?php the_permalink() ?>	
</script>
</div>';
	}	

			if (get_option('yahoobuzz_where') == 'beforeandafter') {
				return $button . $content . $button;
			} else if (get_option('yahoobuzz_where') == 'before') {
				return $button . $content;
			} else {
				return $content . $button;
			}


}


add_filter('the_content', 'yahoo_buzz_button');
add_filter('the_excerpt', 'yahoo_buzz_button');

function yahoobuzz_options() {
	add_menu_page('Yahoo Buzz', 'Yahoo Buzz', 8, basename(__FILE__), 'yahoo_buzz_options_page');
	add_submenu_page(basename(__FILE__), 'Settings', 'Settings', 8, basename(__FILE__), 'yahoo_buzz_options_page');
}


if(is_admin()){
    add_action('admin_menu', 'yahoobuzz_options');
    add_action('admin_init', 'yahoobuzz_init');
}

function yahoobuzz_init(){
    if(function_exists('register_setting')){       
        register_setting('yahoo-buzz-options', 'yahoobuzz_where');       
		register_setting('yahoo-buzz-options', 'yahoobuzz_style');       
    }
}

function yahoobuzz_activate(){
    add_option('yahoobuzz_where', 'before');
    add_option('yahoobuzz_style', 'before');
	
}

function yahoo_buzz_options_page() {
?>
<div style="padding:50px;">
<h2>Settings for yahoo Buzz button Integration in your blog</h2>
			<p>This plugin will install yahoo buzz button in page and post. This plugin will provide you more updated features.  </p>
			<form method="post" action="options.php">
			<?php
				// New way of setting the fields, for WP 2.7 and newer
				if(function_exists('settings_fields')){
					settings_fields('yahoo-buzz-options');
				} else {
					wp_nonce_field('update-options');?>

					<input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="yahoobuzz_where" />
            <?php }?> Display Position<br>
                		<select name="yahoobuzz_where" onchange="if(this.value == 'manual'){getElementById('manualhelp').style.display = 'block';} else {getElementById('manualhelp').style.display = 'none';}">

                			<option <?php if (get_option('yahoobuzz_where') == 'before') echo 'selected="selected"'; ?> value="before">Before</option>

                			<option <?php if (get_option('yahoobuzz_where') == 'after') echo 'selected="selected"'; ?> value="after">After</option>

                			<option <?php if (get_option('yahoobuzz_where') == 'beforeandafter') echo 'selected="selected"'; ?> value="beforeandafter">Before and After</option>

							<option <?php if (get_option('yahoobuzz_where') == 'manual') echo 'selected="selected"'; ?> value="manual">Manual</option>

                		</select><br>
<p>
If you use yahoo buzz button it like on digcms.com then use<b> clear:left; float: left; margin-right: 10px; margin-top:10px;</b> </p>

                    <input name="yahoobuzz_style" type="text" id="yahoobuzz_style" value="<?php echo htmlspecialchars(get_option('yahoobuzz_style')); ?>" size="30" />
                  

		<br><br>
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
    </form>
		</div>
<?php } ?>
