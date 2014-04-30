# Release Notes

1.0.4 (2014-04-29)
- #30/#31 Don't throw an exception for invalid tag names.

1.0.3 (2014-02-28)
- #23 and #29: Ignore attributes with illegal chars in name for the PHP DOM.

1.0.2 (2014-02-12)
- #23: Handle missing tag close in attribute list.
- #25: Fixed text escaping in the serializer (HTML% 8.3).
- #27: Fixed tests on Windows: changed "\n" -> PHP_EOL.
- #28: Fixed infinite loop for char "&" in unquoted attribute in parser.
- #26: Updated tag name case handling to deal with uppercase usage.
- #24: Newlines and tabs are allowed inside quoted attributes (HTML5 8.2.4).
- Fixed Travis CI testing.

1.0.1 (2013-11-07)
- CDATA encoding is improved. (Non-standard; Issue #19)
- Some parser rules were not returning the new current element. (Issue #20)
- Added, to the README, details on code test coverage and to packagist version.
- Fixed processor instructions.
- Improved test coverage and documentation coverage.

1.0.0 (2013-10-02)
- Initial release.
