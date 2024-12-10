<?php

require 'vendor/autoload.php';

$router = new AltoRouter();

$router->map('POST', '/api/medications', 'MedicationController#createMedication');
$router->map('DELETE', '/api/medications/[i:id]', 'MedicationController#deleteMedication');
$router->map('PUT', '/api/medications/[i:id]', 'MedicationController#updateMedication');
$router->map('GET', '/api/medications/[i:user_id]', 'MedicationController#getMedications');

$match = $router->match();

if ($match) {
    list($controllerName, $method) = explode('#', $match['target']);

    if (class_exists($controllerName) && method_exists($controllerName, $method)) {
        $controller = new $controllerName();
        call_user_func_array([$controller, $method], $match['params']);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(['error' => 'Method not found'], JSON_THROW_ON_ERROR);
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Route not found'], JSON_THROW_ON_ERROR);
}
