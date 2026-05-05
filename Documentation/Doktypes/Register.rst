..  include:: /Includes.rst.txt

..  _doktype_register:

============
Register Page types
============

..  code-block:: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php

    \TRAW\TcaHelper\Configuration\CTypes::registerCTypes(
        $cTypeArray,
        $groupLabel
    );

..  literalinclude:: /_codesnippets/ctype_example1.php
    :language: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php