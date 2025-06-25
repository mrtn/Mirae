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
                    <input type="text" id="profile_picture" name="profile_picture" value="<?php echo $profile_url; ?>" class="regular-text" />
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
                    <input type="text" id="background_image" name="background_image" value="<?php echo $background_url; ?>" class="regular-text" />
                    <input type="button" class="button" id="upload_background_image" value="Upload / Select Image" />
                    <div id="background_image_preview" style="margin-top: 10px;">
                        <?php if ($background_url): ?>
                            <img src="<?php echo $background_url; ?>" alt="Background preview" style="max-height: 100px; border: 1px solid #ccc;" />
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <?php $value = esc_url(get_option('overlay_pattern')); ?>
            <tr>
                <th scope="row"><label for="overlay_pattern">Overlay Pattern</label></th>
                <td>
                    <input type="text" id="overlay_pattern" name="overlay_pattern" value="<?php echo $value; ?>" class="regular-text" />
                    <input type="button" class="button" id="upload_overlay_pattern" value="Upload / Select Pattern" />
                    <div id="overlay_pattern_preview" style="margin-top: 10px;">
                    <?php if ($value): ?>
                        <img src="<?php echo $value; ?>" alt="Overlay preview" style="max-height: 100px; border: 1px solid #ccc;" />
                    <?php endif; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="container_bg_color">Container Background (RGBA)</label></th>
                <td>
                    <input type="text" id="container_bg_color" name="container_bg_color" value="<?php echo esc_attr(get_option('container_bg_color')); ?>" class="color-picker" data-alpha="true" />
                </td>
            </tr>
            <?php $alpha = get_option('container_bg_alpha', '0.8'); ?>
            <tr>
                <th scope="row"><label for="container_bg_alpha">Container Transparency</label></th>
                <td>
                    <input type="range" id="container_bg_alpha" name="container_bg_alpha" min="0" max="1" step="0.05" value="<?php echo esc_attr($alpha); ?>" oninput="this.nextElementSibling.value = this.value" />
                    <output style="margin-left: 0.5rem;"><?php echo esc_attr($alpha); ?></output>
                    <p class="description">Adjust background transparency (0 = fully transparent, 1 = solid)</p>
                </td>
            </tr>

            <tr>
                <th scope="row"></th>
                <td>
                <?php
                    $bg_color = get_option('container_bg_color');
                    $alpha = floatval(get_option('container_bg_alpha')) ?: 0.8;
                ?>
                <div id="container_preview" style="
                width: 300px;
                height: 60px;
                background-color: <?php echo hex_to_rgba($bg_color, $alpha); ?>;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                border-radius: 4px;
                border: 1px solid #ccc;
                margin-top: 0.5rem;
                ">
                    Preview
            </tr>


            <tr>
                <th scope="row"><label for="text_color">Text Color</label></th>
                <td>
                    <input type="text" id="text_color" name="text_color" value="<?php echo esc_attr(get_option('text_color')); ?>" class="color-picker" data-alpha="true" />
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>

<?php
    function hex_to_rgba($hex, $alpha = 0.8) {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat($hex[0], 2));
            $g = hexdec(str_repeat($hex[1], 2));
            $b = hexdec(str_repeat($hex[2], 2));
        } elseif (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            return 'rgba(0,0,0,' . $alpha . ')';
        }

        return "rgba($r, $g, $b, $alpha)";
    }
?>