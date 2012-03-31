<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>
<?php get_header(); ?>


<div class="sleeve_main">
	<div id="main">
	
		<ul id="postlist">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
	    		<?php p2_load_entry(); ?>
			<?php endwhile; ?>

		<?php else : ?>

			<li class="no-posts">
		    	<h3><?php _e( 'Start up the Story!', 'p2' ); ?></h3>
			</li>

		<?php endif; ?>
		</ul>

	</div> <!-- main -->

	<?php if ( p2_user_can_post() && !is_archive() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>


</div> <!-- sleeve -->

<?php get_footer(); ?>