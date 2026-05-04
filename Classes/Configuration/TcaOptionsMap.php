<?php

/**
 * adapted from https://www.in2code.de/aktuelles/typo3-tt-contentlayout-und-frame-class-optionen-dynamisch-setzen/
 */
declare(strict_types=1);

namespace TRAW\TcaHelper\Configuration;

class TcaOptionsMap
{
    /**
     * Usage:
     * Add a map to your table with conditions for when the items should be (or should not be) added
     */
    protected array $mapping = [];

    protected array $properties = [];

    protected array $items = [];

    protected string $field = '';

    public function __construct(private readonly \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool) {}

    public function addOptions(array &$params): void
    {
        $this->initialize($params);
        $this->setOptions();
    }

    protected function initialize(array &$params): void
    {
        $table = $params['table'];

        $this->mapping = $GLOBALS['TCA'][$table]['tx_tcahelper_option_map'] ?? [];
        $this->properties = $params['row'];

        foreach (array_keys($this->mapping) as $propertyName) {
            if (isset($this->properties[$propertyName])) {
                //if it's an array (tt_content) use the first entry
                $this->properties[$propertyName] = $this->properties[$propertyName][0]
                    ?? $this->properties[$propertyName]
                    ?? '';
            }
        }
        /**
         * $params Reference to TCA field configuration array (expects ['items'] to be passed by reference)
         */
        $this->items = &$params['items'];
        $this->field = $params['field'];
    }

    protected function setOptions(): void
    {
        if (!empty($this->mapping[$this->field])) {
            foreach ($this->mapping[$this->field] as $configuration) {
                if (!empty($configuration['conditions'])) {
                    if ($this->isConditionMatching($configuration['conditions'])) {
                        $this->items = $this->mergeItems($configuration['options'] ?? []);
                    }
                } elseif (!empty($configuration['options'])) {
                    $this->items = $this->mergeItems($configuration['options']);
                }
            }
        }
    }

    /**
     * Merge items but ignore duplicate values
     */
    protected function mergeItems(array $options): array
    {
        $existingValues = [];
        foreach ($this->items as $item) {
            if (is_array($item) && isset($item['value'])) {
                $existingValues[] = $item['value'];
            } elseif ($item instanceof \TYPO3\CMS\Core\Schema\Struct\SelectItem) {
                $existingValues[] = $item->getValue();
            }
        }

        $mergedItems = $this->items;
        foreach ($options as $item) {
            $value = null;
            if (is_array($item) && isset($item['value'])) {
                $value = $item['value'];
            } elseif ($item instanceof \TYPO3\CMS\Core\Schema\Struct\SelectItem) {
                $value = $item->getValue();
            }

            if ($value !== null && !in_array($value, $existingValues, true)) {
                $mergedItems[] = $item;
                $existingValues[] = $value; // Avoid duplicates in input itself
            }
        }

        return $mergedItems;
    }

    protected function isConditionMatching(array $conditions): bool
    {
        foreach ($conditions['fields'] ?? [] as $startField => $compareFields) {
            $needle = $this->extractNeedle($this->properties[$startField] ?? null);

            if (!in_array($needle, $compareFields, true)) {
                return false;
            }
        }

        foreach ($conditions['notFields'] ?? [] as $startField => $compareFields) {
            $needle = $this->extractNeedle($this->properties[$startField] ?? null);

            if (in_array($needle, $compareFields, true)) {
                return false;
            }
        }

        foreach ($conditions['functions'] ?? [] as $function => $values) {
            if (!method_exists($this, $function) || $this->$function($values) === false) {
                return false;
            }
        }

        return true;
    }

    private function extractNeedle(mixed $value): mixed
    {
        return is_array($value) ? ($value[0] ?? null) : $value;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentPageProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'pages', 'pid');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentContainerProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'tt_content', 'tx_container_parent');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentNewsRecordProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'tx_news_domain_model_news', 'tx_news_related_news');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentAnythingProperties(array $configuration, string $table, string $parentDetectionField): bool
    {
        if(!isset($this->properties[$parentDetectionField])) {
            return false;
        }

        $parentUid = is_array($this->properties[$parentDetectionField]) ? ($this->properties[$parentDetectionField][0] ?? false) : ($this->properties[$parentDetectionField] ?? false);

        if ($parentUid === false) {
            return false;
        }

        $result = $this->connectionPool
            ->getConnectionForTable($table)
            ->select(
                array_merge(['uid'], array_keys($configuration)),
                $table,
                ['uid' => $parentUid]
            )->fetchAssociative();

        if (empty($result) || $result === false) {
            return false;
        }

        foreach ($configuration as $property => $values) {
            if (array_key_exists($property, $result) === false || in_array($result[$property], $values) === false) {
                return false;
            }
        }

        return true;
    }

    public static function addToOptionMap(string $table, string $field, array $options): void
    {
        $currentFunc = $GLOBALS['TCA'][$table]['columns'][$field]['config']['itemsProcFunc'] ?? null;
        $targetFunc = \TRAW\TcaHelper\Configuration\TcaOptionsMap::class . '->addOptions';

        // If another itemsProcFunc is already set, abort
        if ($currentFunc !== null && $currentFunc !== $targetFunc) {
            throw new \Exception("Can't set option map because $field in $table already has an itemsProcFunc ($currentFunc)");
        }

        if ($currentFunc !== $targetFunc) {
            $GLOBALS['TCA'][$table]['columns'][$field]['config']['itemsProcFunc'] = $targetFunc;
        }

        $GLOBALS['TCA'][$table]['tx_tcahelper_option_map'][$field] =
            array_merge($GLOBALS['TCA'][$table]['tx_tcahelper_option_map'][$field] ?? [], array_values($options));
    }
}
