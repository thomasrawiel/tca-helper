..  include:: /Includes.rst.txt

..  _ctype_register:

============
Register content element types
============

.. include:: /_includes/TreeContent.rst.txt

Register a content element by calling `\TRAW\TcaHelper\Configuration\CTypes::registerCTypes()`

`registerCTypes()` is a batch-processing method that:

- Normalizes input into CType objects
- Validates required properties
- Registers each CType in the CType select field
- Applies corresponding TCA type configuration
- Assigns icons and creation options
- Stores the processed configuration for later retrieval in a custom TCA array

..  literalinclude:: /_codesnippets/ctypes/minimal.php
    :language: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php


..  _ctype_register_grouplabel:

Select Item Group Label
=======================

The optional ``$selectItemGroupLabel`` parameter is used to define the **backend display label of a CType group** in the content element selector and the :guilabel:`New Content Element` wizard.


Behavior
--------

When registering or updating CTypes, each CType is assigned to a group via its ``group`` property.

If the CType doesn't have a group configured, the `default` group is used ("Typical page content")

If the group does not yet exist in the TCA configuration, it is created using:

.. code-block:: php

   ExtensionManagementUtility::addTcaSelectItemGroup(
       'tt_content',
       'CType',
       $cType->getGroup(),
       $groupLabel
   );

Group Label Resolution
-----------------------

The value of ``$selectItemGroupLabel`` determines the label shown in the backend:

- If ``$selectItemGroupLabel`` is provided:

  It is used as the **label for the CType group** in the backend and in the :guilabel:`New Content Element` wizar.

- If ``$selectItemGroupLabel`` is ``null``:

  The group key (``$cType->getGroup()``) is used as fallback label.

.. note::

    - A group is only created if it does not already exist.
    - Existing group labels are **not overwritten**.
    - The first registered CType defining a group effectively determines its label.
    - The parameter only affects **backend grouping display**, not CType behavior or structure.

Example
-------

.. code-block:: php

   CTypes::registerCTypes($cTypes, 'Custom Content Elements');

This will create a CType group labeled: ``Custom Content Elements`` if the group does not already exist.

