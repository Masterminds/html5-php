<?php
namespace HTML5\Parser;

/**
 * The FileInputStream loads a file to be parsed.
 *
 * @todo A buffered input stream would be useful.
 */
class FileInputStream extends StringInputStream implements InputStream {

  /*
   * So right now we read files into strings and then process the 
   * string. We chose to do this largely for the sake of expediency of 
   * development, and also because we could optimize toward processing 
   * arbitrarily large chunks of the input. But in the future, we'd 
   * really like to rewrite this class to efficiently handle lower level 
   * stream reads (and thus efficiently handle large documents).
   */

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
