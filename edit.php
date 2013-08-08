<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="css/suitup.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/suitup.jquery.js"></script>
<script src="js/extended-commands.suitup.jquery.js"></script>
<script>
$( function(){
	$('.suitup-textarea')
		.suitUp()
		.show();
		
	function CSSLoad(file){
		var link = document.createElement("link");
		link.setAttribute("rel", "stylesheet");
		link.setAttribute("type", "text/css");
		link.setAttribute("href", file);
		document.getElementsByTagName("head")[0].appendChild(link)
	}
	//Feature dynamic loading css 
	//CSSLoad('css/main.css');
});
</script>
</head>
<body>
<?error_reporting(0);
	function DOMinnerHTML($element) 
	{ 
		$innerHTML = ""; 
		$children = $element->childNodes; 
		foreach ($children as $child) 
		{ 
			$tmp_dom = new DOMDocument(); 
			$tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
			$innerHTML.=trim($tmp_dom->saveHTML()); 
		} 
		return $innerHTML; 
	} 
	// считываем файл 	
	$filename = "index.php";
	$newdata = $_POST['newd'];
	
	if ($newdata != '') {
		//Open to read
		$fh = fopen($filename, "r") or die("Could not open file!");
		$data_file = fread($fh, filesize($filename)) or die("Could not read file!");
		fclose($fh);
		//Modify main content
		$u8_data = iconv('windows-1251', 'utf-8',$newdata);
		$output_data = preg_replace( '|<!--edit_area-->.*<!--edit_area-->|ism', $u8_data, $data_file );
		//Open to write
		$fw = fopen($filename, 'w') or die('Невозможно открыть файл для записи');
		$fb = fwrite($fw,stripslashes($output_data)) or die('Невозможно иязменить файл');
		fclose($fw);
	} 
	//Load main content into form
	$dom = new DOMDocument(); 
	$dom->loadHTMLFile($filename); 
	$dom->preserveWhiteSpace = false; 
	$data = $dom->getElementById('main'); 
	$data_html = DOMinnerHTML($data); 
	$data_ru = iconv('utf-8', 'windows-1251',$data);?>
		
	<div id="redaktor">
		<h3>Редактор лендинга <?=$filename?></h3>
		<form action='<?=$_SERVER['php_self']?>' method= 'post' >
			<div class="shad">
				<textarea class="suitup-textarea" name='newd'  rows="50"> <?=$data_html?> </textarea>
			</div><br>
			<input type='submit' value='Редактировать'>
		</form><br>
	</div>
</body>
</html>