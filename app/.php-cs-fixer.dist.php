<?php
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'single_quote' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder);
