<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <meta name="copyright" content="&copy; WCC - Smart Search & Match">
  <meta name="keywords" content="Occupation Mapping ESCO-ONET">
  <meta name="description" content="Mapping of ESCO preferredLabels to ONET job_titles and alternate_titles">
  <title>Occupation Mapping ESCO-ONET</title>
  <link href="../../css/wd/style.css" rel="stylesheet" type="text/css">
</head>
<BODY background="../../css/wd/bg_main.gif" leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0" style="margin-top: 0px; margin-left: 10px;"><center><br>
<?php
$keyword = $_GET["keyword"];
$filter = (strlen($keyword)>0?" where esco_pt like '%" . $keyword . "%'":"");

/******************** Mapped Occupations ******************************/
?>
<br>&nbsp;
<table align="left" class="stap" cellpadding="0" cellspacing="0" width=400 border=0>
  <tbody>
    <tr bgcolor=#ABB2B9>
	<td align=center class="tekst_gwn"><br><font size=+1><b>ESCO Preferred Labels </b><br>&nbsp;</b></font>(mapped to O*NET)</td></tr>
    <tr>
	<td align=left class="tekst_gwn"> <form action=content.php method=post target=content>&nbsp;<br>
		<?php echo (strlen($keyword)>0?"&nbsp;filter: <i>" . $keyword . "</i><br>":""); ?>
		&nbsp;<select name=occupation id="wgtmsr">
		<option value="">Select Preferred Label</option>
<?php
$servername = "localhost";
$username = "mysql_rh";
$password = "Summer-69";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "select distinct esco_code, esco_pt from onet_22_2.ESCO_ONET_mapping" . $filter . " order by esco_pt";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $label =  $row["esco_pt"];
        if (strlen($label)>43)
           $label = substr ($label,0,43) . " ...";
        echo "<option value='" . $label . "__" . $row["esco_code"]. "' alt='" . $row["esco_pt"] . "'>" . $label . " (" .  $row["esco_code"]. ")</option>";
    }
} else {
    echo "0 results";
}
$conn->close();
//echo "Connected successfully";
?>
</select>&nbsp;
</td></tr>
<tr><td>
<input type="submit" value="Show Mapping" class="tekst_gwn" style="font-weight: bold; font-size: 14px;"><br>&nbsp;
</td></tr>
<tr><td align=left class="tekst_gwn" style="color:grey;">
<br>&nbsp; <br>
</td></tr>
</table>
</form>

</html>
</html>
