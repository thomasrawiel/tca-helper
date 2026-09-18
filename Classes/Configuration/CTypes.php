<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Configuration;

use TRAW\TcaHelper\Configuration\TCA\CType;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class CTypes
{
    /**
     * @throws \Exception
     */
    public static function registerCType(array|CType $cType, ?string $selectItemGroupLabel = null): void
    {
        $cType = $cType instanceof CType
            ? $cType
            : ($cType !== [] ? new CType($cType) : null);

        if($cType === null) {
            throw new \Exception('CType must be an instance of ' . CType::class . ' or non empty array', 9552057115);
        }

        self::validateCType($cType);
        self::registerSelectItemGroup($cType->getGroup(), $selectItemGroupLabel);
        self::registerTcaTypeConfiguration($cType);
    }

    /**
     * @throws \Exception
     * @deprecated
     */
    public static function register(array|CType $cType, ?string $selectItemGroupLabel = null): void
    {
        self::registerCType($cType, $selectItemGroupLabel);
    }

    /**
     * alias
     * @throws \Exception
     */
    public static function registerCTypes(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        foreach ($cTypes as $cType) {
            self::registerCType($cType, $selectItemGroupLabel);
        }
    }

    /**
     * @throws \Exception
     * @deprecated
     */
    public static function registerMultiple(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        self::registerCTypes($cTypes, $selectItemGroupLabel);
    }

    public static function update(CType $cType, ?string $selectItemGroupLabel = null): void
    {
        self::validateCType($cType, true);
        self::updateSelectItem($cType, $selectItemGroupLabel);
        self::registerTcaTypeConfiguration($cType);
    }

    /**
     * alias
     */
    public static function updateCType(CType $cType, ?string $selectItemGroupLabel = null): void
    {
        self::update($cType, $selectItemGroupLabel);
    }

    public static function updateCTypes(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        foreach ($cTypes as $cType) {
            self::update($cType, $selectItemGroupLabel);
        }
    }

    public static function getCType(string $cTypeValue): CType
    {
        $cTypes = $GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'] ?? [];

        if (isset($cTypes[$cTypeValue])) {
            return new CType($cTypes[$cTypeValue]);
        }

        $cType = self::fetchCTypeData($cTypeValue);

        if ($cType === null) {
            throw new \InvalidArgumentException(
                'CType [' . $cTypeValue . '] does not exist',
                9021369368
            );
        }

        return $cType;
    }

    private static function fetchCTypeData(string $cTypeValue): ?CType
    {
        foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $key => $item) {
            if (($item['value'] ?? null) === $cTypeValue) {
                $cType = new CType($item);
                $cType->setFlexform(
                    $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $cTypeValue] //TYPO3 13
                    ?? $GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['columnsOverrides']['pi_flexform']['config']['ds'] //TYPO3 14
                    ?? null
                )
                    ->setShowitem($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['showitem'] ?? null)
                    ->setColumnsOverrides($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['columnsOverrides'] ?? null)
                    ->setPreviewRenderer($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['previewRenderer'] ?? null)
                    ->setDefaultValues($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['creationOptions']['defaultValues'] ?? null)
                    ->setSaveAndClose((bool)($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['creationOptions']['saveAndClose'] ?? false))
                    ->setWizardLabel($GLOBALS['TCA']['tt_content']['types'][$cTypeValue]['creationOptions']['title'] ?? '');
                return $cType;
            }
        }

        return null;
    }


    private static function validateCType(CType $cType, bool $update = false): void
    {
        if (trim($cType->getValue()) === '') {
            throw new \InvalidArgumentException('CType value must not be empty', 9856944126);
        }

        if (trim($cType->getLabel()) === '') {
            throw new \InvalidArgumentException('CType [' . $cType->getValue() . ']: label must not be empty', 9021369363);
        }

        $allCTypes = array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 'value');
        if (!$update && in_array(trim($cType->getValue()), $allCTypes)) {
            throw new \InvalidArgumentException('CType [' . $cType->getValue() . ']: already exists', 9021369367);
        }

        if ($update && !in_array(trim($cType->getValue()), $allCTypes)) {
            throw new \InvalidArgumentException('CType [' . $cType->getValue() . ']: does not exist', 9021369367);
        }

        if ($cType->getDefaultValues() !== null && !is_array($cType->getDefaultValues())) {
            throw new \InvalidArgumentException('CType [' . $cType->getValue() . ']: default values must be an array', 9021369369);
        }
    }

    private static function registerSelectItemGroup(string $group, ?string $groupLabel): void
    {
        if (!isset($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$group])) {
            ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', $group, $groupLabel ?? $group);
        }
    }

    private static function updateSelectItem(CType $cType, ?string $groupLabel): void
    {
        self::registerSelectItemGroup($cType->getGroup(), $groupLabel);
        $allCTypes = array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 'value');
        if (in_array($cType->getValue(), $allCTypes)) {
            foreach ($allCTypes as $item) {
                if ($item === $cType->getValue()) {
                    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][$item] = new SelectItem(
                        type: 'select',
                        label: $cType->getLabel(),
                        value: $cType->getValue(),
                        icon: $cType->getIconIdentifier(),
                        group: $cType->getGroup(),
                        description: $cType->getDescription(),
                    );
                }
            }
            return;
        }

        throw new \InvalidArgumentException(
            'CType [' . $cType->getValue() . '] cannot be updated because it does not exist',
            9021369367
        );
    }

    private static function registerTcaTypeConfiguration(CType $cType): void
    {
        $typeConfig = [];

        if (!isset($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$cType->getGroup()])) {
            ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', $cType->getGroup(), $groupLabel ?? $cType->getGroup());
        }

        if ($columnsOverrides = $cType->getColumnsOverrides()) {
            $typeConfig['columnsOverrides'] = $columnsOverrides;
        }

        if ($flexform = $cType->getFlexform()) {
            $typeConfig['columnsOverrides'] ??= [];
            $typeConfig['columnsOverrides']['pi_flexform'] = [
                'config' => [
                    'ds' => $flexform,
                ],
            ];
        }

        if ($previewRenderer = $cType->getPreviewRenderer()) {
            $typeConfig['previewRenderer'] = $previewRenderer;
        }

        if ($cType->getSaveAndClose()) {
            $typeConfig['creationOptions']['saveAndClose'] = true;
        }

        if (!empty($cType->getDefaultValues())) {
            $typeConfig['creationOptions']['defaultValues'] = $cType->getDefaultValues();
        }

        if ($cType->getWizardLabel() !== $cType->getLabel()) {
            $typeConfig['creationOptions']['title'] = $cType->getWizardLabel();
        }

        ExtensionManagementUtility::addRecordType(
            new SelectItem(
                type: 'select',
                label: $cType->getLabel(),
                value: $cType->getValue(),
                icon: $cType->getIconIdentifier(),
                group: $cType->getGroup(),
                description: $cType->getDescription(),
            ),
            $cType->getShowitem(),
            $typeConfig,
            $cType->getRelativePosition()
        );

        $GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'][$cType->getValue()] = $cType->__toArray();
    }
}
