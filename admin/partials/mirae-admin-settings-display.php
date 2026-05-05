<?php
/**
 * Admin front-page settings page.
 *
 * @package Mirae
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$bg_color = (string) get_option( 'container_bg_color' );
$alpha    = get_option( 'container_bg_alpha', '0.8' );
$alpha    = '' === $alpha ? '0.8' : $alpha;
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Mirae – Front page settings', 'mirae' ); ?></h1>

	<?php
	// `settings-updated` is a read-only redirect marker set by WordPress's own Settings API
	// after a successful options.php save. We only use it to render a confirmation notice;
	// no state is mutated based on its value, so nonce verification is not applicable here.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$mirae_settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	if ( '' !== $mirae_settings_updated && 'false' !== $mirae_settings_updated ) :
		?>
		<div id="message" class="updated notice is-dismissible">
			<p><strong><?php esc_html_e( 'Settings saved successfully.', 'mirae' ); ?></strong></p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'mirae_settings' );
		do_settings_sections( 'mirae_settings' );
		?>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="display_name"><?php esc_html_e( 'Display Name', 'mirae' ); ?></label></th>
				<td>
					<input type="text" id="display_name" name="display_name" value="<?php echo esc_attr( get_option( 'display_name' ) ); ?>" class="regular-text" />
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="intro_text"><?php esc_html_e( 'Intro Text', 'mirae' ); ?></label></th>
				<td>
					<textarea id="intro_text" name="intro_text" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'intro_text' ) ); ?></textarea>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="profile_picture"><?php esc_html_e( 'Profile Picture', 'mirae' ); ?></label></th>
				<td>
					<?php $profile_url = esc_url( get_option( 'profile_picture' ) ); ?>
					<input type="text" id="profile_picture" name="profile_picture" value="<?php echo esc_attr( $profile_url ); ?>" class="regular-text" />
					<input type="button" class="button" id="upload_profile_picture" value="<?php esc_attr_e( 'Upload / Select Image', 'mirae' ); ?>" />
					<div id="profile_picture_preview" style="margin-top: 10px;">
						<?php if ( $profile_url ) : ?>
							<img src="<?php echo esc_url( $profile_url ); ?>" alt="<?php esc_attr_e( 'Profile preview', 'mirae' ); ?>" style="max-height: 100px; border: 1px solid #ccc;" />
						<?php endif; ?>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="background_image"><?php esc_html_e( 'Background Image', 'mirae' ); ?></label></th>
				<td>
					<?php $background_url = esc_url( get_option( 'background_image' ) ); ?>
					<input type="text" id="background_image" name="background_image" value="<?php echo esc_attr( $background_url ); ?>" class="regular-text" />
					<input type="button" class="button" id="upload_background_image" value="<?php esc_attr_e( 'Upload / Select Image', 'mirae' ); ?>" />
					<div id="background_image_preview" style="margin-top: 10px;">
						<?php if ( $background_url ) : ?>
							<img src="<?php echo esc_url( $background_url ); ?>" alt="<?php esc_attr_e( 'Background preview', 'mirae' ); ?>" style="max-height: 100px; border: 1px solid #ccc;" />
						<?php endif; ?>
					</div>
				</td>
			</tr>

			<tr>
				<?php $pattern_url = esc_url( get_option( 'overlay_pattern' ) ); ?>
				<th scope="row"><label for="overlay_pattern"><?php esc_html_e( 'Overlay Pattern', 'mirae' ); ?></label></th>
				<td>
					<input type="text" id="overlay_pattern" name="overlay_pattern" value="<?php echo esc_attr( $pattern_url ); ?>" class="regular-text" />
					<input type="button" class="button" id="upload_overlay_pattern" value="<?php esc_attr_e( 'Upload / Select Pattern', 'mirae' ); ?>" />
					<div id="overlay_pattern_preview" style="margin-top: 10px;">
						<?php if ( $pattern_url ) : ?>
							<img src="<?php echo esc_url( $pattern_url ); ?>" alt="<?php esc_attr_e( 'Overlay preview', 'mirae' ); ?>" style="max-height: 100px; border: 1px solid #ccc;" />
						<?php endif; ?>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="container_bg_color"><?php esc_html_e( 'Container Background', 'mirae' ); ?></label></th>
				<td>
					<input type="text" id="container_bg_color" name="container_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" class="color-picker" data-alpha="true" />
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="container_bg_alpha"><?php esc_html_e( 'Container Transparency', 'mirae' ); ?></label></th>
				<td>
					<input type="range" id="container_bg_alpha" name="container_bg_alpha" min="0" max="1" step="0.05" value="<?php echo esc_attr( $alpha ); ?>" oninput="this.nextElementSibling.value = this.value" />
					<output style="margin-left: 0.5rem;"><?php echo esc_html( $alpha ); ?></output>
					<p class="description"><?php esc_html_e( 'Adjust background transparency (0 = fully transparent, 1 = solid)', 'mirae' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row"></th>
				<td>
					<div id="container_preview" style="
						width: 300px;
						height: 60px;
						background-color: <?php echo esc_attr( Mirae_Util::hex_to_rgba( $bg_color, (float) $alpha ) ); ?>;
						display: flex;
						align-items: center;
						justify-content: center;
						color: #fff;
						border-radius: 4px;
						border: 1px solid #ccc;
						margin-top: 0.5rem;
					">
						<?php esc_html_e( 'Preview', 'mirae' ); ?>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="text_color"><?php esc_html_e( 'Text Color', 'mirae' ); ?></label></th>
				<td>
					<input type="text" id="text_color" name="text_color" value="<?php echo esc_attr( get_option( 'text_color' ) ); ?>" class="color-picker" data-alpha="true" />
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
