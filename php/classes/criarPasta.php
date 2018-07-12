<?
$id = "4";


$dirname = dirname(__DIR__,2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;

//echo ($dirname)."<br>"; 

if (!mkdir($dirname."anexos".DIRECTORY_SEPARATOR, 0777, true)) {
    echo "error";
}
?>