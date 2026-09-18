<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Service;

use TRAW\TcaHelper\Configuration\TCA\CType;

/**
 * Generates PageTS configuration to register or remove custom content elements
 * from the content element wizard depending on TYPO3 version and CType settings.
 */
final class PageTsGenerator
{
    /**
     * Generates the PageTS for the given CType objects.
     *
     * @param array<CType> $cTypes Array of CType objects
     *
     * @return string PageTS configuration string
     */
    public static function generate(): string
    {
        $cTypes = $GLOBALS['TCA']['tt_content']['tx_tcahelper_ctypes'] ?? [];

        $removeItems = array_values(array_map(
            static fn(array $item): array => array_intersect_key(
                $item,
                ['value' => true, 'group' => true]
            ),
            array_filter(
                $cTypes,
                static fn(array $item): bool => !$item['registerInNewContentElementWizard']
            )
        ));;

        $tsLines = [];

        foreach ($removeItems as $item) {
            $tsLines[] = sprintf(
                'mod.wizards.newContentElement.wizardItems.%s.removeItems := addToList(%s)',
                $item['group'],
                $item['value']
            );
        }

        return implode(PHP_EOL, $tsLines) . PHP_EOL;
    }
}
