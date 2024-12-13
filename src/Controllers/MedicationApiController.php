<?php

namespace App\Controllers;

use App\Utils\DatabaseConnector;
use App\Utils\QueryBuilder;
use App\Utils\Validator;
use PDOException;
use RuntimeException;

readonly class MedicationApiController
{
    private \PDO $pdo;
    private QueryBuilder $queryBuilder;

    // Constants for validation
    private const MAX_NAME_LENGTH = 100;
    private const MAX_NOTE_LENGTH = 500;

    // Constructor
    public function __construct()
    {
        $databaseConnector  = DatabaseConnector::getInstance();
        $this->pdo          = $databaseConnector->getPdo();
        $this->queryBuilder = new QueryBuilder();
    }

    // ==========================
    // API METHODS
    // ==========================

    /**
     * Create a new medication entry.
     */
    public function createMedication(): void
    {
        $this->handleApiRequest(function () {
            $data = $this->parseJsonInput();
            $this->validateRole($data, 'customer');
            $this->validateInput($data, $this->getCreateValidationRules());

            $query = $this->queryBuilder->table('medications')->insert([
                'user_id' => (int)($data['user_id'] ?? 1),
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ]);

            $this->executeQuery($query['sql'], $query['bindings']);
            $this->jsonResponse(['message' => 'Medication created'], 201);
        });
    }

    /**
     * Delete a medication entry by ID.
     */
    public function deleteMedication($vars): void
    {
        $this->handleApiRequest(function () use ($vars) {
            $data = $this->parseJsonInput();
            $this->validateRole($data, 'customer');
            $this->validateInput($vars, $this->getDeleteValidationRules());

            $query = $this->queryBuilder->table('medications')->delete($vars['id']);
            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication deleted'], 200);
        });
    }

    /**
     * Update a medication entry by ID.
     */
    public function updateMedication($vars): void
    {
        $this->handleApiRequest(function () use ($vars) {
            $data = $this->parseJsonInput();
            $this->validateRole($data, 'customer');
            $this->validateInput($data, $this->getUpdateValidationRules());

            $query = $this->queryBuilder->table('medications')->update([
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ], $vars['id']);

            $this->executeQuery($query['sql'], $query['bindings']);
            $this->jsonResponse(['message' => 'Medication updated'], 200);
        });
    }

    /**
     * Retrieve all medications for a user by user_id.
     */
    public function getMedications($vars): void
    {
        $this->handleApiRequest(function () use ($vars) {
            $this->validateInput($vars, $this->getRetrieveValidationRules());
            $this->validateRole($vars, 'pharmacist');

            $query       = $this->queryBuilder->table('medications')->where('user_id', '=', $vars['user_id'])->get();
            $stmt        = $this->executeQuery($query['sql'], $query['bindings']);
            $medications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($medications) {
                $this->jsonResponse($medications, 200);
            } else {
                $this->jsonResponse(['error' => 'No medications found'], 404);
            }
        });
    }

    /**
     * Retrieve a single medication by ID.
     */
    public function getMedication($vars): void
    {
        $this->handleApiRequest(function () use ($vars) {
            $medicationId = $vars['id'];
            $query        = $this->queryBuilder->table('medications')->where('id', '=', $medicationId)->get();

            $stmt       = $this->executeQuery($query['sql'], $query['bindings']);
            $medication = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($medication) {
                $this->jsonResponse($medication, 200);
            } else {
                $this->jsonResponse(['error' => 'Medication not found'], 404);
            }
        });
    }

    // ==========================
    // HELPER METHODS
    // ==========================

    /**
     * Parse JSON input from request body.
     */
    private function parseJsonInput(): array
    {
        $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON input');
        }
        return $data;
    }

    /**
     * Handle API requests with consistent error handling.
     */
    private function handleApiRequest(callable $callback): void
    {
        try {
            $callback();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get validation rules for creating a medication.
     */
    private function getCreateValidationRules(): array
    {
        return [
            'user_id' => $this->createValidationRule('sanitizeInt', 'Invalid UserId input'),
            'name' => $this->createValidationRule('sanitizeString', 'Invalid Name input', [self::MAX_NAME_LENGTH]),
            'started_at' => $this->createValidationRule('sanitizeDateTime', 'Invalid startedAt input'),
            'dosage' => $this->createValidationRule('sanitizeInt', 'Invalid Dosage input'),
            'note' => $this->createValidationRule('sanitizeString', 'Invalid Note input', [self::MAX_NOTE_LENGTH])
        ];
    }

    /**
     * Get validation rules for deleting a medication.
     */
    private function getDeleteValidationRules(): array
    {
        return [
            'id' => $this->createValidationRule('sanitizeInt', 'Invalid Medication ID'),
        ];
    }

    /**
     * Get validation rules for updating a medication.
     */
    private function getUpdateValidationRules(): array
    {
        return [
            'name' => $this->createValidationRule('sanitizeString', 'Invalid input', [self::MAX_NAME_LENGTH]),
            'started_at' => $this->createValidationRule('sanitizeDateTime', 'Invalid input'),
            'dosage' => $this->createValidationRule('sanitizeInt', 'Invalid input'),
            'note' => $this->createValidationRule('sanitizeString', 'Invalid input', [self::MAX_NOTE_LENGTH])
        ];
    }

    /**
     * Get validation rules for retrieving medications.
     */
    private function getRetrieveValidationRules(): array
    {
        return [
            'user_id' => $this->createValidationRule('sanitizeInt', 'Invalid input'),
        ];
    }

    /**
     * Create a validation rule.
     */
    private function createValidationRule(string $method, string $error, array $params = []): array
    {
        return [
            'validator' => Validator::class,
            'method' => $method,
            'error' => $error,
            'params' => $params
        ];
    }

    /**
     * Execute a SQL query with bindings.
     */
    private function executeQuery(string $sql, array $bindings): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }
        $stmt->execute($bindings);
        return $stmt;
    }

    /**
     * Send a JSON response.
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        try {
            echo json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            error_log($jsonException->getMessage());
            echo json_encode(['error' => 'JSON encoding error'], JSON_THROW_ON_ERROR);
        }
    }

    /**
     * Validate input data against rules.
     */
    private function validateInput(array $data, array $rules): void
    {
        foreach ($rules as $field => $rule) {
            if (!call_user_func([$rule['validator'], $rule['method']], $data[$field] ?? null, ...($rule['params'] ?? []))) {
                throw new RuntimeException($rule['error']);
            }
        }
    }

    /**
     * Validate the role of the user.
     */
    private function validateRole(array $data, string $expectedRole): void
    {
        if ($data['role'] !== $expectedRole) {
            throw new RuntimeException('Unauthorized access');
        }
    }
}