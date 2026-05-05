<?php
declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

\TRAW\TcaHelper\Configuration\CTypes::registerCTypes(
    $cTypeArray,
    $groupLabel
);