<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/language/english/logger.php';
require_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';

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
        $text     = "Check this link:<br>http://example.com\nand this email:<br />test@example.com";
        $expected = 'Check this link:<br><a href="http://example.com" target="_blank" rel="external noopener nofollow">http://example.com</a> and this email:<br /><a href="mailto:test@example.com">test@example.com</a>';
        $result   = $this->sanitizer->makeClickable($text);
        $this->assertEquals($expected, $result);
    }

    public function testMultiLineText2()
    {
        $text     = "Check this link:<br>http://example.com\nand this email:<br />test@example.com";
        $expected = 'Check this link:<br><a href="http://example.com" target="_blank" rel="external noopener nofollow">http://example.com</a> and this email:<br /><a href="mailto:test@example.com">test@example.com</a>';
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
            "This is some text with a link on the next line:<br />https://www.another-example.com" => 'This is some text with a link on the next line:<br /><a href="https://www.another-example.com" target="_blank" rel="external noopener nofollow">https://www.another-example.com</a>',
            "This is some text with a link on the next line:<br>https://www.another-example.com" => 'This is some text with a link on the next line:<br><a href="https://www.another-example.com" target="_blank" rel="external noopener nofollow">https://www.another-example.com</a>',
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


    public function testUrlsWithAngularBrackets2()
    {
        $input    = "Visit <http://www.example.com> for more info.";
        $expected = 'Visit <<a href="http://www.example.com" target="_blank" rel="external noopener nofollow">http://www.example.com</a>> for more info.';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testUrlsWithAngularBrackets3()
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

    public function testMakeClickableMultiLine3()
    {
        $text     = "No elitr elit quis nobis soluta cum sanctus fugiat dolor liber facer, sint exercitation kasd et nonumy assum commodi laboris culpa, commodo diam labore nisl illum consectetur nihil elitr invidunt non tempor. Invidunt facilisi soluta nisi te anim soluta labore, cillum elitr quis tempor congue vel liber est aliquyam cupiditat obcaecat tempor obcaecat sint no elit. Nostrud dignissim aliquid. https://www.monxoops.fr/modules/newbb/viewtopic.php?topic_id=139&post_id=1440#forumpost1440 and this email: test@example.com Vel ipsum eiusmod. Kasd accusam nisi.";
        $expected = 'No elitr elit quis nobis soluta cum sanctus fugiat dolor liber facer, sint exercitation kasd et nonumy assum commodi laboris culpa, commodo diam labore nisl illum consectetur nihil elitr invidunt non tempor. Invidunt facilisi soluta nisi te anim soluta labore, cillum elitr quis tempor congue vel liber est aliquyam cupiditat obcaecat tempor obcaecat sint no elit. Nostrud dignissim aliquid. ' .
                    '<a href="https://www.monxoops.fr/modules/newbb/viewtopic.php?topic_id=139&amp;post_id=1440#forumpost1440" target="_blank" rel="external noopener nofollow">https://www.monxoops.fr/modules/newbb/viewtopic.php?topic_id=139&amp;post_id=1440#forumpost1440</a>' .
                    ' and this email: ' . '<a href="mailto:test@example.com">test@example.com</a> ' . 'Vel ipsum eiusmod. Kasd accusam nisi.';

        $result = $this->sanitizer->makeClickable($text);

        $this->assertEquals($expected, $result);
    }

    public function testNewLine0()
    {
        $input    = '<span class="fas fa-bug mx-2 text-warning"></span> block id</button></h2>
<div id="accordion-blockid"';
        $expected = '<span class="fas fa-bug mx-2 text-warning"></span> block id</button></h2> <div id="accordion-blockid"';
        $this->assertEquals($expected, $this->sanitizer->makeClickable($input));
    }

    public function testNewLine()
    {
        $text     = '<span class="fas fa-bug mx-2 text-warning"></span> block id</button></h2>
<div id="accordion-blockid"';
        $expected = '<span class="fas fa-bug mx-2 text-warning"></span> block id</button></h2> <div id="accordion-blockid"';
        $result   = $this->sanitizer->makeClickable($text);
        $this->assertEquals($expected, $result);
    }

    public function testFilePathsAndCustomProtocol()
    {
        $testCases = [
            // Test for file paths
            "Check this file path: file:///usr/local/bin" => 'Check this file path: <a href="file:///usr/local/bin" target="_blank" rel="external">file:///usr/local/bin</a>',

            // Test for custom protocol
            "Use the custom protocol: custom://myapp/resource" => 'Use the custom protocol: <a href="custom://myapp/resource" target="_blank" rel="external">custom://myapp/resource</a>',
        ];

        foreach ($testCases as $input => $expected) {
            $output = $this->sanitizer->makeClickable($input);
            $this->assertEquals($expected, $output);
        }
    }

    public function testInvalidUrls()
    {
        $testCases = [
            // Prevent javascript URLs
            "Don't click this: javascript:alert('XSS')" => "Don't click this: javascript:alert('XSS')",

            // Disallow unsupported protocols
            "Unsupported protocol: gopher://example.com" => "Unsupported protocol: gopher://example.com",
        ];

        foreach ($testCases as $input => $expected) {
            $output = $this->sanitizer->makeClickable($input);
            $this->assertEquals($expected, $output);
        }
    }


//    public function testNestedTagsAndIncompleteTags()
//    {
//        $testCases = [
//            // Nested tags
//            "Nested link: <a href='http://example.com'>http://example.com</a>" => "Nested link: <a href='http://example.com'>http://example.com</a>",
//
//            // Incomplete tags
//            "Incomplete tag: <http://example.com" => "Incomplete tag: <a href=\"http://example.com\" target=\"_blank\" rel=\"external noopener nofollow\">http://example.com</a>",
//        ];
//
//        foreach ($testCases as $input => $expected) {
//            $output = $this->sanitizer->makeClickable($input);
//            $this->assertEquals($expected, $output);
//        }
//    }

}
