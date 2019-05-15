<?php
/**
  * Option Tabs
  * @param string css_id
  * @param string css_class
  * @param string active_tab
  * @param string key
  * @param array lis{
  *  array{
  *   @param string slug
  *   @param string label
  *   @param string icon
  *  }
  */
?>
<script type="text/html" id="tmpl-btn_opt_tabs">
  <# var css_id = ( data.css_id ) ? ' id="'+data.css_id+'"' : '',
      css_class = ( data.css_class ) ? data.css_class : '',
      key = ( data.key ) ? data.key + '_' : ''; #>
  <ul{{css_id}} class="btn_data_tabs {{css_class}}" style="display:none;">
    <# _.each( data.lis, function( li ) { #>
      <# var active = ( li.slug === data.active_tab ) ? ' active' : ''; #>
    <li class="{{li.slug}}_options {{li.slug}}_tab{{active}}">
      <a style="display:none" href="#{{key}}{{li.slug}}_options_data">
      </a>
    </li>
    <# } ) #>
  </ul>
</script>

<?php
/**
  * Option Panel
  * @param string slug
  * @param string label
  * @param string icon
  * @param string active_tab
  * @param string key
  * @param string content
  */
?>
<script type="text/html" id="tmpl-btn_opt_panel">
  <# var active = ( data.slug === data.active_tab ) ? ' active' : '',
     key = ( data.key ) ? data.key + '_' : '';#>
  <div id="{{key}}{{data.slug}}_options_data" class="panel btn_option_panel{{active}}">
    <legend class="{{data.slug}}_legend">
      <# if( data.icon > '' ){ #>
      <i class="{{data.icon}}"></i>
      <# } #>
      {{data.label}}
    </legend>
    <# if( data.content ){ #>
      {{{data.content}}}
    <# } #>
  </div>
</script>

<?php
/**
  * Admin Notice
  * @param null|string title
  * @param string type notice notice-error | warning | success | info
  * @param string id
  * @param string content
  * @param string dismissable
  *
  */
?>
<script type="text/html" id="tmpl-btn_admin_notice">
  <# var title = ( data.title ) ? data.title : data.type,
    dismissable = ( data.dismissable > '' ) ? ' is-dismissible' : '';
   #>
  <div id="{{data.id}}" class="btn-admin-notice-wrap">
    <div class="notice notice-{{data.type}} {{data.type}} btn-admin-notice{{dismissable}}">
      <p>
        <strong class="btn-notice-title">{{title}}: </strong>
        {{{ data.content }}}
      </p>
      <# if(dismissable > ''){ #>
      <button type="button" class="notice-dismiss">
		<span class="screen-reader-text">{{data.dismissable}}</span>
      </button>
    <# } #>
    </div>
  </div>
</script>

<?php
/**
  * BTN Section
  * @param string slug
  * @param string desc
  * @param string content
  *
**/
?>
<script type="text/html" id="tmpl-btn_section">
  <section id="{{data.slug}}" class="btn_section">
    <h4>{{data.title}}</h4>
    <# if(data.desc > ''){ #>
      <p class="description">{{{data.desc}}}</p>
    <# } #>
    <# var content = ( data.content ) ? data.content : ''; #>
    <table class="form-table">
      <tbody class="btn_section_content">
        {{{content}}}
      </tbody>
    </table>
  </section>
</script>

<?php
/**
  * BTN Table Row
  * @param string label
  * @param string tooltip
  * @param string tooltip_title
  * @param int help_id
  * @param string content
  * @param string tooltip_img
  * @param string doc_url
  *
**/
?>
<script type="text/html" id="tmpl-btn_table_row_ui">
  <tr class="btn_setting_label">
    <th scope="row">
      {{data.label}}
      <# if( data.tooltip || data.tooltip_title ){ #>
      <div class="btn-tooltip">&nbsp;
        <# if( data.help_id ){ #>
          <a href="{{data.doc_url}}?p={{data.help_id}}" target="_blank">
        <# } #>
          <i class="dashicons dashicons-info"></i>
        <# if( data.help_id ){ #>
          </a>
        <# } #>
      <div class="right">
        <# if(data.tooltip_title){ #>
        <h3 style="color:white;">{{{data.tooltip_title}}}</h3>
        <# } #>
        <p>{{{data.tooltip}}}</p>
      </div>
      <# } #>
    </th>
    <td>
      <label style="valign:top;">
        {{{data.content}}}
      </label>
      <# if(data.desc){ #>
        <p class="description">{{{data.desc}}}</p>
      <# } #>
    </td>
  </tr>
</script>

<?php
/**
  * BTN Tabbed Panels
  * @param string tabs
  * @param string panels
  * @param string buttons
  *
**/
?>
<script type="text/html" id="tmpl-btn_tabbed_panels">
    <div class="btn_admin_table btn_tabbed_table">
        <div class="btn_option_tabs">{{{data.tabs}}}</div>
        <div class="btn_option_panels">{{{data.panels}}}</div>
	</div>
    <# if( data.buttons ){ #>
	<div class="btn_option_buttons">
        {{{data.buttons}}}
	</div>
    <# } #>
</script>

<?php
/**
  * BTN Post Box
  * @param string css_id
  * @param string title
  * @param string content
  * @param string expaned
  *
**/
?>
<script type="text/html" id="tmpl-btn_post_box">
    <# var expaned = (data.expanded && data.expanded === 'open' ) ? true : false,
        closed = (expaned) ? '': ' closed'; #>
    <div id="{{data.css_id}}" class="postbox">
        <button type="button"
            class="handlediv btn_handlediv button-link{{closed}}"
            aria-expanded="{{expaned}}"
            data-target="inside_{{data.css_id}}">
            <span class="screen-reader-text">Toggle panel: {{data.title}}</span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
        <div class="title_bar clearfix">
            <# if( data.title ){ #>
            <h2>{{{data.title}}}</h2>
            <# } #>
        </div>
        <div id="inside_{{data.css_id}}" class="inside{{closed}}">
            {{{data.content}}}
        </div>
    </div>
</script>
