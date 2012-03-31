<?php

function p2_new_user_prompt() {
	echo p2_get_new_user_prompt();
}
	function p2_get_new_user_prompt() {
		$prompt = get_option( 'p2_prompt_text' );

		return $prompt;
	}

function custom_excerpt_length( $length ) {
	return 35;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');
?>
<?php
function my_scripts_method() {
	wp_enqueue_script(
		'custom_script',
		get_bloginfo('stylesheet_directory') . '/js/custom.js',
		array('jquery')
	);
}
add_action('wp_enqueue_scripts', 'my_scripts_method');
?>