<?php

return [

    'permanent_admin_emails' => [
        'asal.pandey@goldeneye.edu.np',
        'admin@goldeneye.edu.np',
    ],
    'organization_email_domain' => 'goldeneye.edu.np',
    'security_email' => 'security@goldeneye.edu.np',
    'security_email_name' => 'Golden Eye Academy Security',

    /*
    |--------------------------------------------------------------------------
    | Official Academy Email
    |--------------------------------------------------------------------------
    |
    | The canonical official email address for Golden Eye Academy.
    | Used for inquiry and course-help notification recipients,
    | public contact presentation, structured data, and seeder baselines.
    |
    */

    'official_email' => env('GOLDENEYE_OFFICIAL_EMAIL', 'contact@goldeneye.edu.np'),

];
