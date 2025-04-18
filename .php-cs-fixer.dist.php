<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('docker')
    ->exclude('config')
    ->exclude('var')
    ->exclude('public/bundles')
    ->exclude('public/build')
    // exclude files generated by Symfony Flex recipes
    ->notPath('bin/console')
    ->notPath('public/index.php')
    ->notPath('importmap.php')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'blank_line_between_import_groups' => false,
        'concat_space' => ['spacing' => 'one'],
        'yoda_style' => false,
        'global_namespace_import' => true,
        'native_function_invocation' => false,
        'native_constant_invocation' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
;
