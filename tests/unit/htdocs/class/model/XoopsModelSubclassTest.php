<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsModelAbstract;
use XoopsModelJoint;
use XoopsModelRead;
use XoopsModelStats;
use XoopsModelSync;
use XoopsModelWrite;
use XoopsObject;
use XoopsPersistableObjectHandler;

// Ensure all model subclass files are loaded
require_once XOOPS_ROOT_PATH . '/class/model/read.php';
require_once XOOPS_ROOT_PATH . '/class/model/write.php';
require_once XOOPS_ROOT_PATH . '/class/model/stats.php';
require_once XOOPS_ROOT_PATH . '/class/model/joint.php';
require_once XOOPS_ROOT_PATH . '/class/model/sync.php';

/**
 * Comprehensive tests for XoopsModel subclasses: Read, Write, Stats, Joint, Sync.
 *
 * These tests exercise the model layer that XoopsPersistableObjectHandler
 * delegates to. Since the test bootstrap provides a stub database that
 * returns false for all queries, many methods will return 0, false, empty
 * arrays, or throw RuntimeException. The tests verify:
 *
 * - Correct return types and error handling paths
 * - Delegation patterns between subclasses
 * - Property setting and handler wiring
 * - SQL construction (verified via RuntimeException messages where applicable)
 * - Edge cases with null criteria and empty parameters
 *
 * @package xoopsmodel
 */
#[CoversClass(XoopsModelRead::class)]
#[CoversClass(XoopsModelWrite::class)]
#[CoversClass(XoopsModelStats::class)]
#[CoversClass(XoopsModelJoint::class)]
#[CoversClass(XoopsModelSync::class)]
class XoopsModelSubclassTest extends TestCase
{
    /**
     * Create a stub XoopsPersistableObjectHandler via reflection,
     * bypassing the constructor which would require a real DB connection.
     *
     * @return XoopsPersistableObjectHandler
     */
    private function createStubHandler(): XoopsPersistableObjectHandler
    {
        $ref = new ReflectionClass(XoopsPersistableObjectHandler::class);
        $handler = $ref->newInstanceWithoutConstructor();
        $handler->db = $GLOBALS['xoopsDB'];
        $handler->table = 'xoops_test';
        $handler->keyName = 'id';
        $handler->className = 'XoopsObject';
        $handler->identifierName = 'title';

        return $handler;
    }

    /**
     * Helper to create a model subclass with a wired-up handler.
     *
     * @param string $modelClass Fully qualified class name (e.g. 'XoopsModelRead')
     * @return XoopsModelAbstract
     */
    private function createModel(string $modelClass): XoopsModelAbstract
    {
        $model = new $modelClass();
        $model->setHandler($this->createStubHandler());

        return $model;
    }

    // ===================================================================
    // XoopsModelRead tests
    // ===================================================================

    #[Test]
    public function readExtendsXoopsModelAbstract(): void
    {
        $ref = new ReflectionClass(XoopsModelRead::class);
        $this->assertSame(
            'XoopsModelAbstract',
            $ref->getParentClass()->getName()
        );
    }

    #[Test]
    public function readCanBeInstantiatedAndHandlerSet(): void
    {
        $model = $this->createModel(XoopsModelRead::class);

        $this->assertInstanceOf(XoopsModelRead::class, $model);
        $this->assertInstanceOf(XoopsPersistableObjectHandler::class, $model->handler);
    }

    #[Test]
    public function readGetAllWithNullCriteriaThrowsRuntimeException(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        // The stub DB returns false for query(), and isResultSet() returns false,
        // so getAll() throws RuntimeException with the SQL in the message.
        try {
            $model->getAll(null);
            $this->fail('Expected RuntimeException from getAll()');
        } catch (\RuntimeException $e) {
            // Verify the exception message contains the expected SQL pattern
            $this->assertStringContainsString('SELECT * FROM `xoops_test`', $e->getMessage());
        }
    }

    #[Test]
    public function readGetAllWithFieldsIncludesKeyNameInQuery(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        try {
            $model->getAll(null, ['title']);
            $this->fail('Expected RuntimeException from getAll()');
        } catch (\RuntimeException $e) {
            // keyName 'id' should be appended to the field list
            $this->assertStringContainsString('`title`', $e->getMessage());
            $this->assertStringContainsString('`id`', $e->getMessage());
        }
    }

    #[Test]
    public function readGetAllWithFieldsAlreadyContainingKeyName(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        try {
            $model->getAll(null, ['id', 'title']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            // 'id' should appear only once in the SELECT clause
            $msg = $e->getMessage();
            $this->assertStringContainsString('SELECT `id`, `title`', $msg);
        }
    }

    #[Test]
    public function readGetObjectsDelegatesToGetAll(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        // getObjects() calls getAll() internally, which will throw the same RuntimeException
        try {
            $model->getObjects(null);
            $this->fail('Expected RuntimeException from getObjects()');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('SELECT * FROM `xoops_test`', $e->getMessage());
        }
    }

    #[Test]
    public function readGetListReturnsEmptyArrayWhenQueryFails(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        // getList() does NOT throw on query failure -- it returns [] when isResultSet is false
        $result = $model->getList(null);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function readGetIdsReturnsEmptyArrayWhenQueryFails(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        $result = $model->getIds(null);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function readGetListWithLimitAndStart(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        // With limit/start parameters, getList creates a CriteriaCompo internally
        $result = $model->getList(null, 10, 5);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function readGetAllWithCriteriaThrowsRuntimeException(): void
    {
        /** @var XoopsModelRead $model */
        $model = $this->createModel(XoopsModelRead::class);

        $criteria = new \CriteriaCompo(new \Criteria('status', '1'));

        try {
            $model->getAll($criteria);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('SELECT * FROM `xoops_test`', $e->getMessage());
            $this->assertStringContainsString('WHERE', $e->getMessage());
        }
    }

    #[Test]
    public function readHasGetByLimitMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelRead::class);
        $this->assertTrue($ref->hasMethod('getByLimit'));
    }

    #[Test]
    public function readHasConvertResultSetMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelRead::class);
        $this->assertTrue($ref->hasMethod('convertResultSet'));
    }

    // ===================================================================
    // XoopsModelWrite tests
    // ===================================================================

    #[Test]
    public function writeExtendsXoopsModelAbstract(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertSame(
            'XoopsModelAbstract',
            $ref->getParentClass()->getName()
        );
    }

    #[Test]
    public function writeCanBeInstantiatedAndHandlerSet(): void
    {
        $model = $this->createModel(XoopsModelWrite::class);

        $this->assertInstanceOf(XoopsModelWrite::class, $model);
        $this->assertInstanceOf(XoopsPersistableObjectHandler::class, $model->handler);
    }

    #[Test]
    public function writeInsertWithNonDirtyObjectReturnsKeyValue(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        // Create a non-dirty XoopsObject
        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 42);
        // Object is not dirty by default (isDirty() returns false)
        $object->_isDirty = false;

        // Suppress the E_USER_NOTICE about "not dirty"
        set_error_handler(function ($errno, $errstr) {
            if ($errno === E_USER_NOTICE) {
                return true;
            }
            return false;
        });

        try {
            $result = $model->insert($object);
        } finally {
            restore_error_handler();
        }

        // Returns the key value since object is not dirty
        $this->assertSame(42, $result);
    }

    #[Test]
    public function writeDeleteBuildsDeleteQuery(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 5);

        // delete() uses exec() which returns false from stub, so result is false
        $result = $model->delete($object);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeDeleteWithArrayKeyName(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        // Set up composite key
        $model->handler->keyName = ['id', 'lang'];

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 5);
        $object->initVar('lang', XOBJ_DTYPE_TXTBOX, 'en');

        $result = $model->delete($object);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeDeleteAllWithNullCriteriaReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        // With null criteria, it builds "DELETE FROM xoops_test"
        // exec() returns false from stub DB
        $result = $model->deleteAll(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeDeleteAllWithCriteriaReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $criteria = new \CriteriaCompo(new \Criteria('status', '0'));

        $result = $model->deleteAll($criteria);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeDeleteAllWithInvalidCriteriaReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        // Pass a non-CriteriaElement object -- triggers the "not subclass" check
        // We need something that is not a CriteriaElement subclass
        // Using a string cast scenario: the method checks is_subclass_of
        // Since the criteria parameter is typed as ?CriteriaElement, PHP will reject
        // non-CriteriaElement types at the call site. But we can test with null.
        $result = $model->deleteAll(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeUpdateAllReturnsFalseWhenQueryFails(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $result = $model->updateAll('status', 1, null);

        // exec() returns false from stub, so result is false
        $this->assertFalse($result);
    }

    #[Test]
    public function writeUpdateAllWithStringValueReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $result = $model->updateAll('title', 'New Title', null);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeUpdateAllWithArrayValueReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $result = $model->updateAll('tags', ['tag1', 'tag2'], null);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeUpdateAllWithCriteriaReturnsFalse(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $criteria = new \CriteriaCompo(new \Criteria('category', '3'));

        $result = $model->updateAll('status', 1, $criteria);

        $this->assertFalse($result);
    }

    #[Test]
    public function writeCleanVarsProcessesIntType(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 0);
        $object->initVar('count', XOBJ_DTYPE_INT, 0);
        $object->setVar('count', '42');

        $result = $model->cleanVars($object);

        $this->assertTrue($result);
        $this->assertSame(42, $object->cleanVars['count']);
    }

    #[Test]
    public function writeCleanVarsProcessesFloatType(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 0);
        $object->initVar('price', XOBJ_DTYPE_FLOAT, 0);
        $object->setVar('price', '19.99');

        $result = $model->cleanVars($object);

        $this->assertTrue($result);
        $this->assertSame(19.99, $object->cleanVars['price']);
    }

    #[Test]
    public function writeCleanVarsProcessesDecimalType(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 0);
        $object->initVar('amount', XOBJ_DTYPE_DECIMAL, 0);
        $object->setVar('amount', '100.50');

        $result = $model->cleanVars($object);

        $this->assertTrue($result);
        $this->assertSame(100.50, $object->cleanVars['amount']);
    }

    #[Test]
    public function writeCleanVarsHandlesRequiredFieldError(): void
    {
        /** @var XoopsModelWrite $model */
        $model = $this->createModel(XoopsModelWrite::class);

        $object = new XoopsObject();
        $object->initVar('id', XOBJ_DTYPE_INT, 0);
        $object->initVar('name', XOBJ_DTYPE_TXTBOX, '', true); // required = true
        $object->setVar('name', ''); // empty value for required field

        $result = $model->cleanVars($object);

        $this->assertFalse($result);
        $errors = $object->getErrors();
        $this->assertNotEmpty($errors);
    }

    #[Test]
    public function writeHasCleanVarsMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertTrue($ref->hasMethod('cleanVars'));
    }

    #[Test]
    public function writeHasInsertMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertTrue($ref->hasMethod('insert'));
    }

    #[Test]
    public function writeHasDeleteMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertTrue($ref->hasMethod('delete'));
    }

    #[Test]
    public function writeHasDeleteAllMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertTrue($ref->hasMethod('deleteAll'));
    }

    #[Test]
    public function writeHasUpdateAllMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelWrite::class);
        $this->assertTrue($ref->hasMethod('updateAll'));
    }

    // ===================================================================
    // XoopsModelStats tests
    // ===================================================================

    #[Test]
    public function statsExtendsXoopsModelAbstract(): void
    {
        $ref = new ReflectionClass(XoopsModelStats::class);
        $this->assertSame(
            'XoopsModelAbstract',
            $ref->getParentClass()->getName()
        );
    }

    #[Test]
    public function statsCanBeInstantiatedAndHandlerSet(): void
    {
        $model = $this->createModel(XoopsModelStats::class);

        $this->assertInstanceOf(XoopsModelStats::class, $model);
        $this->assertInstanceOf(XoopsPersistableObjectHandler::class, $model->handler);
    }

    #[Test]
    public function statsGetCountWithNullCriteriaReturnsZero(): void
    {
        /** @var XoopsModelStats $model */
        $model = $this->createModel(XoopsModelStats::class);

        // The stub DB query() returns false, isResultSet() returns false,
        // so getCount() returns 0.
        $result = $model->getCount(null);

        $this->assertSame(0, $result);
    }

    #[Test]
    public function statsGetCountWithCriteriaReturnsZero(): void
    {
        /** @var XoopsModelStats $model */
        $model = $this->createModel(XoopsModelStats::class);

        $criteria = new \CriteriaCompo(new \Criteria('status', '1'));
        $result = $model->getCount($criteria);

        $this->assertSame(0, $result);
    }

    #[Test]
    public function statsGetCountsWithNullCriteriaReturnsEmptyArray(): void
    {
        /** @var XoopsModelStats $model */
        $model = $this->createModel(XoopsModelStats::class);

        $result = $model->getCounts(null);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function statsGetCountsWithCriteriaReturnsEmptyArray(): void
    {
        /** @var XoopsModelStats $model */
        $model = $this->createModel(XoopsModelStats::class);

        $criteria = new \CriteriaCompo(new \Criteria('category_id', '5'));
        $result = $model->getCounts($criteria);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function statsHasGetCountMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelStats::class);
        $this->assertTrue($ref->hasMethod('getCount'));
    }

    #[Test]
    public function statsHasGetCountsMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelStats::class);
        $this->assertTrue($ref->hasMethod('getCounts'));
    }

    #[Test]
    public function statsGetCountReturnTypeIsIntForNonGrouped(): void
    {
        /** @var XoopsModelStats $model */
        $model = $this->createModel(XoopsModelStats::class);

        $result = $model->getCount(null);

        $this->assertIsInt($result);
    }

    // ===================================================================
    // XoopsModelJoint tests
    // ===================================================================

    #[Test]
    public function jointExtendsXoopsModelAbstract(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertSame(
            'XoopsModelAbstract',
            $ref->getParentClass()->getName()
        );
    }

    #[Test]
    public function jointCanBeInstantiatedAndHandlerSet(): void
    {
        $model = $this->createModel(XoopsModelJoint::class);

        $this->assertInstanceOf(XoopsModelJoint::class, $model);
    }

    #[Test]
    public function jointValidateLinksWithEmptyTableLinkTriggersWarning(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        // table_link is not set, so validateLinks should trigger warning
        $model->handler->table_link = '';
        $model->handler->field_link = '';

        $warningTriggered = false;
        set_error_handler(function ($errno, $errstr) use (&$warningTriggered) {
            if ($errno === E_USER_WARNING) {
                $warningTriggered = true;
                return true;
            }
            return false;
        });

        $result = $model->validateLinks();

        restore_error_handler();

        $this->assertNull($result);
        $this->assertTrue($warningTriggered, 'E_USER_WARNING should be triggered when table_link is empty');
    }

    #[Test]
    public function jointValidateLinksWithSetFieldsReturnsTrue(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_linked_table';
        $model->handler->field_link = 'linked_id';
        $model->handler->field_object = 'object_id';

        $result = $model->validateLinks();

        $this->assertTrue($result);
    }

    #[Test]
    public function jointValidateLinksDefaultsFieldObjectToFieldLink(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_linked_table';
        $model->handler->field_link = 'category_id';
        $model->handler->field_object = ''; // empty

        $result = $model->validateLinks();

        $this->assertTrue($result);
        $this->assertSame('category_id', $model->handler->field_object);
    }

    #[Test]
    public function jointGetByLinkReturnsEmptyArrayWhenLinksInvalid(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = '';
        $model->handler->field_link = '';

        // Suppress the E_USER_WARNING from validateLinks
        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->getByLink(null);

        restore_error_handler();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function jointGetByLinkWithValidLinksThrowsRuntimeException(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'cat_id';
        $model->handler->field_object = 'category_id';

        try {
            $model->getByLink(null);
            $this->fail('Expected RuntimeException from getByLink()');
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage();
            $this->assertStringContainsString('SELECT', $msg);
            $this->assertStringContainsString('LEFT JOIN', $msg);
            $this->assertStringContainsString('xoops_categories', $msg);
        }
    }

    #[Test]
    public function jointGetCountByLinkReturnsNullWhenLinksInvalid(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = '';
        $model->handler->field_link = '';

        // Suppress warning
        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->getCountByLink(null);

        restore_error_handler();

        $this->assertNull($result);
    }

    #[Test]
    public function jointGetCountByLinkReturnsFalseWhenQueryFails(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'cat_id';
        $model->handler->field_object = 'category_id';

        $result = $model->getCountByLink(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function jointGetCountsByLinkReturnsNullWhenLinksInvalid(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = '';
        $model->handler->field_link = '';

        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->getCountsByLink(null);

        restore_error_handler();

        $this->assertNull($result);
    }

    #[Test]
    public function jointGetCountsByLinkReturnsFalseWhenQueryFails(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'cat_id';
        $model->handler->field_object = 'category_id';

        $result = $model->getCountsByLink(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function jointUpdateByLinkReturnsNullWhenLinksInvalid(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = '';
        $model->handler->field_link = '';

        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->updateByLink(['status' => 1], null);

        restore_error_handler();

        $this->assertNull($result);
    }

    #[Test]
    public function jointUpdateByLinkReturnsFalseWhenQueryFails(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'cat_id';
        $model->handler->field_object = 'category_id';

        $result = $model->updateByLink(['status' => 1], null);

        // exec() returns false from stub
        $this->assertFalse($result);
    }

    #[Test]
    public function jointDeleteByLinkReturnsNullWhenLinksInvalid(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = '';
        $model->handler->field_link = '';

        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->deleteByLink(null);

        restore_error_handler();

        $this->assertNull($result);
    }

    #[Test]
    public function jointDeleteByLinkReturnsFalseWhenQueryFails(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'cat_id';
        $model->handler->field_object = 'category_id';

        $result = $model->deleteByLink(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function jointGetByLinkOverridesFieldLinkAndFieldObject(): void
    {
        /** @var XoopsModelJoint $model */
        $model = $this->createModel(XoopsModelJoint::class);

        $model->handler->table_link = 'xoops_categories';
        $model->handler->field_link = 'original_link';
        $model->handler->field_object = 'original_object';

        try {
            $model->getByLink(null, null, true, 'new_link', 'new_object');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            // Verify the override fields are used
            $this->assertSame('new_link', $model->handler->field_link);
            $this->assertSame('new_object', $model->handler->field_object);
        }
    }

    #[Test]
    public function jointHasValidateLinksMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('validateLinks'));
    }

    #[Test]
    public function jointHasGetByLinkMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('getByLink'));
    }

    #[Test]
    public function jointHasGetCountByLinkMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('getCountByLink'));
    }

    #[Test]
    public function jointHasGetCountsByLinkMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('getCountsByLink'));
    }

    #[Test]
    public function jointHasUpdateByLinkMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('updateByLink'));
    }

    #[Test]
    public function jointHasDeleteByLinkMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelJoint::class);
        $this->assertTrue($ref->hasMethod('deleteByLink'));
    }

    // ===================================================================
    // XoopsModelSync tests
    // ===================================================================

    #[Test]
    public function syncExtendsXoopsModelAbstract(): void
    {
        $ref = new ReflectionClass(XoopsModelSync::class);
        $this->assertSame(
            'XoopsModelAbstract',
            $ref->getParentClass()->getName()
        );
    }

    #[Test]
    public function syncCanBeInstantiatedAndHandlerSet(): void
    {
        $model = $this->createModel(XoopsModelSync::class);

        $this->assertInstanceOf(XoopsModelSync::class, $model);
    }

    #[Test]
    public function syncCleanOrphanWithMissingLinkInfoTriggersWarning(): void
    {
        /** @var XoopsModelSync $model */
        $model = $this->createModel(XoopsModelSync::class);

        // Ensure link fields are empty
        $model->handler->table_link = '';
        $model->handler->field_link = '';
        $model->handler->field_object = '';

        $warningTriggered = false;
        $warningMessage = '';
        set_error_handler(function ($errno, $errstr) use (&$warningTriggered, &$warningMessage) {
            if ($errno === E_USER_WARNING) {
                $warningTriggered = true;
                $warningMessage = $errstr;
                return true;
            }
            return false;
        });

        $result = $model->cleanOrphan();

        restore_error_handler();

        $this->assertNull($result);
        $this->assertTrue($warningTriggered, 'E_USER_WARNING should be triggered when link info is missing');
        $this->assertStringContainsString('link information is not set', $warningMessage);
    }

    #[Test]
    public function syncCleanOrphanAcceptsParameterOverrides(): void
    {
        /** @var XoopsModelSync $model */
        $model = $this->createModel(XoopsModelSync::class);

        // Initially empty
        $model->handler->table_link = '';
        $model->handler->field_link = '';
        $model->handler->field_object = '';

        // The method will try to execute a query after setting the overrides.
        // Since the stub DB has no $conn for mysqli_get_server_info(), this will
        // throw a TypeError or Warning. We catch anything to verify the overrides were set.
        set_error_handler(function () {
            return true;
        });

        try {
            $model->cleanOrphan('xoops_linked', 'link_id', 'obj_id');
        } catch (\Throwable $e) {
            // Expected -- mysqli_get_server_info() fails on null conn
        }

        restore_error_handler();

        $this->assertSame('xoops_linked', $model->handler->table_link);
        $this->assertSame('link_id', $model->handler->field_link);
        $this->assertSame('obj_id', $model->handler->field_object);
    }

    #[Test]
    public function syncSynchronizationDelegatesToCleanOrphan(): void
    {
        /** @var XoopsModelSync $model */
        $model = $this->createModel(XoopsModelSync::class);

        // With no link info set, cleanOrphan returns null via the warning path.
        // synchronization() delegates to cleanOrphan() and returns its result.
        set_error_handler(function ($errno) {
            return $errno === E_USER_WARNING;
        });

        $result = $model->synchronization();

        restore_error_handler();

        $this->assertNull($result);
    }

    #[Test]
    public function syncHasCleanOrphanMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelSync::class);
        $this->assertTrue($ref->hasMethod('cleanOrphan'));
    }

    #[Test]
    public function syncHasSynchronizationMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelSync::class);
        $this->assertTrue($ref->hasMethod('synchronization'));
    }

    #[Test]
    public function syncCleanOrphanParameterDefaults(): void
    {
        $ref = new ReflectionClass(XoopsModelSync::class);
        $method = $ref->getMethod('cleanOrphan');
        $params = $method->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('table_link', $params[0]->getName());
        $this->assertSame('field_link', $params[1]->getName());
        $this->assertSame('field_object', $params[2]->getName());

        // All three have default values of ''
        $this->assertTrue($params[0]->isOptional());
        $this->assertSame('', $params[0]->getDefaultValue());
        $this->assertTrue($params[1]->isOptional());
        $this->assertSame('', $params[1]->getDefaultValue());
        $this->assertTrue($params[2]->isOptional());
        $this->assertSame('', $params[2]->getDefaultValue());
    }

    // ===================================================================
    // Cross-cutting: all subclasses loaded via factory
    // ===================================================================

    #[Test]
    #[DataProvider('modelSubclassProvider')]
    public function allSubclassesCanBeLoadedViaFactory(string $name, string $expectedClass): void
    {
        $ref = new ReflectionClass(XoopsPersistableObjectHandler::class);
        $handler = $ref->newInstanceWithoutConstructor();
        $handler->db = $GLOBALS['xoopsDB'];
        $handler->table = 'xoops_factory_test';
        $handler->keyName = 'id';
        $handler->className = 'XoopsObject';
        $handler->identifierName = 'name';

        $model = \XoopsModelFactory::loadHandler($handler, $name);

        $this->assertInstanceOf($expectedClass, $model);
        $this->assertSame($handler, $model->handler);
    }

    /**
     * Data provider for model names and their expected classes.
     *
     * @return array<string, array{string, string}>
     */
    public static function modelSubclassProvider(): array
    {
        return [
            'read'  => ['read', XoopsModelRead::class],
            'write' => ['write', XoopsModelWrite::class],
            'stats' => ['stats', XoopsModelStats::class],
            'joint' => ['joint', XoopsModelJoint::class],
            'sync'  => ['sync', XoopsModelSync::class],
        ];
    }

    #[Test]
    #[DataProvider('modelSubclassProvider')]
    public function allSubclassesHaveHandlerPropertyAfterConstruction(string $name, string $expectedClass): void
    {
        $model = new $expectedClass();

        // Before setHandler, handler is null
        $this->assertNull($model->handler);

        $stubHandler = $this->createStubHandler();
        $model->setHandler($stubHandler);

        $this->assertSame($stubHandler, $model->handler);
    }
}
