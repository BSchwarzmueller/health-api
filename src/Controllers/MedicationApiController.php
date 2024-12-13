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

    private const MAX_ROLE_LENGTH = 50;
    private const MAX_NAME_LENGTH = 100;
    private const MAX_NOTE_LENGTH = 500;

    public function __construct()
    {
        $databaseConnector = DatabaseConnector::getInstance();
        $this->pdo = $databaseConnector->getPdo();
        $this->queryBuilder = new QueryBuilder();
    }

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

    private function createValidationRule(string $method, string $error, array $params = []): array
    {
        return [
            'validator' => Validator::class,
            'method' => $method,
            'error' => $error,
            'params' => $params
        ];
    }

    public function createMedication(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new RuntimeException('Invalid input');
            }

            $this->validateRole($data, 'customer');

            $userId = (int)($data['user_id'] ?? 1);

            $this->validateInput($data, $this->getCreateValidationRules());

            $this->validateRole($data, 'customer');

            $query = $this->queryBuilder->table('medications')->insert([
                'user_id' => $userId,
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ]);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication created'], 201);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteMedication($vars): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new RuntimeException('Invalid input');
            }

            $this->validateRole($data, 'customer');

            $this->validateInput($vars, [
                'id' => $this->createValidationRule('sanitizeInt', 'Invalid Medication ID'),
            ]);

            $query = $this->queryBuilder->table('medications')->delete($vars['id']);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication deleted'], 200);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function updateMedication($vars): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new RuntimeException('Invalid input');
            }
            $this->validateRole($data, 'customer');
            $this->validateInput($data, [
                'name' => $this->createValidationRule('sanitizeString', 'Invalid input', [self::MAX_NAME_LENGTH]),
                'started_at' => $this->createValidationRule('sanitizeDateTime', 'Invalid input'),
                'dosage' => $this->createValidationRule('sanitizeInt', 'Invalid input'),
                'note' => $this->createValidationRule('sanitizeString', 'Invalid input', [self::MAX_NOTE_LENGTH])
            ]);

            $query = $this->queryBuilder->table('medications')->update([
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ], $vars['id']);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication updated'], 200);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function getMedications($vars): void
    {
        try {
            $this->validateInput($vars, [
                'user_id' => $this->createValidationRule('sanitizeInt', 'Invalid input'),
            ]);

            $this->validateRole($vars, 'pharmacist');

            $query = $this->queryBuilder->table('medications')->where('user_id', '=', $vars['user_id'])->get();

            $stmt = $this->executeQuery($query['sql'], $query['bindings']);
            $medications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($medications) {
                $this->jsonResponse($medications, 200);
            } else {
                $this->jsonResponse(['error' => 'No medications found'], 404);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function getMedication($vars): void
    {
        try {
            $medicationId = $vars['id'];
            $query = $this->queryBuilder->table('medications')->where('id', '=', $medicationId)->get();

            $stmt = $this->executeQuery($query['sql'], $query['bindings']);
            $medication = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($medication) {
                $this->jsonResponse($medication, 200);
            } else {
                $this->jsonResponse(['error' => 'Medication not found'], 404);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function executeQuery(string $sql, array $bindings): false|\PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }
        $stmt->execute($bindings);
        return $stmt;
    }

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

    private function validateInput(array $data, array $rules): void
    {
        foreach ($rules as $field => $rule) {
            if (!call_user_func([$rule['validator'], $rule['method']], $data[$field] ?? null, ...($rule['params'] ?? []))) {
                throw new RuntimeException($rule['error']);
            }
        }
    }

    private function validateRole(array $data, string $expectedRole): void
    {
        if ($data['role'] !== $expectedRole) {
            throw new RuntimeException('Unauthorized access');
        }
    }
}