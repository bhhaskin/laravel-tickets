<?php

use Bhhaskin\LaravelWorkspaces\Models\Workspace;

return [
    'workspace_model' => Workspace::class,
    'invitation_model' => \Bhhaskin\LaravelWorkspaces\Models\WorkspaceInvitation::class,
    'user_model' => Bhhaskin\Tickets\Tests\Fixtures\User::class,
    'tables' => [
        'workspaces' => 'workspaces',
        'workspaceables' => 'workspaceables',
    ],
];
