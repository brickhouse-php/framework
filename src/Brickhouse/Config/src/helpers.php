<?php

if (! function_exists("env")) {
    /**
     * Get the value of the given environment variable, if it exists. If not, returns `$default`.
     *
     * @param string    $name       Name of the environment variable.
     * @param mixed     $default    Default value to return if the environment variable isn't set.
     *
     * @return null|string
     */
    function env(string $name, $default = null): null|string
    {
        if (($value = getenv($name))) {
            return $value;
        }

        return $default;
    }
}
