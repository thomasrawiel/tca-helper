..  include:: /Includes.rst.txt

..  _ctype_reference:

============
Reference
============

..  literalinclude:: /_codesnippets/ctypes/minimal.php
    :language: php
    :caption: EXT:my_ext/Configuration/TCA/Overrides/tt_content.php

.. confval:: label
   :name: ctype-label
   :type: string
   :required: true

   Backend label of the CType.

   Must not be empty.

.. confval:: value
   :name: ctype-value
   :type: string
   :required: true

   Internal CType identifier used in TCA.

   Must not be empty.

.. confval:: wizardLabel
   :name: ctype-wizardLabel
   :type: string
   :default: label

   Label used in the :guilabel:`New Content Element` wizard.

.. confval:: description
   :name: ctype-description
   :type: string
   :default: ""

   Optional description shown in the backend and :guilabel:`New Content Element` wizard.

.. confval:: icon
   :name: ctype-icon
   :type: string|null
   :default: null

   Icon identifier used in the backend content element selector.

.. confval:: group
   :name: ctype-group
   :type: string|null
   :default: "default"

   Group used for organizing CType select items.

.. confval:: showitem
   :name: ctype-showitem
   :type: string|null
   :default: null

   TCA ``showitem`` configuration for the CType.

.. confval:: flexform
   :name: ctype-flexform
   :type: string|null
   :default: null

   FlexForm Data Structure definition.

   Behavior differs depending on TYPO3 version.

.. confval:: columnsOverrides
   :name: ctype-columnsOverrides
   :type: array|null
   :default: null

   TCA column overrides for this CType.

.. confval:: relativeToField
   :name: ctype-relativeToField
   :type: string|null
   :default: null

   Field used as reference for positioning in the CType selector.

.. confval:: relativePosition
   :name: ctype-relativePosition
   :type: string|null
   :default: null

   Position relative to ``relativeToField``.

.. confval:: previewRenderer
   :name: ctype-previewRenderer
   :type: string|null
   :default: null

   Backend preview renderer class identifier.

.. confval:: registerInNewContentElementWizard
   :name: ctype-registerInNewContentElementWizard
   :type: bool
   :default: true

   Defines whether the CType is shown in the :guilabel:`New Content Element` wizard.

.. confval:: defaultValues
   :name: ctype-defaultValues
   :type: array
   :default: []

   Default values applied when creating a new content element.

.. confval:: saveAndClose
   :name: ctype-saveAndClose
   :type: bool
   :default: false

   If enabled, the backend "Save and Close" behavior is activated for this CType.