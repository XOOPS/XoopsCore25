<?php

return [
	// module name (dirname)
    'profile'   => [
        'main' => 'fa-solid fa-circle-user',
        'sub'  => [
            // Use the URL as the key
			// If you want not overload 1 line, comment the line
			// exemple:
			// 'edituser.php'   => 'fa-solid fa-pen',
			
			'edituser.php'   => 'fa-solid fa-pen',
			'search.php'     => 'fa-solid fa-magnifying-glass',
			'changepass.php' => 'fa-solid fa-rotate',
        ],
    ],
    'publisher' => [
        'main' => 'fa-solid fa-book',
        'sub'  => [
            'search.php'        => 'fa-solid fa-magnifying-glass',
            'submit.php?op=add' => 'fa-solid fa-calendar-days',
            'archive.php'       => 'fa-solid fa-pen',
        ],
    ],
];