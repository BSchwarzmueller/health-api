<?php

namespace api\Controller;

use PDOException;
use utils\DatabaseConnector;
use utils\QueryBuilder;
use utils\Validator;

class MedicationController
{
    private readonly QueryBuilder $queryBuilder;

    public function __construct(DatabaseConnector $databaseConnector)
    {
        $this->queryBuilder = new QueryBuilder($databaseConnector->getPdo());
    }

    private function executeQuery(string $sql, array $bindings): false|\PDOStatement
    {
        $stmt = $this->queryBuilder->getPdo()->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Failed to prepare statement');
        }
        $stmt->execute($bindings);
        return $stmt;
    }

    private function jsonResponse(array $data)
    {
        try {
            echo json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            error_log($jsonException->getMessage());
            echo json_encode(['error' => 'JSON encoding error']);
        }
    }

    private function validateInput(array $data, array $rules): void
    {
        foreach ($rules as $field => $rule) {
            if (!call_user_func([$rule['validator'], $rule['method']], $data[$field] ?? null, ...($rule['params'] ?? []))) {
                throw new \RuntimeException($rule['error']);
            }
        }
    }

    private function validateRole(array $data, string $expectedRole): void
    {
        if ($data['role'] !== $expectedRole) {
            throw new \Exception('Unauthorized access');
        }
    }

    public function createMedication()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) {
                throw new \RuntimeException('Invalid input');
            }

            $this->validateInput($data, [
                'role' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Unauthorized access', 'params' => []],
                'name' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input', 'params' => [100]],
                'started_at' => ['validator' => Validator::class, 'method' => 'sanitizeDateTime', 'error' => 'Invalid input', 'params' => []],
                'dosage' => ['validator' => Validator::class, 'method' => 'sanitizeInt', 'error' => 'Invalid input', 'params' => []],
                'note' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input', 'params' => [500]]
            ]);

            $this->validateRole($data, 'customer');

            $query = $this->queryBuilder->table('medications')->insert([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ]);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication created']);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function deleteMedication($vars)
    {
        try {
            $this->validateInput($vars, [
                'id' => ['validator' => Validator::class, 'method' => 'sanitizeInt', 'error' => 'Invalid input or unauthorized access', 'params' => []],
                'role' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input or unauthorized access', 'params' => []]
            ]);

            $this->validateRole($vars, 'customer');

            $query = $this->queryBuilder->table('medications')->delete($vars['id']);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication deleted']);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function updateMedication($vars)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) {
                throw new \RuntimeException('Invalid input');
            }

            $this->validateInput($data, [
                'role' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Unauthorized access', 'params' => []],
                'name' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input', 'params' => [100]],
                'started_at' => ['validator' => Validator::class, 'method' => 'sanitizeDateTime', 'error' => 'Invalid input', 'params' => []],
                'dosage' => ['validator' => Validator::class, 'method' => 'sanitizeInt', 'error' => 'Invalid input', 'params' => []],
                'note' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input', 'params' => [500]]
            ]);

            $this->validateRole($data, 'customer');

            $query = $this->queryBuilder->table('medications')->update([
                'name' => $data['name'],
                'started_at' => $data['started_at'],
                'dosage' => $data['dosage'],
                'note' => $data['note'] ?? null
            ], $vars['id']);

            $this->executeQuery($query['sql'], $query['bindings']);

            $this->jsonResponse(['message' => 'Medication updated']);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function getMedications($vars)
    {
        try {
            $this->validateInput($vars, [
                'user_id' => ['validator' => Validator::class, 'method' => 'sanitizeInt', 'error' => 'Invalid input', 'params' => []],
                'role' => ['validator' => Validator::class, 'method' => 'sanitizeString', 'error' => 'Invalid input', 'params' => [50]]
            ]);

            $this->validateRole($vars, 'pharmacist');

            $query = $this->queryBuilder->table('medications')->where('user_id', '=', $vars['user_id'])->get();

            $stmt = $this->executeQuery($query['sql'], $query['bindings']);
            $medications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $this->jsonResponse($medications);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Database error']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }
}