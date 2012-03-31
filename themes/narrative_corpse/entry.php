<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>
<li id="prologue-<?php the_ID(); ?>" <?php post_class(); ?>>

	

	<?php
	/**
	 * CONTENT
	 */
	?>

	<div id="content-<?php the_ID(); ?>" class="postcontent">
		<?php
		if ( 'status' == p2_get_the_category() || 'link' == p2_get_the_category() ) :
			the_excerpt( __( '(More ...)' , 'p2' ) );

		elseif ( 'quote' == p2_get_the_category() ) :
			echo "<blockquote>";
			p2_quote_content();
			echo "</blockquote>";

		elseif ( 'post' == p2_get_the_category() ) :
			p2_title();
			the_content( __( '(More ...)' , 'p2' ) );

		else :
			p2_title();
			the_content( __( '(More ...)' , 'p2' ) );

		endif; ?>
	</div>

		
<?php
	wp_link_pages( array( 'before' => '<p class="page-nav">' . __( 'Pages:', 'p2' ) ) ); ?>

	
</li>
