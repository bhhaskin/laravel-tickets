<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Specify the model class that represents your application users. This is
    | used for relationships and factories when generating tickets. If null,
    | the package will automatically fall back to the default auth provider.
    |
    */
    'user_model' => env('TICKETS_USER_MODEL'),

    /*
    |--------------------------------------------------------------------------
    | Workspace Model
    |--------------------------------------------------------------------------
    |
    | Optionally specify a workspace model class to link tickets to a
    | workspace. Leave null to disable workspace integration entirely.
    |
    */
    'workspace_model' => env('TICKETS_WORKSPACE_MODEL'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Rendering Options
    |--------------------------------------------------------------------------
    |
    | Control how the package converts ticket bodies and replies into HTML.
    | These options are forwarded to the Str::markdown helper.
    |
    */
    'markdown' => [
        'options' => [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ],
    ],
];
