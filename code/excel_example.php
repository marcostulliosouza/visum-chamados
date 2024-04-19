<?php

	include("excelwriter.inc.php");
	
	$excel=new ExcelWriter("../Report2.xls");
	
	if($excel==false)	
		echo $excel->error;
		
	$myArr=array("Name","Last Name","Address","Age");
	$excel->writeLine($myArr);

	$myArr=array("Sriram","Pandit","23 mayur vihar", "24");
	$excel->writeLine($myArr);
	
	$excel->writeRow();
	$excel->writeCol("Manoj", 300);
	$excel->writeCol("Tiwari", 300);
	$excel->writeCol("80 Preet Vihar", 300);
	$excel->writeCol("24");
	
	$excel->writeRow();
	$excel->writeCol("Harish");
	$excel->writeCol("Chauhan");
	$excel->writeCol("115 Shyam Park Main");
	$excel->writeCol("22");

	$myArr=array("Tapan","Chauhan","1st Floor Vasundhra", "25");
	$excel->writeLine($myArr);
	
	$excel->close();
	echo "data is write into myXls.xls Successfully.";
?>