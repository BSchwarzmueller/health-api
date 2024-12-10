<?php

namespace tests\api\Controller;

use api\Controller\MedicationController;
use PHPUnit\Framework\TestCase;
use utils\DatabaseConnector;
use utils\QueryBuilder;

class MedicationControllerTest extends TestCase
{
    private MedicationController $controller;
    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->controller = new MedicationController($this->queryBuilder);
    }

    public function testCreateMedication(): void
    {
        $this->queryBuilder->method('table')->willReturnSelf();
        $this->queryBuilder->method('insert')->willReturn(['sql' => 'INSERT INTO medications ...', 'bindings' => []]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', json_encode([
            'user_id' => 1,
            'name' => 'Test Medication',
            'started_at' => '2023-01-01',
            'dosage' => 10,
            'note' => 'Test note',
            'role' => 'customer'
        ]));

        ob_start();
        $this->controller->createMedication();
        $output = ob_get_clean();

        $this->assertStringContainsString('Medication created', $output);
        $this->assertEquals(201, http_response_code());
    }

    public function testDeleteMedication(): void
    {
        $this->queryBuilder->method('table')->willReturnSelf();
        $this->queryBuilder->method('delete')->willReturn(['sql' => 'DELETE FROM medications WHERE id = ?', 'bindings' => [1]]);

        $vars = ['id' => 1, 'role' => 'customer'];

        ob_start();
        $this->controller->deleteMedication($vars);
        $output = ob_get_clean();

        $this->assertStringContainsString('Medication deleted', $output);
        $this->assertEquals(200, http_response_code());
    }

    public function testUpdateMedication(): void
    {
        $this->queryBuilder->method('table')->willReturnSelf();
        $this->queryBuilder->method('update')->willReturn(['sql' => 'UPDATE medications SET ... WHERE id = ?', 'bindings' => [1]]);

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', json_encode([
            'name' => 'Updated Medication',
            'started_at' => '2023-01-01',
            'dosage' => 20,
            'note' => 'Updated note',
            'role' => 'customer'
        ]));

        $vars = ['id' => 1];

        ob_start();
        $this->controller->updateMedication($vars);
        $output = ob_get_clean();

        $this->assertStringContainsString('Medication updated', $output);
        $this->assertEquals(200, http_response_code());
    }

    public function testGetMedications(): void
    {
        $this->queryBuilder->method('table')->willReturnSelf();
        $this->queryBuilder->method('where')->willReturnSelf();
        $this->queryBuilder->method('get')->willReturn(['sql' => 'SELECT * FROM medications WHERE user_id = ?', 'bindings' => [1]]);

        $vars = ['user_id' => 1, 'role' => 'pharmacist'];

        ob_start();
        $this->controller->getMedications($vars);
        $output = ob_get_clean();

        $this->assertStringContainsString('[]', $output); // Assuming no medications are returned
        $this->assertEquals(200, http_response_code());
    }
}