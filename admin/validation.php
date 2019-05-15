<?php
/**
 * Validate - Empty
 *
 * @since    1.0.0
 */
function ctb_validate_not_empty( $value ){
    $stripped = preg_replace('/\s+/', ' ', $value);
    return ( $stripped > '' ) ? $stripped : false;
}

/**
 * Is Number
 *
 * @since    1.0.0
 */
function ctb_is_number($value){
    $int = filter_var($value, FILTER_VALIDATE_INT);
    if ($int === 0 || !$int === false) {
        return $int;
    }
    else {
        return false;
    }
}

/**
 * sanitize - Slug
 *
 * @since    1.0.0
 */
function ctb_sanitize_slug( $value ){
    return sanitize_title( $value, '' );
}

/**
 * Sanitize - Multi Select
 *
 * @since    1.0.0
 */
function ctb_sanitize_multi_select( $value ){
    $trimmed = trim($value,',');
    $stripped = preg_replace('/\s+/', ' ', $trimmed);
    return $stripped;
}
