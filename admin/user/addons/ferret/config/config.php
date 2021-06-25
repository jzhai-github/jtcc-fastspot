<?php

/*
 * Ferret core config file
 */
return [
    /* --- Engines ---
     * List available indexing engines below using the same format as
     * the following example:
     * 'AlgoliaEngine' => 'Algolia'
     */
    'engines' => [
        'AlgoliaEngine' => 'Algolia',
    ],

    'chunkTags' => [
        '<p>',
    ],

    /* --- Integrations ---
     * List available integrations below using the same format as the
     * following example:
     * 'StructureIntegration' => 'Structure'
     */
    'integrations' => [
        'StructureIntegration' => 'Structure',
        'BreakpointsIntegration' => 'Breakpoints',
    ],

    'userIntegrations' => [
    ],

    /*
     * Integration settings
     */

    'BreakpointsIntegration' => [
        /*
         * Define url breakpoints. If you wish to include a wildcard just
         * add the string "{wildcard}" to a value, as in the following example:
         * 'module-{wildcard}'
         */
        'breakpoints' => [
        ],
    ],

    'StructureIntegration' => [
        'ignore' => [],
        /*
         * Array of labels and entry title names. Eg:
         * 'Grades' => array(
         *  'grade 1',
         *  'grade 2'
         * );
         */
        'labels' => [],
    ],
];
