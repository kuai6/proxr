<?php
/**
 * Usage /path/to/php /path/to/project/vendor/bin/php-cs-fixer fix --diff --config-file /path/to/config/.php.cs.php
 */
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__.'/module')
    ->in(__DIR__.'/config')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
        if(in_array($file->getBasename(), ['autoload_classmap.php', 'template_classmap.php'])) {
            return false;
        }
        return true;
    });
$config = Symfony\CS\Config\Config::create();
$config->level(null);
$config->fixers([
    'braces',
    'duplicate_semicolon',
    'elseif',
    'encoding',
    'eof_ending',
    'function_call_space',
    'function_declaration',
    'indentation',
    'join_function',
    'line_after_namespace',
    'linefeed',
    'lowercase_keywords',
    'parenthesis',
    'multiple_use',
    'method_argument_space',
    'object_operator',
    'php_closing_tag',
    'remove_lines_between_uses',
    'short_array_syntax',
    'short_tag',
    'standardize_not_equal',
    'trailing_spaces',
    'unused_use',
    'visibility',
    'whitespacy_lines',
]);
$config->finder($finder);
return $config;