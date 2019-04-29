<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//add slim-csrf middleware
$app->add($container->get('csrf'));
