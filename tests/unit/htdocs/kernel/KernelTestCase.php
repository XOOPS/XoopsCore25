<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsMySQLDatabase;

/**
 * Base test case for XOOPS kernel tests.
 *
 * Provides common helpers for mocking the database layer
 * and injecting dependencies via reflection.
 */
abstract class KernelTestCase extends TestCase
{
    /**
     * Create a mock of XoopsMySQLDatabase with common method stubs.
     *
     * @return XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockDatabase()
    {
        $db = $this->createMock(XoopsMySQLDatabase::class);
        $db->method('prefix')->willReturnCallback(function ($table) {
            return 'xoops_' . $table;
        });
        $db->method('quote')->willReturnCallback(function ($value) {
            return "'" . addslashes((string) $value) . "'";
        });
        $db->method('escape')->willReturnCallback(function ($value) {
            return addslashes((string) $value);
        });

        return $db;
    }

    /**
     * Create an XoopsObjectHandler subclass by bypassing its constructor
     * and injecting a mock database.
     *
     * @param string $className Handler class name
     * @param XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject|null $db
     * @return object Handler instance
     */
    protected function createHandler(string $className, $db = null)
    {
        $db = $db ?? $this->createMockDatabase();
        $ref = new ReflectionClass($className);
        $handler = $ref->newInstanceWithoutConstructor();
        $this->setProtectedProperty($handler, 'db', $db);

        return $handler;
    }

    /**
     * Set a protected or private property on an object via reflection.
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected function setProtectedProperty($object, string $property, $value): void
    {
        $ref = new ReflectionClass($object);
        // Walk up the class hierarchy to find the property
        while ($ref) {
            if ($ref->hasProperty($property)) {
                $prop = $ref->getProperty($property);
                $prop->setAccessible(true);
                $prop->setValue($object, $value);
                return;
            }
            $ref = $ref->getParentClass();
        }
        throw new \RuntimeException("Property {$property} not found on " . get_class($object));
    }

    /**
     * Get a protected or private property from an object via reflection.
     *
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function getProtectedProperty($object, string $property)
    {
        $ref = new ReflectionClass($object);
        while ($ref) {
            if ($ref->hasProperty($property)) {
                $prop = $ref->getProperty($property);
                $prop->setAccessible(true);
                return $prop->getValue($object);
            }
            $ref = $ref->getParentClass();
        }
        throw new \RuntimeException("Property {$property} not found on " . get_class($object));
    }

    /**
     * Simulate a DB query that returns one row matching the given data.
     *
     * @param XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject $db
     * @param array $row The row data to return
     */
    protected function stubSingleRowResult($db, array $row): void
    {
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(1);
        $db->method('fetchArray')->willReturnOnConsecutiveCalls($row, false);
    }

    /**
     * Simulate a DB query that returns multiple rows.
     *
     * @param XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject $db
     * @param array $rows Array of row arrays
     */
    protected function stubMultiRowResult($db, array $rows): void
    {
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $returns = $rows;
        $returns[] = false;
        $db->method('fetchArray')->willReturnOnConsecutiveCalls(...$returns);
    }

    /**
     * Simulate a DB COUNT(*) query that returns a count.
     *
     * @param XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject $db
     * @param int $count
     */
    protected function stubCountResult($db, int $count): void
    {
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchRow')->willReturn([$count]);
    }
}
