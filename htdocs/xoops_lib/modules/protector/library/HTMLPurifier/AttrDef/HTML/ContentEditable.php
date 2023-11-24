<?php

class HTMLPurifier_AttrDef_HTML_ContentEditable extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $allowed = ['false'];
        if ($config->get('HTML.Trusted')) {
            $allowed = ['', 'true', 'false'];
        }

        $enum = new HTMLPurifier_AttrDef_Enum($allowed);

        return $enum->validate($string, $config, $context);
    }
}
