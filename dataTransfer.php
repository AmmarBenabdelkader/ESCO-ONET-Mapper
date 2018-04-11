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
<?php
$pos = stripos($_POST["occupation"],"__");
$preferredLabel = substr($_POST["occupation"],0,$pos);
$esco_code = substr($_POST["occupation"],($pos+2),strlen($_POST["occupation"]));

$servername = "localhost";
$username = "mysql_rh";
$password = "Summer-69";
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "select a.onet_code, a.onet_alttitle, a.mapping_type, b.title from onet_22_2.ESCO_ONET_mapping a,  onet_22_2.occupation_data b where a.onet_code=b.onetsoc_code and esco_code='" . $esco_code . "'";

//echo $sql;
$result = $conn->query($sql);
$list = "(";
?>
<BODY background="../../css/wd/bg_main.gif" leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0" style="margin-top: 0px; margin-left: 5px; margin-right: 5px;"><center>
<br>&nbsp;
<table class="tekst_gwn" align="center" class="stap" cellpadding="0" cellspacing="0" width=70% border=1>
   <tr>
     <td colspan=2 align=left height=32 class="tekst_gwn"><br> <font size=+1>&nbsp;<b><font color=brown>
	<?php
	   if ($esco_code=="" or $_POST['taxonomy_concept']=="") {
		echo "<center>Missing input Data!</font></b><br> You need to specify both <i>ESCO PreferredLabel</i> and <i> O*NET concept</i><br>&nbsp;";
		exit();
	   }
	   echo $_POST['taxonomy_concept'] . "</font> data transfer to ESCO Preferred Label: <font color=brown>" .  $esco_code . ": " . $preferredLabel  
	?> </font></b></font><br><br> 
	&nbsp;&nbsp;Using the the Following O*NET occupations:<ul>
	<?php
	while ($row = mysqli_fetch_array($result)) {
        	echo "<li>" . $row['onet_code'] . ": " . $row['title'] . " &nbsp;&nbsp;<i>alt_titile: " . $row['onet_alttitle'] . " | mapping type: " . $row['mapping_type'] . "</i></li>";
		$list = $list . "'" .  $row["onet_code"] . "',";
	}
	$list = $list . "'')";
	?>
	</ul>
     </td>
   </tr>
<?php


function proccessONET_tools($conn, $list) {

$sql = "SELECT distinct t2_type, b.commodity_title, t2_example, hot_technology FROM onet_22_2.tools_and_technology a, onet_22_2.unspsc_reference b where a.commodity_code=b.commodity_code and onetsoc_code in " . $list . " order by t2_type, b.commodity_title, t2_example";
//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = "";
$preferredLabel = "";
$html_content = "";
	echo "<tr><td colspan=2>";
	$skill_type ="";
	$commodity = "";
    while($row = $result->fetch_assoc()) {
	if ($i==0) {
		echo "<ol style='list-style-type: upper-alpha; line-height:150%; font-size:22px'><li>" . $row["t2_type"] . "</li>";
		echo "<ul style='line-height:150%; font-size:18px'><li>" . $row["commodity_title"] . "</li>";
                echo "<ul style='line-height:150%; font-size:14px'>";
		$skill_type=$row["t2_type"];
		$commodity=$row["commodity_title"];
	}
        $i++;
        if ($commodity!=$row["commodity_title"] and $i>0) {
                $commodity=$row["commodity_title"];
                echo "</ul><li>" . $commodity . "</li>";
                echo "<ul style='line-height:150%; font-size:14px'>";
        }
	if ($skill_type!=$row["t2_type"] and $i>0) {
		$skill_type=$row["t2_type"];
                echo "</ul></ul><li>" . $skill_type . "</li>";
                echo "<ul style='line-height:150%; font-size:18px'><li>" . $commodity . "</li>";
                echo "<ul style='line-height:150%; font-size:14px'>";
	}
        if ($row["hot_technology"]=='Y')
                $hot_img = "<img src='../../css/images/hottech.png' alt='" . $row["hot_technology"] . "'>";
        else
                $hot_img = "";

        echo  "<li><input type=checkbox name='Tools and Technology' value='" .  $row["t2_example"] . "' checked>&nbsp;&nbsp;" . $row["t2_example"] . " &nbsp;&nbsp;" . $hot_img . "</li>";

	}
	echo "</ol></td></tr></table>"; 
}

	/************************** SKILLS ************************************/
function proccessONET_skills($conn, $list, $para) {

$sql = "SELECT distinct b.element_id code, b.element_name name, b.description, a.data_value FROM onet_22_2.skills a, onet_22_2.content_model_reference b where a.element_id=b.element_id and a.onetsoc_code in " . $list . " and scale_id='" . $para . "' and data_value>=1 order by data_value desc"; //and data_value>=3 order by name";
//echo $sql;

$result = $conn->query($sql);
$i=0;
$code = "";
$preferredLabel = "";
$html_content = "";
        echo "<tr><td colspan=2><ol style='line-height:150%; font-size:14px'>";
    while($row = $result->fetch_assoc()) {
        $i++;
	$data_value = intval( $row["data_value"]/5*100);
        echo  "<li><input type=checkbox name='Skills' value='" .  $row["code"] . "' " . ($row["data_value"]>=3?'checked':'') . "><span title='" . $row['description'] . "'>&nbsp;&nbsp;" . $row["name"] . " &nbsp;&nbsp;</span><img src='../../css/images/" .  intval( $row["data_value"]) . "stars.png' height=13></li>";
     }
        echo "</ol></td></tr>";
        echo "<tr><td colspan=2 align=center height=32><input type=Button value='Transfer Skills' class='tekst_gwn'></td></tr></table>";
}

if ($_POST['taxonomy_concept']=="Tools and Technology")
    proccessONET_tools($conn, $list);
if ($_POST['taxonomy_concept']=="Skills_im")
    proccessONET_skills($conn, $list, 'IM');
if ($_POST['taxonomy_concept']=="Skills_lv")
    proccessONET_skills($conn, $list, 'LV');


$conn->close();
?>
</html>
