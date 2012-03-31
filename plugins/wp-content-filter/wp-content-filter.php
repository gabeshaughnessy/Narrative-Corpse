<?php
/*
Plugin Name: WP Content Filter
Plugin URI: http://www.presscoders.com/plugins/wp-content-filter/
Description: Filter out profanity, swearing, abusive comments and any other keywords from your site.
Version: 2.28
Author: David Gwyer
Author URI: http://www.presscoders.com
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer@presscoders.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// pccf_ prefix is derived from [p]ress [c]oders [c]ontent [f]ilter
register_activation_hook(__FILE__, 'pccf_add_defaults');
register_uninstall_hook(__FILE__, 'pccf_delete_plugin_options');
add_action('admin_init', 'pccf_init' );
add_action('admin_menu', 'pccf_add_options_page');
add_action( 'plugins_loaded', 'pccf_contfilt' );

// ***************************************
// *** START - Create Admin Options    ***
// ***************************************

// delete options table entries ONLY when plugin deactivated AND deleted
function pccf_delete_plugin_options() {
	delete_option('pccf_options');
}

// Define default option settings
function pccf_add_defaults() {
	$old_options = get_option('wpcf_options');
	if ( is_array($old_options) ) {
		$tmp = get_option('wpcf_options');
	}
	else {
		$tmp = get_option('pccf_options');
	}
    if( ($tmp['chk_default_options_db']=='1') || (!is_array($tmp)) ) {
		$arr = array("chk_post_content" => "1", "chk_comments" => "1", "txtar_keywords" => "Saturn, Jupiter, Pluto", "rdo_word" => "all", "drp_filter_char" => "star", "rdo_case" => "insen", "chk_default_options_db" => "", "rdo_strict_filtering" => "strict_on");
		update_option('pccf_options', $arr);
	}
}

// Init plugin options to white list our options
function pccf_init(){
	// put the below into a function and add checks all sections (especially radio buttons) have a valid choice (i.e. no section is blank)
	// this is primarily to check newly added options have correct initial values
	$tmp = get_option('pccf_options');
	if(!$tmp['rdo_strict_filtering'])
	{   // check strict filtering option has a starting value
		$tmp["rdo_strict_filtering"] = "strict_off";
		update_option('pccf_options', $tmp);
	}
	register_setting( 'pccf_plugin_options', 'pccf_options', 'pccf_validate_options' );
}

// Add menu page
function pccf_add_options_page() {
	add_options_page('WP Content Filter Options Page', 'WP Content Filter', 'manage_options', __FILE__, 'pccf_render_form');
}

// Draw the menu page itself
function pccf_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>WP Content Filter Options</h2>
		<p>Configure the Plugin options below</p>
		<form method="post" action="options.php">
			<?php settings_fields('pccf_plugin_options'); ?>
			<?php $options = get_option('pccf_options'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Keywords to Remove</th>
					<td>
						<textarea name="pccf_options[txtar_keywords]" rows="7" cols="50" type='textarea'><?php echo $options['txtar_keywords']; ?></textarea><br /><span style="color:#666666;margin-left:2px;">Separate keywords with commas</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Strict Filtering</th>
					<td>
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_off" <?php checked('strict_off', $options['rdo_strict_filtering']); ?> /> Strict Filtering OFF <span style="color:#666666;margin-left:124px;">['ass' => 'p***able']</span></label><br />
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_on" <?php checked('strict_on', $options['rdo_strict_filtering']); ?> /> Strict Filtering ON (recommended) <span style="color:#666666;margin-left:32px;">['ass' => 'passable']</span></label><br /><span style="color:#666666;">Note: When strict filtering is ON, embedded keywords are no longer filtered.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Content to be filtered</th>
					<td>
						<label><input name="pccf_options[chk_post_content]" type="checkbox" value="1" <?php if (isset($options['chk_post_content'])) { checked('1', $options['chk_post_content']); } ?> /> Blog Posts</label><br />
						<label><input name="pccf_options[chk_post_title]" type="checkbox" value="1" <?php if (isset($options['chk_post_title'])) { checked('1', $options['chk_post_title']); } ?> /> Post Title <em>(also filters recent posts sidebar widget)</em></label><br />
						<label><input name="pccf_options[chk_comments]" type="checkbox" value="1" <?php if (isset($options['chk_comments'])) { checked('1', $options['chk_comments']); } ?> /> Comments <em>(filters comment author names too)</em></label><br />
						<label><input name="pccf_options[chk_tags]" type="checkbox" value="1" <?php if (isset($options['chk_tags'])) { checked('1', $options['chk_tags']); } ?> /> Tags</label><br />
						<label><input name="pccf_options[chk_tag_cloud]" type="checkbox" value="1" <?php if (isset($options['chk_tag_cloud'])) { checked('1', $options['chk_tag_cloud']); } ?> /> Tag Cloud</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Word Rendering</th>
					<td>
						<label><input name="pccf_options[rdo_word]" type="radio" value="first" <?php checked('first', $options['rdo_word']); ?> /> First letter retained <span style="color:#666666;margin-left:40px;">[dog => d**]</span></label><br />
						<label><input name="pccf_options[rdo_word]" type="radio" value="all" <?php checked('all', $options['rdo_word']); ?> /> All letters removed <span style="color:#666666;margin-left:40px;">[dog => ***]</span></label><br />
						<label><input name="pccf_options[rdo_word]" type="radio" value="firstlast" <?php checked('firstlast', $options['rdo_word']); ?> /> First/last letter retained <span style="color:#666666;margin-left:15px;">[dog => d*g]</span></label>
					</td>
				</tr>
				<tr>
					<th scope="row">Filter Character</th>
					<td>
						<select name='pccf_options[drp_filter_char]'>
							<option value='star' <?php selected('star', $options['drp_filter_char']); ?>>[*] Asterisk</option>
							<option value='dollar' <?php selected('dollar', $options['drp_filter_char']); ?>>[$] Dollar</option>
							<option value='question' <?php selected('question', $options['drp_filter_char']); ?>>[?] Question</option>
							<option value='exclamation' <?php selected('exclamation', $options['drp_filter_char']); ?>>[!] Exclamation</option>
							<option value='hyphen' <?php selected('hyphen', $options['drp_filter_char']); ?>>[-] Hyphen</option>
							<option value='hash' <?php selected('hash', $options['drp_filter_char']); ?>>[#] Hash</option>
							<option value='tilde' <?php selected('tilde', $options['drp_filter_char']); ?>>[~] Tilde</option>
							<option value='blank' <?php selected('blank', $options['drp_filter_char']); ?>>[] Blank</option>
						</select>
						<span style="color:#666666;margin-left:2px;">[] Blank - completely removes the filtered keywords from view</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Case Matching</th>
					<td>
						<label><input name="pccf_options[rdo_case]" type="radio" value="sen" <?php checked('sen', $options['rdo_case']); ?> /> Case Sensitive</label><br />
						<label><input name="pccf_options[rdo_case]" type="radio" value="insen" <?php checked('insen', $options['rdo_case']); ?> /> Case Insensitive (recommended)</label><br /><span style="color:#666666;">Note: 'Case Insensitive' matching type is better as it captures more words!</span>
					</td>
				</tr>
				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="pccf_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<p style="margin-top:20px;font-style:italic;">
			<span><a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-content-filter/images/facebook-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-content-filter/images/twitter-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.presscoders.com" title="PressCoders.com" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/wp-content-filter/images/pc-icon.png" /></a></span>
		</p>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function pccf_validate_options($input) {
	 // strip html from textboxes
	$input['txtar_keywords'] =  wp_filter_nohtml_kses($input['txtar_keywords']);
	return $input;
}

// ***************************************
// *** END - Create Admin Options    ***
// ***************************************

// ---------------------------------------------------------------------------------

// ***************************************
// *** START - Plugin Core Functions   ***
// ***************************************

function pccf_contfilt() {
	$tmp = get_option('pccf_options');
	
	if (isset($tmp['chk_post_content'])) {
		if($tmp['chk_post_content']=='1'){ add_filter('the_content', 'pccf_filter'); }
	}
	if (isset($tmp['chk_post_title'])) {
		if($tmp['chk_post_title']=='1'){ add_filter('the_title', 'pccf_filter'); }
	}
	if (isset($tmp['chk_comments'])) {
		if($tmp['chk_comments']=='1'){ add_filter('comment_text','pccf_filter'); }
	}
	if (isset($tmp['chk_comments'])) {
		if($tmp['chk_comments']=='1'){ add_filter('get_comment_author', 'pccf_filter'); }
	}
	if (isset($tmp['chk_tags'])) {
		if($tmp['chk_tags']=='1'){ add_filter('term_links-post_tag', 'pccf_filter' ); }
	}
	if (isset($tmp['chk_cloud'])) {
		if($tmp['chk_cloud']=='1'){ add_filter('wp_tag_cloud', 'pccf_filter'); }
	}
}

function pccf_filter($text) {
	$tmp = get_option('pccf_options');
	$wildcard_filter_type = $tmp['rdo_word'];
	$wildcard_char = $tmp['drp_filter_char'];

	if($wildcard_char == 'star'){
		$wildcard = '*';
	} else if($wildcard_char == 'dollar') {
		$wildcard = '$';
	} else if($wildcard_char == 'question') {
		$wildcard = '?';
	} else if($wildcard_char == 'exclamation') {
		$wildcard = '!';
	} else if($wildcard_char == 'hyphen') {
		$wildcard = '-';
	} else if($wildcard_char == 'hash') {
		$wildcard = '#';
	} else if($wildcard_char == 'tilde') {
		$wildcard = '~';
	} else {
		$wildcard = '';
	}

	$filter_type = $tmp['rdo_case'];
	$db_search_string = $tmp['txtar_keywords'];
	$search = explode(",", $db_search_string);
	$search = array_unique($search); // get rid of duplicates in the keywords textbox

	if($tmp['rdo_strict_filtering']=='strict_off')
	{
		// If strict filtering is OFF - use the standard str_ireplace, and str_ireplace functions
		foreach($search as $sub_search)
		{
			$sub_search = trim($sub_search); // remove whitespace chars from start/end of string
			if(strlen($sub_search) > 2)
			{
				if($wildcard_filter_type == 'first')
				{
					$tmp_search = substr($sub_search, 0, 1).str_repeat($wildcard, strlen(substr($sub_search, 1)));
				}
				else if($wildcard_filter_type == 'all')
				{
					$tmp_search = str_repeat($wildcard, strlen(substr($sub_search, 0)));
				}
				else
				{
					$tmp_search = substr($sub_search, 0, 1).str_repeat($wildcard, strlen(substr($sub_search, 2))).substr($sub_search, -1, 1);
				}
				if($filter_type == "insen")
				{
					$text = str_ireplace($sub_search, $tmp_search, $text);
				}
				else
				{ $text = str_replace($sub_search, $tmp_search, $text); }
			}
		}
	}
	else
	{
		// If strict filtering is ON - use regular expressions for more powerful seach and replace
		foreach($search as $sub_search)
		{
			$sub_search = trim($sub_search); // remove whitespace chars from start/end of string
			if(strlen($sub_search) > 2)
			{
				if($wildcard_filter_type == 'first')
				{
					$tmp_search = substr($sub_search, 0, 1).str_repeat($wildcard, strlen(substr($sub_search, 1)));
				}
				else if($wildcard_filter_type == 'all')
				{
					$tmp_search = str_repeat($wildcard, strlen(substr($sub_search, 0)));
				}
				else
				{
					$tmp_search = substr($sub_search, 0, 1).str_repeat($wildcard, strlen(substr($sub_search, 2))).substr($sub_search, -1, 1);
				}
				if($filter_type == "insen")
				{
					$text = str_replace_word_i($sub_search, $tmp_search, $text);
				}
				else
				{ $text = str_replace_word($sub_search, $tmp_search, $text); }
			}
		}
	}

return $text;
}

// case insensitive
function str_replace_word_i($needle,$replacement,$haystack){
	$needle = str_replace('/','\\/', preg_quote($needle)); // allow '/' in keywords
	$pattern = "/\b$needle\b/i";
    $haystack = preg_replace($pattern, $replacement, $haystack);
    return $haystack;
}

// case sensitive
function str_replace_word($needle,$replacement,$haystack){
	$needle = str_replace('/','\\/',preg_quote($needle)); // allow '/' in keywords
	$pattern = "/\b$needle\b/";
    $haystack = preg_replace($pattern, $replacement, $haystack);
    return $haystack;
}

// ***************************************
// *** END - Plugin Core Functions     ***
// ***************************************