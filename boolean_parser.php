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
#function parse_boolean_string($boolean_string, $boolean_tokens)
#	{
#	returns error message or true on error, returns false on successful parse
#	passes $boolean_string as value, $boolean_string is formatted on success, left unchanged on failure
#	}
# END INSTRUCTIONS

/* TEST AREA */
$boolean_test_array[] = "((test1 & test2) & test3)";
$boolean_test_array[] = "((test1 & ) & test2)";
$boolean_test_array[] = "(test1 & test2) & test3";
$boolean_test_array[] = "(test1 & test2) & (test3 | test 4)";
$boolean_test_array[] = "(test1 & test2) & ((test3 | test 4) & test 5)";
$boolean_test_array[] = "(test1 AND test2) AND ((test3 OR test 4) AND test 5)";
$boolean_test_array[] = "(test1 & test2) !& test3";
$boolean_test_array[] = "test1 & test2";
$boolean_test_array[] = "test1 | & test2";
$boolean_test_array[] = "&";
$boolean_test_array[] = "Test1 & !test2)";

echo "<p>PHP BOOLEAN PARSER</p>";
echo "<table border=1><tr><td>Boolean String</td><td>Error Message</td></tr>";

foreach ($boolean_test_array as $boolean_string)
	{
	$error_message = parse_boolean_string($boolean_string, array());
	echo "<tr><td>" . $boolean_string . "</td><td>" . $error_message . "</td></tr>";	
	}
echo "</table>";
/* END TEST AREA */

/* MAIN FUNCTION */
function parse_boolean_string(&$boolean_string, &$boolean_tokens) {

	/* CONTAINS FOUR WATERFALL RETURNS */
	if (trim($boolean_string) == "") 
		{
		//return and exit on empty string
		return "Enter Search Terms or Tokens";
		}
	
	/* TOKENIZE BOOLEAN STRING */
	//purge unwanted chars
	$boolean_string = str_replace(array("\r","\n","\t"), "", $boolean_string);
	//replace plain language boolean with character representation
	$boolean_string = str_replace(" AND ", " & ", $boolean_string);
	$boolean_string = str_replace(" OR ", " | ", $boolean_string);
	$boolean_string = str_replace(" NOT ", " ! ", $boolean_string);
	//split up tokens and operators
	$tokens = preg_split('/([\|&!\)\(\s])/', $boolean_string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	//get rid of space tokens
	$tokens = array_diff($tokens, array(" "));
	//re-increment array
	$tokens = array_merge($tokens);
	//use new line as eol
	array_push($tokens, "\n");
		
	/* CHECK FOR UNBALANCED PARENTHESIS */
	$i = 0; //count parenthesis
	foreach ($tokens as $token)
		{
		if ($token == ")")
			{
			$i++;	
			}
		elseif ($token == "(")
			{
			$i--;	
			}
		}		
	//return and exit on unbalanced parenthesis	
	if ($i <> 0)
		{
		return "Search term(s) have unbalanced parenthesis";	
		}
			
	//SPLICE CONJOINING TOKENS WITH PIPE FOR OR
	$arr_splice = array();
	$path = "/[^&!\|\(\)\\n]/";
	for ($i=1; $i<count($tokens); $i++)
		{
		if (preg_match($path, $tokens[$i-1]) && preg_match($path, $tokens[$i]))
			{
			array_push($arr_splice, $i);	
			}
		}
	$i = 0; //increase of offset when splicing
	foreach ($arr_splice as $key)
		{
		array_splice($tokens,$key+$i,0,"|");
		$i++;
		}
		
	/* ENTER RECURSIVE DESCENT PARSER */
	//message and tokens passed by value
	$i = 0;	//token position	
	$next = $tokens[$i];
	//deal with first token
	if (preg_match("/[^&!\|\(\)\\n]/", $next))
		{
		//pointer is a token
		$boolean_tokens[] = $next;
		closed($tokens, $next ,$i, $message, $boolean_tokens); 
		}
	elseif (preg_match("/\({1}/", $next))
		{
		//pointer is an open parenthesis
		open($tokens, $next ,$i, $message, $boolean_tokens);
		}	
	elseif (preg_match("/!{1}/", $next))
		{
		//pointer is a NOT
		not($tokens, $next ,$i, $message, $boolean_tokens);   
		}
	else
		{
		$message = "Error in boolean expression at token 1 near " . $next;	
		}
	
	/* RETURN ERROR AFTER DESCENT */
	if ($message) //error in boolean expression
		{
		//return and exit on populated error message
		return $message;
		}
	
	/* SUCCESSFUL PARSE - IMPLODE, TRIM AND RETURN FALSE */
	//$boolean_string passed as a value, trim off new line
	$boolean_string = trim(implode($tokens));

	return false;
	}
/* END MAIN FUNCTION */
 
/* RECURSIVE DESCENT PARSING FUNCTIONS */
function not($tokens, &$next, &$i, &$message, &$boolean_tokens)
	{
	//comes from a NOT
	$i++;
	$next = $tokens[$i];
	if (preg_match("/[^&!\|\(\)\\n]/", $next))
		{
		//pointer is a token
		$boolean_tokens[] = $next;
		closed($tokens, $next ,$i, $message, $boolean_tokens); 
		}
	elseif (preg_match("/\({1}/", $next))
		{
		//pointer is an open parenthesis
		open($tokens, $next ,$i, $message, $boolean_tokens);
		}	
	elseif (preg_match("/!{1}/", $next))
		{
		//pointer is a NOT
		not($tokens, $next ,$i, $message, $boolean_tokens);   
		}
	else
		{
		//error found
		$message = ($tokens[$i] == "\n") ? "Error near end of boolean expression" : "Error in boolean expression at token " . ($i+1). " near " . $next;
		}
	}
	
function open($tokens, &$next, &$i, &$message, &$boolean_tokens)
	{
	//comes from an open parenthesis
	$i++;
	$next = $tokens[$i];
	if (preg_match("/\({1}/", $next))
		{
		//pointer is open parenthesis
		open($tokens, $next ,$i, $message, $boolean_tokens);
		}
	elseif (preg_match("/!{1}/", $next))
		{
		//pointer is a NOT
		not($tokens, $next ,$i, $message, $boolean_tokens);
		}	
	elseif (preg_match("/[^&!\|\(\)\\n]/", $next))
		{
		//pointer is a token
		$boolean_tokens[] = $next;
		closed($tokens, $next ,$i, $message, $boolean_tokens);
		}
	else
		{
		//error found
		$message = ($tokens[$i] == "\n") ? "Error near end of boolean expression" : "Error in boolean expression at token " . ($i+1) . " near " . $next;
		}
	}
	
function operator($tokens, &$next, &$i, &$message, &$boolean_tokens)
	{
	//comes from an operator
	$i++;
	$next = $tokens[$i];
	if (preg_match("/[^&!\|\(\)\\n]/", $next))
		{
		//pointer is a token
		$boolean_tokens[] = $next;
		closed($tokens, $next ,$i, $message, $boolean_tokens);
		}
	elseif (preg_match("/!{1}/", $next))
		{
		//pointer is a NOT
		not($tokens, $next ,$i, $message, $boolean_tokens);   
		}
	elseif (preg_match("/\({1}/", $next))
		{
		//pointer is open parenthesis
		open($tokens, $next ,$i, $message, $boolean_tokens);
		}
	else
		{
		//error found
		$message = ($tokens[$i] == "\n") ? "Error near end of boolean expression" : "Error in boolean expression at token " . ($i+1) . " near " . $next;
		}
	}
	
function closed($tokens, &$next, &$i, &$message, &$boolean_tokens)
	{
	//comes from closed parenthesis or token
	$i++;
	$next = $tokens[$i];
	if (preg_match("/[\|&]{1}/", $next))
		{
		//pointer is an operator
		operator($tokens, $next, $i, $message, $boolean_tokens);
		}
	elseif (preg_match("/\){1}/", $next))
		{
		//pointer is a closed
		closed($tokens, $next, $i, $message, $boolean_tokens);
		}
	elseif ($next == "\n")
		{
		//end of descent, no errors
		$message = false;  
		} 
	else
		{
		//error found
		$message = ($tokens[$i] == "\n") ? "Error near end of boolean expression" : "Error in boolean expression at token " . ($i+1) . " near " . $next;
		}
	}
?>
