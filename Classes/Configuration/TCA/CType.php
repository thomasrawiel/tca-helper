<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Configuration\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Represents a custom CType configuration.
 */
final class CType
{
    protected string $label;

    protected string $wizardLabel;

    protected string $description;

    protected string $value;

    protected ?string $iconIdentifier;

    protected ?string $group;

    protected ?string $showitem;

    protected ?string $flexform;

    protected ?array $columnsOverrides;

    protected ?string $relativeToField;

    protected ?string $relativePosition;

    protected ?string $previewRenderer;

    protected bool $registerInNewContentElementWizard;

    protected ?array $defaultValues;

    protected bool $saveAndClose;

    /**
     * @param array<string, mixed> $cTypeConfiguration
     *
     * @throws \RuntimeException if configuration is invalid
     */
    public function __construct(array $cTypeConfiguration)
    {
        $this->label = $cTypeConfiguration['label'] ?? '';
        $this->value = $cTypeConfiguration['value'] ?? '';

        $this->assertRequiredFields();

        $this->wizardLabel = $cTypeConfiguration['wizardLabel'] ?? $this->label;
        $this->description = $cTypeConfiguration['description'] ?? '';
        $this->iconIdentifier = $cTypeConfiguration['icon'] ?? null;

        $this->group = $cTypeConfiguration['group'] ?? 'default';
        $this->showitem = $cTypeConfiguration['showitem'] ?? null;
        $this->flexform = $cTypeConfiguration['flexform'] ?? null;
        $this->columnsOverrides = $cTypeConfiguration['columnsOverrides'] ?? null;
        $this->relativeToField = $cTypeConfiguration['relativeToField'] ?? null;
        $this->relativePosition = $cTypeConfiguration['relativePosition'] ?? null;
        $this->previewRenderer = $cTypeConfiguration['previewRenderer'] ?? null;
        $this->registerInNewContentElementWizard = (bool)($cTypeConfiguration['registerInNewContentElementWizard'] ?? true);
        $this->defaultValues = $cTypeConfiguration['defaultValues'] ?? null;
        $this->saveAndClose = (bool)($cTypeConfiguration['saveAndClose'] ?? false);
    }

    /**
     * Ensures label and value are set.
     */
    private function assertRequiredFields(): void
    {
        if ($this->label === '' || $this->value === '') {
            throw new \RuntimeException('A CType must have at least a label and a value', 2787735958);
        }
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getWizardLabel(): string
    {
        return $this->wizardLabel;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIconIdentifier(): ?string
    {
        return $this->iconIdentifier;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getShowitem(): ?string
    {
        return $this->showitem;
    }

    public function getFlexform(): ?string
    {
        return $this->flexform;
    }

    public function getColumnsOverrides(): ?array
    {
        return $this->columnsOverrides;
    }

    public function getRelativeToField(): string
    {
        return $this->relativeToField ?? '';
    }

    public function getRelativePosition(): string
    {
        return $this->relativePosition ?? '';
    }

    public function getPreviewRenderer(): ?string
    {
        return $this->previewRenderer;
    }

    public function getRegisterInNewContentElementWizard(): bool
    {
        return $this->registerInNewContentElementWizard;
    }

    public function getDefaultValues(): ?array
    {
        return $this->defaultValues;
    }

    public function getSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }

    public function __toArray(): array
    {
        return [
            'label' => $this->label,
            'wizardLabel' => $this->wizardLabel,
            'value' => $this->value,
            'description' => $this->description,
            'icon' => $this->iconIdentifier,
            'group' => $this->group,
            'showitem' => $this->showitem,
            'flexform' => $this->flexform,
            'columnsOverrides' => $this->columnsOverrides,
            'relativeToField' => $this->relativeToField,
            'relativePosition' => $this->relativePosition,
            'previewRenderer' => $this->previewRenderer,
            'registerInNewContentElementWizard' => $this->registerInNewContentElementWizard,
            'defaultValues' => $this->defaultValues,
            'saveAndClose' => $this->saveAndClose,
        ];
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setWizardLabel(string $wizardLabel): self
    {
        $this->wizardLabel = $wizardLabel;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function setIconIdentifier(?string $iconIdentifier): self
    {
        $this->iconIdentifier = $iconIdentifier;
        return $this;
    }

    public function setGroup(?string $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function setShowitem(?string $showitem): self
    {
        $this->showitem = $showitem;
        return $this;
    }

    public function setFlexform(?string $flexform): self
    {
        $this->flexform = $flexform;
        return $this;
    }

    public function setColumnsOverrides(?array $columnsOverrides): self
    {
        if ($this->columnsOverrides === null) {
            $this->columnsOverrides = $columnsOverrides;
        } else {
            $this->columnsOverrides = array_replace_recursive($this->columnsOverrides, $columnsOverrides);
        }
        return $this;
    }

    public function setRelativeToField(?string $relativeToField): self
    {
        $this->relativeToField = $relativeToField;
        return $this;
    }

    public function setRelativePosition(?string $relativePosition): self
    {
        $this->relativePosition = $relativePosition;
        return $this;
    }

    public function setPreviewRenderer(?string $previewRenderer): self
    {
        $this->previewRenderer = $previewRenderer;
        return $this;
    }

    public function setRegisterInNewContentElementWizard(bool $registerInNewContentElementWizard): self
    {
        $this->registerInNewContentElementWizard = $registerInNewContentElementWizard;
        return $this;
    }

    public function setDefaultValues(?array $defaultValues): self
    {
        if ($this->defaultValues === null) {
            $this->defaultValues = $defaultValues;
        } else {
            $this->defaultValues = array_replace_recursive($this->defaultValues, $defaultValues);
        }
        return $this;
    }

    public function setSaveAndClose(bool $saveAndClose): self
    {
        $this->saveAndClose = $saveAndClose;
        return $this;
    }
}
