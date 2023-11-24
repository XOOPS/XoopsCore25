<?php

class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'CommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = [
        'Core' => [
            0 => ['Style'],
            // 'xml:space' => false,
            'class' => 'Class',
            'id' => 'ID',
            'title' => 'CDATA',
            'contenteditable' => 'ContentEditable',
        ],
        'Lang' => [],
        'I18N' => [
            0 => ['Lang'], // proprietary, for xml:lang/lang
        ],
        'Common' => [
            0 => ['Core', 'I18N']
        ]
    ];
}

// vim: et sw=4 sts=4
