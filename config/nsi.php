<?php
return [
    'project' => [
        'description_max_length' => 500,
        'team_size_min' => 2,
        'team_size_max' => 5,
        'presentation_file_size_max' => 1024, // size in Kbytes
        'image_file_size_max' => 1024, // size in Kbytes
        'zip_file_size_max' => 1024, // size in Kbytes
        'parental_permissions_file_size_max' => 1024, // size in Kbytes
    ],
    'awards_limit_per_jury_member' => [
        'award_mixed' => 4,
        'award_citizenship' => 4,
        'award_engineering' => 4,
        'award_heart' => 4,
        'award_originality' => 4,
    ]
];