<?php

namespace Brickhouse\Console\Prompts;

use Brickhouse\Console\Prompts\Interactive;

if (!function_exists('\Brickhouse\Console\Prompts\text')) {
    /**
     * Prompts the user for text input in the console.
     *
     * @param string                                $label          Label to present for the prompt.
     * @param string                                $placeholder    Optional placeholder for the prompt. Defaults to ''.
     * @param string                                $initial        Optional initial value of the prompt. Defaults to ''.
     * @param string                                $hint           Optional hint to show to the user. Defaults to ''.
     * @param bool|string                           $required       Defines whether the field is required. If a string, defines the error message.
     * @param null|\Closure(string $value):?string  $validate       Defines a custom validation step for the prompt.
     *
     * @return string
     */
    function text(
        string $label,
        string $placeholder = '',
        string $initial = '',
        string $hint = '',
        bool|string $required = false,
        null|\Closure $validate = null,
    ): string {
        return new Interactive\TextPrompt(...func_get_args())->prompt();
    }
}

if (!function_exists('\Brickhouse\Console\Prompts\confirm')) {
    /**
     * Prompts the user to confirm an action in the console.
     *
     * @param string        $label          Label to present for the prompt.
     * @param bool          $initial        Optional initial value of the prompt. Defaults to `false`.
     * @param string        $active         Defines the text for the active option. Defaults to 'Yes'.
     * @param string        $inactive       Defines the text for the inactive option. Defaults to 'No'.
     * @param string        $hint           Optional hint to show to the user. Defaults to ''.
     *
     * @return bool
     */
    function confirm(
        string $label,
        bool $initial = false,
        string $active = 'Yes',
        string $inactive = 'No',
        string $hint = ''
    ): bool {
        return new Interactive\ConfirmPrompt(...func_get_args())->prompt();
    }
}

if (!function_exists('\Brickhouse\Console\Prompts\select')) {
    /**
     * Prompts the user to select one of the given choices in the console.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param string                    $label          Label to present for the prompt.
     * @param array<TKey,TValue>        $choices        Available choices for the prompt.
     * @param null|string               $initial        Optional initial value of the prompt. Defaults to the first choice.
     * @param string                    $hint           Optional hint to show to the user. Defaults to ''.
     *
     * @return string
     */
    function select(
        string $label,
        array $choices,
        null|string $initial = null,
        string $hint = ''
    ): string {
        return new Interactive\SelectPrompt(...func_get_args())->prompt();
    }
}

if (!function_exists('\Brickhouse\Console\Prompts\multiselect')) {
    /**
     * Prints the given body text inside of a box.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param string                    $label          Label to present for the prompt.
     * @param array<TKey,TValue>        $choices        Available choices for the prompt.
     * @param array<string>             $initial        Optional initial value of the prompt. Defaults to an empty array.
     * @param string                    $hint           Optional hint to show to the user. Defaults to ''.
     *
     * @return TValue[]
     */
    function multiselect(
        string $label,
        array $choices,
        array $initial = [],
        null|string $hint = 'Space to select. Return to submit',
        bool|string $required = false,
    ): array {
        return new Interactive\MultiselectPrompt(...func_get_args())->prompt();
    }
}

if (!function_exists('\Brickhouse\Console\Prompts\pause')) {
    /**
     * Prompts the user to press a key in the console, before continuing.
     *
     * @param string    $label  Label to present for the prompt.
     *
     * @return void
     */
    function pause(string $label = 'Press ENTER to continue.'): void
    {
        new Interactive\PausePrompt(...func_get_args())->prompt();
    }
}

if (!function_exists('\Brickhouse\Console\Prompts\spin')) {
    /**
     * Prompts the user to press a key in the console, before continuing.
     *
     * @template TReturn
     *
     * @param string                $label      Label to present for the prompt.
     * @param \Closure():TReturn    $callback   The callback to execute.
     * @param bool                  $dots       Whether to add dynamic "..." to the end of the label. Defaults to `true`.
     *
     * @return TReturn
     */
    function spin(string $label, \Closure $callback, bool $dots = true)
    {
        return new Interactive\Spinner(...func_get_args())->spin();
    }
}
