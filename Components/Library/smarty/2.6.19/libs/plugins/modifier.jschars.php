<?PHP 
function smarty_modifier_jschars($str)
{
    //return strtoupper($str);

//	$str = mb_ereg_replace("\\\\", "\\\\", $str);
//    $str = mb_ereg_replace("&quot;", "\"", $str);
    $str = mb_ereg_replace("&#039;", "\'", $str);
    $str = mb_ereg_replace("'", "\'", $str);
//    $str = mb_ereg_replace("\r\n", "\\n", $str);
//    $str = mb_ereg_replace("\r", "\\n", $str);
//    $str = mb_ereg_replace("\n", "\\n", $str);
//    $str = mb_ereg_replace("\t", "\\t", $str);
//    $str = mb_ereg_replace("<", "\\x3C", $str); // for inclusion in HTML
//    $str = mb_ereg_replace(">", "\\x3E", $str);
    return $str;
 
}
?>