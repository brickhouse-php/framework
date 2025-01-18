<?php

namespace Brickhouse\Console\Prompts;

class Key
{
    const UP_ARROW = "\e[A";
    const DOWN_ARROW = "\e[B";
    const RIGHT_ARROW = "\e[C";
    const LEFT_ARROW = "\e[D";
    const TAB = "\t";
    const SPACE = " ";
    const ESCAPE = "\e";
    const DELETE = "\e[3~";
    const BACKSPACE = "\177";
    const ENTER = "\n";
    const CTRL_A = "\x01";
    const CTRL_B = "\x02";
    const CTRL_C = "\x03";
    const CTRL_D = "\x04";
    const CTRL_E = "\x05";
    const CTRL_F = "\x06";

    public const CONTROL_KEYS = [
        Key::UP_ARROW,
        Key::DOWN_ARROW,
        Key::RIGHT_ARROW,
        Key::LEFT_ARROW,
        Key::ESCAPE,
        Key::DELETE,
        Key::BACKSPACE,
        Key::CTRL_A,
        Key::CTRL_B,
        Key::CTRL_C,
        Key::CTRL_D,
        Key::CTRL_E,
        Key::CTRL_F,
    ];
}
