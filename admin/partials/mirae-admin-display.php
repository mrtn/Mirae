<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
  <div class="notice notice-success is-dismissible">
    <p>Settings saved successfully.</p>
  </div>
<?php endif; ?>

<div class="wrap">
  <h1 class="wp-heading-inline">Mirae</h1>

  <p class="description">
    Select a platform, enter your link and (optionally) customize the button text. You can reorder the links by dragging the rows below.
  </p>

  <h2>Add new link</h2>
  
  <div id="error-message" class="notice notice-error is-dismissible" style="display: none;">
    <p>Please fill in all required fields before adding a link.</p>
  </div>

  <table class="form-table">
    <tr>
      <th scope="row"><label for="platform">Platform</label></th>
      <td>
        <select id="platform" class="regular-text">
          <option value="">-- Select a platform --</option>
        </select>
      </td>
    </tr>
    <tr>
      <th scope="row"><label for="url">URL</label></th>
      <td><input type="url" id="url" class="regular-text" placeholder="https://..." /></td>
    </tr>
    <tr>
      <th scope="row"><label for="buttonText">Button text</label></th>
      <td>
        <input type="text" id="buttonText" class="regular-text" placeholder="Leave empty to use default">
        <p class="description">If left empty, the default button text from the platform will be used.</p>
      </td>
    </tr>
    <tr>
      <th scope="row"></th>
      <td><button type="button" id="add" class="button button-primary">Add</button></td>
    </tr>
  </table>

  <h2>Links</h2>
  <table id="overview" class="widefat fixed striped">
    <thead>
      <tr>
        <th>⇅</th>
        <th>Platform</th>
        <th>URL</th>
        <th>Button text</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <form method="post" action="options.php">
    <div id="save-message" class="notice notice-success is-dismissible" style="display:none;">
      <p>Settings saved successfully.</p>
    </div>
    <?php
      settings_fields('mirae_links');
      do_settings_sections('mirae_links');
    ?>
    <p><button type="submit" id="save" class="button button-primary">Save</button></p>
    <textarea id="link_data" name="link_data" rows="10" ><?php echo esc_textarea(get_option('link_data')); ?></textarea>
  </form>
</div>
