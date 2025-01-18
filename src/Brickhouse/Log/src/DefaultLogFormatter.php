<?php

namespace Brickhouse\Log;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Bramus\Ansi\Writers\BufferWriter;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\LogRecord;

class DefaultLogFormatter extends LineFormatter
{
    /**
     * ANSI wrapper which provides colors.
     *
     * @var Ansi
     */
    protected readonly Ansi $ansi;

    public function __construct()
    {
        parent::__construct(
            format: "%message%" . PHP_EOL,
            dateFormat: "H:i:s",
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: false,
            includeStacktraces: false
        );

        $this->ansi = new Ansi(new BufferWriter());
    }

    /**
     * {@inheritdoc}
     */
    public function format(LogRecord $record): string
    {
        $levelString = $this->getLevelString($record->level);

        $format = parent::format($record);
        $substitutions = $this->getSubstitutions($record);

        $format = str_replace(array_keys($substitutions), array_values($substitutions), $format);

        return "  " . $levelString . " " . $format;
    }

    /**
     * {@inheritdoc}
     */
    private function getLevelString(Level $level): string
    {
        $colors = [
            Level::Debug->value => [SGR::COLOR_BG_BLUE, SGR::COLOR_FG_WHITE],
            Level::Info->value => [SGR::COLOR_BG_BLUE, SGR::COLOR_FG_WHITE],
            Level::Notice->value => [SGR::COLOR_BG_CYAN_BRIGHT, SGR::COLOR_FG_WHITE],
            Level::Warning->value => [SGR::COLOR_BG_YELLOW, SGR::COLOR_FG_WHITE],
            Level::Error->value => [SGR::COLOR_BG_RED_BRIGHT, SGR::COLOR_FG_WHITE],
            Level::Critical->value => [SGR::COLOR_BG_RED, SGR::COLOR_FG_WHITE, SGR::STYLE_BLINK],
            Level::Alert->value => [SGR::COLOR_BG_PURPLE_BRIGHT, SGR::COLOR_FG_WHITE, SGR::STYLE_BLINK],
            Level::Emergency->value => [SGR::COLOR_BG_PURPLE, SGR::COLOR_FG_WHITE, SGR::STYLE_BLINK],
        ];

        $color = $this->ansi->color($colors[$level->value])->get();
        $reset = $this->ansi->reset()->get();

        $levelName = match ($level) {
            Level::Debug => "DBUG",
            Level::Info => "INFO",
            Level::Notice => "NOTI",
            Level::Warning => "WARN",
            Level::Error => "ERRO",
            Level::Critical => "CRIT",
            Level::Alert => "ALRT",
            Level::Emergency => "EMRG",
        };

        return $color . " " . $levelName . " " . $reset;
    }

    /**
     * Get all the fields to substitute.
     *
     * @return array<string,string>
     */
    private function getSubstitutions(LogRecord $record): array
    {
        if (!preg_match_all("/\{[\w_]+\}/", $record->message, $matches, PREG_PATTERN_ORDER)) {
            return [];
        }

        $substitutions = array_reduce(
            $matches[0],
            function (array $carry, string $template) use ($record): array {
                $key = trim($template, "{}");
                $carry[$template] = $record->context[$key] ?? $template;

                return $carry;
            },
            []
        );

        return $substitutions;
    }
}
