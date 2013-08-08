<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
	@import url(css/suitup.css);
	@import url(css/main.css);
	body {
	background: url('http://freelansim.ru/assets/body.bg-62bc77c03c1531646a55482139044a67.png');
	color: #000;
	}
	.redaktor {
	width: 1000px;
	margin: 0 auto;
	}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/suitup.jquery.js"></script>
<script src="js/extended-commands.suitup.jquery.js"></script>
<script>
$( function(){
	$('.suitup-textarea')
		.suitUp()
		.show();
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
// Зачитываем файл
$filename = "index.php";
   
$newdata = $_POST['newd'];
if ($newdata != '') {

$fh = fopen($filename, "r") or die("Could not open file!");
// Читаем его
$data = fread($fh, filesize($filename)) or die("Could not read file!");
// Закрываем
fclose($fh);

	$dom = new DOMDocument(); 
	$dom->loadHTML($data); 
	$dom->preserveWhiteSpace = false; 
	$lement = $dom->getElementsByTagName('body'); 
	$data_htm = DOMinnerHTML($lement); 
	//$ru = iconv('utf-8', 'windows-1251',$data);
	echo $data_htm;
	
/*/ Открываем файл на запись
$fw = fopen($filename, 'w') or die('Невозможно открыть файл для записи');
// Ведём запись в файл + stripslashes
$fb = fwrite($fw,stripslashes($newdata)) or die('Невозможно иязменить файл');
// Закрываем
fclose($fw);*/
} 

$dom = new DOMDocument(); 
$dom->loadHTMLFile($filename); 
$dom->preserveWhiteSpace = false; 

$data = $dom->getElementById('main'); 
$data_html = DOMinnerHTML($data); 

$data_ru = iconv('utf-8', 'windows-1251',$data);

//just for testing
// Открываем файл на чтение
/*$fh = fopen($filename, "r") or die("Невозможно открыть файл для чтения");
// Читаем его
$data = fread($fh, filesize($filename)) or die("Невозможно прочитать файл");
$data_ru = iconv('utf-8', 'windows-1251',$data);
// Закрываем
fclose($fh);
*/?>
<div class="redaktor">
<h3>Редактор лендинга <?=$filename?></h3>
<form action='<?=$_SERVER['php_self']?>' method= 'post' >
<div class="shad">
<textarea class="suitup-textarea" name='newd'  rows="50"> <?=$data_html?> </textarea>
</div><br>
<input type='submit' value='Редактировать'>
</form><br><br>
</div>
</body>
</html>