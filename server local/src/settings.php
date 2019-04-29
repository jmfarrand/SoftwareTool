<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        //Database config
        //CHANGE FOR COMMERCE3.
        'db' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'st100385188'
        ]
    ],
];
