<?php

namespace GeneaLabs\LaravelCasts;

class Label extends Component
{
    protected $escapeHtml;
    protected $excludedClasses;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(string $name, $value = null, array $options = [], bool $escapeHtml = true)
    {
        $value = $value ?: $name;
        $value = str_replace('_id', '', $value);
        $value = str_replace('[]', '', $value);
        $value = str_replace('-', ' ', $value);
        $value = str_replace('_', ' ', $value);

        if ($options['label'] ?? '' === ' ') {
            $value = '';
        }

        if ($options['label'] ?? false) {
            $value = $options['label'];
        }

        $value = ucwords($value);
        $options['class'] = $options['class'] ?? '';
        unset($options['id']);

        parent::__construct($name, $value, $options);

        if ($this->framework === 'tailwind') {
            $options['class'] .= ' block';
        }

        $this->attributes['options'] = $options;

        $this->excludedKeys = $this->excludedKeys->merge(collect([
            'autocomplete' => '',
            'data-target' => '',
            'list' => '',
            'placeholder' => '',
            'selected' => '',
            'subFormAction' => '',
            'subFormBlade' => '',
            'subFormClass' => '',
            'subFormFieldName' => '',
            'subFormMethod' => '',
            'subFormTitle' => '',
            'subFormResponseObjectPrimaryKey' => '',
            'type' => '',
        ]));
        $this->excludedClasses = collect([
            'custom-file-input' => '',
            'custom-select' => '',
            'btn-primary' => '',
            'btn-default' => '',
            'btn-danger' => '',
            'btn-warning' => '',
            'btn-success' => '',
            'btn-secondary' => '',
            'btn' => '',
            'datetimepicker-input' => '',
            'form-control' => '',
            'form-control-file' => '',
            'form-control-success' => '',
            'form-control-warning' => '',
            'form-control-danger' => '',
        ]);
        $this->escapeHtml = $escapeHtml;
    }

    protected function renderBaseControl() : string
    {
        if (! $this->name && ! $this->value) {
            return '';
        }

        return app('form')->callParentMethod(
            $this->type,
            $this->name,
            $this->value,
            $this->options,
            $this->escapeHtml
        );
    }

    public function getHtmlAttribute() : string
    {
        return $this->renderBaseControl();
    }
}
