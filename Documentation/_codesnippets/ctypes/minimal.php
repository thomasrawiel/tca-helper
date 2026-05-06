<?php
declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

\TRAW\TcaHelper\Configuration\CTypes::registerCTypes(
    [
        [
            'label' => 'My CE-Type',
            'value' => 'my_type',
            //...
        ],
        //...
    ],
    'My custom types' //optional group label
);