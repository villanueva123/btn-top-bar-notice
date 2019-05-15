<div id="elite-order-topbar-admin" class="wrap">
    <h1>
        <i class="btn-logo" style="background-image:url(<?php echo $this->svg_icon('black', true); ?>);"></i>
        <?php echo esc_html__( 'Custom Top Bar Settings', 'btn-top-bar-notice' ); ?>
        <button id="new-topbar" type="submit"
            data-btn-toggle="#new_topbar_form"
            data-enabled-text="Dismiss"
            data-disabled-text="Add New"
            class="button page-title-action hide-if-no-js"
            aria-expanded="false" >
        <?php echo __('Add New', 'btn-top-bar-notice'); ?>
        </button>
    </h1>
    <?php
    function register_top_bar_settings() {
    		//register our settings
    		register_setting( 'top-bar-settings-group', 'container_class' );
    	}
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'top-bar-settings-group' ); ?>
        <?php do_settings_sections( 'top-bar-settings-group' ); ?>
        <table class="form-table">

            <tr valign="top">
            <th scope="row">Container Class</th>
            <td>
              <input type="text" style="width:600px;" name="container_class" class="form-control" value="<?php echo esc_attr( get_option('container_class') ); ?>" /><br>
              <label><i>Add the class container that will display the alert box like this (.container)</i></label>
            </td>
            </tr>
        </table>
        <?php submit_button(); ?>

    </form>
    <div id="btn-notice"></div>
    <form id="new_topbar_form" method="post" class="add-new-form hidden">
        <div class="wrap btn_postbox_wrap">
          <h3 class="settings-form-title">
              <?php echo __('Add A New TopBar Notice', ''); ?>
          </h3>
          <div class="inside"></div>
        </div>
    </form>

    <div id="ep-topbars-wrap" class="btn_postbox_wrap">
    </div>

</div>
