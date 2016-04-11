
(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:Masterminds" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Masterminds.html">Masterminds</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Masterminds_HTML5" >                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Masterminds/HTML5.html">HTML5</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Masterminds_HTML5_Parser" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Masterminds/HTML5/Parser.html">Parser</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Masterminds_HTML5_Parser_CharacterReference" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/CharacterReference.html">CharacterReference</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_DOMTreeBuilder" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/DOMTreeBuilder.html">DOMTreeBuilder</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_EventHandler" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/EventHandler.html">EventHandler</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_FileInputStream" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/FileInputStream.html">FileInputStream</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_InputStream" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/InputStream.html">InputStream</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_ParseError" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/ParseError.html">ParseError</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_Scanner" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/Scanner.html">Scanner</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_StringInputStream" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/StringInputStream.html">StringInputStream</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_Tokenizer" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/Tokenizer.html">Tokenizer</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_TreeBuildingRules" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/TreeBuildingRules.html">TreeBuildingRules</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Parser_UTF8Utils" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Parser/UTF8Utils.html">UTF8Utils</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Masterminds_HTML5_Serializer" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Masterminds/HTML5/Serializer.html">Serializer</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Masterminds_HTML5_Serializer_HTML5Entities" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Serializer/HTML5Entities.html">HTML5Entities</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Serializer_OutputRules" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Serializer/OutputRules.html">OutputRules</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Serializer_RulesInterface" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Serializer/RulesInterface.html">RulesInterface</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Serializer_Traverser" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Masterminds/HTML5/Serializer/Traverser.html">Traverser</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Masterminds_HTML5_Elements" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Masterminds/HTML5/Elements.html">Elements</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Entities" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Masterminds/HTML5/Entities.html">Entities</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_Exception" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Masterminds/HTML5/Exception.html">Exception</a>                    </div>                </li>                            <li data-name="class:Masterminds_HTML5_InstructionProcessor" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Masterminds/HTML5/InstructionProcessor.html">InstructionProcessor</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Masterminds_HTML5" >                    <div style="padding-left:26px" class="hd leaf">                        <a href="Masterminds/HTML5.html">HTML5</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "Masterminds.html", "name": "Masterminds", "doc": "Namespace Masterminds"},{"type": "Namespace", "link": "Masterminds/HTML5.html", "name": "Masterminds\\HTML5", "doc": "Namespace Masterminds\\HTML5"},{"type": "Namespace", "link": "Masterminds/HTML5/Parser.html", "name": "Masterminds\\HTML5\\Parser", "doc": "Namespace Masterminds\\HTML5\\Parser"},{"type": "Namespace", "link": "Masterminds/HTML5/Serializer.html", "name": "Masterminds\\HTML5\\Serializer", "doc": "Namespace Masterminds\\HTML5\\Serializer"},
            {"type": "Interface", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5/InstructionProcessor.html", "name": "Masterminds\\HTML5\\InstructionProcessor", "doc": "&quot;Provide an processor to handle embedded instructions.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\InstructionProcessor", "fromLink": "Masterminds/HTML5/InstructionProcessor.html", "link": "Masterminds/HTML5/InstructionProcessor.html#method_process", "name": "Masterminds\\HTML5\\InstructionProcessor::process", "doc": "&quot;Process an individual processing instruction.&quot;"},
            
            {"type": "Interface", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/EventHandler.html", "name": "Masterminds\\HTML5\\Parser\\EventHandler", "doc": "&quot;Standard events for HTML5.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_doctype", "name": "Masterminds\\HTML5\\Parser\\EventHandler::doctype", "doc": "&quot;A doctype declaration.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_startTag", "name": "Masterminds\\HTML5\\Parser\\EventHandler::startTag", "doc": "&quot;A start tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_endTag", "name": "Masterminds\\HTML5\\Parser\\EventHandler::endTag", "doc": "&quot;An end-tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_comment", "name": "Masterminds\\HTML5\\Parser\\EventHandler::comment", "doc": "&quot;A comment section (unparsed character data).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_text", "name": "Masterminds\\HTML5\\Parser\\EventHandler::text", "doc": "&quot;A unit of parsed character data.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_eof", "name": "Masterminds\\HTML5\\Parser\\EventHandler::eof", "doc": "&quot;Indicates that the document has been entirely processed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_parseError", "name": "Masterminds\\HTML5\\Parser\\EventHandler::parseError", "doc": "&quot;Emitted when the parser encounters an error condition.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_cdata", "name": "Masterminds\\HTML5\\Parser\\EventHandler::cdata", "doc": "&quot;A CDATA section.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_processingInstruction", "name": "Masterminds\\HTML5\\Parser\\EventHandler::processingInstruction", "doc": "&quot;This is a holdover from the XML spec.&quot;"},
            
            {"type": "Interface", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/InputStream.html", "name": "Masterminds\\HTML5\\Parser\\InputStream", "doc": "&quot;Interface for stream readers.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_currentLine", "name": "Masterminds\\HTML5\\Parser\\InputStream::currentLine", "doc": "&quot;Returns the current line that is being consumed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_columnOffset", "name": "Masterminds\\HTML5\\Parser\\InputStream::columnOffset", "doc": "&quot;Returns the current column of the current line that the tokenizer is at.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_remainingChars", "name": "Masterminds\\HTML5\\Parser\\InputStream::remainingChars", "doc": "&quot;Get all characters until EOF.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_charsUntil", "name": "Masterminds\\HTML5\\Parser\\InputStream::charsUntil", "doc": "&quot;Read to a particular match (or until $max bytes are consumed).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_charsWhile", "name": "Masterminds\\HTML5\\Parser\\InputStream::charsWhile", "doc": "&quot;Returns the string so long as $bytes matches.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_unconsume", "name": "Masterminds\\HTML5\\Parser\\InputStream::unconsume", "doc": "&quot;Unconsume one character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_peek", "name": "Masterminds\\HTML5\\Parser\\InputStream::peek", "doc": "&quot;Retrieve the next character without advancing the pointer.&quot;"},
            
            {"type": "Interface", "fromName": "Masterminds\\HTML5\\Serializer", "fromLink": "Masterminds/HTML5/Serializer.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface", "doc": "&quot;To create a new rule set for writing output the RulesInterface needs to be\nimplemented.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method___construct", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::__construct", "doc": "&quot;The class constructor.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_setTraverser", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::setTraverser", "doc": "&quot;Register the traverser used in but the rules.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_document", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::document", "doc": "&quot;Write a document element (\\DOMDocument).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_element", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::element", "doc": "&quot;Write an element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_text", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::text", "doc": "&quot;Write a text node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_cdata", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::cdata", "doc": "&quot;Write a CDATA node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_comment", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::comment", "doc": "&quot;Write a comment node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_processorInstruction", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::processorInstruction", "doc": "&quot;Write a processor instruction.&quot;"},
            
            
            {"type": "Class", "fromName": "Masterminds", "fromLink": "Masterminds.html", "link": "Masterminds/HTML5.html", "name": "Masterminds\\HTML5", "doc": "&quot;This class offers convenience methods for parsing and serializing HTML5.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method___construct", "name": "Masterminds\\HTML5::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_getOptions", "name": "Masterminds\\HTML5::getOptions", "doc": "&quot;Get the default options.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_load", "name": "Masterminds\\HTML5::load", "doc": "&quot;Load and parse an HTML file.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_loadHTML", "name": "Masterminds\\HTML5::loadHTML", "doc": "&quot;Parse a HTML Document from a string.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_loadHTMLFile", "name": "Masterminds\\HTML5::loadHTMLFile", "doc": "&quot;Convenience function to load an HTML file.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_loadHTMLFragment", "name": "Masterminds\\HTML5::loadHTMLFragment", "doc": "&quot;Parse a HTML fragment from a string.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_getErrors", "name": "Masterminds\\HTML5::getErrors", "doc": "&quot;Return all errors encountered into parsing phase&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_hasErrors", "name": "Masterminds\\HTML5::hasErrors", "doc": "&quot;Return true it some errors were encountered into parsing phase&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_parse", "name": "Masterminds\\HTML5::parse", "doc": "&quot;Parse an input stream.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_parseFragment", "name": "Masterminds\\HTML5::parseFragment", "doc": "&quot;Parse an input stream where the stream is a fragment.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_save", "name": "Masterminds\\HTML5::save", "doc": "&quot;Save a DOM into a given file as HTML5.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5.html#method_saveHTML", "name": "Masterminds\\HTML5::saveHTML", "doc": "&quot;Convert a DOM into an HTML5 string.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5/Elements.html", "name": "Masterminds\\HTML5\\Elements", "doc": "&quot;This class provides general information about HTML5 elements,\nincluding syntactic and semantic issues.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_isA", "name": "Masterminds\\HTML5\\Elements::isA", "doc": "&quot;Check whether the given element meets the given criterion.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_isHtml5Element", "name": "Masterminds\\HTML5\\Elements::isHtml5Element", "doc": "&quot;Test if an element is a valid html5 element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_isMathMLElement", "name": "Masterminds\\HTML5\\Elements::isMathMLElement", "doc": "&quot;Test if an element name is a valid MathML presentation element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_isSvgElement", "name": "Masterminds\\HTML5\\Elements::isSvgElement", "doc": "&quot;Test if an element is a valid SVG element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_isElement", "name": "Masterminds\\HTML5\\Elements::isElement", "doc": "&quot;Is an element name valid in an html5 document.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_element", "name": "Masterminds\\HTML5\\Elements::element", "doc": "&quot;Get the element mask for the given element name.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_normalizeSvgElement", "name": "Masterminds\\HTML5\\Elements::normalizeSvgElement", "doc": "&quot;Normalize a SVG element name to its proper case and form.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_normalizeSvgAttribute", "name": "Masterminds\\HTML5\\Elements::normalizeSvgAttribute", "doc": "&quot;Normalize a SVG attribute name to its proper case and form.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Elements", "fromLink": "Masterminds/HTML5/Elements.html", "link": "Masterminds/HTML5/Elements.html#method_normalizeMathMlAttribute", "name": "Masterminds\\HTML5\\Elements::normalizeMathMlAttribute", "doc": "&quot;Normalize a MathML attribute name to its proper case and form.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5/Entities.html", "name": "Masterminds\\HTML5\\Entities", "doc": "&quot;Entity lookup tables.&quot;"},
                    
            {"type": "Class", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5/Exception.html", "name": "Masterminds\\HTML5\\Exception", "doc": "&quot;The base exception for the HTML5 project.&quot;"},
                    
            {"type": "Class", "fromName": "Masterminds\\HTML5", "fromLink": "Masterminds/HTML5.html", "link": "Masterminds/HTML5/InstructionProcessor.html", "name": "Masterminds\\HTML5\\InstructionProcessor", "doc": "&quot;Provide an processor to handle embedded instructions.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\InstructionProcessor", "fromLink": "Masterminds/HTML5/InstructionProcessor.html", "link": "Masterminds/HTML5/InstructionProcessor.html#method_process", "name": "Masterminds\\HTML5\\InstructionProcessor::process", "doc": "&quot;Process an individual processing instruction.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/CharacterReference.html", "name": "Masterminds\\HTML5\\Parser\\CharacterReference", "doc": "&quot;Manage entity references.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\CharacterReference", "fromLink": "Masterminds/HTML5/Parser/CharacterReference.html", "link": "Masterminds/HTML5/Parser/CharacterReference.html#method_lookupName", "name": "Masterminds\\HTML5\\Parser\\CharacterReference::lookupName", "doc": "&quot;Given a name (e.g.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\CharacterReference", "fromLink": "Masterminds/HTML5/Parser/CharacterReference.html", "link": "Masterminds/HTML5/Parser/CharacterReference.html#method_lookupDecimal", "name": "Masterminds\\HTML5\\Parser\\CharacterReference::lookupDecimal", "doc": "&quot;Given a decimal number, return the UTF-8 character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\CharacterReference", "fromLink": "Masterminds/HTML5/Parser/CharacterReference.html", "link": "Masterminds/HTML5/Parser/CharacterReference.html#method_lookupHex", "name": "Masterminds\\HTML5\\Parser\\CharacterReference::lookupHex", "doc": "&quot;Given a hexidecimal number, return the UTF-8 character.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "doc": "&quot;Create an HTML5 DOM tree from events.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_document", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::document", "doc": "&quot;Get the document.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_fragment", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::fragment", "doc": "&quot;Get the DOM fragment for the body.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_setInstructionProcessor", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::setInstructionProcessor", "doc": "&quot;Provide an instruction processor.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_doctype", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::doctype", "doc": "&quot;A doctype declaration.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_startTag", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::startTag", "doc": "&quot;Process the start tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_endTag", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::endTag", "doc": "&quot;An end-tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_comment", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::comment", "doc": "&quot;A comment section (unparsed character data).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_text", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::text", "doc": "&quot;A unit of parsed character data.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_eof", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::eof", "doc": "&quot;Indicates that the document has been entirely processed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_parseError", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::parseError", "doc": "&quot;Emitted when the parser encounters an error condition.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_getErrors", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::getErrors", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_cdata", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::cdata", "doc": "&quot;A CDATA section.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder", "fromLink": "Masterminds/HTML5/Parser/DOMTreeBuilder.html", "link": "Masterminds/HTML5/Parser/DOMTreeBuilder.html#method_processingInstruction", "name": "Masterminds\\HTML5\\Parser\\DOMTreeBuilder::processingInstruction", "doc": "&quot;This is a holdover from the XML spec.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/EventHandler.html", "name": "Masterminds\\HTML5\\Parser\\EventHandler", "doc": "&quot;Standard events for HTML5.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_doctype", "name": "Masterminds\\HTML5\\Parser\\EventHandler::doctype", "doc": "&quot;A doctype declaration.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_startTag", "name": "Masterminds\\HTML5\\Parser\\EventHandler::startTag", "doc": "&quot;A start tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_endTag", "name": "Masterminds\\HTML5\\Parser\\EventHandler::endTag", "doc": "&quot;An end-tag.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_comment", "name": "Masterminds\\HTML5\\Parser\\EventHandler::comment", "doc": "&quot;A comment section (unparsed character data).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_text", "name": "Masterminds\\HTML5\\Parser\\EventHandler::text", "doc": "&quot;A unit of parsed character data.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_eof", "name": "Masterminds\\HTML5\\Parser\\EventHandler::eof", "doc": "&quot;Indicates that the document has been entirely processed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_parseError", "name": "Masterminds\\HTML5\\Parser\\EventHandler::parseError", "doc": "&quot;Emitted when the parser encounters an error condition.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_cdata", "name": "Masterminds\\HTML5\\Parser\\EventHandler::cdata", "doc": "&quot;A CDATA section.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\EventHandler", "fromLink": "Masterminds/HTML5/Parser/EventHandler.html", "link": "Masterminds/HTML5/Parser/EventHandler.html#method_processingInstruction", "name": "Masterminds\\HTML5\\Parser\\EventHandler::processingInstruction", "doc": "&quot;This is a holdover from the XML spec.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/FileInputStream.html", "name": "Masterminds\\HTML5\\Parser\\FileInputStream", "doc": "&quot;The FileInputStream loads a file to be parsed.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\FileInputStream", "fromLink": "Masterminds/HTML5/Parser/FileInputStream.html", "link": "Masterminds/HTML5/Parser/FileInputStream.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\FileInputStream::__construct", "doc": "&quot;Load a file input stream.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/InputStream.html", "name": "Masterminds\\HTML5\\Parser\\InputStream", "doc": "&quot;Interface for stream readers.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_currentLine", "name": "Masterminds\\HTML5\\Parser\\InputStream::currentLine", "doc": "&quot;Returns the current line that is being consumed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_columnOffset", "name": "Masterminds\\HTML5\\Parser\\InputStream::columnOffset", "doc": "&quot;Returns the current column of the current line that the tokenizer is at.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_remainingChars", "name": "Masterminds\\HTML5\\Parser\\InputStream::remainingChars", "doc": "&quot;Get all characters until EOF.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_charsUntil", "name": "Masterminds\\HTML5\\Parser\\InputStream::charsUntil", "doc": "&quot;Read to a particular match (or until $max bytes are consumed).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_charsWhile", "name": "Masterminds\\HTML5\\Parser\\InputStream::charsWhile", "doc": "&quot;Returns the string so long as $bytes matches.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_unconsume", "name": "Masterminds\\HTML5\\Parser\\InputStream::unconsume", "doc": "&quot;Unconsume one character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\InputStream", "fromLink": "Masterminds/HTML5/Parser/InputStream.html", "link": "Masterminds/HTML5/Parser/InputStream.html#method_peek", "name": "Masterminds\\HTML5\\Parser\\InputStream::peek", "doc": "&quot;Retrieve the next character without advancing the pointer.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/ParseError.html", "name": "Masterminds\\HTML5\\Parser\\ParseError", "doc": "&quot;Emit when the parser has an error.&quot;"},
                    
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/Scanner.html", "name": "Masterminds\\HTML5\\Parser\\Scanner", "doc": "&quot;The scanner.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\Scanner::__construct", "doc": "&quot;Create a new Scanner.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_position", "name": "Masterminds\\HTML5\\Parser\\Scanner::position", "doc": "&quot;Get the current position.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_peek", "name": "Masterminds\\HTML5\\Parser\\Scanner::peek", "doc": "&quot;Take a peek at the next character in the data.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_next", "name": "Masterminds\\HTML5\\Parser\\Scanner::next", "doc": "&quot;Get the next character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_current", "name": "Masterminds\\HTML5\\Parser\\Scanner::current", "doc": "&quot;Get the current character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_consume", "name": "Masterminds\\HTML5\\Parser\\Scanner::consume", "doc": "&quot;Silently consume N chars.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_unconsume", "name": "Masterminds\\HTML5\\Parser\\Scanner::unconsume", "doc": "&quot;Unconsume some of the data.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_getHex", "name": "Masterminds\\HTML5\\Parser\\Scanner::getHex", "doc": "&quot;Get the next group of that contains hex characters.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_getAsciiAlpha", "name": "Masterminds\\HTML5\\Parser\\Scanner::getAsciiAlpha", "doc": "&quot;Get the next group of characters that are ASCII Alpha characters.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_getAsciiAlphaNum", "name": "Masterminds\\HTML5\\Parser\\Scanner::getAsciiAlphaNum", "doc": "&quot;Get the next group of characters that are ASCII Alpha characters and numbers.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_getNumeric", "name": "Masterminds\\HTML5\\Parser\\Scanner::getNumeric", "doc": "&quot;Get the next group of numbers.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_whitespace", "name": "Masterminds\\HTML5\\Parser\\Scanner::whitespace", "doc": "&quot;Consume whitespace.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_currentLine", "name": "Masterminds\\HTML5\\Parser\\Scanner::currentLine", "doc": "&quot;Returns the current line that is being consumed.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_charsUntil", "name": "Masterminds\\HTML5\\Parser\\Scanner::charsUntil", "doc": "&quot;Read chars until something in the mask is encountered.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_charsWhile", "name": "Masterminds\\HTML5\\Parser\\Scanner::charsWhile", "doc": "&quot;Read chars as long as the mask matches.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_columnOffset", "name": "Masterminds\\HTML5\\Parser\\Scanner::columnOffset", "doc": "&quot;Returns the current column of the current line that the tokenizer is at.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Scanner", "fromLink": "Masterminds/HTML5/Parser/Scanner.html", "link": "Masterminds/HTML5/Parser/Scanner.html#method_remainingChars", "name": "Masterminds\\HTML5\\Parser\\Scanner::remainingChars", "doc": "&quot;Get all characters until EOF.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html", "name": "Masterminds\\HTML5\\Parser\\StringInputStream", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::__construct", "doc": "&quot;Create a new InputStream wrapper.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_currentLine", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::currentLine", "doc": "&quot;Returns the current line that the tokenizer is at.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_getCurrentLine", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::getCurrentLine", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_columnOffset", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::columnOffset", "doc": "&quot;Returns the current column of the current line that the tokenizer is at.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_getColumnOffset", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::getColumnOffset", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_current", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::current", "doc": "&quot;Get the current character.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_next", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::next", "doc": "&quot;Advance the pointer.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_rewind", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::rewind", "doc": "&quot;Rewind to the start of the string.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_valid", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::valid", "doc": "&quot;Is the current pointer location valid.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_remainingChars", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::remainingChars", "doc": "&quot;Get all characters until EOF.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_charsUntil", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::charsUntil", "doc": "&quot;Read to a particular match (or until $max bytes are consumed).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_charsWhile", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::charsWhile", "doc": "&quot;Returns the string so long as $bytes matches.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_unconsume", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::unconsume", "doc": "&quot;Unconsume characters.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_peek", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::peek", "doc": "&quot;Look ahead without moving cursor.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\StringInputStream", "fromLink": "Masterminds/HTML5/Parser/StringInputStream.html", "link": "Masterminds/HTML5/Parser/StringInputStream.html#method_key", "name": "Masterminds\\HTML5\\Parser\\StringInputStream::key", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/Tokenizer.html", "name": "Masterminds\\HTML5\\Parser\\Tokenizer", "doc": "&quot;The HTML5 tokenizer.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Tokenizer", "fromLink": "Masterminds/HTML5/Parser/Tokenizer.html", "link": "Masterminds/HTML5/Parser/Tokenizer.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\Tokenizer::__construct", "doc": "&quot;Create a new tokenizer.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Tokenizer", "fromLink": "Masterminds/HTML5/Parser/Tokenizer.html", "link": "Masterminds/HTML5/Parser/Tokenizer.html#method_parse", "name": "Masterminds\\HTML5\\Parser\\Tokenizer::parse", "doc": "&quot;Begin parsing.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\Tokenizer", "fromLink": "Masterminds/HTML5/Parser/Tokenizer.html", "link": "Masterminds/HTML5/Parser/Tokenizer.html#method_setTextMode", "name": "Masterminds\\HTML5\\Parser\\Tokenizer::setTextMode", "doc": "&quot;Set the text mode for the character data reader.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/TreeBuildingRules.html", "name": "Masterminds\\HTML5\\Parser\\TreeBuildingRules", "doc": "&quot;Handles special-case rules for the DOM tree builder.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\TreeBuildingRules", "fromLink": "Masterminds/HTML5/Parser/TreeBuildingRules.html", "link": "Masterminds/HTML5/Parser/TreeBuildingRules.html#method___construct", "name": "Masterminds\\HTML5\\Parser\\TreeBuildingRules::__construct", "doc": "&quot;Build a new rules engine.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\TreeBuildingRules", "fromLink": "Masterminds/HTML5/Parser/TreeBuildingRules.html", "link": "Masterminds/HTML5/Parser/TreeBuildingRules.html#method_hasRules", "name": "Masterminds\\HTML5\\Parser\\TreeBuildingRules::hasRules", "doc": "&quot;Returns true if the given tagname has special processing rules.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\TreeBuildingRules", "fromLink": "Masterminds/HTML5/Parser/TreeBuildingRules.html", "link": "Masterminds/HTML5/Parser/TreeBuildingRules.html#method_evaluate", "name": "Masterminds\\HTML5\\Parser\\TreeBuildingRules::evaluate", "doc": "&quot;Evaluate the rule for the current tag name.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Parser", "fromLink": "Masterminds/HTML5/Parser.html", "link": "Masterminds/HTML5/Parser/UTF8Utils.html", "name": "Masterminds\\HTML5\\Parser\\UTF8Utils", "doc": "&quot;UTF-8 Utilities&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\UTF8Utils", "fromLink": "Masterminds/HTML5/Parser/UTF8Utils.html", "link": "Masterminds/HTML5/Parser/UTF8Utils.html#method_countChars", "name": "Masterminds\\HTML5\\Parser\\UTF8Utils::countChars", "doc": "&quot;Count the number of characters in a string.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\UTF8Utils", "fromLink": "Masterminds/HTML5/Parser/UTF8Utils.html", "link": "Masterminds/HTML5/Parser/UTF8Utils.html#method_convertToUTF8", "name": "Masterminds\\HTML5\\Parser\\UTF8Utils::convertToUTF8", "doc": "&quot;Convert data from the given encoding to UTF-8.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Parser\\UTF8Utils", "fromLink": "Masterminds/HTML5/Parser/UTF8Utils.html", "link": "Masterminds/HTML5/Parser/UTF8Utils.html#method_checkForIllegalCodepoints", "name": "Masterminds\\HTML5\\Parser\\UTF8Utils::checkForIllegalCodepoints", "doc": "&quot;Checks for Unicode code points that are not valid in a document.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Serializer", "fromLink": "Masterminds/HTML5/Serializer.html", "link": "Masterminds/HTML5/Serializer/HTML5Entities.html", "name": "Masterminds\\HTML5\\Serializer\\HTML5Entities", "doc": "&quot;A mapping of entities to their html5 representation.&quot;"},
                    
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Serializer", "fromLink": "Masterminds/HTML5/Serializer.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html", "name": "Masterminds\\HTML5\\Serializer\\OutputRules", "doc": "&quot;Generate the output html5 based on element rules.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method___construct", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::__construct", "doc": "&quot;The class constructor.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_addRule", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::addRule", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_setTraverser", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::setTraverser", "doc": "&quot;Register the traverser used in but the rules.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_document", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::document", "doc": "&quot;Write a document element (\\DOMDocument).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_element", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::element", "doc": "&quot;Write an element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_text", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::text", "doc": "&quot;Write a text node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_cdata", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::cdata", "doc": "&quot;Write a CDATA node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_comment", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::comment", "doc": "&quot;Write a comment node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\OutputRules", "fromLink": "Masterminds/HTML5/Serializer/OutputRules.html", "link": "Masterminds/HTML5/Serializer/OutputRules.html#method_processorInstruction", "name": "Masterminds\\HTML5\\Serializer\\OutputRules::processorInstruction", "doc": "&quot;Write a processor instruction.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Serializer", "fromLink": "Masterminds/HTML5/Serializer.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface", "doc": "&quot;To create a new rule set for writing output the RulesInterface needs to be\nimplemented.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method___construct", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::__construct", "doc": "&quot;The class constructor.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_setTraverser", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::setTraverser", "doc": "&quot;Register the traverser used in but the rules.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_document", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::document", "doc": "&quot;Write a document element (\\DOMDocument).&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_element", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::element", "doc": "&quot;Write an element.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_text", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::text", "doc": "&quot;Write a text node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_cdata", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::cdata", "doc": "&quot;Write a CDATA node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_comment", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::comment", "doc": "&quot;Write a comment node.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\RulesInterface", "fromLink": "Masterminds/HTML5/Serializer/RulesInterface.html", "link": "Masterminds/HTML5/Serializer/RulesInterface.html#method_processorInstruction", "name": "Masterminds\\HTML5\\Serializer\\RulesInterface::processorInstruction", "doc": "&quot;Write a processor instruction.&quot;"},
            
            {"type": "Class", "fromName": "Masterminds\\HTML5\\Serializer", "fromLink": "Masterminds/HTML5/Serializer.html", "link": "Masterminds/HTML5/Serializer/Traverser.html", "name": "Masterminds\\HTML5\\Serializer\\Traverser", "doc": "&quot;Traverser for walking a DOM tree.&quot;"},
                                                        {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\Traverser", "fromLink": "Masterminds/HTML5/Serializer/Traverser.html", "link": "Masterminds/HTML5/Serializer/Traverser.html#method___construct", "name": "Masterminds\\HTML5\\Serializer\\Traverser::__construct", "doc": "&quot;Create a traverser.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\Traverser", "fromLink": "Masterminds/HTML5/Serializer/Traverser.html", "link": "Masterminds/HTML5/Serializer/Traverser.html#method_walk", "name": "Masterminds\\HTML5\\Serializer\\Traverser::walk", "doc": "&quot;Tell the traverser to walk the DOM.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\Traverser", "fromLink": "Masterminds/HTML5/Serializer/Traverser.html", "link": "Masterminds/HTML5/Serializer/Traverser.html#method_node", "name": "Masterminds\\HTML5\\Serializer\\Traverser::node", "doc": "&quot;Process a node in the DOM.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\Traverser", "fromLink": "Masterminds/HTML5/Serializer/Traverser.html", "link": "Masterminds/HTML5/Serializer/Traverser.html#method_children", "name": "Masterminds\\HTML5\\Serializer\\Traverser::children", "doc": "&quot;Walk through all the nodes on a node list.&quot;"},
                    {"type": "Method", "fromName": "Masterminds\\HTML5\\Serializer\\Traverser", "fromLink": "Masterminds/HTML5/Serializer/Traverser.html", "link": "Masterminds/HTML5/Serializer/Traverser.html#method_isLocalElement", "name": "Masterminds\\HTML5\\Serializer\\Traverser::isLocalElement", "doc": "&quot;Is an element local?&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


