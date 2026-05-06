..  include:: /Includes.rst.txt

..  _ctype_update:

============
Update content element types
============

.. include:: /_includes/TreeContent.rst.txt

..  code-block:: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php

    \TRAW\TcaHelper\Configuration\CTypes::registerCTypes(
        $cTypeArray,
        $groupLabel
    );

..  literalinclude:: /_codesnippets/ctype_example1.php
    :language: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php



Select Item Group Label
=======================
see :ref:`Select Item Group Label <ctype_register_grouplabel>`