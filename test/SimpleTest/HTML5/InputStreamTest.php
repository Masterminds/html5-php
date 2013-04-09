<?php

require_once dirname(__FILE__) . '/../autorun.php';

class HTML5_InputStreamTest extends UnitTestCase
{   
    public function invalidReplaceTestHandler($input, $name) {
        $stream = new HTML5_InputStream($input);
        $this->assertIdentical("\xEF\xBF\xBD", $stream->remainingChars(), $name);
    }
    
    public function testInvalidReplace() {
        // Above U+10FFFF
        $this->invalidReplaceTestHandler("\xF5\x90\x80\x80", 'U+110000');
        
        // Incomplete
        $this->invalidReplaceTestHandler("\xDF", 'Incomplete two byte sequence (missing final byte)');
        $this->invalidReplaceTestHandler("\xEF\xBF", 'Incomplete three byte sequence (missing final byte)');
        $this->invalidReplaceTestHandler("\xF4\xBF\xBF", 'Incomplete four byte sequence (missing final byte)');
        
        // Min/max continuation bytes
        $this->invalidReplaceTestHandler("\x80", 'Lone 80 continuation byte');
        $this->invalidReplaceTestHandler("\xBF", 'Lone BF continuation byte');
        
        // Invalid bytes (these can never occur)
        $this->invalidReplaceTestHandler("\xFE", 'Invalid FE byte');
        $this->invalidReplaceTestHandler("\xFF", 'Invalid FF byte');
        
        // Min/max overlong
        $this->invalidReplaceTestHandler("\xC0\x80", 'Overlong representation of U+0000');
        $this->invalidReplaceTestHandler("\xE0\x80\x80", 'Overlong representation of U+0000');
        $this->invalidReplaceTestHandler("\xF0\x80\x80\x80", 'Overlong representation of U+0000');
        $this->invalidReplaceTestHandler("\xF8\x80\x80\x80\x80", 'Overlong representation of U+0000');
        $this->invalidReplaceTestHandler("\xFC\x80\x80\x80\x80\x80", 'Overlong representation of U+0000');
        $this->invalidReplaceTestHandler("\xC1\xBF", 'Overlong representation of U+007F');
        $this->invalidReplaceTestHandler("\xE0\x9F\xBF", 'Overlong representation of U+07FF');
        $this->invalidReplaceTestHandler("\xF0\x8F\xBF\xBF", 'Overlong representation of U+FFFF');
    }
    
    public function testStripLeadingBOM() {
        $leading = new HTML5_InputStream("\xEF\xBB\xBFa");
        $this->assertIdentical('a', $leading->char(), 'BOM should be stripped');
    }
    
    public function testZWNBSP() {
        $stream = new HTML5_InputStream("a\xEF\xBB\xBF");
        $this->assertIdentical("a\xEF\xBB\xBF", $stream->remainingChars(), 'A non-leading U+FEFF (BOM/ZWNBSP) should remain');
    }
    
    public function testNull() {
        $stream = new HTML5_InputStream("\0\0\0");
        $this->assertIdentical("\xEF\xBF\xBD\xEF\xBF\xBD\xEF\xBF\xBD", $stream->remainingChars(), 'Null character should be replaced by U+FFFD');
        $this->assertIdentical(3, count($stream->errors), 'Null character should be throw parse error');
    }
    
    public function testCRLF() {
        $stream = new HTML5_InputStream("\r\n");
        $this->assertIdentical("\n", $stream->remainingChars(), 'CRLF should be replaced by LF');
    }
    
    public function testCR() {
        $stream = new HTML5_InputStream("\r");
        $this->assertIdentical("\n", $stream->remainingChars(), 'CR should be replaced by LF');
    }
    
    public function invalidParseErrorTestHandler($input, $numErrors, $name) {
        $stream = new HTML5_InputStream($input);
        $this->assertIdentical($input, $stream->remainingChars(), $name . ' (stream content)');
        $this->assertIdentical($numErrors, count($stream->errors), $name . ' (number of errors)');
    }
    
    public function testInvalidParseError() {
        // C0 controls (except U+0000 and U+000D due to different handling)
        $this->invalidParseErrorTestHandler("\x01", 1, 'U+0001 (C0 control)');
        $this->invalidParseErrorTestHandler("\x02", 1, 'U+0002 (C0 control)');
        $this->invalidParseErrorTestHandler("\x03", 1, 'U+0003 (C0 control)');
        $this->invalidParseErrorTestHandler("\x04", 1, 'U+0004 (C0 control)');
        $this->invalidParseErrorTestHandler("\x05", 1, 'U+0005 (C0 control)');
        $this->invalidParseErrorTestHandler("\x06", 1, 'U+0006 (C0 control)');
        $this->invalidParseErrorTestHandler("\x07", 1, 'U+0007 (C0 control)');
        $this->invalidParseErrorTestHandler("\x08", 1, 'U+0008 (C0 control)');
        $this->invalidParseErrorTestHandler("\x09", 0, 'U+0009 (C0 control)');
        $this->invalidParseErrorTestHandler("\x0A", 0, 'U+000A (C0 control)');
        $this->invalidParseErrorTestHandler("\x0B", 1, 'U+000B (C0 control)');
        $this->invalidParseErrorTestHandler("\x0C", 0, 'U+000C (C0 control)');
        $this->invalidParseErrorTestHandler("\x0E", 1, 'U+000E (C0 control)');
        $this->invalidParseErrorTestHandler("\x0F", 1, 'U+000F (C0 control)');
        $this->invalidParseErrorTestHandler("\x10", 1, 'U+0010 (C0 control)');
        $this->invalidParseErrorTestHandler("\x11", 1, 'U+0011 (C0 control)');
        $this->invalidParseErrorTestHandler("\x12", 1, 'U+0012 (C0 control)');
        $this->invalidParseErrorTestHandler("\x13", 1, 'U+0013 (C0 control)');
        $this->invalidParseErrorTestHandler("\x14", 1, 'U+0014 (C0 control)');
        $this->invalidParseErrorTestHandler("\x15", 1, 'U+0015 (C0 control)');
        $this->invalidParseErrorTestHandler("\x16", 1, 'U+0016 (C0 control)');
        $this->invalidParseErrorTestHandler("\x17", 1, 'U+0017 (C0 control)');
        $this->invalidParseErrorTestHandler("\x18", 1, 'U+0018 (C0 control)');
        $this->invalidParseErrorTestHandler("\x19", 1, 'U+0019 (C0 control)');
        $this->invalidParseErrorTestHandler("\x1A", 1, 'U+001A (C0 control)');
        $this->invalidParseErrorTestHandler("\x1B", 1, 'U+001B (C0 control)');
        $this->invalidParseErrorTestHandler("\x1C", 1, 'U+001C (C0 control)');
        $this->invalidParseErrorTestHandler("\x1D", 1, 'U+001D (C0 control)');
        $this->invalidParseErrorTestHandler("\x1E", 1, 'U+001E (C0 control)');
        $this->invalidParseErrorTestHandler("\x1F", 1, 'U+001F (C0 control)');
        
        // DEL (U+007F)
        $this->invalidParseErrorTestHandler("\x7F", 1, 'U+007F');
        
        // C1 Controls
        $this->invalidParseErrorTestHandler("\xC2\x80", 1, 'U+0080 (C1 control)');
        $this->invalidParseErrorTestHandler("\xC2\x9F", 1, 'U+009F (C1 control)');
        $this->invalidParseErrorTestHandler("\xC2\xA0", 0, 'U+00A0 (first codepoint above highest C1 control)');
        
        // Single UTF-16 surrogates
        $this->invalidParseErrorTestHandler("\xED\xA0\x80", 1, 'U+D800 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF", 1, 'U+DB7F (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80", 1, 'U+DB80 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF", 1, 'U+DBFF (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xB0\x80", 1, 'U+DC00 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xBE\x80", 1, 'U+DF80 (UTF-16 surrogate character)');
        $this->invalidParseErrorTestHandler("\xED\xBF\xBF", 1, 'U+DFFF (UTF-16 surrogate character)');
        
        // Paired UTF-16 surrogates
        $this->invalidParseErrorTestHandler("\xED\xA0\x80\xED\xB0\x80", 2, 'U+D800 U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xA0\x80\xED\xBF\xBF", 2, 'U+D800 U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF\xED\xB0\x80", 2, 'U+DB7F U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAD\xBF\xED\xBF\xBF", 2, 'U+DB7F U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80\xED\xB0\x80", 2, 'U+DB80 U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAE\x80\xED\xBF\xBF", 2, 'U+DB80 U+DFFF (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF\xED\xB0\x80", 2, 'U+DBFF U+DC00 (paired UTF-16 surrogates)');
        $this->invalidParseErrorTestHandler("\xED\xAF\xBF\xED\xBF\xBF", 2, 'U+DBFF U+DFFF (paired UTF-16 surrogates)');
        
        // Charcters surrounding surrogates
        $this->invalidParseErrorTestHandler("\xED\x9F\xBF", 0, 'U+D7FF (one codepoint below lowest surrogate codepoint)');
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBD", 0, 'U+DE00 (one codepoint above highest surrogate codepoint)');
        
        // Permanent noncharacters
        $this->invalidParseErrorTestHandler("\xEF\xB7\x90", 1, 'U+FDD0 (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xEF\xB7\xAF", 1, 'U+FDEF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBE", 1, 'U+FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xEF\xBF\xBF", 1, 'U+FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\x9F\xBF\xBE", 1, 'U+1FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\x9F\xBF\xBF", 1, 'U+1FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\xAF\xBF\xBE", 1, 'U+2FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\xAF\xBF\xBF", 1, 'U+2FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\xBF\xBF\xBE", 1, 'U+3FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF0\xBF\xBF\xBF", 1, 'U+3FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\x8F\xBF\xBE", 1, 'U+4FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\x8F\xBF\xBF", 1, 'U+4FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\x9F\xBF\xBE", 1, 'U+5FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\x9F\xBF\xBF", 1, 'U+5FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\xAF\xBF\xBE", 1, 'U+6FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\xAF\xBF\xBF", 1, 'U+6FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\xBF\xBF\xBE", 1, 'U+7FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF1\xBF\xBF\xBF", 1, 'U+7FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\x8F\xBF\xBE", 1, 'U+8FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\x8F\xBF\xBF", 1, 'U+8FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\x9F\xBF\xBE", 1, 'U+9FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\x9F\xBF\xBF", 1, 'U+9FFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\xAF\xBF\xBE", 1, 'U+AFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\xAF\xBF\xBF", 1, 'U+AFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\xBF\xBF\xBE", 1, 'U+BFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF2\xBF\xBF\xBF", 1, 'U+BFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\x8F\xBF\xBE", 1, 'U+CFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\x8F\xBF\xBF", 1, 'U+CFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\x9F\xBF\xBE", 1, 'U+DFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\x9F\xBF\xBF", 1, 'U+DFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\xAF\xBF\xBE", 1, 'U+EFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\xAF\xBF\xBF", 1, 'U+EFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\xBF\xBF\xBE", 1, 'U+FFFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF3\xBF\xBF\xBF", 1, 'U+FFFFF (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF4\x8F\xBF\xBE", 1, 'U+10FFFE (permanent noncharacter)');
        $this->invalidParseErrorTestHandler("\xF4\x8F\xBF\xBF", 1, 'U+10FFFF (permanent noncharacter)');
    }
}
