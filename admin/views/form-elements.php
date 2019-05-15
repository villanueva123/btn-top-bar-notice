<?php
/**
  * Toggle Switch
  * @param string css_id
  * @param string css_class
  * @param string name
  * @param bol checked
  * @param string on
  * @param string off
  **/
?>
<script type="text/html" id="tmpl-btn_switch">
  <# var data_on = ( data.on ) ? data.on : 'On',
    data_off = ( data.off ) ? data.off : 'Off',
    checked = ( parseInt(data.checked) > 0 ) ? ' checked="checked"' : ''; #>
  <div class="btn_toggle_group">
    <label class="btn_toggle_switch">
        <input id="{{data.css_id}}"
            type="checkbox"
            class="btn_switch_input {{data.css_class}}"
            value="1"
            name="{{data.name}}"
            {{checked}}
        />
      <span class="btn_switch_label" data-on="{{data_on}}" data-off="{{data_off}}"></span>
      <span class="btn_switch_handle"></span>
    </label>
  </div>
</script>

<?php
/**
  * Textarea
  * @param string css_id
  * @param string css_class
  * @param string name
  * @param int row
  * @param string content
  */
?>
<script type="text/html" id="tmpl-btn_textarea">
  <# #>
  <# var row = ( data.row ) ? data.row : 5,
    css_class = ( data.id ) ? ' ' + data.css_class : '',
    content = ( data.content ) ? data.content : '';
  #>
  <textarea id="{{data.css_class}}" style="width:400px;"
    rows="{{row}}"
    name="{{data.name}}"
    class="widefat textarea{{css_class}}">{{{data.content}}}</textarea>
</script>



<?php
/**
  * Select
  * @param array attrs : { array { prop: string, value: string } }
  * @param array choices{
  *  array{
  *   attrs : { prop: string, value: string }
  *   label : string
  *   value : string
  *  }
  * }
  * @param string selected
  **/
?>
<script type="text/html" id="tmpl-btn_select">
  <# //console.log({data:data, tmpl:'btn_select'}); #>

  <# if( data.label ){ #>
    <label>{{{data.label}}}
  <# } #>
  <# var attrs = ''; #>
  <# if( data.attrs ){ #>
    <# _.each( data.attrs, function( attr ) { #>
      <# attrs += ' ' + attr.prop + '="'+attr.value+'"'; #>
    <# } ) #>
  <# } #>
  <select{{{attrs}}}>
    <# _.each( data.choices, function( choice ) { #>
      <# var choiceChecked = (choice.value == data.selected) ? ' selected' : ''; #>
      <# var opt_attrs = ''; #>
      <# if( choice.attrs ){ #>
        <# _.each( choice.attrs, function( attrs ) { #>
          <# attrs += ' ' + attrs.prop + '="' + attrs.value +'"'; #>
        <# } ) #>
      <# } #>
      <option value="{{choice.value}}"{{choiceChecked}}>{{{choice.label}}}</option>
    <# } ) #>
  </select>
  <# if( data.label ){ #>
    </label>
  <# } #>
</script>

<?php
/**
  * Genreic Input
  * @param string wrapEl
  * @param string wrapElClass
  * @param string labelWrapEl
  * @param string labelWrapElClass
  * @param string labelPos
  * @param string label
  * @param string dataAtrr
  * @param array inputAttr = {
  *   name : 'value',
  * }
  **/
?>
<script type="text/html" id="tmpl-btn_input">
  <# //console.log({data:data, tmpl:'btn_input'}); #>
  <# if( data.wrapEl ){ #>
    <{{data.wrapEl}}<# if( data.wrapElClass ){ #> class="{{data.wrapElClass}}"<# } #>>
  <# } #>

  <# if( data.labelPos && data.labelPos === 'after' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.labelWrapEl ){ #>
    <{{data.labelWrapEl}} class="label_text<# if( data.labelWrapElClass ){ #> {{data.labelWrapElClass}}<# } #>">
  <# } #>
    <# if( data.label ){ #>
    {{data.label}}
    <# } #>
  <# if( data.labelWrapEl ){ #>
    </{{data.labelWrapEl}}>
  <# } #>

  <# if( !data.labelPos || data.labelPos === 'before' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.wrapEl ){ #>
  </{{data.wrapEl}}>
  <# } #>
</script>

<?php
/**
  * Form Button
  * @param string el
  * @param string label
  * @param array attrs = {
  *   name : 'value',
  * }
  **/
?>
<script type="text/html" id="tmpl-btn_button">
  <# //console.log({data:data, tmpl:'btn_button'}); #>
  <# var el = (data.el) ? data.el : 'button'; #>
  <# var attrs = ''; #>
  <# if( data.attrs ){ #>
    <# _.each( data.attrs, function( attr ) { #>
      <# attrs += ' ' + attr.prop + '="'+attr.value+'"'; #>
    <# } ) #>
  <# } #>
  <{{{el}}}{{{attrs}}}>{{{data.label}}}</{{{el}}}>
</script>


<?php
/**
  * Textarea
  * @param string css_id
  * @param string css_class
  * @param string name
  * @param int row
  * @param string content
  */
?>

<script type="text/html" id="tmpl-btn_colorpicker">
  <# //console.log({data:data, tmpl:'btn_input'}); #>
  <# if( data.wrapEl ){ #>
    <{{data.wrapEl}}<# if( data.wrapElClass ){ #> class="{{data.wrapElClass}}"<# } #>>
  <# } #>

  <# if( data.labelPos && data.labelPos === 'after' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.labelWrapEl ){ #>
    <{{data.labelWrapEl}} class="label_text<# if( data.labelWrapElClass ){ #> {{data.labelWrapElClass}}<# } #>">
  <# } #>
    <# if( data.label ){ #>
    {{data.label}}
    <# } #>
  <# if( data.labelWrapEl ){ #>
    </{{data.labelWrapEl}}>
  <# } #>

  <# if( !data.labelPos || data.labelPos === 'before' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.wrapEl ){ #>
  </{{data.wrapEl}}>
  <# } #>

</script>
<script type="text/html" id="tmpl-btn_datepicker">

    <# //console.log({data:data, tmpl:'btn_input'}); #>
  <# if( data.wrapEl ){ #>
    <{{data.wrapEl}}<# if( data.wrapElClass ){ #> class="{{data.wrapElClass}}"<# } #>>
  <# } #>

  <# if( data.labelPos && data.labelPos === 'after' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.labelWrapEl ){ #>
    <{{data.labelWrapEl}} class="label_text<# if( data.labelWrapElClass ){ #> {{data.labelWrapElClass}}<# } #>">
  <# } #>
    <# if( data.label ){ #>
    {{data.label}}
    <# } #>
  <# if( data.labelWrapEl ){ #>
    </{{data.labelWrapEl}}>
  <# } #>

  <# if( !data.labelPos || data.labelPos === 'before' ){ #>
    <# if( data.inputAttr ){ #>
      <input
      <# _.each( data.inputAttr, function( attr ) { #>
        {{attr.name}}="{{attr.value}}"
        <# } ) #>
      />
    <# } #>
  <# } #>

  <# if( data.wrapEl ){ #>
  </{{data.wrapEl}}>
  <# } #>

</script>
