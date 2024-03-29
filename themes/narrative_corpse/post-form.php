<?php
/**
 * @package WordPress
 * @subpackage P2
 */
?>
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery(document).ready(function($) {
			jQuery('#post_cat').val($('#post-types a.selected').attr('id'));
			$('#post-types a').click(function(e) {
				jQuery('.post-input').hide();
				$('#post-types a').removeClass('selected');
				jQuery(this).addClass('selected');
				if ($(this).attr('id') == 'post') {
					jQuery('#posttitle').val("<?php echo esc_js( __('Post Title', 'p2') ); ?>");
				} else {
					jQuery('#posttitle').val('');
				}
				jQuery('#postbox-type-' + $(this).attr('id')).show();
				jQuery('#post_cat').val($(this).attr('id'));
				return false;
			});
		});
	/* ]]> */
	</script>

<?php
// set default post type
$post_type = p2_get_posting_type();
?>
<div id="postbox">
	

		<div class="inputarea">

			<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
				<?php if ( 'status' == p2_get_posting_type() || '' == p2_get_posting_type() ) : ?>
				<label for="posttext">
					<?php p2_new_user_prompt(); ?>
				</label>
				<?php endif; ?>

				<div id="postbox-type-post" class="post-input <?php if ( 'post' == p2_get_posting_type() ) echo ' selected'; ?>">
					<input type="text" name="posttitle" id="posttitle" tabindex="1" value=""
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Post Title', 'p2' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Post Title', 'p2' ) ); ?>' : this.value;" />
				</div>
				
				<textarea class="expand70-200" name="posttext" id="posttext" tabindex="1" rows="4" cols="60"></textarea>
				<div id="postbox-type-quote" class="post-input <?php if ( 'quote' == p2_get_posting_type() ) echo " selected"; ?>">
					<label for="postcitation" class="invisible"><?php _e( 'Citation', 'p2' ); ?></label>
						<input id="postcitation" name="postcitation" type="text" tabindex="2"
							value="<?php esc_attr_e( 'Citation', 'p2' ); ?>"
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Citation', 'p2' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Citation', 'p2' ) ); ?>' : this.value;" />
				</div>
				<label class="post-error" for="posttext" id="posttext_error"></label>
				<div class="postrow">
					<input id="submit" type="submit" tabindex="3" value="<?php esc_attr_e( 'Submit', 'p2' ); ?>" />
				</div>
				<input type="hidden" name="post_cat" id="post_cat" value="<?php echo esc_attr( $post_type ); ?>" />
				<span class="progress" id="ajaxActivity">
					<img src="<?php echo str_replace( WP_CONTENT_DIR, content_url(), locate_template( array( 'i/indicator.gif' ) ) ); ?>"
						alt="<?php esc_attr_e( 'Loading...', 'p2' ); ?>" title="<?php esc_attr_e( 'Loading...', 'p2' ); ?>"/>
				</span>

				<?php do_action( 'p2_post_form' ); ?>

				<input type="hidden" name="action" value="post" />
				<?php wp_nonce_field( 'new-post' ); ?>
			</form>

		</div>

		<div class="clear"></div>

</div> <!-- // postbox -->
