<?php
	session_start();
	include("sub_init_database.php");
	
	
	function getFatherCat($nameX) {
		$name=explode("|", $nameX);
		$id=$name[0];
		$name=$name[1];
		// Liest den Vater aus der Datenbank	
		$abfrage = "SELECT DISTINCT C1.id_uppercat AS cat, C2.name  FROM piwigo_categories C1 INNER JOIN piwigo_categories C2 on (C1.id_uppercat = C2.id) WHERE C1.id=".$id;
		$ergebnis = mysql_query($abfrage);
		$erg = $nameX;
		while($row = mysql_fetch_object($ergebnis)) {
				$erg=$row->cat."|".$row->name."/".$name;
				$erg = getFatherCat($erg);		
		}
		return $erg;
	}
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf8"/>
	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet" href="css/foundation.css">
	<link rel="stylesheet" href="icons/foundation-icons.css"/>
	
<style>      
	.size-12 { font-size: 12px; }
	.size-14 { font-size: 14px; }
	.size-16 { font-size: 16px; }
	.size-18 { font-size: 18px; }
	.size-21 { font-size: 21px; }
	.size-24 { font-size: 24px; }
	.size-36 { font-size: 36px; }
	.size-48 { font-size: 48px; }
	.size-60 { font-size: 60px; }
	.size-72 { font-size: 72px; }
	.size-X { font-size: 26px; }
</style>

</head>

<body>
	<?php
		$abfrage = "SELECT I.file, I.file as IName, C.name as CName, IC.category_id, I.path FROM piwigo_images I 
							INNER JOIN piwigo_image_category IC ON (IC.image_id = I.id)
							INNER JOIN piwigo_categories C ON (C.id = IC.category_id)";
		
/*		
SELECT 
  SUM(b.anzahl), 
  p.produkt
FROM 
  produkt p
INNER JOIN 
  bestellung b  ON (b.pr_id = p.id)
GROUP BY
  p.produkt
*/


		echo $abfrage."<br>";
	?>
	<!-- Ergebnisse anzeiegn -->
	<div class="row">
		<table>
			<th>Bild</th>
			<th>Ordner</th>
			<th>Vater</th>
		<?php
//			$i=0;
			// Inhalte			
			$ergebnis = mysql_query($abfrage);		
			while($row = mysql_fetch_object($ergebnis)) {
				$i++;
				echo "<tr>";
				echo "<td>".$row->file."</td>";
				echo "<td>".$row->CName."</td>";
				$erg=$row->category_id."|".$row->CName;
				$cat=explode("|",getFatherCat($erg));
				echo "<td>".$cat[1]."</td>";
				echo "</tr>";
//				if ($i==1) {
					$ordner = "xbmc/".$cat[1];
					if (!file_exists($ordner) && !is_dir($ordner)) {
    					if (mkdir($ordner, 0777, true) != true) {
						echo "<br>ORDNER KONNTE NICHT ERZEUGT WERDEN<br>";						
						echo "Ordner: ".$ordner."<br>";						
					};         
					} 					
					$target = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/".substr($row->path,2);					
					$link = $ordner."/".$row->IName;
					if (symlink($target, $link) != true) {
						echo "<br>LINK KONNTE NICHT ERZEUGT WERDEN<br>";						
						echo "target: ".$target."<br>";
						echo "link:   ".$link."<br>";					
					} 				
//				}
			}
		?>
		</table>
	</div>



  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation/foundation.js"></script>
  <script src="js/foundation/foundation.topbar.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
  <script src="js/foundation/foundation.reveal.js"></script>
  <script>$(document).foundation();</script>  

</body>
