<?php
namespace HTML5\Parser;

/**
 * The FileInputStream loads a file to be parsed.
 *
 * @todo A buffered input stream would be useful.
 */
class FileInputStream extends StringInputStream implements InputStream {

  /**
   * Load a file input stream.
   * 
   * @param string $data
   *   The file or url path to load.
   */
  function __construct($data, $encoding = 'UTF-8', $debug = '') {

    // Get the contents of the file.
    $content = file_get_contents($data);

    parent::__construct($content, $encoding, $debug);

  }

}