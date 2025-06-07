<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://maartenkumpen.com
 * @since      1.0.0
 *
 * @package    Mirae
 * @subpackage Mirae/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<form method="post" action="options.php">
    <?php
        settings_fields( 'miraesettings' );
        do_settings_sections( 'miraesettings' );
    ?>
    <div class="mb-3">
        <label for="display_name" class="form-label">Dispplay Name</label>
        <input type="text" name="theemail" value="<?php echo get_option('displayName'); ?>" class="form-control" id="display_name" placeholder="John Doe">
    </div>
    <div class="mb-3">
        <label for="bio_text" class="form-label">Bio/Introduction</label>
        <textarea class="form-control" name="bio_text" id="bio_text" rows="3"><?php echo get_option('bioText'); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<ul>
    <li>Profiel foto</li>
    <li><s>Display Name</s></li>
    <li><s>Bio text</s></li>
    <li>BackgroundImage</li>
</ul>