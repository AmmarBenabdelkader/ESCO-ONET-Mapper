<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <meta name="copyright" content="&copy; WCC - Smart Search & Match">
  <meta name="keywords" content="Occupation Mapping ESCO-ONET">
  <meta name="description" content="Mapping of ESCO preferredLabels to ONET job_titles and alternate_titles">
  <title>Occupation Mapping ESCO-ONET</title>
  <link href="../../css/wd/style.css" rel="stylesheet" type="text/css">
<style>
.button {
    background-color: #ABB2B9;
    border: 1px solid #E67E22;
    border-radius: 12px;
    color: white;
    padding: 5px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
.button:hover {
    box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
}
</style>
</head>
<BODY background="../../css/wd/bg_main.gif" leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0" style="margin-top: 0px; margin-left: 0px;"><center><br>
<table align="center" class="stap" cellpadding="0" cellspacing="0" width=700 border=0>
  <tbody>
  <tr>
     <td align=left>&nbsp;&nbsp;&nbsp;<img src="../../css/wd/logo.svg" alt=" WCC - Smart Search & Match" border="0" height="50"></td>
     <td align=left class="tekst_gwn"><br>
        <font size=+1><b>ESCO-ONET Occupation Mapping</b></font><br>&nbsp;&nbsp;&nbsp;WCC - Smart Search & Match,&nbsp;&nbsp;&nbsp;Taxonomy Team<br>&nbsp;</td>
  </tr>
</table>
<table align="center" cellpadding="0" cellspacing="0" width=700 border=0>

  <tr height=35 valign=center>
     <td colspan=2 align=center>
	<a href='menu_mapOccupation.php?keyword=<?php echo $_GET["keyword"] ?>' target=menu><input type=button class="button" value='Map Occupations' id='map_occupation' onclick="check('map_occupation');"/></a>
	<a href='menu_mappedOccupation.php?keyword=<?php echo $_GET["keyword"] ?>' target=menu><input type=button class="button" value='Mapped Occupations' id='mapped_occupation' onclick="check('mapped_occupation');"></a>
	<a href='menu_dataTransfer.php?keyword=<?php echo $_GET["keyword"] ?>'  target=menu><input type=button class="button" value='Data Transfer' id='transfer_data' onclick="check('transfer_data');"></a>
     </td>
   </tr>
</table>
<script language="javascript" type="text/javascript">
function check(id) {
    document.getElementById('map_occupation').style.backgroundColor='#ABB2B9';
    document.getElementById('mapped_occupation').style.backgroundColor='#ABB2B9';
    document.getElementById('transfer_data').style.backgroundColor='#ABB2B9';
    document.getElementById(id).style.backgroundColor='#5D6D7E';
}
</script>
</html>
