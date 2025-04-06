<?php
return [
    'project' => [
        'description_max_length' => 500,
        'team_size_min' => 2,
        'team_size_max' => 6,
        'presentation_file_size_max' => 20480, // size in Kbytes
        'image_max_width' => 500, // px
        'image_max_height' => 500, // px
        'image_file_size_max' => 20480, // size in Kbytes
        'zip_file_size_max' => 20480, // size in Kbytes
        'parental_permissions_file_size_max' => 20480, // size in Kbytes
    ],
    'awards_limit_per_jury_member' => [
        'award_mixed' => 4,
        'award_citizenship' => 4,
        'award_engineering' => 4,
        'award_heart' => 4,
        'award_originality' => 4,
    ],
    'evaluation_server' => [
        'ip_address' => '',
        'api_password' => '',
        'fixed_users' => [
            [
                'local_username' => '',
                'remote_username' => '',
                'local_password_md5' => '',
                'remote_password' => '',
            ],
        ]
    ]
];
