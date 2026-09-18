<?php

namespace TRAW\TcaHelper\Configuration;

use TRAW\TcaHelper\Configuration\TCA\Doktype;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Doktypes
{
    /**
     * Call in TCA/Overrides/pages.php
     *
     *
     * @throws \Exception
     */
    public static function registerDoktypes(array $doktypes, ?string $groupLabel = null): void
    {
        foreach ($doktypes as $doktype) {
            $d = $doktype instanceof Doktype
                ? $doktype
                : ($doktype !== [] ? new Doktype($doktype) : null);

            if($d === null) {
                throw new \Exception('doktype must be an instance of ' . Doktype::class . ' or non empty array', 9552057115);
            }

            if (!isset($GLOBALS['TCA']['pages']['columns']['doktype']['config']['itemGroups'][$d->getGroup()])) {
                ExtensionManagementUtility::addTcaSelectItemGroup('pages', 'doktype', $d->getGroup(), $groupLabel ?? $d->getGroup());
            }

            $GLOBALS['TCA']['pages']['types'][$d->getValue()] = $GLOBALS['TCA']['pages']['types'][1];
            $GLOBALS['TCA']['pages']['types'][$d->getValue()]['allowedRecordTypes'] = $d->getAllowedRecordTypes();

            ExtensionManagementUtility::addTcaSelectItem(
                'pages',
                'doktype',
                new SelectItem(
                    'select',
                    label: $d->getLabel(),
                    value: $d->getValue(),
                    icon: $d->getIconIdentifier(),
                    group: $d->getGroup(),
                    description: $d->getDescription()
                )
            );

            if (!in_array($d->getIconIdentifier(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue()] = $d->getIconIdentifier();
            }

            if (!in_array($d->getIconIdentifierHide(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-hideinmenu'] = $d->getIconIdentifierHide();
            }

            if (!in_array($d->getIconIdentifierContentFromPid(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-contentFromPid'] = $d->getIconIdentifierContentFromPid();
            }

            if (!in_array($d->getIconIdentifierRoot(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-root'] = $d->getIconIdentifierRoot();
            }

            $showItem = $d->getShowItem() ?? $GLOBALS['TCA']['pages']['types'][$d->getValue()]['showitem'] ?? '';

            if (!in_array($d->getAdditionalShowitem(), [null, '', '0'], true)) {
                $showItem = $showItem . (str_starts_with($d->getAdditionalShowitem(), ',') ? '' : ',') . $d->getAdditionalShowitem();
            }

            $GLOBALS['TCA']['pages']['types'][$d->getValue()]['showitem'] = $showItem;

            if (!in_array($d->getColumnsOverrides(), [null, []], true)) {
                $GLOBALS['TCA']['pages']['types'][$d->getValue()]['columnsOverrides'] = $d->getColumnsOverrides();
            }

            if ($d->getWizardSteps() !== []) {
                if (isset($d->getWizardSteps()['setup'])) {
                    $GLOBALS['TCA']['pages']['types'][$d->getValue()]['wizardSteps'] = $d->getWizardSteps();
                } else {
                    $GLOBALS['TCA']['pages']['types'][$d->getValue()]['wizardSteps'] = array_replace_recursive(
                        $GLOBALS['TCA']['pages']['types'][$d->getValue()]['wizardSteps'],
                        $d->getWizardSteps()
                    );
                }
            }

            $GLOBALS['TCA']['pages']['tx_tcahelper_doktypes'][$d->getValue()] = $doktype;
        }
    }
}
