<?php

$guard = 'admin';

// Dashboard
Breadcrumbs::register($guard.'.dashboard.index', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->push(trans('menu.dashboard'), route($guard.'.dashboard.index', $request->route()->parameters()));
});

// Role
Breadcrumbs::register($guard.'.roles.index', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->push(trans('menu.role_backoffice'), route($guard.'.roles.index'));
});

// Role > Add
Breadcrumbs::register($guard.'.roles.create', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.roles.index', $request);
    $breadcrumbs->push(trans('role.add_role'), route($guard.'.roles.create'));
});

// Role > Edit
Breadcrumbs::register($guard.'.roles.edit', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.roles.index', $request);
    $breadcrumbs->push(trans('role.edit_role'), route($guard.'.roles.edit', $request->route()->parameters()));
});

// Profile
Breadcrumbs::register($guard.'.users.profile', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->push(trans('strings.profile'), route($guard.'.users.profile'));
});

// Profile > Change profile
Breadcrumbs::register($guard.'.users.editProfile', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.users.profile', $request);
    $breadcrumbs->push(trans('user.change_profile'), route($guard.'.users.editProfile'));
});

// Profile > Change password
Breadcrumbs::register($guard.'.password.changePasswordForm', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.users.profile', $request);
    $breadcrumbs->push(trans('user.change_password'), route($guard.'.password.changePasswordForm'));
});

// User
Breadcrumbs::register($guard.'.users.index', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->push(trans('menu.admin_manager'), route($guard.'.users.index'));
});

// User > Add
Breadcrumbs::register($guard.'.users.create', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.users.index', $request);
    $breadcrumbs->push(trans('user.add_user'), route($guard.'.users.create'));
});

// User > Edit
Breadcrumbs::register($guard.'.users.edit', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.users.index', $request);
    $breadcrumbs->push(trans('user.edit_user'), route($guard.'.users.edit', $request->route()->parameters()));
});

// User > Edit
Breadcrumbs::register($guard.'.users.show', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.users.index', $request);
    $breadcrumbs->push(trans('user.detail_user'), route($guard.'.users.show', $request->route()->parameters()));
});

// Workspace
Breadcrumbs::register($guard.'.restaurants.index', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->push(trans('menu.workspaces'), route($guard.'.restaurants.index'));
});

// Workspace > Add
Breadcrumbs::register($guard.'.restaurants.create', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.restaurants.index', $request);
    $breadcrumbs->push(trans('workspace.add_workspace'), route($guard.'.restaurants.create'));
});

// Workspace > Edit
Breadcrumbs::register($guard.'.restaurants.edit', function ($breadcrumbs, $request) use ($guard) {
    $breadcrumbs->parent($guard.'.restaurants.index', $request);
    $breadcrumbs->push(trans('workspace.edit_workspace'), route($guard.'.restaurants.edit', $request->route()->parameters()));
});
