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
#function parse_boolean_string($boolean_string)
#	{
#	returns error message or true on error, returns false on successful parse
#	passes $boolean_string as value, $boolean_string is formatted on success, left unchanged on failure
#	}
# END INSTRUCTIONS

/* TEST AREA */
$boolean_test_array[] = "((test1 & test2) & test3)";
$boolean_test_array[] = "((test1 & ) & test2)";
$boolean_test_array[] = "(test1 & test2) & test3";
$boolean_test_array[] = "(test1 & test2) !& test3";
$boolean_test_array[] = "test1 & test2";
$boolean_test_array[] = "test1 | & test2";
$boolean_test_array[] = "&";
$boolean_test_array[] = "Test1 & !test2)";

echo "<p>PHP BOOLEAN PARSER</p>";
echo "<table border=1><tr><td>Boolean String</td><td>Error Message</td></tr>";
foreach ($boolean_test_array as $boolean_string)
	{
	$error_message = parse_boolean_string($boolean_string);
	echo "<tr><td>" . $boolean_string . "</td><td>" . $error_message . "</td></tr>";	
	}
echo "</table>";
/* END TEST AREA */

?>
<?php
function parse_boolean_string(&$boolean_string) {
	
    /* MAIN FUNCTION */    	
    if (trim($boolean_string) == "") 
	{
	//error on empty string
	return "Enter Search Terms or Tokens";
	}
    else
        {
        //split up tokens
        $tokens= preg_split('/([\|&!\)\(\s])/', $boolean_string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	//get rid of space tokens
        $tokens = array_diff($tokens, array(" "));
	//re-increment array
        $tokens = array_merge($tokens);
        
        //initial values
        $i = 0;
        $n = 0;
        $next = $tokens[$i];
        $message = false;
        
        /* ENTRANCE TO RECURSIVE DESCENT PARSER */
        //message and tokens passed by value
        if ($next == "(") //open parentheses
            {
            open($tokens,$next,$i,$n,$message);
            }
        else //token or boolean not (!)
            {
            token($tokens,$next,$i,$n,$message);    
            }
        
        /* RETURN VALUES */       
        if ($message) //error in boolean expression
            {
            return $message;       
            }
		
        elseif ($n <> 0) //unbalanced parentheses
            {
            return "Search term(s) have unbalanced parenthesis";    
            }        
        
        else //good boolean expression
            {
	    //$boolean_string passed as a value
            $boolean_string = implode($tokens);
            return false;                     
            }        
        }    
    }
    /* END MAIN FUNCTION */


/* RECURSIVE DESCENT PARSING FUNCTIONS */
//also checks for balanced parentheses and adds ORs between search toekns
function advance($regex, &$next, &$i, $tokens)
    {
    //check condition and advance pointer
    if (preg_match($regex, $next))
        {
        $i++;
        $next = $tokens[$i];
        return true;
        }
    else
        {
        return false;
        }
    }    
 
function token(&$tokens, $next ,$i, &$n, &$message)
    {
    //comes from a NOT
    if (advance("/[^&!\|\(\)]/", $next, $i, $tokens))
        {
	//pointer is a token
        operator($tokens, $next ,$i, $n, $message); 
        }
    elseif (advance("/!{1}/", $next, $i, $tokens))
        {
	//pointer is a NOT
        token($tokens, $next ,$i, $n, $message);   
        }
    else
        {
	//error found
        $message = "Error in boolean expression at token " . ($i + 1) . " near " . $next;
        }
    }
    
function open(&$tokens,$next,$i,&$n, &$message)
    {
    //comes from a NOT or open parenthesis
    if (advance("/\({1}/", $next, $i, $tokens))
        {
	//pointer is open parenthesis
        $n++;    
        open($tokens, $next ,$i, $n, $message);
        }
    elseif (advance("/!{1}/", $next, $i, $tokens))
        {
	//pointer is a NOT
        open($tokens, $next ,$i, $n, $message);
        }    
    elseif (advance("/[^&!\|\(\)]/", $next, $i, $tokens))
        {
	//pointer is a token
        operator($tokens, $next ,$i, $n, $message);
        }
    else
        {
	//error found
        $message = "Error in boolean expression at token " . ($i + 1) . " near " . $next;
        }
    }
    
function operator(&$tokens,$next,$i,&$n,&$message)
    {
    //comes from closed parenthesis or token
    if (advance("/[\|&]{1}/", $next, $i, $tokens))
        {
	//pointer is an AND, OR or NOT
        open($tokens, $next ,$i, $n, $message);
        }
    elseif (advance("/\){1}/", $next, $i, $tokens))
        {
	//pointer is a closed parenthesis
        $n--;
        operator($tokens, $next ,$i, $n, $message);
        }
    elseif (advance("/[^&!\|\(\)]/", $next, $i, $tokens))
        {
	//pointer is a token
        array_splice($tokens,$i-1,0,"|"); //put an OR between two tokens
        $i++;
        operator($tokens, $next ,$i, $n, $message);   
        }
    elseif (empty($next))
        {
	//end of descent, no errors
        $message = false;  
        } 
    else
        {
	//error found
        $message = "Error in boolean expression at token " . ($i + 1) . " near " . $next;
        }
    } //end function
?>
