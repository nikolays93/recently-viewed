<?php

namespace NikolayS93\WPAdminForm;

if ( ! empty( $field['options'] ) ) {
    if ( '' === current($field['options']) ) {
        if ( empty($attrs['placeholder']) ) {
            $attrs['placeholder'] = $text ? $text : __( 'Choose an option' );
        }
    }

    if( !empty($attrs['class']) ) $attrs['class'].= ' ';
    $attrs['class'].= 'input-select';

    $input .= $label[0];
    $input .= '<select ' . Util::get_attributes_text( $attrs ) . '>';
    $input .= Util::get_select_options($field['options'], $entry);
    $input .= '</select>';
    $input .= $label[1];
}
