<?php

/**
 * @author  Alexander Vakhovski (AlexWaha)
 *
 * @link    https://alexwaha.com
 *
 * @email   support@alexwaha.com
 *
 * @license MIT
 */

return [
    'language_provider' => [
        'class' => \Alexwaha\Localize\LanguageProvider::class,
        'params' => [
            'languages' => [
                [
                    'locale' => 'en',
                    'slug' => 'en',
                ],
            ],
        ],
    ],
];
