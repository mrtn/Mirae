<div class="wrap">
  <h1 class="wp-heading-inline">Mirae</h1>

  <h2>Add new link</h2>

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
      <td><input type="text" id="buttonText" class="regular-text" placeholder="Leave empty to use default" /></td>
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
    <?php
      settings_fields('miraeSettings');
      do_settings_sections('miraeSettings');
    ?>
    <p><button type="submit" id="save" class="button button-primary">Save</button></p>
    <textarea id="userdata" name="userdata" rows="10" style="display: none;"><?php echo esc_textarea(get_option('userdata')); ?></textarea>
  </form>
</div>
