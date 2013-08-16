<?session_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="css/main.css" />  
<link rel="stylesheet" type="text/css" href="css/suitup.css" />
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/suitup.jquery.js"></script>
<script src="js/extended-commands.suitup.jquery.js"></script>
<script>
$( function(){
	$('.suitup-textarea')
		.suitUp()
		//.show();

	    $("#lp-pop-click").toggle(function(){
		$('#lp-pop-up').slideToggle();
        $('#lp-header').animate({height:85});
    },function(){
		$('#lp-pop-up').slideToggle();
         $('#lp-header').animate({height:35});
    });

	function CSSLoad(file){
		var link = document.createElement("link");
		link.setAttribute("rel", "stylesheet");
		link.setAttribute("type", "text/css");
		link.setAttribute("href", file);
		document.getElementsByTagName("head")[0].appendChild(link)
	}
	//Feature dynamic loading css 
	//CSSLoad('css/main.css');

	 $('#sel').change(function(){
		var text = $("#sel option:selected").text();
		$("input.newF").val(text);
	});
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
	function draw_form($bad_login = false) {?>
		<div id="lp-pop-up" style="display: none;">
			<form  action="<?=$_SERVER['php_self']?>" method="post">
				<input type="text" name="login" value="логин" onclick="this.value = '';"></input><br/>
				<input type="password" name="pass" value="пароль" onclick="this.value = '';" style="float: left;"></input>
			  <input type="image" src="img/entry.png" style="height: 22px; margin-top:0px; margin-left: -2px;"></input>
			</form>
		</div>
		<?if ($bad_login) {
			echo 'неправильный логин и/или пароль | <a id="lp-pop-click"  href="javascript: void(0)">Вход</a>';
			die();
		}
	}
/* Проверку логина и пароля, вынес в отдельную функцию
 * это будет очень полезно, если в будущем решим хранить
 * данные пользователей в базе данных. 
 */
	function check_login($login, $pass) {
		return ($_POST['login'] == 'admin') && ($_POST['pass'] == 'qwerty');
	}
// >>> точка входа <<<
// считываем входящие POST и GET параметры	
	if (isset($_GET['logout'])) {
		session_unset();
		session_destroy();
		header("Location: edit.php");
		exit();	
// после передачи редиректа всегда нужен exit или die
// иначе выполнение скрипта продолжится.
	}
//если получили новый файл, сохраняем его имя в переменную сессии, 
//иначе берем значение записанное ранее
	$newdata = $_POST['newD'];
	if (isset($_POST["newF"])) {
		$filename = $_POST["newF"];
		$_SESSION['newF'] =$_POST["newF"];
	} else {
		$filename = $_SESSION['newF'];
	}
	if ($newdata != '') {
		//Открываем файл на чтение
		$fh = fopen($filename, "r") or die("Could not open file!");
		$data_file = fread($fh, filesize($filename)) or die("Could not read file!");
		fclose($fh);
		//Меняем главный контент
		$u8_data = iconv('windows-1251', 'utf-8',$newdata);
		$output_data = preg_replace( '|<!--edit_area-->.*<!--edit_area-->|ism', $u8_data, $data_file );
		//Открываем файл на запись
		$fw = fopen($filename, 'w') or die('Невозможно открыть файл для записи');
		$fb = fwrite($fw,stripslashes($output_data)) or die('Невозможно иязменить файл');
		fclose($fw);		
	} 
	//Загружаем содержимое контента в форму
	$dom = new DOMDocument(); 
	$dom->loadHTMLFile($filename); 
	$dom->preserveWhiteSpace = false; 
	$data = $dom->getElementById('main'); 
	$data_html = DOMinnerHTML($data); 
	$data_ru = iconv('utf-8', 'windows-1251',$data);?>
	
	<div id="lp-header">
		<div class="lp-mid">
			<div class="lp-title" style="float: left;"> <b>Редактор лендинга </b></div>
			<?if(isset($_SESSION['login'])) {?>
				<form id="file-load" action='<?=$_SERVER['php_self']?>' method= 'post'>
					<select name="sel" id="sel">
					<option value='' disabled selected style='display:none;'>Выберите файл</option>
					<?foreach (array_merge(glob("*.php"),glob("*.html")) as $name) {?>
						<?if ($name != "edit.php" ) {?>
							<?if ($name == $filename ) {?>
							<option name='newF' value="<?=$name?>" selected> <?=$name?></option>
							<?} else {?>
							<option name='newF' value="<?=$name?>"><?=$name?></option>
							<?}?>
						<?}?>
					<?}?>
					</select>
					<input type="hidden" name="newF" value="" class="newF">
				</form>
				 <a href="#" onclick="document.getElementById('file-load').submit(); return false;"> Подгрузить страницу</a>
			<?}?>
			<div class="float-right">
				<?if(!isset($_SESSION['login'])) {  // на случай если мы уже авторизированы 
					$login = $_POST['login'];
					$pass = $_POST['pass'];
					if (count($_POST) <= 0) {
						draw_form();
					} else {
						if (check_login($login, $pass)) {
							$_SESSION['login'] = $login;
							header("Location: edit.php");
							exit();
						} else {
							draw_form(true);	// параметр true передается чтобы показать, что был введен неправильный пароль
						}
					}?>
					Здравствуйте <b style="color: #EF7247;">Гость</b> |  <a id="lp-pop-click"  href="javascript: void(0)">Вход</a>
					<?die();		
				}?>
			</div>
			<div class="float-right">Здравствуйте <b style="color: #EF7247;"><?=$_SESSION['login']?></b> |  <a href="edit.php?logout">Выход</a></div>
		</div>
	</div>
	<?if(!isset($_SESSION['login'])){
		die();
	}?>
	<div id="lp-wrapper">		
		<div id="lp-redaktor">
			<form action='<?=$_SERVER['php_self']?>' method= 'post' >
				<div class="lp-shad">
					<textarea class="suitup-textarea" name='newD'  rows="50"><?=$data_html?> </textarea>
				</div><br>
				<input type='submit' value='Редактировать'>
			</form><br>
		</div>
	</div>
</body>
</html>