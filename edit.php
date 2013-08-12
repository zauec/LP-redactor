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
	 
	 $("#change-file").on("click", function() {        
			var sel = getElementById(sel);
			sel.selected.val(function(){
			//click action
			}) 
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
	function draw_form($bad_login = false) {
    ?>
	<div id="lp-pop-up" style="display: none;">
    <form  action="<?=$_SERVER['php_self']?>" method="post">
        <input type="text" name="login" value="логин" onclick="this.value = '';"></input><br/>
        <input type="password" name="pass" value="пароль" onclick="this.value = '';" style="float: left;"></input>
      <input type="image" src="img/entry.png" style="height: 22px; margin-top:0px; margin-left: -2px;"></input>
    </form>
	</div>
    <?
    if ($bad_login) {
        echo 'неправильный логин и/или пароль | <a id="lp-pop-click"  href="javascript: void(0)">Вход</a>';
		die();
	}
}
 
/* Проверку логина и пароля, я вынес в отдельную функцию
 * это будет очень полезно, если в будущем мы решим хранить
 * данные пользователей например в базе данных. 
 * Тогда придется изменить только эту функцию и ничего больше.
 */
 
function check_login($login, $pass) {
    return ($_POST['login'] == 'admin') && ($_POST['pass'] == 'qwerty');
}
 
// >>> точка входа <<<
 
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: edit.php");
    exit(); // после передачи редиректа всегда нужен exit или die
    // иначе выполнение скрипта продолжится.
}

 
	// считываем файл 	
	$newdata = $_POST['newd'];
	$newfile = $_POST['newf'];
	$filename = "index.php";
	
	//if ($filename != '') {
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
			
	<div id="lp-header">
	<div class="lp-mid">
		<div class="lp-title" style="float: left;"><b>Редактор лендинга </b><?=$filename?></div>
			<form action='<?=$_SERVER['php_self']?>' method= 'post' style="float: left;">
			<select id='sel'>
			<?foreach (glob("*.php") as $name) {?>
				<option name='newf' value="<?=$name?>"<?=$name?>"><?=$name?></option><?	
			}?>
			</select>
			</form> <a id="change-file" href="javascript: void(0)"> Выбрать файл</a>
	
		<div class="float-right"><?// на случай если мы уже авторизированы 
		if(!isset($_SESSION['login'])){
		
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
					draw_form(true);
					// параметр true передается чтобы показать, что был введен
					// неправильный пароль
			}}?>Здравствуйте <b>Гость</b> |  <a id="lp-pop-click"  href="javascript: void(0)">Вход</a>
		<?die();		
		}
		 ?>
	</div>
		<div class="float-right">Здравствуйте <b><?=$_SESSION['login']?></b> |  <a href="edit.php?logout">Выход</a></div>
	</div>
	</div>
	<?if(!isset($_SESSION['login'])){
		die();
	}?>
	<div id="lp-wrapper">		
		<div id="lp-redaktor">
			<form action='<?=$_SERVER['php_self']?>' method= 'post' >
				<div class="lp-shad">
					<textarea class="suitup-textarea" name='newd'  rows="50"><?=$data_html?> </textarea>
				</div><br>
				<input type='submit' value='Редактировать'>
			</form><br>
		</div>
<?/*
	} else {
		echo "Error in opening file"?>
		<form action='<?=$_SERVER['php_self']?>' method= 'post' >
			<select id='sel'>
			<?foreach (glob("*.php") as $name) {?>
				<option name='newf' value="<?=$name?>"<?=$name?>"><?=$name?></option><?	
			}?>
			</select>
		</form>
	<?}*/?>
</div>
</body>
</html>