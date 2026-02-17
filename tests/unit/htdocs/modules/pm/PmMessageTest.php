<?php

declare(strict_types=1);

namespace modulespm;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\PmMessage::class)]
#[CoversClass(\PmMessageHandler::class)]
class PmMessageTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/pm/class/message.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // PmMessage tests
    // ---------------------------------------------------------------

    #[Test]
    public function pmMessageExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\PmMessage::class, \XoopsObject::class));
    }

    #[Test]
    public function pmMessageConstructorInitializesVars(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertArrayHasKey('msg_id', $vars);
        $this->assertArrayHasKey('msg_image', $vars);
        $this->assertArrayHasKey('subject', $vars);
        $this->assertArrayHasKey('from_userid', $vars);
        $this->assertArrayHasKey('to_userid', $vars);
        $this->assertArrayHasKey('msg_time', $vars);
        $this->assertArrayHasKey('msg_text', $vars);
        $this->assertArrayHasKey('read_msg', $vars);
        $this->assertArrayHasKey('from_delete', $vars);
        $this->assertArrayHasKey('to_delete', $vars);
        $this->assertArrayHasKey('from_save', $vars);
        $this->assertArrayHasKey('to_save', $vars);
    }

    #[Test]
    public function pmMessageHasTwelveVars(): void
    {
        $msg = new \PmMessage();
        $this->assertCount(12, $msg->getVars());
    }

    #[Test]
    public function pmMessageIsNotNewByDefault(): void
    {
        // Direct instantiation does not call setNew(); only handler->create() does
        $msg = new \PmMessage();
        $this->assertFalse($msg->isNew());
    }

    #[Test]
    public function pmMessageImageDefaultsToIcon1(): void
    {
        $msg = new \PmMessage();
        $this->assertSame('icon1.gif', $msg->getVar('msg_image'));
    }

    #[Test]
    public function pmMessageReadMsgDefaultsToZero(): void
    {
        $msg = new \PmMessage();
        $this->assertEquals(0, $msg->getVar('read_msg'));
    }

    #[Test]
    public function pmMessageFromDeleteDefaultsToOne(): void
    {
        $msg = new \PmMessage();
        $this->assertEquals(1, $msg->getVar('from_delete'));
    }

    #[Test]
    public function pmMessageToDeleteDefaultsToZero(): void
    {
        $msg = new \PmMessage();
        $this->assertEquals(0, $msg->getVar('to_delete'));
    }

    #[Test]
    public function pmMessageSetAndGetSubject(): void
    {
        $msg = new \PmMessage();
        $msg->setVar('subject', 'Test Subject');
        $this->assertSame('Test Subject', $msg->getVar('subject'));
    }

    #[Test]
    public function pmMessageSetAndGetText(): void
    {
        $msg = new \PmMessage();
        $msg->setVar('msg_text', 'Hello, this is a test message.');
        $this->assertSame('Hello, this is a test message.', $msg->getVar('msg_text'));
    }

    #[Test]
    public function pmMessageSetUserIds(): void
    {
        $msg = new \PmMessage();
        $msg->setVar('from_userid', 1);
        $msg->setVar('to_userid', 2);
        $this->assertEquals(1, $msg->getVar('from_userid'));
        $this->assertEquals(2, $msg->getVar('to_userid'));
    }

    #[Test]
    public function pmMessageSubjectIsRequired(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertTrue($vars['subject']['required']);
    }

    #[Test]
    public function pmMessageFromUserIdIsRequired(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertTrue($vars['from_userid']['required']);
    }

    #[Test]
    public function pmMessageToUserIdIsRequired(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertTrue($vars['to_userid']['required']);
    }

    #[Test]
    public function pmMessageTextIsRequired(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertTrue($vars['msg_text']['required']);
    }

    #[Test]
    public function pmMessageMsgTimeHasDefault(): void
    {
        $msg = new \PmMessage();
        $msgTime = $msg->getVar('msg_time');
        // Should be set to time() during construction
        $this->assertGreaterThan(0, (int)$msgTime);
    }

    // ---------------------------------------------------------------
    // Data type checks
    // ---------------------------------------------------------------

    #[Test]
    public function pmMessageMsgIdIsIntType(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['msg_id']['data_type']);
    }

    #[Test]
    public function pmMessageSubjectIsTxtboxType(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['subject']['data_type']);
    }

    #[Test]
    public function pmMessageTextIsTxtareaType(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['msg_text']['data_type']);
    }

    #[Test]
    public function pmMessageImageIsOtherType(): void
    {
        $msg = new \PmMessage();
        $vars = $msg->getVars();
        $this->assertSame(XOBJ_DTYPE_OTHER, $vars['msg_image']['data_type']);
    }

    // ---------------------------------------------------------------
    // PmMessageHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function pmMessageHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function pmMessageHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $this->assertStringContainsString('priv_msgs', $handler->table);
    }

    #[Test]
    public function pmMessageHandlerSetsClassName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $this->assertSame('PmMessage', $handler->className);
    }

    #[Test]
    public function pmMessageHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\PmMessage::class, $obj);
        $this->assertTrue($obj->isNew());
    }

    #[Test]
    public function pmMessageHandlerKeyName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $this->assertSame('msg_id', $handler->keyName);
    }

    #[Test]
    public function pmMessageHandlerIdentifierName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \PmMessageHandler($db);
        $this->assertSame('subject', $handler->identifierName);
    }
}
