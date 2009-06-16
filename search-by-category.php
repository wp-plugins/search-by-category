<?php
/*
Plugin Name: Search By Category
Plugin URI: http://fire-studios.com/blog/search-by-category/
Description: Reconfigures search results to display results based on category of posts.
Version: 1.0.0
Author: Fire G
Author URI: http://fire-studios.com/blog/
*/

/* 
Change log

1.0.0
 - Default text
 - Custom styling

Beta 3
 - Search Text
 - Exclude Child categories
 - search box auto empties and refills if nothing entered

Beta 2
 - First complete working version
 - Hide Empty
 - Focus
 - Exclude Categories

Beta 1
 - First working version
 - Category exclustion from drop-down list isn't functional

Alpha 1
 - All functions are present but independent

*/

// Some Defaults
$focus					= 'In All Categories';
$hide_empty				= '1'; // 1 means true
$excluded_cats			= array();
$search_text			= 'Search For...';
$exclude_child			= '0'; // 0 means false
$raw_excluded_cats		= array();
$sbc_style				= '1';

// Put our defaults in the "wp-options" table
add_option("sbc-focus", $focus);
add_option("sbc-hide-empty", $hide_empty);
add_option("sbc-excluded-cats", $excluded_cats);
add_option("sbc-search-text", $search_text);
add_option("sbc-selected-excluded", $raw_excluded_cats);
add_option("sbc-exclude-child", $exclude_child);
add_option("sbc-style", $sbc_style);

// Start the plugin
if ( ! class_exists( 'SBC_Admin' ) ) {

	class SBC_Admin {

		// prep options page insertion
		function add_config_page() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_options_page('Search By Category Options', 'Search By Category', 10, basename(__FILE__), array('SBC_Admin','config_page'));
				add_filter( 'plugin_action_links', array( 'SBC_Admin', 'filter_plugin_actions' ), 10, 2 );
				add_filter( 'ozh_adminmenu_icon', array( 'SBC_Admin', 'add_ozh_adminmenu_icon' ) );
			}
		}

		function filter_plugin_actions( $links, $file ){
			//Static so we don't call plugin_basename on every plugin row.
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
			
			if ( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=search-by-category.php">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link ); // before other links
			}
			return $links;
		}

		// Options/Settings page in WP-Admin
		function config_page() {
			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
				check_admin_referer('sbc-updatesettings');
				
				// Get our new option values
				$focus					= $_POST['focus'];
				$hide_empty				= $_POST['hide-empty'];
				$search_text			= $_POST['search-text'];
				$exclude_child			= $_POST['exclude-child'];
				$sbc_style				= $_POST['sbc-style'];
				
				if(isset($_POST['post_category'])){
					$raw_excluded_cats 		= $_POST['post_category'];
					
					// Fix our excluded category return values
					$fix					= $raw_excluded_cats;
					array_unshift($fix, "1");
					$excluded_cats			= implode(',',$fix);
				}
				
				// Make sure "$hide_empty" & "$exclude_child" are set right
				if (empty($hide_empty)) $hide_empty = '0'; // 0 means false
				if (empty($exclude_child)) $exclude_child = '0'; // 0 means false
				if (empty($sbc_style)) $sbc_style = '0'; // 0 means false 
				
				// Update the DB with the new option values
				update_option("sbc-focus", $focus);
				update_option("sbc-hide-empty", $hide_empty);
				update_option("sbc-selected-excluded", $raw_excluded_cats);
				update_option("sbc-excluded-cats", $excluded_cats);
				update_option("sbc-search-text", $search_text);
				update_option("sbc-exclude-child", $exclude_child);
				update_option("sbc-style", $sbc_style);
			}

			$focus					= get_option("sbc-focus");
			$hide_empty				= get_option("sbc-hide-empty");
			$search_text			= get_option("sbc-search-text");
			$excluded_cats			= get_option("sbc-excluded-cats");
			$exclude_child			= get_option("sbc-exclude-child");
			$raw_excluded_cats 		= get_option("sbc-selected-excluded"); // For Admin Checklist
			$sbc_style				= get_option("sbc-style");
			
			?>
			<div class="wrap">
				<h2>Seach By Category Options</h2>
				<form action="" method="post" id="sbc-config">
					<table class="form-table">
						<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('sbc-updatesettings'); } ?>
						<tr>
							<th scope="row" valign="top"><label for="search-text">Display text in the search box:</label></th>
							<td><input type="text" name="search-text" id="search-text" value="<?php echo $search_text; ?>"/></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><label for="focus">Display text in drop-down selection:</label></th>
							<td><input type="text" name="focus" id="focus" value="<?php echo $focus; ?>"/></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><label for="hide-empty">Hide categories with no posts?</label></th>
							<td><input type="checkbox" name="hide-empty" id="hide-empty" value="1" <?php if ($hide_empty == '1') echo 'checked="checked"'; ?> /></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><label for="exclude-child">Exclude Child categories from list?</label></th>
							<td><input type="checkbox" name="exclude-child" id="exclude-child" value="1" <?php if ($exclude_child == '1') echo 'checked="checked"'; ?> /></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><label for="sbc-style">Use the SBC Form styling?</label></th>
							<td><input type="checkbox" name="sbc-style" id="sbc-style" value="1" <?php if ($sbc_style == '1') echo 'checked="checked"'; ?> /></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><label for="focus">Categories to exclude:</label></th>
							<td><ul><?php wp_category_checklist(0,0,$raw_excluded_cats); ?></ul></td>
						</tr>
					</table>
					<br/>
					<span class="submit" style="border: 0;"><input type="submit" name="submit" value="Save Settings" /></span>
				</form>
			</div>
<?php		}
	}
}


// Base function
function sbc() {
	global $wp_query, $post;
	
	$focus					= get_option("sbc-focus");
	$hide_empty				= get_option("sbc-hide-empty");
	$excluded_cats			= get_option("sbc-excluded-cats");
	$search_text			= get_option("sbc-search-text");
	$exclude_child			= get_option("sbc-exclude-child");
	
	$settings = array('show_option_all' => $focus.' &nbsp; &nbsp; &nbsp; &nabla;',
						'show_option_none' => '',
						'orderby' => 'name', 
						'order' => 'ASC',
						'show_last_update' => 0,
						'show_count' => 0,
						'hide_empty' => $hide_empty, 
						'child_of' => 0,
						'exclude' => "'".$excluded_cats."'",
						'echo' => 0,
						'selected' => 0,
						'hierarchical' => 1, 
						'name' => 'cat',
						'class' => 'postform',
						'depth' => $exclude_child);
	$list = wp_dropdown_categories($settings); 
	
	$blog_url = get_bloginfo("url");
	
	$form = <<< EOH
	<div id="sbc">
		<form method="get" id="sbc-search" action="{$blog_url}">
			<input type="text" value="{$search_text}" name="s" id="s" onblur="if (this.value == '') {this.value = '{$search_text}';}"  onfocus="if (this.value == '{$search_text}') {this.value = '';}" />
			{$list}
			<input type="submit" id="sbc-submit" value="Search" />
		</form>
	</div>	
EOH;
	
	echo $form;
}

// Get results only from selected category
function return_only_selected_category() {
	if (isset($_POST['sbc-submit'])){
		global $wp_query;
		
		$desired_cat = $_POST['cat'];
		if ($desired_cat == '*') $desired_cat = '';
		
		$excluded = get_categories('hide_empty=false&exclude={$desired_cat}');
		
		$wp_query->query_vars['cat'] = '-' . $excluded;
	}
}

if($sbc_style == '1'){
// Add our styling
function style_insert() {
	$current_path = get_option('siteurl') .'/wp-content/plugins/' . basename(dirname(__FILE__)) .'/';
	?>
	<link href="<?php echo $current_path; ?>sbc-style.css" type="text/css" rel="stylesheet" />
	<?php
}

// insert custom stylesheet
add_action('wp_head','style_insert');
}

// Highjack the search
add_filter('pre_get_posts', 'return_only_selected_category');

// insert into admin panel
add_action('admin_menu', array('SBC_Admin','add_config_page'));
?>