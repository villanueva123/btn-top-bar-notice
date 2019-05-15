/**
 * Define Variables
 */
var $doc,
 $add_new_form,
 $add_new_btn,
 $inner_add_new,
 $submit,
 $notice,
 $topbars_wrap,
 I18n,
 btn_toggles,
 panel_settings = {},
 ctb_tmpls = {},
 init_tmpls = {},
 ctb_data = {},
 ctb_topbars = {},
 ctb_index = false,
 ctb_next_id,
 ctb_tabs,
 ctb_tab = 'main',
 ctb_form,
 ctb_add_new = {
     label : 'Add New',
     attrs : [
         { prop : 'class', value : 'add-topbar button button-primary' },
     ]
 },
 ctb_delete = {
     label : 'Delete',
     attrs : [
         { prop : 'class', value : 'delete-topbar button button-delete' },
     ]
 },
 ctb_save = {
     label : 'Save',
     attrs : [
         { prop : 'class', value : 'save-topbar button button-primary' },
     ]
 };

(function($) {
 'use strict';

 /**
   * Window Loaded
   */
 window.addEventListener('load', function () {

   //Bail if btn_profile_data is undefined
   if( (typeof elite_topbars_data === 'undefined') ){
     return false;
   }

   $doc = document;
   ctb_data = ( window.elite_topbars_data ) ? window.elite_topbars_data : {};
   ctb_tabs = ctb_data.settings_tabs;
   ctb_form = ctb_data.form_config;
   ctb_next_id = ctb_data.next_id;
   I18n = ctb_data.I18n;

   /*
   console.log({
     elite_topbars_data:elite_topbars_data,
   });
   */
   init();

   btn_init_toggles();

 }, false);

 /**
   * Init
   */
 function init(){
   $notice = $doc.getElementById('btn-notice');
   $add_new_form = $doc.getElementById('new_topbar_form');
   $inner_add_new = $add_new_form.querySelector('.btn_postbox_wrap .inside');
   $topbars_wrap = $doc.getElementById('ep-topbars-wrap');

   //Setting Templates
   ctb_tmpls.button = wp.template('btn_button');
   ctb_tmpls.switch = wp.template('btn_switch');
   ctb_tmpls.input = wp.template('btn_input');
   ctb_tmpls.select = wp.template('btn_select');
   ctb_tmpls.textarea = wp.template('btn_textarea');
   ctb_tmpls.colorpicker = wp.template('btn_colorpicker');
   ctb_tmpls.datepicker = wp.template('btn_datepicker');
   //Element Templates
   ctb_tmpls.tabs = wp.template('btn_opt_tabs');
   ctb_tmpls.panel = wp.template('btn_opt_panel');
   ctb_tmpls.section = wp.template('btn_section');
   ctb_tmpls.row = wp.template('btn_table_row_ui');
   ctb_tmpls.post_box = wp.template('btn_post_box');
   ctb_tmpls.tabbed_panels = wp.template('btn_tabbed_panels');

   //Notice Template
   ctb_tmpls.notice = wp.template('btn_admin_notice');

   //Existing topbars
   _.each( ctb_data.topbars, function( topbar, p ) {
       //Add Postbox Form to List
       add_postbox_form( topbar, p );
   });

   //Add New
   populate_new_form();
}

/**
  * Populate / Reset Add New Form
  */
function populate_new_form(){
    $inner_add_new.innerHTML = '';
    var new_form_data = JSON.parse(JSON.stringify( ctb_form ));
    new_form_data[1].value = ctb_next_id;
    ctb_add_form( $inner_add_new, new_form_data, 'add_new_topbar', '', 'open', [ ctb_add_new ] );
    $add_new_btn = $inner_add_new.querySelector('.add-topbar');

    /**
      * Save Settings
      */
    $add_new_btn.onclick = function(e){
      e.preventDefault();
      var get_form_data = generate_form_data( $add_new_form );
      if( get_form_data.error ){
          console.log( { error:'add_new_topbar error' } );
      }
      else {
          var ajax_data = get_form_data.data;
          ajax_data.action = 'add_new_topbar';
          ctb_ajax('POST', ajax_data, ctb_settings_callback );
      }
      return false;
   }
}

/**
  * Generate Form Data
  */
function generate_form_data( $form ){
    var error = {},
    data = {};
    _.each( ctb_form, function( el ) {
        if( el.slug ){
            var $field = $form.querySelector('[name="'+el.slug+'"]');
            if( $field != null ){
                var sanitize = ( el.sanitive ) ? el.sanitive : false,
                validate = ( el.validate ) ? el.validate : false,
                type = el.type,
                value = $field.value;
                switch (type) {
                    case 'switch':
                      data[$field.name] = ( $field.checked ) ? 1 : 0;
                        break;
                    default:
                      data[$field.name] = $field.value;
                      break;
                }
            }
        }
     });
     return {
         error : ( !Object.keys(error).length ) ? false : error,
         data : data
     };
}


/**
  * Add Postbox Form to list
  */
 function add_postbox_form( data, index ){
     var id = 'topbar-'+index,
     buttons = [ ctb_delete, ctb_save ],
     form_data = JSON.parse(JSON.stringify( ctb_form )),
     title = generate_topbar_title(data);
     _.each( ctb_form, function( el, i ) {
         var slug = el.slug;
         if (typeof data[slug] != 'undefined') {
             var value = data[slug];
             if(el.type==='switch'){
                 form_data[i].checked = parseInt(value);
             }
             else {
                 form_data[i].value = data[slug];
             }
         }
     });
     ctb_add_form( $topbars_wrap, form_data, id, title, 'closed', buttons );
     var $postbox = $doc.getElementById(id),
      $btn = $postbox.querySelector('button.btn_handlediv');

     ctb_topbars[id] = {
         topbar    : data,
         $postbox : $doc.getElementById(id),
         $btn     : $postbox.querySelector('button.btn_handlediv'),
         $inside  : $doc.getElementById($btn.getAttribute('data-target')),
         $save    : $postbox.querySelector('.btn_option_buttons .save-topbar'),
         $delete  : $postbox.querySelector('.btn_option_buttons .delete-topbar')
     };

     ctb_topbars[id].$btn.onclick = function(e){
         var closed = has_class( ctb_topbars[id].$btn, 'closed');
         if( closed ){
             ctb_topbars[id].$btn.setAttribute('aria-expanded', true);
             ctb_topbars[id].$btn.classList.remove('closed');
             ctb_topbars[id].$inside.setAttribute('aria-expanded', true);
             ctb_topbars[id].$inside.classList.remove('closed');
         }
         else{
             ctb_topbars[id].$btn.setAttribute('aria-expanded', false);
             ctb_topbars[id].$btn.classList.add('closed');
             ctb_topbars[id].$inside.setAttribute('aria-expanded', false);
             ctb_topbars[id].$inside.classList.add('closed');
         }
     };

     ctb_topbars[id].$save.onclick = function(e){
         e.preventDefault();
         //todo loop through ctb_topbars[id] validate
         var get_form_data = generate_form_data( ctb_topbars[id].$inside );
         if( get_form_data.error ){
             console.log( { error:'edit_form_data error' } );
         }
         else {
             var ajax_data = get_form_data.data;
             ajax_data.action = 'edit_topbar';
             ajax_data.css_id = id;
             /*
             console.log({
                 func:'edit_form_data',
                 ajax_data:ajax_data
             });
             */
             ctb_ajax('POST', ajax_data, ctb_settings_callback );
         }
     }

     ctb_topbars[id].$delete.onclick = function(e){
         e.preventDefault();
         var confirmation = confirm('Are you sure you want to delete topbar : '+ data.topbar_code +'?');
         if (!confirmation) {
             e.stopPropagation();
         }
         else {
             ctb_ajax('POST', {
                 action     : 'delete_topbar',
                 id         : ctb_topbars[id].topbar.id,
                 topbar_code : ctb_topbars[id].topbar.topbar_code,
                 css_id     : id
             }, ctb_settings_callback );
         }
     }
     ctb_index = index;
 }

 /**
   * Populate Tabbed Post Box
   */
 function ctb_add_form( $el, form_data, key, title, expanded, buttons ){
     var tabs = ctb_tmpls.tabs( {
         lis         : ctb_tabs,
         active_tab  : 'main',
         key         : key
     } ),
     form_buttons = ( buttons ) ? generate_button_row(buttons) : '',
     tabbed_content = ctb_tmpls.tabbed_panels( {
        tabs    : tabs,
        panels  : generate_panels( key, ctb_tabs, form_data ),
        buttons : form_buttons
     } );
     //beforeend
     $el.insertAdjacentHTML('afterbegin', ctb_tmpls.post_box( {
         css_id : key,
         title  : title,
         content : tabbed_content,
         expanded : expanded
     } ) );
     var $postbox = $doc.getElementById(key);
     activate_panel( $postbox, key, ctb_tabs );
 }

 /**
   * Generate Panel HTML
   */
 function generate_panels( key, tabs, form_data ){
     var return_html = '';
     panel_settings[key] = {};
     _.each( tabs, function( tab, t ) {
         var data = panel_data(tab, form_data),
         panel_id = '#'+key+'_'+tab.slug+'_options_data';
         data.key = key;
         return_html += ctb_tmpls.panel( data );
         panel_settings[key][t] = {
             id : panel_id,
             $btn : null,
             $panel : null
         }
      });
      return return_html;
 }


  /**
    * Generate Button Row
    */
 function generate_button_row(buttons){
     /*
     console.log({
         buttons:buttons
     });
     */
     var return_html = '';
     _.each( buttons, function( button, b ) {
         return_html += settings_tmpl({
             type: 'button',
             settings: button
         });
     });
     return return_html;
 }

 /**
   * Activate Panels
   */
 function activate_panel( $el, key, tabs ){
     _.each( tabs, function( tab, t ) {
         var panel_id = panel_settings[key][t].id;
         panel_settings[key][t].$btn = $el.querySelector('a[href="'+panel_id+'"]');
         panel_settings[key][t].$panel = $el.querySelector(panel_id);
         // Toggle Panels
         panel_settings[key][t].$btn.onclick = function(e){
           e.preventDefault();
           var $li = panel_settings[key][t].$btn.parentNode;
           if( !has_class( $li, 'active') ){
               var actives = $el.querySelectorAll('.active');
               _.each( actives, function( $ael ) {
                   $ael.classList.remove('active');
               });
             $li.classList.add('active');
             panel_settings[key][t].$panel.classList.add('active');
           }
         }
         //TODO - Activate Special Settings
     });

     //Initialize Functional Settings
     _.each( init_tmpls, function( tmpl, id ) {
         var $form_el = $el.querySelector('#'+id);
         switch (tmpl) {
             case 'multi_select':
             case 'select':
                var data_slug = $form_el.getAttribute('data-choices'),
                data = ( ctb_data[data_slug] ) ? ctb_data[data_slug] : {};
                init_select( $form_el, data );
                break;
            default:
              break;
          }
    });

    //Flush TMPLS init
    init_tmpls = {};
 }

 /**
   * Save Settings Callback
   */
 function ctb_settings_callback(response){

   /*
   console.log({
     func:'ctb_settings_callback',
     response:response
   });
   */

   var data = response.data,
    action = ( data.action ) ? data.action : false,
    is_error = ( data.error ) ? data.error : false;
   if( data.notice ){
     $notice.innerHTML = ctb_tmpls.notice(data.notice);
     $notice.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
     if(data.notice.dismissable){
         $notice.querySelector('.notice-dismiss').onclick = function(e) {
            $notice.innerHTML = '';
         }
     }
   }
   //TODO Check for close or toggle function
   if( action ){
       var css_id, $postbox;
       switch (action) {
           case 'add_new_topbar':
                if( is_error ){
                    //todo display errors
                    display_errors(is_error);
                }
                else {
                    ctb_index = ( !ctb_index ) ? 0 : ctb_index++;
                    ctb_next_id = data.next_id;
                    //trigger dismiss
                    var $new_topbar = $doc.getElementById('new-topbar');
                    $new_topbar.click();
                    //Add New topbar
                    add_postbox_form( data.topbar, ctb_next_id );
                    //Reset Add New Form
                    populate_new_form();
                }
               break;
              case 'delete_topbar':
                  if( !is_error ){
                      css_id = data.css_id;
                      $postbox = ctb_topbars[css_id].$postbox;
                      $postbox.parentNode.removeChild($postbox);
                      delete ctb_topbars[css_id];
                  }
               break;
               case 'edit_topbar':
                    css_id = data.css_id;
                    $postbox = ctb_topbars[css_id].$postbox;
                    if( is_error ){
                        display_errors(is_error);
                    }
                    else {
                        //Update topbar Array
                        ctb_topbars[css_id].topbar = data.topbar;
                            _.each( data.topbar, function( value, slug ) {
                                var $field = $postbox.querySelector('[name="'+slug+'"]');
                                if($field.value != value){
                                    $field.value = value;
                                }
                            });
                        var title = generate_topbar_title( ctb_topbars[css_id].topbar );
                        $postbox.querySelector('.title_bar h2').innerHTML = title;
                    }
               break;
           default:

       }
   }
 }

 /**
   * Update topbar Title
   */
 function generate_topbar_title( data ){
     var title = I18n.topbar_code_title + ' ' + data.tag_id;

    var data_active = ( typeof data.active === 'undefined') ? 1 : parseInt(data.active),
    status = ( data_active > 0 ) ? 'yes' : 'no';
    title += '<span class="status dashicons dashicons-'+status+'"></span>';
    return title;
 }

 /**
   * Display Errors on Form
   */
 function display_errors(errors){
     console.log({
         func:'display_errors',
         errors:errors
     });
 }

 /**
   * Generate Settings For Tab
   */
  function panel_data( data, form_data ){
    data.active_tab = ctb_tab;
    data.content = '';
    data.sections = panel_sections( data.slug, form_data );
    if( data.sections ){
      _.each( data.sections, function( section ) {
        if( section.settings ){
          section.content = '';
          _.each( section.settings, function( settings ) {
              section.content += settings_row(settings);
          });
          data.content += ctb_tmpls.section(section);
        }
      });
    }
    return data;
  }

  //Filter Sections For Tab
  function panel_sections( slug, form_data ){
    var sections = {};
    //Filter Settings for tab
    sections = _.filter(form_data, function (setting, s) {
      return ( setting.tab === slug && setting.type === 'section'  ) ? setting : null;
    });
    //Filter Settings For Each Section
    if( sections ){
      var i = 0;
      _.each( sections, function( section, s ) {
        var settings = {};
        sections[s].settings = _.filter(form_data, function (setting, s) {
          return ( setting.tab === slug && setting.section === section.slug ) ? settings[i++] = setting : null;
        });
      });
    }
    return sections;
  }

  /**
    * Generate Settings For Templates
   */
  function settings_row(setting){
      var type = setting.type,
      slug = setting.slug,
      key = setting.key ? '_'+setting.key : '',
      css_id = slug + key,
      default_val = ( typeof setting.default !== 'undefined' ) ? setting.default : '';
      setting.settings = {}
      switch (type) {
          case 'switch':
            if (typeof setting.checked === 'undefined') {
                setting.checked = default_val;
            }
            setting.settings.css_id = slug;
            setting.settings.css_class = '';
            setting.settings.name = slug;
            setting.settings.checked = ( parseInt(setting.checked) > 0 ) ? 1 : 0;
            setting.settings.on = ( setting.on ) ? setting.on : 'On';
            setting.settings.off = ( setting.off ) ? setting.off : 'Off';
        break;
        case 'input':
        case 'number':
        case 'hidden':
            setting.settings.id = 0;
            setting.settings.inputAttr = [
                { name: 'id', value : slug },
                { name: 'name', value : slug },
                { name: 'value', value : ( setting.value ) ? setting.value : '' },
                { name: 'class', value : ( setting.class ) ? setting.class : 'widefat' }
            ];
            var input_type = 'text';
            if( type === 'number' ){
                input_type = type;
                if( setting.min !== null ){
                  setting.settings.inputAttr.push( {  name: 'min', value : setting.min } );
                }
                if( setting.max ){
                  setting.settings.inputAttr.push( {  name: 'max', value : setting.max } );
                }
            }
            else if( type === 'hidden' ){
                input_type = type;
            }
            setting.settings.inputAttr.push( {  name: 'type', value : input_type } );
         break;
         case 'colorpicker':
              $('.colorpicker').wpColorPicker();
             setting.settings.id = 0;
             setting.settings.inputAttr = [
                 { name: 'id', value : slug },
                 { name: 'name', value : slug },
                 { name: 'value', value : ( setting.value ) ? setting.value : '' },
                 { name: 'class', value : ( setting.class ) ? setting.class : 'colorpicker' }
             ];
             setting.settings.inputAttr.push( {  name: 'type', value : input_type } );
          break;
          case 'datepicker':

              setting.settings.id = 0;
              setting.settings.inputAttr = [
                  { name: 'id', value : slug },
                  { name: 'name', value : slug },
                  { name: 'type', value : 'date' },
                  { name: 'value', value : ( setting.value ) ? setting.value : '' },
                  { name: 'class', value : ( setting.class ) ? setting.class : 'datepicker' }
              ];

           break;
      case 'editor-html':
      case 'editor-css':
      case 'editor-js':
      case 'textarea':
        setting.settings.css_id = slug;
        setting.settings.css_class = 'btn-code-editor';
        setting.settings.name = slug;
        setting.settings.content = setting.value;
        init_tmpls[slug] = type;
        break;
        case 'multi_select':
        case 'select':
            var default_val = ( setting.default ) ? setting.default : '';
            setting.settings.inputAttr = [
              { name: 'id', value : slug },
              { name: 'type', value : 'text' },
              { name: 'name', value : slug },
              { name: 'value', value : ( setting.value ) ? setting.value : default_val },
              { name: 'data-choices', value : setting.choices },
            ];
            if( type === 'multi_select' ){
                setting.settings.inputAttr.push({
                    name: 'data-multiple', value : 'true'
                });
            }
            if( setting.search && setting.search === 'no' ){
                setting.settings.inputAttr.push({
                    name: 'data-disable-search', value : 'true'
                });
            }
        break;
      default:
    }
    if( type === 'hidden' ){
        return settings_tmpl(setting);
    }
    else{
        var tooltip = (setting.tooltip) ? setting.tooltip : false,
        tooltip_title = (setting.tooltip_title) ? setting.tooltip_title : false;
        tooltip_title = ( tooltip && !tooltip_title ) ? setting.title : tooltip_title;
        return ctb_tmpls.row({
          label:setting.title,
          tooltip:tooltip,
          tooltip_title:tooltip_title,
          help_id:(setting.help_id) ? setting.help_id : 0,
          content:settings_tmpl(setting),
          desc:(setting.desc) ? setting.desc : ''
        });
    }
  }

  /*
  document.addEventListener('keyup', function (event) {
      if (event.defaultPrevented) {
          return;
      }
      var key = event.key || event.keyCode;
      if (key === 'Escape' || key === 'Esc' || key === 27) {

      }
  });
  */


  /**
    * Generate Settings From Templates
   */
  function settings_tmpl( config ){
    /*
    console.log({
      func:'settings_tmpl',
      config:config
    });
    */
    var html = '';
    if( config ){
      switch (config.type) {
        case 'text':
          html += config.text;
        break;
        case 'select':
        case 'multi_select':
          html += ctb_tmpls.input( config.settings );
          init_tmpls[config.slug] = config.type;
        break;
        case 'input':
        case 'number':
        case 'hidden':
         html += ctb_tmpls.input( config.settings );
         break;
        case 'switch':
        case 'checkbox':
         html += ctb_tmpls.switch( config.settings );
         break;
        case 'button':
         html += ctb_tmpls.button( config.settings );
         break;
        case 'textarea':
        case 'editor-html':
        case 'editor-css':
        case 'editor-js':
         html += ctb_tmpls.textarea( config.settings );
         break;
        case 'button':
          html += ctb_tmpls.button( config.settings );
          break;
        case 'datepicker':
            html += ctb_tmpls.datepicker( config.settings );
            break;
        case 'colorpicker':
            html += ctb_tmpls.colorpicker( config.settings );
            break;
        default:
          console.log({
            todo : 'add tmpl ' + config.type
          });
          //TODO - check if template exists else try to create it?
      }
    }
    return html;
  }

 /**
   * Has Class
  */
  function has_class(element, css_class) {
    return (' ' + element.className + ' ').indexOf(' ' + css_class + ' ') > -1;
  }

  /**
    * WP Ajax Call
   */
  function ctb_ajax(methodType, data, callback) {
    //Prepare Data
    if (typeof data === 'string' || data instanceof String){
      var action = data;
      data = { action : action };
    }
    var postData = Object.keys(data).map(function(key) {
        return key + '=' + data[key]
    }).join('&');
    var request = new XMLHttpRequest();
    request.open(methodType, ctb_data.ajax_url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function () {
      var response = JSON.parse(request.responseText),
      status = request.status, DONE = 4,OK = 200;
      //success
      if (status >= 200 && status < 300) {
        callback(response);
      }
      else {
        console.log({
          func:'ctb_ajax',
          status:'error',
          request:request
        });
      }
    }
    request.send(postData);
  }

  /**
    * Init Select 2 type Functionality
   */
  function init_select( $el, data ){
      var args = {
          data:data
      };
      if( ( $el.getAttribute('data-disable-search') ) ){
          args.minimumResultsForSearch = -1;
      }
    $($el).selectWoo(args);
  }

  /**
    * Toggles
    */
  function btn_init_toggles(){
      btn_toggles = $doc.querySelectorAll('[data-btn-toggle]');
      if( btn_toggles ){
          _.each( btn_toggles, function( $toggle, t ) {
 			$toggle.onclick = function(e) {
                 var els_data = $toggle.getAttribute('data-btn-toggle'),
                 els = $doc.querySelectorAll(els_data),
                 enabled = $toggle.getAttribute('data-enabled-text'),
                 disabled = $toggle.getAttribute('data-disabled-text'),
                 aria = $toggle.getAttribute('aria-expanded'),
                 action = ( aria === 'false' ) ? 'show' : 'hide';
                 $toggle.setAttribute('aria-expanded', ( aria === 'false' ) ? 'true' : 'false' );
                 $toggle.innerHTML = ( aria === 'false' ) ? enabled : disabled;
                 _.each( els, function( $el ) {
                     if( action === 'show' ){
                         $el.classList.remove('hidden');
                     }
                     else {
                         $el.classList.add('hidden');
                     }
                 });
 			};
 		});
 	}
  }


})(jQuery);
