<?php
	include'lib/Session.php';
	Session::init();
	include'lib/Database.php';
	include'helpers/Format.php';

	spl_autoload_register(function($class){
		include_once'classes/'.$class.'.php';
	});

	$db  = new Database();
	$fm  = new Format();
	$usr = new User();
	$pro = new Property();
	$cat = new Category();
	$ibx = new Inbox();
	$ntf = new Notification();
	$src = new Search();
	$bk  = new Booking();
	
	if(isset($_GET['action']) && $_GET['action'] == "logout"){
		Session::destroy();
	}
?>

<?php
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache"); 
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
  header("Cache-Control: max-age=2592000");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="house rental system, system, house">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="author" content="Munaim Khan">
	<title>
		<?php echo $fm->title()." - ".TITLE; ?>
	</title>

	<link rel="stylesheet" type="text/css" href="css/fontawesome/css/all.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/fontawesome/css/fontawesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="mystyle.css"/>
</head>

<body>

<div class="top_header overflow">
	<div class="top_left col overflow">
		<div class="logoimg overflow">
			<img src="images/logo.jpg" alt="logo"/>
		</div>
	</div>

	<div class="col top_right overflow">
	<?php if(Session::get("userlogin") == true){ ?>
		<div class="userimg overflow">
			<?php if(empty(Session::get("userImg"))){ ?>
				<img src="images/avater.png"/>
			<?php } else{ ?>
				<img src="<?php echo Session::get("userImg");?>"/>
			<?php } ?>
		</div>
		<div class="users_name">
			<p><?php echo Session::get("userFName")." ".Session::get("userLName");?></p>
		</div>
	<?php } ?>
	</div>
</div>

<?php
	$path = $_SERVER['SCRIPT_FILENAME'];
	$title = basename($path, '.php');
?>

<nav class="topnav" id="navList">
<ul>

<li>
	<a href="index.php" <?php if($title == "index"){ ?> id="active" <?php } ?>>home</a>
</li>

<li>
	<a href="property_list.php" <?php if($title == "property_list"){ ?> id="active" <?php } ?>>property list</a>
</li>

<!-- ✅ MY BOOKINGS BUTTON -->
<?php if(Session::get("userlogin") == true && Session::get("userLevel") == 1){ ?>
<li>
	<a href="mybookings.php" <?php if($title == "mybookings"){ ?> id="active" <?php } ?>>
		my bookings
	</a>
</li>
<?php } ?>

<li>
	<a href="help_support.php">help & support</a>
</li>

<?php if(Session::get("userlogin") == true){ ?>
<li>
	<a href="?action=logout">sign out</a>
</li>
<?php } else{ ?>
<li>
	<a href="signin.php">sign in</a>
</li>
<?php } ?>

</ul>
</nav>