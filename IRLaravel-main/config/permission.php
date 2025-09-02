<?php
return [
    'dashboard' => [
        'name' => 'Home',
        'actions' => [
            [
                'name' => 'Home',
                'only_super_admin' => false,
                'action' => 'dashboard@index'
            ],
        ]
    ],
    'restaurant' => [
        'name' => 'Restaurants',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => false,
                'action' => 'workspace@index'
            ],
            [
                'name' => 'Add',
                'only_super_admin' => false,
                'action' => 'workspace@create'
            ],
            [
                'name' => 'Edit',
                'only_super_admin' => false,
                'action' => 'workspace@edit'
            ],
            [
                'name' => 'Delete',
                'only_super_admin' => true,
                'action' => 'workspace@destroy'
            ],
            [
                'name' => 'Update status',
                'only_super_admin' => false,
                'action' => 'workspace@updatestatus'
            ],
            [
                'name' => 'Auto login',
                'only_super_admin' => false,
                'action' => 'workspace@autologin'
            ],
            [
                'name' => 'Assign Account Manager',
                'only_super_admin' => true,
                'action' => 'workspace@assignaccountmanager'
            ],
            [
                'name' => 'Send Invitation',
                'only_super_admin' => false,
                'action' => 'workspace@sendinvitation'
            ],
        ]
    ],
    'printer_group' => [
        'name' => 'Printer Groups',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => false,
                'action' => 'printergroup@index'
            ],
            [
                'name' => 'Create',
                'only_super_admin' => false,
                'action' => 'printergroup@create'
            ],
            [
                'name' => 'Delete',
                'only_super_admin' => false,
                'action' => 'printergroup@destroy'
            ],
            [
                'name' => 'Update status',
                'only_super_admin' => false,
                'action' => 'printergroup@updatestatus'
            ]
        ]
    ],
    'user_settings' => [
        'name' => 'Managers',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => true,
                'action' => 'user@index'
            ],
            [
                'name' => 'Add',
                'only_super_admin' => true,
                'action' => 'user@create'
            ],
            [
                'name' => 'Edit',
                'only_super_admin' => true,
                'action' => 'user@edit'
            ],
            [
                'name' => 'Delete',
                'only_super_admin' => true,
                'action' => 'user@destroy'
            ],
            [
                'name' => 'Send invitation',
                'only_super_admin' => true,
                'action' => 'user@sendinvitation'
            ],
        ]
    ],
    'vats' => [
        'name' => 'Vats',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => true,
                'action' => 'vat@index'
            ],
            [
                'name' => 'Create',
                'only_super_admin' => true,
                'action' => 'vat@create'
            ],
        ]
    ],
    'extras' => [
        'name' => 'Extras',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => true,
                'action' => 'workspaceextra@index'
            ],
            [
                'name' => 'Add',
                'only_super_admin' => true,
                'action' => 'workspaceextra@updateorcreate'
            ],
        ]
    ],
    'notifications' => [
        'name' => 'Notifications',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => true,
                'action' => 'notification@index'
            ],
            [
                'name' => 'Add',
                'only_super_admin' => true,
                'action' => 'notification@create'
            ],
            [
                'name' => 'Edit',
                'only_super_admin' => true,
                'action' => 'notification@edit'
            ],
            [
                'name' => 'Delete',
                'only_super_admin' => true,
                'action' => 'notification@destroy'
            ]
        ]
    ],
    'orders' => [
        'name' => 'Orders',
        'actions' => [
            [
                'name' => 'List',
                'only_super_admin' => true,
                'action' => 'order@index'
            ]
        ]
    ],
];