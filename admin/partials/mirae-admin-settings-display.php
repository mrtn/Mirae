<div class="wrap">
    <h1>Mirae – Front page settings</h1>

    <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
        <div id="message" class="updated notice is-dismissible">
            <p><strong>Settings saved successfully.</strong></p>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php
            settings_fields('mirae_settings');
            do_settings_sections('mirae_settings');
        ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="displayName">Display Name</label></th>
                <td>
                    <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr(get_option('display_name')); ?>" class="regular-text" />
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="introText">Intro Text</label></th>
                <td>
                    <textarea id="intro_text" name="intro_text" rows="3" class="large-text"><?php echo esc_textarea(get_option('intro_text')); ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="profilePicture">Profile Picture</label></th>
                <td>
                    <?php $profile_url = esc_url(get_option('profile_picture')); ?>
                    <input type="text" id="profilePicture" name="profile_picture" value="<?php echo $profile_url; ?>" class="regular-text" />
                    <input type="button" class="button" id="upload_profile_picture" value="Upload / Select Image" />
                    <div id="profile_picture_preview" style="margin-top: 10px;">
                        <?php if ($profile_url): ?>
                            <img src="<?php echo $profile_url; ?>" alt="Profile preview" style="max-height: 100px; border: 1px solid #ccc;" />
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="backgroundImage">Background Image</label></th>
                <td>
                    <?php $background_url = esc_url(get_option('background_image')); ?>
                    <input type="text" id="backgroundImage" name="background_image" value="<?php echo $background_url; ?>" class="regular-text" />
                    <input type="button" class="button" id="upload_background_image" value="Upload / Select Image" />
                    <div id="background_image_preview" style="margin-top: 10px;">
                        <?php if ($background_url): ?>
                            <img src="<?php echo $background_url; ?>" alt="Background preview" style="max-height: 100px; border: 1px solid #ccc;" />
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
