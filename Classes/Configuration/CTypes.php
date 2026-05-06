<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Configuration;

use TRAW\TcaHelper\Configuration\TCA\CType;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class CTypes
{
    public static function registerCTypes(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        foreach ($cTypes as $cType) {
            if (($cType instanceof CType || is_array($cType)) && !empty($cType)) {
                $cType = is_array($cType) ? new CType($cType) : $cType;
            } else {
                throw new \Exception('CType must be an instance of ' . CType::class . ' or array', 9552057115);
            }

            self::validateCType($cType);
            self::registerSelectItem($cType, $selectItemGroupLabel);
            self::registerTcaTypeConfiguration($cType);
            self::registerIconIfAvailable($cType);
            self::registerCreationOptionsIfSupported($cType);

            self::storeCTypeForLaterUse($cType);
        }
    }

    private static function validateCType(CType $cType): void
    {
        if (trim($cType->getValue()) === '') {
            throw new \InvalidArgumentException('CType value must not be empty', 9856944126);
        }

        if (trim($cType->getLabel()) === '') {
            throw new \InvalidArgumentException('CType label must not be empty', 9021369363);
        }
    }

    private static function registerSelectItem(CType $cType, ?string $groupLabel): void
    {
        if (!isset($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$cType->getGroup()])) {
            ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', $cType->getGroup(), $groupLabel ?? $cType->getGroup());
        }

        ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            [
                'label' => $cType->getLabel(),
                'description' => $cType->getDescription(),
                'value' => $cType->getValue(),
                'icon' => $cType->getIconIdentifier(),
                'group' => $cType->getGroup(),
            ],
            $cType->getRelativeToField(),
            $cType->getRelativePosition()
        );
    }

    private static function registerTcaTypeConfiguration(CType $cType): void
    {
        $value = $cType->getValue();
        $typeConfig = [];

        if ($showItem = $cType->getShowItem()) {
            $typeConfig['showitem'] = $showItem;
        }

        if ($columnsOverrides = $cType->getColumnsOverrides()) {
            $typeConfig['columnsOverrides'] = $columnsOverrides;
        }

        if ($flexform = $cType->getFlexform()) {
            if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() > 13) {
                $typeConfig['columnsOverrides'] ??= [];
                $typeConfig['columnsOverrides']['pi_flexform'] = [
                    'config' => [
                        'ds' => $flexform,
                    ],
                ];
            } else {
                ExtensionManagementUtility::addPiFlexFormValue('', $flexform, $cType->getValue());
            }
        }

        if ($previewRenderer = $cType->getPreviewRenderer()) {
            $typeConfig['previewRenderer'] = $previewRenderer;
        }

        if ($typeConfig !== []) {
            $GLOBALS['TCA']['tt_content']['types'][$value] = array_replace_recursive(
                $GLOBALS['TCA']['tt_content']['types'][$value] ?? [],
                $typeConfig
            );
        }
    }

    private static function registerIconIfAvailable(CType $cType): void
    {
        $icon = $cType->getIconIdentifier();
        if ($icon) {
            $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$cType->getValue()] = $icon;
        }
    }

    private static function registerCreationOptionsIfSupported(CType $cType): void
    {
        if ($cType->getSaveAndClose()) {
            $GLOBALS['TCA']['tt_content']['types'][$cType->getValue()]['creationOptions']['saveAndClose'] = true;
        }

        if ($cType->getDefaultValues() !== []) {
            $GLOBALS['TCA']['tt_content']['types'][$cType->getValue()]['creationOptions']['defaultValues'] = $cType->getDefaultValues();
        }
    }

    private static function storeCTypeForLaterUse(CType $cType): void
    {
        if (!isset($GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'])) {
            $GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'] = [];
        }

        $GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'][$cType->getValue()] = $cType->__toArray();
    }
}
