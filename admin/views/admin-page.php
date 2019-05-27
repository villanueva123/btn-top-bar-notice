<div id="elite-order-topbar-admin" class="wrap">
    <h1>
        <i class="btn-logo"></i>
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
