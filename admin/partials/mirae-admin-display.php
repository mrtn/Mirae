<?php
/**
 * Admin links overview page.
 *
 * @package Mirae
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<?php
// `settings-updated` is a read-only redirect marker set by WordPress's own Settings API
// after a successful options.php save. We only use it to render a confirmation notice;
// no state is mutated based on its value, so nonce verification is not applicable here.
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$mirae_settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) : '';
// phpcs:enable WordPress.Security.NonceVerification.Recommended
if ( 'true' === $mirae_settings_updated ) :
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Settings saved successfully.', 'mirae' ); ?></p>
	</div>
<?php endif; ?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Mirae', 'mirae' ); ?></h1>

	<p class="description">
		<?php esc_html_e( 'Select a platform, enter your link and (optionally) customize the button text. You can reorder the links by dragging the rows below.', 'mirae' ); ?>
	</p>

	<h2><?php esc_html_e( 'Add new link', 'mirae' ); ?></h2>

	<div id="error-message" class="notice notice-error is-dismissible" style="display: none;">
		<p><?php esc_html_e( 'Please fill in all required fields before adding a link.', 'mirae' ); ?></p>
	</div>

	<table class="form-table">
		<tr>
			<th scope="row"><label for="platform"><?php esc_html_e( 'Platform', 'mirae' ); ?></label></th>
			<td>
				<select id="platform" class="regular-text">
					<option value=""><?php esc_html_e( '-- Select a platform --', 'mirae' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="url"><?php esc_html_e( 'URL', 'mirae' ); ?></label></th>
			<td><input type="url" id="url" class="regular-text" placeholder="https://..." /></td>
		</tr>
		<tr>
			<th scope="row"><label for="buttonText"><?php esc_html_e( 'Button text', 'mirae' ); ?></label></th>
			<td>
				<input type="text" id="buttonText" class="regular-text" placeholder="<?php esc_attr_e( 'Leave empty to use default', 'mirae' ); ?>">
				<p class="description"><?php esc_html_e( 'If left empty, the default button text from the platform will be used.', 'mirae' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"></th>
			<td><button type="button" id="add" class="button button-primary"><?php esc_html_e( 'Add', 'mirae' ); ?></button></td>
		</tr>
	</table>

	<h2><?php esc_html_e( 'Links', 'mirae' ); ?></h2>
	<table id="overview" class="widefat fixed striped">
		<thead>
			<tr>
				<th>&#x21F5;</th>
				<th><?php esc_html_e( 'Platform', 'mirae' ); ?></th>
				<th><?php esc_html_e( 'URL', 'mirae' ); ?></th>
				<th><?php esc_html_e( 'Button text', 'mirae' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'mirae' ); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

	<form method="post" action="options.php">
		<div id="save-message" class="notice notice-success is-dismissible" style="display:none;">
			<p><?php esc_html_e( 'Settings saved successfully.', 'mirae' ); ?></p>
		</div>
		<?php
		settings_fields( 'mirae_links' );
		do_settings_sections( 'mirae_links' );
		?>
		<p><button type="submit" id="save" class="button button-primary"><?php esc_html_e( 'Save', 'mirae' ); ?></button></p>
		<input type="hidden" id="link_data" name="link_data" value="<?php echo esc_attr( get_option( 'link_data' ) ); ?>" />
	</form>
</div>
