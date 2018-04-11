<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <meta name="copyright" content="&copy; WCC - Smart Search & Match">
  <meta name="keywords" content="Occupation Mapping ESCO-ONET">
  <meta name="description" content="Mapping of ESCO preferredLabels to ONET job_titles and alternate_titles">
  <title>Occupation Mapping ESCO-ONET</title>
  <link href="../../css/wd/style.css" rel="stylesheet" type="text/css">
</head>
<BODY background="../../css/wd/bg_main.gif" leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0" style="margin-top: 0px; margin-left: 5px; margin-right: 5px;"><center><br>
<?php
function printSelection($onet_code,$onet_label, $score_total) {
	$text = "<td align=center>";
        $text =  $text . "<input type='radio' name='mapping__" . $onet_code . "[]' value='". $onet_code. "__" . $onet_label . "##=" . $score_total . "' id='". $onet_code. "__" . $onet_label . "##='>";
        $text =  $text . "<input type='radio' name='mapping__" . $onet_code . "[]' value='". $onet_code. "__" . $onet_label . "##b" . $score_total . "' id='". $onet_code. "__" . $onet_label . "##b'>";
        $text =  $text . "<input type='radio' name='mapping__" . $onet_code . "[]' value='". $onet_code. "__" . $onet_label . "##n" . $score_total . "' id='". $onet_code. "__" . $onet_label . "##n'>";
        $text =  $text . "<input type='radio' name='mapping__" . $onet_code . "[]' value='". $onet_code. "__" . $onet_label . "##?" . $score_total . "' id='". $onet_code. "__" . $onet_label . "##?'></td>";
	return $text;
}
$servername = "localhost";
$username = "mysql_rh";
$password = "Summer-69";

//echo $_POST["r1_score_a"];
$ratio_rule1 = $_POST["r1_score_a"];
$ratio_rule2 = $_POST["r2_score_a"];
$ratio_rule3 = $_POST["r3_score_a"];
$ratio_rule4 = $_POST["r4_score_a"];
$ratio_rule5 = $_POST["r5_score_a"];
// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
$pos = stripos($_POST["occupation"],"__");
$preferredLabel = substr($_POST["occupation"],0,$pos);
$esco_code = substr($_POST["occupation"],($pos+2),strlen($_POST["occupation"]));
?>
<table class="tekst_gwn" align="center" class="stap" style="border-collapse: collapse; border: 1px solid blue;" cellpadding="0" cellspacing="0" width=100%>
  <tbody>
        <?php
           if ($esco_code=="") {
                echo "<tr><td colspan=11><br><center><font color=orange>Missing input Data!...</font></b><ul><li> You need to specify <i>ESCO PreferredLabel</i> <br>&nbsp;</td></tr>";
                exit();
           }
        ?>

    <tr bgcolor=#ABB2B9>
	<td colspan=11 align=center height=32><br> <font size=+1>
	<b>ESCO-ONET Mapping Suggestions for Preferred Label: <font color=brown><?php echo $preferredLabel ?> </font></b></font>&nbsp;<br>&nbsp;<br></td>
    </tr>
    <tr bgcolor=#D5D8DC align=left style="font-weight:bold;">
	<td width=40>&nbsp;Rule</td><td width=60>ESCO Code</td><td>ESCO PreferredLabel</td><td>ONET-Reported/AlternateJobTitle</td>
	<td width=25>Score_A&nbsp;&nbsp;</td><td>ONET SOC Code</td><td>O*NET Occupation Title</td><td>Score_B</td><td>Score_C</td><td>Final Score</td><td>Seletion<br>=&nbsp;&nbsp;&nbsp;B&nbsp;&nbsp;&nbsp;N&nbsp;&nbsp;&nbsp;?</td>
    </tr>
  </tbody>

  <form action="processMapping.php" method="POST" name="applyMapping">
  <input type='hidden' name='esco_code' value='<?php echo $esco_code ?>'>
  <input type='hidden' name='preferredLabel' value='<?php echo $preferredLabel ?>'>
<?php
echo "\t<tr height=10><td colspan=11></td></tr>";

// ***************** RULE 1 **************************

$sql = "SELECT distinct a.esco_code, a.preferredLabel, a.conceptURI, b.onetsoc_code, b.alternate_title, '100' score, c.title, c.childs FROM esco_v102.occupation_en a, onet_22_2.alternate_titles b , onet_22_2.occupation_data c " . "where a.preferredLabel=b.alternate_title and a.preferredLabel='" . $preferredLabel . "' and b.onetsoc_code=c.onetsoc_code";
//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$onet_label = "";
$html_content = "";
    // output data of each row
    while($row = $result->fetch_assoc()) {
	$i++;
	$score_1 = intval($row["score"]);
        //$score_1 = intval($row["score"]*intval($ratio_rule1)/100);
	//echo $row["score"] . ": " . $ratio_rule1;
        $score_2 = 0;
	$code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $conceptURI = $row["conceptURI"];
        $onet_label = $row["alternate_title"];
	if ($row["childs"]!=null)
		$alert = "&nbsp;<img src='../../css/images/alert_icon.png' width=13>";
	else
		$alert = "";

	//$preferredLabel = $row["preferredLabel"];
	$html_content = $html_content . "<td height=20>&nbsp;". $row["onetsoc_code"]."_<font color='green'><b>".$row["alternate_title"]. "</b></font><td>&nbsp;". $score_1 . "%</td>";
	$html_content = $html_content . "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
	$title = $row["title"] ;
	$words = explode( " " ,  $row['preferredLabel'] );
	foreach ($words as $value ) {
    	   $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score, b.parent";
	   $sql = $sql . " FROM esco_v102.occupation_en a, onet_22_2.occupation_data b";
           $sql = $sql . " where b.title like '%" . $value . "%' and a.preferredLabel='" . $row["preferredLabel"] . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
	   //$title = $row["title"];
	   $result2 = $conn->query($sql);
	   //$toReplaceWith = "<font color='green'><b>" . $value . "</b></font>";
	   if ($row2 = $result2->fetch_assoc()){
		$score_2 = $score_2 + intval($row2["score"]);
		$start = stripos($title,$value);
		$title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
		$title = $title2;
	   }
	}

	$sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
	$result3 = $conn->query($sql);
	$count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
	$score_3 = intval($count_matched*100/$count_NPTs);
	$score_total = $score_1+$score_2+$score_3;
        $html_content =  $html_content . "\n\t\t<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','O*NET window',1000,600)\"\ style='opacity: 0.7; filter: alpha(opacity=50);'></td>\n\t\t<td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td>\n\t\t<td><b>&nbsp;" . $score_total . "%</b></td>";

        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";

    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $code . "</td><td rowspan=" . $i . "><font color='green'><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 1</td>" . $html_content . "</tr>";

    echo "\n\t<tr height=10><td colspan=11></td></tr>";

// ***************** RULE 2 **************************

	//System.out.println("Rule 2: esco-PT_word(s) = onet-Reported/AlternateJobTitle_string");
	$sql = "SELECT distinct a.esco_code, a.preferredLabel, a.conceptURI, b.onetsoc_code, b.alternate_title, length(a.preferredLabel)/length(b.alternate_title)*100 score, c.title, c.childs FROM esco_v102.occupation_en a, onet_22_2.alternate_titles b, onet_22_2.occupation_data c where b.alternate_title like concat ('%',a.preferredLabel,'%') and a.preferredLabel='" . $preferredLabel . "' and b.alternate_title!='" . $preferredLabel . "' and b.onetsoc_code=c.onetsoc_code";

//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$html_content = "";

    while($row = $result->fetch_assoc()) {
	$i++;
        $score_1 = intval($row["score"]);
        //$score_1 = intval($row["score"]*$ratio_rule2/100);
        $score_2 = 0;
        $code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $conceptURI = $row["conceptURI"];
        $onet_label = $row["alternate_title"];
        //$onet_code = $row["onetsoc_code"]."_".$row["alternate_title"];
        $alert = ($row["childs"]!=null)? "&nbsp;<img src='../../css/images/alert_icon.png' width=13>":  "";


	$title = $row["alternate_title"];
        $start = stripos($title, $row["preferredLabel"]);
        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($row["preferredLabel"])) . "</b></font>" . substr($title,$start+strlen($row["preferredLabel"]), strlen($title));
        $title = $title2;
        $html_content = $html_content . "<td height=20>&nbsp;". $row["onetsoc_code"]."_". $title . "</td><td>&nbsp;". $score_1 . "%</td>";
        $html_content = $html_content .  "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
        $title = $row["title"];
        $words = explode( " " ,  $row['preferredLabel'] );
        foreach ($words as $value ) {
           $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score FROM esco_v102.occupation_en a, onet_22_2.occupation_data b where b.title like '%" . $value . "%' and a.preferredLabel='" . $row["preferredLabel"] . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
                $result2 = $conn->query($sql);
                if ($row2 = $result2->fetch_assoc()) {
                        $score_2 = $score_2 + intval($row2["score"]);
                        $start = stripos($title,$value);
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title = $title2;

		}
        }
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
        $result3 = $conn->query($sql);
        $count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
        $score_3 = intval($count_matched*100/$count_NPTs);
        $score_total = $score_1+$score_2+$score_3;
        $html_content =  $html_content . "<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','xtf',1000,600)\"\></td><td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td><td><b>&nbsp;" . $score_total . "%</b></td>";
        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";


    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
        //$html_content = "<td colspan=2>&nbsp;". $code . "</td><td colspan=8 align=center>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $code . "</td><td rowspan=" . $i . "><font color='brown'><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 2</td>" . $html_content . "</tr>";

    echo "<tr height=10><td colspan=11></td></tr>";

// ***************** RULE 3 **************************
//System.out.println("Rule 3: esco-PT_string = onet-Reported/AlternateJobTitle_word(s)");
$sql = "SELECT distinct a.esco_code, a.preferredLabel, conceptURI, b.onetsoc_code, b.alternate_title, length(b.alternate_title)/length(a.preferredLabel)*100 score, c.title, c.childs FROM esco_v102.occupation_en a, onet_22_2.alternate_titles b, onet_22_2.occupation_data c where a.preferredLabel like concat ('%',b.alternate_title,'%') and a.preferredLabel='" . $preferredLabel . "' and b.alternate_title!='" . $preferredLabel . "' and b.onetsoc_code=c.onetsoc_code and (length(b.alternate_title)/length(a.preferredLabel)*100)>50 order by score desc";

//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$preferredLabel2 = $preferredLabel;
$html_content = "";

    while($row = $result->fetch_assoc()) {
        $i++;
        $score_1 = intval($row["score"]);
        //$score_1 = intval($row["score"]*$ratio_rule3/100);
        $score_2 = 0;
        $code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $conceptURI = $row["conceptURI"];
        $onet_label = $row["alternate_title"];
        //$onet_code = $row["onetsoc_code"]."_".$row["alternate_title"];
        $alert = ($row["childs"]!=null)? "&nbsp;<img src='../../css/images/alert_icon.png' width=13>":  "";

        $title = $preferredLabel2;
        $start = stripos($title, $row["alternate_title"]);
        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($row["alternate_title"])) . "</b></font>" . substr($title,$start+strlen($row["alternate_title"]), strlen($title));
        $preferredLabel2 = $title2;
        $html_content = $html_content . "<td height=20>&nbsp;" . $row["onetsoc_code"]."_<font color='green'><b>" .  $row["alternate_title"] . "</b></font></td><td>&nbsp;". $score_1 . "%</td>";
        $html_content = $html_content .  "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
        $title = $row["title"];
	$words = explode( " " ,  $row['preferredLabel'] );
        foreach ($words as $value ) {
           //echo $value;
           $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score FROM esco_v102.occupation_en a, onet_22_2.occupation_data b where b.title like '%" . $value . "%' and a.preferredLabel='" . $row["preferredLabel"] . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
                //echo "\nQuery: '" . $sql . "'";
                $result2 = $conn->query($sql);
                if ($row2 = $result2->fetch_assoc()) {
                        $score_2 = $score_2 + intval($row2["score"]);
                        $start = stripos($title,$value);
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title = $title2;
		}
        }
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
        $result3 = $conn->query($sql);
        $count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
        $score_3 = intval($count_matched*100/$count_NPTs);
        $score_total = $score_1+$score_2+$score_3;

        $html_content =  $html_content . "<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','xtf',1000,600)\"\></td><td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td><td><b>&nbsp;" . $score_total . "%</b></td>";
        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";

    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
        //$html_content = "<td colspan=2>&nbsp;". $code . "</td><td colspan=8 align=center>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $code . "</td><td rowspan=" . $i . "><font color='brown'><b>&nbsp;" . $preferredLabel2 . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 3</td>" . $html_content . "</tr>";


    echo "<tr height=10><td colspan=11></td></tr>";

// ***************** RULE 4 **************************

//System.out.println("Rule 4: esco-PT_string(s) = onet-Reported/AlternateJobTitle_string");
$sub_query = "";
$words = explode( " " , $preferredLabel );
foreach ($words as $value ) 
	$sub_query = $sub_query . "b.alternate_title like '%" . $value . "%' and ";
$sub_query = substr($sub_query, 0, strlen($sub_query)-5);

$sql = "SELECT distinct a.esco_code, a.preferredLabel, a.conceptURI, b.onetsoc_code, b.alternate_title, length(a.preferredLabel)/length(b.alternate_title)*100 score, c.title, c.childs FROM esco_v102.occupation_en a, onet_22_2.alternate_titles b, onet_22_2.occupation_data c where (" . $sub_query . ") and a.preferredLabel='" . $preferredLabel . "' and b.alternate_title not like '%" . $preferredLabel . "%' and b.onetsoc_code=c.onetsoc_code";

//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$html_content = "";

    while($row = $result->fetch_assoc()) {
        $i++;
        $score_1 = intval($row["score"]) ;
        //$score_1 = intval($row["score"]*$ratio_rule4/100) ;
        $score_2 = 0;
        $code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $conceptURI = $row["conceptURI"];
        $onet_label = $row["alternate_title"];
        //$onet_code = $row["onetsoc_code"]."_".$row["alternate_title"];
        $alert = ($row["childs"]!=null)? "&nbsp;<img src='../../css/images/alert_icon.png' width=13>":  "";

        $preferredLabel = $row["preferredLabel"];
        $title = $row["alternate_title"];
        $words = explode( " " ,  $row['preferredLabel'] );
        foreach ($words as $value ) {
        	$start = stripos($title, $value);
        	$title = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));

	}
        $html_content = $html_content . "<td height=20>&nbsp;" . $row["onetsoc_code"]."_".$title . "</td><td>&nbsp;". $score_1 . "%</td>";
        $html_content = $html_content .  "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
        $title = $row["title"];
        $words = explode( " " ,  $row['preferredLabel'] );
        foreach ($words as $value ) {
           //echo $value;
           $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score FROM esco_v102.occupation_en a, onet_22_2.occupation_data b where b.title like '%" . $value . "%' and a.preferredLabel='" . $row["preferredLabel"] . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
                //echo "\nQuery: '" . $sql . "'";
                $result2 = $conn->query($sql);
                if ($row2 = $result2->fetch_assoc()){
                        $score_2 = $score_2 + intval($row2["score"]);
                        $start = stripos($title,$value);
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title = $title2;
		}
        }
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
        $result3 = $conn->query($sql);
        $count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
        $score_3 = intval($count_matched*100/$count_NPTs);
        $score_total = $score_1+$score_2+$score_3;
        $html_content =  $html_content . "<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','xtf',1000,600)\"\></td><td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td><td><b>&nbsp;" . $score_total . "%</b></td>";
        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";

    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $code . "</td><td rowspan=" . $i . "><font color='brown'><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 4</td>" . $html_content . "</tr>";


    echo "<tr height=10><td colspan=11></td></tr>";


// ***************** RULE 5 NEW **************************

$threshold = 30;
$stopwords = ""; //#worker#operator#manager#assistant#officer#advisor#and#engineer#air#app#";
//System.out.println("Rule 4: esco-PT_string(s) = onet-Reported/AlternateJobTitle_string");
$sub_query = "";
$words = explode( " " , $preferredLabel );
foreach ($words as $value ) {
        if(!stripos($stopwords, $value))
                $sub_query = $sub_query . "b.alternate_title like '%" . $value . "%' or ";
}
$sub_query = substr($sub_query, 0, strlen($sub_query)-4);

$sql = "SELECT distinct a.esco_code, a.preferredLabel, a.conceptURI, b.onetsoc_code, b.alternate_title, c.title, c.childs FROM esco_v102.occupation_en a, onet_22_2.alternate_titles b, onet_22_2.occupation_data c where (" . $sub_query . ") and a.preferredLabel='" . $preferredLabel . "' and b.alternate_title not like '%" . $preferredLabel . "%' and b.onetsoc_code=c.onetsoc_code";

//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$html_content = "";

    while($row = $result->fetch_assoc()) {
        $i++;
        $score_2 = 0;
        $code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $conceptURI = $row["conceptURI"];
        $onet_label = $row["alternate_title"];
        //$onet_code = $row["onetsoc_code"]."_".$row["alternate_title"];
        $alert = ($row["childs"]!=null)? "&nbsp;<img src='../../css/images/alert_icon.png' width=13>":  "";

        $preferredLabel = $row["preferredLabel"];
        $words = explode( " " ,  $row['preferredLabel'] );
        // echo "alttitle: " . $title . "<br>";
	$score_1 = 0;
        foreach ($words as $value ) {
                $title = $onet_label;
                $start = stripos($title, $value);
                if (!stripos($stopwords, $value) and $start>-1) {
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title=$title2;
                        $score_1 = $score_1 + intval((strlen($value)/strlen($onet_label)) + (strlen($value)/strlen($preferredLabel))/2 *100);
                        //echo "<br>value: " . $value . " " . $score_1 . " - ";

                }

        }
	//echo $score_1;
	if ($score_1 > $threshold ) {
        $html_content = $html_content . "<td height=20>&nbsp;" . $row["onetsoc_code"]."_".$title2 . "</td><td>&nbsp;". $score_1 . "%</td>";
        $html_content = $html_content .  "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
        $title = $row["title"];
        $words = explode( " " ,  $row['preferredLabel'] );
        //echo "pos: " . stripos("Vicar", "car");
        foreach ($words as $value ) {
           //echo $value;
           $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score FROM esco_v102.occupation_en a, onet_22_2.occupation_data b where b.title like '%" . $value . "%' and a.preferredLabel='" . $row["preferredLabel"] . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
                //echo "\nQuery: '" . $sql . "'";
                $result2 = $conn->query($sql);
                if ($row2 = $result2->fetch_assoc()){
                        $score_2 = $score_2 + intval(strlen($value)/strlen($title)*100);
                        $start = stripos($title,$value);
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title = $title2;
                }
        }
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
        $result3 = $conn->query($sql);
        $count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
        $score_3 = intval($count_matched*100/$count_NPTs);
        $score_total = $score_1+$score_2+$score_3;
        $html_content =  $html_content . "<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','xtf',1000,600)\"\></td><td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td><td><b>&nbsp;" . $score_total . "%</b></td>";
        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";
}
else
  $html_content = $html_content . "<td colspan=6></td></tr>";
    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $code . "</td><td rowspan=" . $i . "><font color='brown'><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 5</td>" . $html_content . "</tr>";


    echo "<tr height=10><td colspan=11></td></tr>";


// ***************** RULE 6 **************************

$stopwords = "#worker#operator#manager#assistant#officer#advisor#and#engineer#air#app#";
//System.out.println("Rule 4: esco-PT_string(s) = onet-Reported/AlternateJobTitle_string");
$sub_query = "";
$words = explode( " " , $_POST["occupation"] );
foreach ($words as $value ) {
        if(!stripos($stopwords, $value))
                $sub_query = $sub_query . "a.alternate_title like '%" . $value . "%' or ";
}
$sub_query = substr($sub_query, 0, strlen($sub_query)-4);

$sql = "SELECT distinct a.onetsoc_code, a.alternate_title, b.title FROM onet_22_2.alternate_titles a, onet_22_2.occupation_data b";
$sql = $sql . " where a.onetsoc_code=b.onetsoc_code and alternate_title in (SELECT altlabel FROM esco_v102.occupation_altlabels where esco_code='" . $esco_code . "')";


//echo $sql;
$result = $conn->query($sql);
$i=0;
$code = $_POST["occupation"];
$html_content = "";

    echo "<ol>";
    while($row = $result->fetch_assoc()) {
        $i++;
        $score_2 = 0;
        $code = $row["esco_code"];
        $onet_code = $row["onetsoc_code"];
        $onet_label = $row["alternate_title"];
        $alert = ($row["childs"]!=null)? "&nbsp;<img src='../../css/images/alert_icon.png' width=13>":  "";

        $words = explode( " " ,  $preferredLabel );
        $score_1 = 0;
        foreach ($words as $value ) {
                if (!stripos($stopwords, $value)) {
                  $title = $row["alternate_title"];
                  $start = stripos($title, $value);
		  if($start>-1) {
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title=$title2;
                        $score_1 = $score_1 + intval((strlen($value)/strlen($preferredLabel))*(strlen($value)/strlen($row["alternate_title"]))*100);

                  }
		}

        }
        $html_content = $html_content . "<td height=20>&nbsp;" . $row["onetsoc_code"]."_".$title . "</td><td>&nbsp;". $score_1 . "%</td>";
        $html_content = $html_content .  "<td>&nbsp;". $row["onetsoc_code"] . "</td>";
        $title = $row["title"];
        $words = explode( " " ,  $preferredLabel );
        foreach ($words as $value ) {
           //echo $value;
           $sql = "SELECT distinct a.esco_code, a.preferredLabel, b.onetsoc_code, b.title, length('" . $value . "')/length(b.title)*100 score FROM esco_v102.occupation_en a, onet_22_2.occupation_data b where b.title like '%" . $value . "%' and a.preferredLabel='" . $preferredLabel . "' and b.onetsoc_code='" .  $row["onetsoc_code"]. "'";
                //echo "\nQuery: '" . $sql . "'";
                $result2 = $conn->query($sql);
                if ($row2 = $result2->fetch_assoc()){
                        $score_2 = $score_2 + intval($row2["score"]);
                        $start = stripos($title,$value);
                        $title2 = substr($title,0, $start) . "<font color='green'><b>" . substr($title,$start, strlen($value)) . "</b></font>" . substr($title,$start+strlen($value), strlen($title));
                        $title = $title2;
                }
        }
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "'";
        $result3 = $conn->query($sql);
        $count_NPTs = mysqli_num_rows($result3);
        $sql = "SELECT distinct altlabel FROM esco_v102.occupation_altlabels where esco_code ='" . $code . "' and altlabel in (SELECT alternate_title FROM onet_22_2.alternate_titles where onetsoc_code='" . $row["onetsoc_code"] . "')";
        $result3 = $conn->query($sql);
        $count_matched = mysqli_num_rows($result3);
        $score_3 = intval($count_matched*100/$count_NPTs);
        $score_total = $score_1+$score_2+$score_3;
	$html_content =  $html_content . "<td>&nbsp;" . $title . $alert . "&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('https://www.onetonline.org/link/summary/" . $onet_code. "','xtf',1000,600)\"\></td><td>&nbsp;" . $score_2 . "%</td><td>&nbsp;" . $score_3 . "% - " . $count_matched . "/" . $count_NPTs . "</td><td><b>&nbsp;" . $score_total . "%</b></td>";

        $html_content =  $html_content . printSelection($onet_code,  $onet_label, $score_total) .  "</tr>";

    }
    if ($i==0) {
        $i = 1;
        $html_content = "<td>&nbsp;". $esco_code . "</td><td>&nbsp;". $preferredLabel . "</td><td colspan=8 align=center style='font-size:8ptx; color:orange;'><font size=-1>-</td>";
    }
    else
        $html_content = "<td rowspan=" . $i . ">&nbsp;" . $esco_code . "</td><td rowspan=" . $i . "><font color='brown'><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>" . $html_content;

    echo "<tr bgcolor='#e0e0e0'><td rowspan=" . $i . ">&nbsp;Rule 6</td>" . $html_content . "</tr>";



/******************* Rule M ***************************/
$html_content = "<td>&nbsp;" . $esco_code . "</td><td><b>&nbsp;" . $preferredLabel . "</b></font>&nbsp;<img src='../../css/images/searchbar_go.png' width=13 onclick=\"return popitup('" . $conceptURI. "','ESCO window',1000,600)\"\></td>";
$html_content = $html_content . "<td>ONET occupation-code: <input name=onet_code id=onet_code> </td>";
$html_content = $html_content . "<td colspan=3>ONET Alternate Title: <input name=onet_alttitle id=onet_alttitle> </td>";
$html_content = $html_content . "<td colspan=3></td>";
$html_content =  $html_content . printSelection('',  '', '') .  "</tr>";
echo "<tr bgcolor='#e0e0e0'><td>&nbsp;Rule M</td>" . $html_content . "</tr>";



?>



<tr bgcolor=#D5D8DC><td colspan=11 align=center class="tekst_gwn">
<br>&nbsp;  <br>&nbsp; 
<input type='submit' value='Save Mapping' class="tekst_gwn" style="font-weight: bold; font-size: 16px;">&nbsp;&nbsp;
<input type='reset' value='Clear Selection' class="tekst_gwn" style="color: grey; font-size: 16px;">&nbsp;<br>&nbsp;
</td></tr>
</form>

<?php
$sql = "select * from onet_22_2.ESCO_ONET_mapping where esco_code='" . $esco_code . "'";

//echo $sql;
$result = $conn->query($sql);
echo "<script type='text/javascript'>\n";
while($row = $result->fetch_assoc()) {
	if ($row['flag']==='manual') {
        	$id = "__##" . $row['mapping_type'] ;
                echo " document.getElementById('onet_code').value = '" . $row['onet_code'] . "';";
                echo " document.getElementById('onet_alttitle').value = '" . $row['onet_alttitle'] . "';";
	}
	else
		$id = $row['onet_code'] . "__" . $row['onet_alttitle'] . "##" . $row['mapping_type'] ;

	echo " document.getElementById('" . $id . "').checked = true;";

}
echo "</script>";
$conn->close();
?>
</table>

        <table align="center" class="stap" cellpadding="0" cellspacing="0" width=100% border=0>
	<tr><td colspan=11 align=center class="tekst_gwn" style="color:grey;"><br><img src='../../css/images/alert_icon.png' width=13>&nbsp;O*NET Occupation has more specialized occupation(s)</td></tr>
        <tr>
         <td colspan=11 align=left class="tekst_gwn" style="color:grey;"><br> <font size=+1><b>&nbsp;Legend:</b></font><br>
        <ul>
           <li><b>Main Rules:</b> These are leading rules, the score for each rule is expressed as <b>Score_A</b> in the mapping table (column 5)
           <ol>
                <li><b>Rule 1: </b>ESCO PreferredLabel = ONET-Reported/AlternateJobTitle. Example: warehouse worker<br>
                <li><b>Rule 2: </b>ESCO-PreferredLabel <b>&sub;</b> ONET-Reported/AlternateJobTitle. Example: veterinary technician<br>
                <li><b>Rule 3: </b>ESCO-PreferredLabel <b>&sup;</b> ONET-Reported/AlternateJobTitle. Example: veterinary technician/vacuum forming machine operator<br>
                <li><b>Rule 4: </b>ESCO-PreferredLabel_word(s) <b>&sub;</b>  ONET-Reported/AlternateJobTitle. Example: Accountant<br>
                <li><b>Rule 5: </b>ESCO-PreferredLabel_word <b>&sub;</b>  ONET-Reported/AlternateJobTitle (with threshold). Example: Warehouse order picker<br>
                <li><b>Rule 6: </b>AltLabel(s) of the ESCO-PreferredLabel <b>=</b>  ONET-Reported/AlternateJobTitle (exact match). Example: cargo vehicle driver<br>
           </ol>
           <li><b>Additional Rules:</b> The score of the additional rules is combined with the score of each leading rule, to give more insight about the  similarity and the mapping
        <ol type='a'>
                <li><b>Rule A: </b>ESCO-PreferredLabel_words <b>&sub;</b> ONET occupation Title (for all rules). The score of this rule is expressed as <b>Score_B</b> in the mapping table (column 8) and added to
the final score of each rule.
                <li><b>Rule B: </b>ESCO-AltLabel(s) <b>&sub;</b> ONET Alternate_Titles (for all rules). The score of this rule is expressed as <b>Score_C</b> in the mapping table (column 9) and added to
the final score of each rule.

        </ol>
        </ul>
        <br>&nbsp;</td>

      </tr>
    </table>

<script language="javascript" type="text/javascript">
<!--
function popitup(url, title, w, h) {
  	var left = (screen.width/2)-(w/2);
  	var top = (screen.height/2)-(h/2);
	newwindow=window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
function check(id) {
    document.getElementById(id).checked = true;
}
</script>
</html>
