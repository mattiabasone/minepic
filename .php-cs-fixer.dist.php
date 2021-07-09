<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/tests'
    ]);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        '@PHP80Migration' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => true,
        'constant_case' => true,
        'date_time_immutable' => true,
        'declare_strict_types' => true,
        'fopen_flags' => [
            'b_mode' => true
        ],
        'fully_qualified_strict_types' => true,
        'line_ending' => true,
        'linebreak_after_opening_tag' => true,
        'lowercase_keywords' => true,
        'mb_str_functions' => true,
        'native_function_invocation' => true,
        'no_closing_tag' => true,
        'no_trailing_whitespace' => true,
        'no_useless_else' => true,
        'no_extra_blank_lines' => true,
        'no_spaces_after_function_name' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_align' => false,
        'phpdoc_order' => true,
        'phpdoc_separation' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_types_order' => ['null_adjustment' => 'always_first'],
        'single_quote' => false,
        'single_trait_insert_per_statement' => false,
        'strict_param' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
        ]
    ])
    ->setRiskyAllowed(true);