<?php
include "functions/html.php";
printHeader("AAU Bordfodbold", "");

function __autoload($class_name) {
    include $class_name . '.php';
}
?>
<div class="leftside">
<h3>Update October 1st 2013</h3>
<li>Link to player profiles added to match history</li>
    
<h3>Update June 8th 2013:</h3>
<li><a href="matchhistory.php?page=1">Match History</a> added</li>   
<li>Improved table styles</li> 
    
<p>Submit any bugs to <A HREF="mailto:mikaelmidt@gmail.com">mikaelmidt@gmail.com</A></p>

<h3>Coming Soon</h3>
<li>Filters on <a href="matchhistory.php?page=1">Match History</a></li>
<li>The ability to challenge other players</li>

</div>
<div class="rightside">
<?php
    printSiteStats();
?>
</div>
<?php
printFooter();
?>
