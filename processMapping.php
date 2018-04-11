<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <meta name="copyright" content="&copy; WCC - Smart Search & Match">
  <meta name="keywords" content="Occupation Mapping ESCO-ONET">
  <meta name="description" content="Mapping of ESCO preferredLabels to ONET job_titles and alternate_titles">
  <title>Occupation Mapping ESCO-ONET</title>
  <link href="../../css/wd/style.css" rel="stylesheet" type="text/css">
  <!--link href="../../css/wd/progress.css" rel="stylesheet" type="text/css"-->
</head>
<BODY background="../../css/wd/bg_main.gif" leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0" style="margin-top: 0px; margin-left: 5px; margin-right: 5px;"><center><br>
<table class="tekst_gwn" align="center" class="stap" cellpadding="0" cellspacing="0" width=70% border=1>
     <tr bgcolor=#ABB2B9>
         <td align=center height=32 class="tekst_gwn"><br> <font size=+1><b>Saving the mapping for ESCO Preferred Label: <font color=brown><?php echo $_POST['esco_code'] . ": " . $_POST['preferredLabel'] ?> </font></b></font>&nbsp;<br>&nbsp;<br></td>
        </tr>
<?php


$mappings = $_POST['mapping'];
$list = "(";
foreach ($mappings as $mapping){
    $list = $list . "'" . substr($mapping,0,-2) . "',";
}
$list = $list . "'')";
//echo $list;

$servername = "localhost";
$username = "mysql_rh";
$password = "Summer-69";
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$i = 0;
$flag = "auto";
foreach ($_POST as $key => $value) {
   //echo "Field " . $key. " is " . $value."<br>";
   if (substr($key, "mapping")==0) {
	$mappings2 = $_POST[$key];
	foreach ($mappings2 as $mapping2){
                $pos1 = strpos($mapping2,"__");
		$pos2 = strpos($mapping2,"##");
                $onet_code = substr($mapping2,0,$pos1);
                $onet_label = substr($mapping2,($pos1+2),$pos2-$pos1-2);
                if ($onet_code === '') {
                        $onet_code = $_POST['onet_code'];
                        $onet_label = $_POST['onet_alttitle'];
			$flag = "manual";
                }
		$sql = "delete from onet_22_2.ESCO_ONET_mapping where esco_code = '" . $_POST['esco_code'] . "' and onet_code = '" . $onet_code . "'";
                //echo $sql;
		if ($conn->query($sql) === FALSE)
                        echo "Error: " . $sql . "<br>" . $conn->error;

		$sql = "insert into onet_22_2.ESCO_ONET_mapping values ('" . $_POST['esco_code'] . "','" . $_POST['preferredLabel'] . "','" . $onet_code . "','" . $onet_label;
                $sql = $sql . "','" . substr($mapping2,($pos2+3),strlen($mapping2)) . "','" . substr($mapping2,($pos2+2),1) . "','" . $flag . "')";
		if ($conn->query($sql) === FALSE) 
    			echo "<tr><td><font color=red>Error</font>: " . $sql . "<br>" . $conn->error . "</td></tr>";
                else
                        echo "<tr><td align=center><br><font color=green><b>Succeeded</b></font>: " . mysqli_affected_rows($conn) . " update(s)<br>&nbsp;</td></tr>";


        	//echo "sql: " . $sql . "<br>";
		$i++;
	}
   }
}

if ($i==0) {
        $sql = "delete from onet_22_2.ESCO_ONET_mapping where esco_code = '" . $_POST['esco_code'] . "'";
	$conn->query($sql);
	if (mysqli_affected_rows($conn)>0)
		echo "<tr><td align=center><br><font color=green><b>Succeeded</b></font>: " . mysqli_affected_rows($conn) . " deletion(s)<br>&nbsp;</td></tr>";
	else
		echo "<tr><td align=center><br><font color=orange>Warning</font>: No mapping has been specified!<br>&nbsp;</td></tr>";
}

$conn->close();
?>
</html>
