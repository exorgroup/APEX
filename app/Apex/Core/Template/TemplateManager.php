<?php

namespace App\Apex\Core\Template;

class TemplateManager
{
    protected array $templates = [];
    protected string $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = config('apex.template');
        $this->templates = config('apex.templates', []);
    }

    public function getActiveTemplate(): string
    {
        return $this->activeTemplate;
    }

    public function getTemplateConfig(string $template = null): array
    {
        $template = $template ?? $this->activeTemplate;
        return $this->templates[$template] ?? [];
    }

    public function getAllTemplates(): array
    {
        return $this->templates;
    }

    public function hasTemplate(string $template): bool
    {
        return isset($this->templates[$template]);
    }

    public function getTemplateLayout(): string
    {
        $config = $this->getTemplateConfig();
        return $config['layout'] ?? 'Laravel';
    }

    public function hasSidebar(): bool
    {
        $config = $this->getTemplateConfig();
        return $config['has_sidebar'] ?? true;
    }
}
