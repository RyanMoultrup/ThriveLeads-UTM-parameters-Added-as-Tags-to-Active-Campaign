<div class="wrap">
  <h1>ThriveLeads UTM Parameters Added as Tags to Active Campaign</h1>

	<hr />

  <form method="post" action="options.php">
    <?php settings_fields('tvac_utm_settings_group'); ?>
    <?php do_settings_sections('thrive-active-campaign-utm'); ?>
    <?php submit_button(); ?>
  </form>
</div>
