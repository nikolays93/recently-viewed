<?php

namespace NikolayS93\WPAdminForm;

class Form extends Active
{
    protected $fields = array(),
              $args;

    protected static $hiddens = array();

    public function add($data = null)
    {
        if( !is_array($data) ) $data = array();
        if( isset($data['id']) || isset($data['name']) ) $data = array($data);

        foreach ($data as $field) {
            $this->fields[ $field['id'] ] = $field;
        }
    }

    public function del($id)
    {
        if( isset( $this->fields[ $id ] ) ) {
            unset($this->fields[ $id ]);
            return true;
        }

        return false;
    }

    public function __construct($data = null, $args = array())
    {
        if( !is_array($args) ) $args = array();

        $args = Preset::parse_args($args);
        if( $args['admin_page'] || $args['sub_name'] ) {
            foreach ($data as &$field) {
                if ( ! isset($field['id']) && ! isset($field['name']) )
                    continue;

                // if( $args['admin_page'] ) {
                $field_name = isset($field['name']) ? $field['name'] : $field['id'];
                $field['name'] = $args['sub_name'] ?
                    sprintf('%s[%s][%s]', $args['admin_page'], $args['sub_name'], $field_name) :
                    sprintf('%s[%s]', $args['admin_page'], $field_name);

                if( !isset($field['check_active']) ) $field['check_active'] = 'id';
                // }
            }
        }

        $this->add($data);
        $this->args = $args;
    }

    final public function display()
    {
        $arrActive = $this->get( $this->args );

        $html = $this->args['form_wrap'][0];

        foreach ($this->fields as $field) {
            /**
             * If is field has id or name
             */
            if ( ! isset($field['id']) && ! isset($field['name']) ) continue;

            /**
             * Check the fieldname and Get active value
             */
            if( !empty($field['check_active']) ) {
                /**
                 * if is key field set manually
                 */
                $active_key = $field[ $field['check_active'] ];
            }
            else {
                $active_key = isset($field['name']) ? $field['name'] : $field['id'];
            }

            /**
             * If is name for array
             */
            $active_key = str_replace('[]', '', $active_key);

            $active_value = isset( $arrActive[ $active_key ] ) ? $arrActive[ $active_key ] : false;

            // &$field
            $input = new Input( $field, $active_value, $this->args );
            $html .= new Field( $field, $input, $this->args );
        }
        $html .= $this->args['form_wrap'][1];

        $result = $html . "\n" . implode("\n", self::$hiddens);
        self::$hiddens = array();

        echo $result;
    }
}
