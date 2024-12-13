<?php

use FastRoute\RouteCollector;

return FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('GET', '/', function () {
        readfile(__DIR__ . '/../Views/index.html');
    });

    // *********************************************************************************************************************
    // MEDICATIONS
    $r->addGroup('/medications', function (RouteCollector $r) {
        $r->addRoute('GET', '/create', function () {
            readfile(__DIR__ . '/../Views/forms/createMedicationForm.html');
        });
        $r->addRoute('GET', '/update', function ($vars) {
            readfile(__DIR__ . '/../Views/forms/updateMedicationForm.html');
        });
        $r->addRoute('GET', '/delete', function ($vars) {
            readfile(__DIR__ . '/../Views/forms/deleteMedicationForm.html');
        });
        $r->addRoute('GET', '/review', function () {
            readfile(__DIR__ . '/../Views/forms/reviewMedicationsForm.html');
        });
    });

    // *********************************************************************************************************************
    // API
    $r->addGroup('/api', function (RouteCollector $r) {
        $r->addRoute('GET', '/medications', 'App\Controllers\api\MedicationApiController@getMedications');
        $r->addRoute('POST', '/medications', 'App\Controllers\api\MedicationApiController@createMedication');
        $r->addRoute('GET', '/medications/{id:\d+}', 'App\Controllers\api\MedicationApiController@getMedication');
        $r->addRoute('PUT', '/medications/{id:\d+}', 'App\Controllers\api\MedicationApiController@updateMedication');
        $r->addRoute('DELETE', '/medication/{id:\d+}', 'App\Controllers\api\MedicationApiController@deleteMedication');
    });
});