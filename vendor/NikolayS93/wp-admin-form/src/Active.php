<?php

namespace NikolayS93\WPAdminForm;

class Active
{
    private $active;

    public function set( $active ) {

        $this->active = $active;
    }

    public function get( $args = array() )
    {
        if( ! $this->active )
            $this->active = $this->_active( $args );

        return $this->active;
    }

    private function _active( $args )
    {
        if( !empty($args['postmeta']) ) {
            global $post;

            // do not use instanceof
            if( ! is_a($post, 'WP_Post') ) {
                return false;
            }

            if( !empty($args['sub_name']) ) {
                $active = get_post_meta( $post->ID, $args['sub_name'], true );
            }
            else {
                foreach ($this->fields as $field) {
                    $active[ $field['id'] ] = get_post_meta( $post->ID, $field['id'], true );
                }
            }
        }
        /**
         * @todo check about admin page
         */
        elseif ( !empty($args['admin_page']) ) {
            $active = get_option( $args['admin_page'], array() );

            if( $sub_name = $args['sub_name'] ) {
                $active = isset($active[ $sub_name ]) ? $active[ $sub_name ] : false;
            }
        }

        /** if active not found */
        if( empty($active) ) return false;

        /**
         * @todo: add recursive handle
         */
        $result = array();
        foreach ($active as $key => $value) {
            if( is_array($value) ){
                foreach ($value as $key2 => $value2) {
                    $result[$key . '_' . $key2] = $value2;
                }
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
