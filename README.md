php-boolean-parser
==================

<p>This is a php boolean expression verifier/parser I built for a Postgres full text index search input.</p>

<p>A while back I searched quite heavily, mainly on Google, to find a PHP program to validate a boolean expression, specifically for the format used in querying a Postgres database with a full text field. After trial and error, I came up with the function here, which seems to do the trick. I put it on Github in case anybody needs to validate a boolean expression in PHP.</p> 

<p>Change Log</p>
<p>
2014-01-16 -- Fully redid the function making major logical and programming changes<br>
2013-12-22 -- Changes made to remove notices for PHP 5.4<br>
2013-11-06 -- Initial upload and get everything going<br>
2014-09-21 -- Added support for plain english boolean commands and for boolean_tokens array</p>

<p>2014-10-05 -- Major changes -- Basically Version 2</p>
<ul><li>Project renamed to php-boolean-validator</li>
<li>Turned into an object</li>
<li>Support added for any boolean operator tokens scheme</li>
<li>Toggle flag added for auto-populating ORs</li>
<li>Redundant procedure removed</li>
<li>General housekeeping</li>
<li>Test code separated into separate file</li></ul>
