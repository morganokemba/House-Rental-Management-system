<?php
	include"inc/header.php";
	
	// ================= BOOK PROPERTY =================
	if(isset($_GET['bookid'])){
		if(Session::get("userlogin") != true){
			echo "<script>window.location='signin.php'</script>";
		} else{
			$adId = $_GET['bookid'];
			$renterId = Session::get("userId");

			$getAd = $bk->getSingleProperty($adId);
			if($getAd){
				$ad = $getAd->fetch_assoc();
				$ownerId = $ad['userId'];

				$data = array(
					"rname"     => Session::get("userName"),
					"rmail"     => Session::get("userEmail"),
					"rphone"    => Session::get("userPhone"),
					"raddress"  => Session::get("userAddress"),
					"renttype"  => $ad['rentType'],
					"adrent"    => $ad['adRent'],
					"gasbill"   => $ad['gasBill'],
					"electricbill" => $ad['electricBill'],
					"scharge"   => $ad['serviceCharge']
				);

				$book = $bk->bookProperty($renterId, $ownerId, $adId, 0, $data);

				if(isset($book)){
					echo $book;
				}
			}
		}
	}

	// ================= CANCEL BOOKING =================
	if(isset($_GET['cancelid']) && isset($_GET['owner'])){
		if(Session::get("userlogin") != true){
			echo "<script>window.location='signin.php'</script>";
		} else{
			$adId = $_GET['cancelid'];
			$ownerId = $_GET['owner'];
			$renterId = Session::get("userId");

			$cancel = $bk->cancelBooking($renterId, $ownerId, $adId);

			if(isset($cancel)){
				echo $cancel;
			}
		}
	}
	
	// ================= WISHLIST =================
	if(isset($_GET['wlistid'])){
		if($_GET['wlistid'] == NULL){
			echo"<script>window.location='index.php'</script>";
		} else{
			if(Session::get("userlogin") != true){
				echo"<script>window.location='signin.php'</script>";
			} else{
				$wlistId = $_GET['wlistid'];
				$loginId  = Session::get("userId");
				$addWlist = $pro->addToWishlist($wlistId, $loginId);
				
				if(isset($addWlist)){ 
					echo $addWlist; 
				}
			}
		}
	}
	
	// ================= CONTACT FORM =================
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['sendmessage'])){
		$sendmsg = $ibx->sendMessage($_POST);
		
		if(isset($sendmsg)){ 
			echo $sendmsg; 
		}
	}
?>

<!--Header Section End------------->

<!--Banner Section Start------------->	
<?php include"inc/banner.php"; ?>
<!--Banner Section End------------->


<!--Property List Section Start------------->	
<div class="list overflow">
	<h1 class="sub-title">property list</h1>

	<?php
	$getAllAd = $pro->getAllPropertyByRange();
	if($getAllAd){ 
	$totalAd = mysqli_num_rows($getAllAd);
	?>

	<div class="list_content <?php if($totalAd > 2){ ?>slider<?php } ?>">

	<?php while($getad = $getAllAd->fetch_assoc()){ ?>

	<div class="list_item overflow">
		<div class="item_box overflow">

		<a href="property_details.php?adid=<?php echo $getad['adId'];?>">
			<div class="item_box_upper overflow">
				<div class="item_upper item_category">
					<p><?php echo $getad['catName'];?></p>
				</div>

				<div class="item_upper item_pricebox overflow">
					<div class="item_upper_left">
						<p>
							<span>
								<?php echo $getad['adRent'];?>TK / 
								<?php echo ($getad['rentType']=="mo")?"Month":"Week"; ?>
							</span>
						</p>
					</div>

					<a href="?wlistid=<?php echo $getad['adId'];?>">
						<div class="item_upper_left item_wlist_icon">
							<p><i class="fa-solid fa-heart"></i></p>
						</div>
					</a>

				</div>
			</div>
		</a>

		<a href="property_details.php?adid=<?php echo $getad['adId'];?>">
			<div class="list_img overflow">
				<img src="<?php echo $getad['adImg'];?>" alt="ad image">
			</div>
		</a>

		<div class="item_box_lower overflow"> 
			<p><?php echo $getad['adTitle'];?></p>

			<h3>
				<i class="fa-brands fa-accusoft"></i>
				<span><?php echo $getad['catName'];?></span>
			</h3>

			<p>
				<i class="fa-solid fa-file-pen"></i>
				Posted on: <?php echo $fm->formatDate($getad['adDate']);?>
			</p>

			<p>
				<img class="taka_sign" src="images/taka.png"/>
				<?php echo $getad['adRent'];?> /
				<?php echo ($getad['rentType']=="mo")?"Month":"Week"; ?>
			</p>

			<p>
				<i class="fa-solid fa-location-dot"></i>
				<?php echo $getad['adAddress'];?>
			</p>

			<!-- BOOK BUTTON -->
			<a href="?bookid=<?php echo $getad['adId']; ?>">
				<button class="btn_success">Book Now</button>
			</a>

		</div>

		</div>
	</div>

	<?php } ?>

	</div>
	<?php } ?>

	<div class="browse_list_button">
		<a href="property_list.php">
			<button class="btn_success btn_browse">browse list</button>
		</a>
	</div>
</div>

<!--Property List Section End------------->	


<!--Popular Category Section Start------------->	
<div class="list overflow">
	<h1 class="sub-title">popular category</h1>

	<div class="list_content">
	<?php
	$getcat = $cat->getAllCat();
	if($getcat){
		while($category = $getcat->fetch_assoc()){
		$catId = $category['catId'];
		$totalAd = $cat->getCatAdNum($catId);
	?>

	<div class="list_item popular_category overflow">
		<div class="item_box overflow">

			<a href="property_by_cat.php?catid=<?php echo $category['catId'];?>">
				<div class="popular_cat_heading">
					<p>category</p>
				</div>

				<div class="popular_cat_img">
					<img src="<?php echo $category['catImg']?>"/>

					<div class="popular_cat_text">
						<p><?php echo $category['catName']?></p>
						<p><?php echo (!empty($totalAd)) ? $totalAd : "0"; ?> Property ads</p>
					</div>
				</div>
			</a>

		</div>
	</div>

	<?php } } ?>
	</div>
</div>

<!--Popular Category Section End------------->	


<!--About Section------------->	
<div class="about"> 
	<h1 class="sub-title">About Us</h1>
	<div class="about_text">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
	</div>
</div>


<!--Contact Section------------->	
<div class="contact">
	<form action="" method="POST">
		<h1 class="sub-title">Get In Touch</h1>

		<div class="contact_body overflow">
			<div class="contact_part">
				<label>Name:</label>
				<input type="text" name="name" required>

				<label>Mobile No:</label>
				<input type="text" name="phone" required>

				<label>Email:</label>
				<input type="text" name="email" required>
			</div>

			<div class="contact_part">
				<label>Message:</label>
				<textarea name="message" required></textarea>
			</div>
		</div>

		<div class="contact_button">
			<button class="btn_success" type="submit" name="sendmessage">Send</button>
		</div>
	</form>
</div>

<?php include"inc/footer.php"; ?>