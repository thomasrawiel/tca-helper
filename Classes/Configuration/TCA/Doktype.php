<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Configuration\TCA;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Represents a custom page doktype configuration.
 */
final class Doktype
{
    private ?string $label;

    private int|string|null $value;

    private ?string $iconIdentifier;

    private ?string $iconIdentifierHide;

    private ?string $iconIdentifierRoot;

    private ?string $iconIdentifierContentFromPid;

    private string $group;

    private ?array $columnsOverrides;

    private ?string $showItem;

    private ?string $additionalShowItem;

    private array $allowedRecordTypes;

    /**
     * @param array<string, mixed> $doktypeConfiguration
     *
     * @throws \RuntimeException if required fields or icons are invalid
     */
    public function __construct(array $doktypeConfiguration)
    {
        $this->label = $doktypeConfiguration['label'] ?? null;
        $this->value = $doktypeConfiguration['value'] ?? null;

        $this->assertRequiredFields();

        $this->iconIdentifier = $doktypeConfiguration['icon'] ?? null;
        $this->iconIdentifierHide = $doktypeConfiguration['icon-hide'] ?? null;
        $this->iconIdentifierRoot = $doktypeConfiguration['icon-root'] ?? null;
        $this->iconIdentifierContentFromPid = $doktypeConfiguration['icon-contentFromPid'] ?? null;

        $this->group = $doktypeConfiguration['group'] ?? 'default';
        $this->columnsOverrides = $doktypeConfiguration['columnsOverrides'] ?? null;
        $this->showItem = $doktypeConfiguration['showitem'] ?? null;
        $this->additionalShowItem = $doktypeConfiguration['additionalShowItem'] ?? null;
        $this->allowedRecordTypes = $doktypeConfiguration['allowedRecordTypes'] ?? ['*'];

    }

    /**
     * @throws \RuntimeException if label or value are missing or invalid
     */
    private function assertRequiredFields(): void
    {
        if ($this->label === null || $this->label === '' || $this->label === '0' || ($this->value === 0 || ($this->value === '' || $this->value === '0') || $this->value === null)) {
            throw new \RuntimeException('A page type must have at least a label and a value', 5869846286);
        }

        if (!MathUtility::canBeInterpretedAsInteger($this->value)) {
            throw new \RuntimeException('Page type value must be an integer or integer string', 6038008085);
        }
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getValue(): int|string|null
    {
        return $this->value;
    }

    public function getIconIdentifier(): ?string
    {
        return $this->iconIdentifier;
    }

    public function getIconIdentifierHide(): ?string
    {
        return $this->iconIdentifierHide;
    }

    public function getIconIdentifierRoot(): ?string
    {
        return $this->iconIdentifierRoot;
    }

    public function getIconIdentifierContentFromPid(): ?string
    {
        return $this->iconIdentifierContentFromPid;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getColumnsOverrides(): ?array
    {
        return $this->columnsOverrides;
    }

    public function getShowItem(): ?string
    {
        return $this->showItem;
    }

    public function getAdditionalShowItem(): ?string
    {
        return $this->additionalShowItem;
    }

    public function getAllowedRecordTypes(): array
    {
        return $this->allowedRecordTypes;
    }
}
