<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/language/english/logger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/module.textsanitizer.php');

require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');

class MyTextSanitizerTest extends TestCase
{
    protected MyTextSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new \MyTextSanitizer(); // Initialize your class
    }

    public function testEmailConversion()
    {
        $input    = "Contact us at info@example.com for more information.";
        $expected = 'Contact us at <a href="mailto:info@example.com">info@example.com</a> for more information.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testHttpUrlConversion()
    {
        $input    = "Visit our website at http://www.example.com.";
        $expected = 'Visit our website at <a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testFtpUrlConversion()
    {
        $input    = "Our ftp is ftp://ftp.example.com.";
        $expected = 'Our ftp is <a href="ftp://ftp.example.com" target="_blank" rel="external">ftp://ftp.example.com</a>.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testEmptyString()
    {
        $this->assertEquals('', $this->sanitizer->makeClickable(''));
    }

    public function testStringWithoutUrlsOrEmails()
    {
        $this->assertEquals('Hello World', $this->sanitizer->makeClickable('Hello World'));
    }

    public function testMultipleUrlsAndEmails()
    {
        $input    = "Visit us at http://www.example.com or https://secure.example.com. Contact us at info@example.com.";
        $expected = 'Visit us at <a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a> or <a href="https://secure.example.com" target="_blank" rel="external noopener nofollow">https://secure.example.com</a>. Contact us at <a href="mailto:info@example.com">info@example.com</a>.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testInvalidUrlsAndEmails()
    {
        $input    = "Visit us at http:/www.example.com. Contact us at info@.com.";
        $expected = 'Visit us at http:/www.example.com. Contact us at info@.com.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testMultiLineText()
    {
        $text     = "Check this link:\nhttp://example.com\nand this email:\ntest@example.com";
        $expected = 'Check this link:<br /><a href="http://example.com" target="_blank" rel="external noopener nofollow">http://example.com</a><br />and this email:<br /><a href="mailto:test@example.com">test@example.com</a>';
        $result   = $this->sanitizer->makeClickable($text);
        $this->assertEquals($expected, $result);
    }

    public function testUrlsEndingWithPunctuation()
    {
        $input    = "Visit our website at http://www.example.com. It's great!";
        $expected = 'Visit our website at <a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>. It\'s great!';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testVariousUrls()
    {
        $testCases = [
            "Visit https://www.example.com for more info"                                      => 'Visit <a href="https://www.example.com" target="_blank" rel="external noopener nofollow">https://www.example.com</a> for more info',
            "Check out www.example.org"                                                        => 'Check out <a href="http://www.example.org" target="_blank" rel="external noopener nofollow">http://www.example.org</a>',
            "Email me at test@example.net"                                                     => 'Email me at <a href="mailto:test@example.net">test@example.net</a>',
            "FTP link: ftp://ftp.example.com/files"                                            => 'FTP link: <a href="ftp://ftp.example.com/files" target="_blank" rel="external">ftp://ftp.example.com/files</a>',
            "This is some text with a link on the next line:\nhttps://www.another-example.com" => 'This is some text with a link on the next line:<br /><a href="https://www.another-example.com" target="_blank" rel="external noopener nofollow">https://www.another-example.com</a>',
        ];

        foreach ($testCases as $input => $expected) {
            $output = $this->sanitizer->makeClickable($input);
            $this->assertEquals($expected, $output);
        }
    }

    public function testUrlsWithParentheses()
    {
        $input    = "Visit our website (http://www.example.com) for more info.";
        $expected = 'Visit our website (<a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>) for more info.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testUrlsWithBrackets()
    {
        $input    = "Check out this link [http://www.example.com].";
        $expected = 'Check out this link [<a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>].';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testUrlsWithAngularBrackets()
    {
        $input    = "Visit <http://www.example.com> for more info.";
        $expected = 'Visit <<a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>> for more info.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testUrlsWithoutProtocol()
    {
        $input    = "Visit www.example.com for more info.";
        $expected = 'Visit <a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a> for more info.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testSftpUrlConversion()
    {
        $input    = "Our sftp is sftp://sftp.example.com.";
        $expected = 'Our sftp is <a href="sftp://sftp.example.com" target="_blank" rel="external">sftp://sftp.example.com</a>.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testEmailAddressWithPlusSign()
    {
        $input    = "Contact us at john+doe@example.com for more information.";
        $expected = 'Contact us at <a href="mailto:john+doe@example.com">john+doe@example.com</a> for more information.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testUrlWithTildeCharacter()
    {
        $input    = "Visit https://www.example.com/~user for more info.";
        $expected = 'Visit <a href="https://www.example.com/~user" target="_blank" rel="external noopener nofollow">https://www.example.com/~user</a> for more info.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }
}
