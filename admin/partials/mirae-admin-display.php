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
<h1>Mirae</h1>

<table>
    <tr>
        <td>Version</td>
        <td><?php echo esc_html( MIRAE_VERSION ); ?></td>
    </tr>
    <tr>
        <td>Author</td>
        <td><a href="https://maartenkumpen.com" target="_blank">Maarten Kumpen</a></td>
    </tr>
    <tr>
        <td>License</td>
        <td><a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></td>
    </tr>
</table>

<table class="table w-75">
        <tr>
            <td colspan="2">
                <select id="platform" class="form-select">
                    <option value="">-- Select a platform --</option>
                </select>
            </td>
            <td><input type="url" class="form-control needs-validation" id="url" placeholder="Url"></td>
            <td><input type="text" class="form-control" id="buttonText" placeholder="leave empty for default"></td>
            <td class="text-end"><button id="add" class="btn btn-primary btn-sm">Add</button></td>
        </tr>
</table>

<div class="row align-items-center g-3">
    <div class="col-auto">

    </div>
    <div class="col-auto">
        <label class="visually-hidden" for="url">Url</label>
        
    </div>
    <div class="col-auto">
        <label class="visually-hidden" for="buttonText">Button text (leave empty to use default)</label>
        
    </div>

    <div class="col-auto">
        
    </div>
</div>

<table class="table w-75" id="overview">
    <thead>
        <tr>
            <th data-field="drag" data-formatter="dragFormatter">⇅</th>
            <th data-field="platform">Platform</th>
            <th data-field="link">Url</th>
            <th data-field="buttonText">Button Text</th>
            <th data-field="editRemove">Edit/Delete</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<br />
<form method="post" action="options.php" class="flex-row align-items-center">
    <?php
        settings_fields( 'miraeSettings' );
        do_settings_sections( 'miraeSettings' );
    ?>
    <button type="submit" id="save" class="btn btn-primary">Save</button>
    <br />
    <br />
    <br />
    <textarea id="userdata" rows="20" cols="150" name="userdata"><?php echo get_option('userdata'); ?></textarea>
</form>
