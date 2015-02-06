<?php
/*
Copyright (C) 2012 - 2013  Kermit Will Richardson, Brimbox LLC

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License Version 3 (GNU GPL v3)
as published by the Free Software Foundation. 

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU GPL v3 for more details. 

You should have received a copy of the GNU GPL v3 along with this program.
If not, see http://www.gnu.org/licenses/
*/

# PHP BOOLEAN PARSER INSTRUCTIONS
#
#    //class invocation 
#    $test = new php_boolean_validator();
#
#    //variables
#    //optional -- whether to splice text tokens with boolean ors
#    $test->splice_or_tokens = true;
#    //optional -- input boolean tokens -- default shown
#    $test->boolean_parse = array('and'=>'&', 'or'=>'|', 'not'=>'!','open'=>'(','closed'=>')');
#    //optional --output boolean tokes -- PHP shown
#    $test->boolean_return = array('and'=>'&&', 'or'=>'||', 'not'=>'!','open'=>'(','closed'=>')');
#
#    //function call -- returns false on successful parse, error message on error
#    //$boolean_string and $boolean_tokens passed as reference
#    //$boolean_string will be formatted on success, untouched on error
#    //$boolean_tokens will be populated with not boolean tokens, partially populated on error
#    //$error_message is false on successful validation, error string on error
#    $error_message = $test->parse_boolean_string(string $boolean_string, optional array $boolean_tokens);
#
# END INSTRUCTIONS


include("boolean_parser.php");

/* TEST AREA 1 */
$boolean_test_array[] = "((test1 & test2) & test3)";
$boolean_test_array[] = "((test1 & ) & test2)";
$boolean_test_array[] = "(test1 test2) & test3";
$boolean_test_array[] = "(test1 & test2) & (test3 | test 4)";
$boolean_test_array[] = "(test1 & test2) & ((test3 | test 4) & test 5)";
$boolean_test_array[] = "(test1 & test2) !& test3";
$boolean_test_array[] = "test1 & test2";
$boolean_test_array[] = "test1 | & test2";
$boolean_test_array[] = "!test1 & test2";
$boolean_test_array[] = "!test1 | test2";
$boolean_test_array[] = "&";
$boolean_test_array[] = "Test1 & !test2)";
$boolean_test_array[] = "Test1 & !test2";
$boolean_test_array[] = "test1 test2 & test3 test4";
$boolean_test_array[] = "!test1 &";

$test = new php_boolean_validator();
$test->splice_or_tokens = true;

echo "<p>PHP BOOLEAN VALIDATOR TEST 1</p>";
echo "<p>Original functionality to check a Postgres full text search input boolean expression splicing conjoining tokens with ORs.</p>";

echo "<table border=1><tr><td>Original String</td><td>Tested String</td><td>Error Message</td></tr>";

foreach ($boolean_test_array as $boolean_string)
	{
    $boolean_input = $boolean_string;
	$error_message = $test->parse_boolean_string($boolean_string);
	echo "<tr><td>" . $boolean_input . "</td><td>" . $boolean_string . "</td><td>" . $error_message . "</td></tr>";	
	}
echo "</table>";
unset($test);
unset($boolean_test_array);
/* END TEST AREA 1 */

/* TEST AREA 2 */
$boolean_test_array[] = "((test1 & test2) & test3)";
$boolean_test_array[] = "((test1 & ) & test2)";
$boolean_test_array[] = "(test1 test2) & test3";
$boolean_test_array[] = "(test1 & test2) & (test3 | test 4)";
$boolean_test_array[] = "(test1 & test2) & ((test3 | test 4) & test 5)";
$boolean_test_array[] = "(test1 & test2) !& test3";
$boolean_test_array[] = "test1 & test2";
$boolean_test_array[] = "test1 | & test2";
$boolean_test_array[] = "!test1 & test2";
$boolean_test_array[] = "&";
$boolean_test_array[] = "Test1 & !test2)";
$boolean_test_array[] = "Test1 & !test2";
$boolean_test_array[] = "test1 test2 & test3 test4";
$boolean_test_array[] = "!test1 &";

$test = new php_boolean_validator();
$test->boolean_return = array('and'=>' AND ', 'or'=>' OR ', 'not'=>' NOT ','open'=>'(','closed'=>')');

echo "<p>PHP BOOLEAN VALIDATOR TEST 2</p>";
echo "<p>This takes single token (&,|,!) boolean operators and substitutes word tokens (AND,OR,NOT) if the parse is successful. Text tokens array returned and shown as string for further parsing.</p>";

echo "<table border=1><tr><td>Original String</td><td>Tested String</td><td>Error Message</td><td>Tokens</td></tr>";

foreach ($boolean_test_array as $boolean_string)
	{
    $boolean_tokens = array();
    $boolean_input = $boolean_string;
	$error_message = $test->parse_boolean_string($boolean_string, $boolean_tokens);
	echo "<tr><td>" . $boolean_input . "</td><td>" . $boolean_string . "</td><td>" . $error_message . "</td><td>" . implode(", ", $boolean_tokens) . "</td></tr>";	
	}
echo "</table>";
unset($test);
unset($boolean_test_array);
/* END TEST AREA 2 */

/* TEST AREA 3 */
$boolean_test_array[] = "((test1 AND test2) AND test3)";
$boolean_test_array[] = "((test1 AND ) AND test2)";
$boolean_test_array[] = "(test1 test2) AND test3";
$boolean_test_array[] = "(test1 AND test2) AND (test3 OR test 4)";
$boolean_test_array[] = "(test1 AND test2) AND ((test3 OR test 4) AND test 5)";
$boolean_test_array[] = "(test1 AND test2) NOT AND test3";
$boolean_test_array[] = "test1 AND test2";
$boolean_test_array[] = "test1 OR AND test2";
$boolean_test_array[] = "NOT test1 AND test2";
$boolean_test_array[] = "AND";
$boolean_test_array[] = "Test1 AND NOT test2)";
$boolean_test_array[] = "Test1 AND NOT test2";
$boolean_test_array[] = "test1 test2 & test3 test4";
$boolean_test_array[] = "NOT test1 AND";

$test = new php_boolean_validator();
$test->boolean_parse = array('and'=>' AND ', 'or'=>' OR ', 'not'=>' NOT ','open'=>'(','closed'=>')');
$test->splice_or_tokens = true;

echo "<p>PHP BOOLEAN VALIDATOR TEST 3</p>";
echo "<p>This takes word token (AND,OR,NOT) boolean operators and parses them. Also splices ORs.</p>";

echo "<table border=1><tr><td>Original String</td><td>Tested String</td><td>Error Message</td></tr>";

foreach ($boolean_test_array as $boolean_string)
	{
    $boolean_input = $boolean_string;
	$error_message = $test->parse_boolean_string($boolean_string);
	echo "<tr><td>" . $boolean_input . "</td><td>" . $boolean_string . "</td><td>" . $error_message . "</td></tr>";	
	}
echo "</table>";
unset($test);
unset($boolean_test_array);
/* TEST AREA 3 */

/* TEST AREA 4 */
$boolean_test_array[] = "((test1 AND test2) AND ||)";
$boolean_test_array[] = "((test1 AND ) AND test2)";
$boolean_test_array[] = "(test1 test2) AND test3";
$boolean_test_array[] = "(test1 AND test2) AND (test3 OR test 4)";
$boolean_test_array[] = "(test1 AND test2) AND ((test3 OR test 4) AND test 5)";
$boolean_test_array[] = "(test1 AND test2) NOT AND test3";
$boolean_test_array[] = "test1 AND test2";
$boolean_test_array[] = "test1 OR AND test2";
$boolean_test_array[] = "NOT test1 AND test2";
$boolean_test_array[] = "AND";
$boolean_test_array[] = "Test1 AND NOT test2)";
$boolean_test_array[] = "Test1 AND NOT test2";
$boolean_test_array[] = "test1 test2 AND test3 test4";
$boolean_test_array[] = "NOT test1 AND";

$test = new php_boolean_validator();
$test->boolean_parse = array('and'=>' AND ', 'or'=>' OR ', 'not'=>' NOT ','open'=>'(','closed'=>')');
$test->boolean_return = array('and'=>'&&', 'or'=>'||', 'not'=>'!','open'=>'(','closed'=>')');

echo "<p>PHP BOOLEAN VALIDATOR TEST 4</p>";
echo "<p>This takes word token (AND,OR,NOT) boolean operators and substitutes PHP tokens (&&,||,!) if the parse is successful.</p>";

echo "<table border=1><tr><td>Original String</td><td>Tested String</td><td>Error Message</td></tr>";

foreach ($boolean_test_array as $boolean_string)
	{
    $boolean_input = $boolean_string;
	$error_message = $test->parse_boolean_string($boolean_string);
	echo "<tr><td>" . $boolean_input . "</td><td>" . $boolean_string . "</td><td>" . $error_message . "</td></tr>";	
	}
echo "</table>";
unset($test);
unset($boolean_test_array);
/* TEST AREA 4 */


?>