<?php

// Abstract of each filter classes
/**
 * Class ProtectorFilterAbstract
 */
class ProtectorFilterAbstract
{
    var $protector = null ;

    function ProtectorFilterAbstract()
    {
        $this->protector =& Protector::getInstance() ;
        $lang = empty( $GLOBALS['xoopsConfig']['language'] ) ? @$this->protector->_conf['default_lang'] : $GLOBALS['xoopsConfig']['language'] ;
        @include_once dirname(dirname(__FILE__)).'/language/'.$lang.'/main.php' ;
        if ( ! defined( '_MD_PROTECTOR_YOUAREBADIP' ) ) {
            include_once dirname(dirname(__FILE__)).'/language/english/main.php' ;
        }
    }

    /**
     * @return bool
     */
    function isMobile()
    {
        if ( class_exists( 'Wizin_User' ) ) {
            // WizMobile (gusagi)
            $user =& Wizin_User::getSingleton();

            return $user->bIsMobile ;
        } elseif ( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER ) {
            // hyp_common ktai-renderer (nao-pon)
            return true ;
        } else {
            return false ;
        }
    }
}

// Filter Handler class (singleton)
/**
 * Class ProtectorFilterHandler
 */
class ProtectorFilterHandler
{
    var $protector = null ;
    var $filters_base = '' ;

    function ProtectorFilterHandler()
    {
        $this->protector =& Protector::getInstance() ;
        $this->filters_base = dirname(dirname(__FILE__)).'/filters_enabled' ;
    }

    /**
     * @return ProtectorFilterHandler
     */
    static function &getInstance()
    {
        static $instance ;
        if ( ! isset( $instance ) ) {
            $instance = new ProtectorFilterHandler() ;
        }

        return $instance ;
    }

    // return: false : execute default action
    /**
     * @param $type
     *
     * @return int|mixed
     */
    function execute( $type )
    {
        $ret = 0 ;

        $dh = opendir( $this->filters_base ) ;
        while ( ( $file = readdir( $dh ) ) !== false ) {
            if ( strncmp( $file , $type.'_' , strlen( $type ) + 1 ) === 0 ) {
                include_once $this->filters_base.'/'.$file ;
                $plugin_name = 'protector_'.substr($file,0,-4) ;
                if ( function_exists( $plugin_name ) ) {
                    // old way
                    $ret |= call_user_func( $plugin_name ) ;
                } elseif ( class_exists( $plugin_name ) ) {
                    // newer way
                    $plugin_obj = new $plugin_name() ; //old code is -> $plugin_obj =& new $plugin_name() ; //hack by Trabis
                    $ret |= $plugin_obj->execute() ;
                }
            }
        }
        closedir( $dh ) ;

        return $ret ;
    }
}
