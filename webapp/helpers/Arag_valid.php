<?php

class valid extends valid_Core {

    // {{{ credit_saman_card
    public static function credit_saman_card($number)
    {
        return parent::credit_card($number, 'saman');
    }
    // }}}
    // {{{
    public static function credit_card($number, $type='default') //Its just a wrapper on Kohana's credit_card validation
    {
        if (is_array($type)) {
            $type = $type[0]; //if its array, get the first value of it
        }
        return parent::credit_card($number, $type);
    }
    // }}}
    // {{{ id
    public static function id($id)
    {
        return (bool) preg_match('/^[a-z][a-z0-9_]++$/iD', (string) $id);
    }
    // }}}
    // {{{ filter_pan
    public static function filter_pan($pan)
    {
        return str_replace('-', Null, $pan);
    }
    // }}}
    // {{{ equals
    public static function equals($value, $options) {
        return $value == current($options);
    }
    // }}}

} // End valid
