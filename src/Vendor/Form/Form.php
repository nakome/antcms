<?php

declare (strict_types = 1);

namespace Vendor\Form;

final class Form
{

    public static function input(array $args)
    {
        $name        = $args['name'] ?? 'name';
        $label       = $args['label'] ?? 'input text';
        $placeholder = $args['placeholder'] ?? '';
        $type        = $args['type'] ?? 'text';
        $pattern     = $args['pattern'] ?? 'name';
        $required    = $args['required'] ? 'required' : '';
        $value       = $args['value'] ?? '';
        $className   = $args['class'] ?? 'form-control';
        return <<<HTML
            <div class="form-group mb-3">
                <label for="{$name}" class="form-label">{$label}</label>
                <input
                    class={$className}
                    type="{$type}"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    placeholder="{$placeholder}"
                    value="{$value}"
                    {$required}
                />
            </div>
        HTML;
    }

    public static function checkbox(array $args)
    {
        $name      = $args['name'] ?? 'name';
        $label     = $args['label'] ?? 'input text';
        $type      = $args['type'] ?? 'text';
        $required  = $args['required'] ? 'required' : '';
        $value     = $args['value'] ?? '';
        $className = $args['class'] ?? 'form-control';
        return <<<HTML
            <div class="form-group mb-3">
                <input
                    class={$className}
                    type="{$type}"
                    name="{$name}"
                    id="{$name}"
                    value="{$value}"
                    {$required}
                />
                <label for="{$name}" class="form-label mx-3">{$label}</label>
            </div>
        HTML;
    }

    public static function inputForm(array $args)
    {
        $name        = $args['name'] ?? 'name';
        $label       = $args['label'] ?? 'input text';
        $placeholder = $args['placeholder'] ?? '';
        $type        = $args['type'] ?? 'text';
        $pattern     = $args['pattern'] ?? 'name';
        $required    = $args['required'] ? 'required' : '';
        $value       = $args['value'] ?? '';
        $className   = $args['class'] ?? 'form-control';
        return <<<HTML
            <div class="form-group mb-3">
                <label for="{$name}" class="form-label">{$label}</label>
                <input
                    class={$className}
                    type="{$type}"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    placeholder="{$placeholder}"
                    value="{$value}"
                    {$required}
                />
            </div>
        HTML;
    }

    public static function hidden(array $args)
    {
        $name  = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    }

    public static function submit(array $args)
    {
        $name  = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        $class = $args['class'] ?? 'btn';
        return '<input class="' . $class . '" type="submit" name="' . $name . '" value="' . $value . '" />';
    }

    public static function textarea(array $args)
    {
        $name        = $args['name'] ?? 'name';
        $label       = $args['label'] ?? 'message';
        $placeholder = $args['placeholder'] ?? '';
        $type        = $args['type'] ?? 'text';
        $pattern     = $args['pattern'] ?? '';
        $rows        = $args['rows'] ?? '5';
        $required    = $args['required'] ? 'required' : '';
        $value       = $args['value'] ?? '';
        return <<<HTML
            <div class="form-group mb-3">
                <label for="{$name}" class="form-label">{$label}</label>
                <textarea
                    class="form-control"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    rows="{$rows}"
                    placeholder="{$placeholder}"
                    {$required}
                >{$value}</textarea>
            </div>
        HTML;
    }
}
