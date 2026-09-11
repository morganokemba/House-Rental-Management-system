<?php
	include"inc/header.php";

	// Only admin access
	if(Session::get("userLevel") != 3){
		echo "<script>window.location='../index.php'</script>";
	}

	if((!isset($_GET['renterid']) || !isset($_GET['ownerid']) || !isset($_GET['adid'])) 
		|| ($_GET['renterid'] == NULL || $_GET['ownerid'] == NULL || $_GET['adid'] == NULL)){
		echo "<script>window.location='../index.php'</script>";
	} else {
		$renterId = $_GET['renterid'];
		$ownerId  = $_GET['ownerid'];
		$adId 	  = $_GET['adid'];
		$nftId    = isset($_GET['nftid']) ? $_GET['nftid'] : 0;

		$renterinfo = $bk->getRenterInfo($renterId,$nftId);
		$ownerinfo = $bk->getOwnerInfo($ownerId);

		if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['book_ad'])){
			// ✅ Pass isAdmin = true for admin booking
			$BookAd = $bk->bookProperty($renterId, $ownerId, $adId, $nftId, $_POST, true);
		}
	}
?>

<div class="container">
	<div class="mcol_12 admin_page_title">
		<div class="page_title overflow">
			<h1 class="sub-title">Property Booking</h1>
			<h4><a href="?action=logout"><i class="fa-solid fa-right-from-bracket"></i><span>Sign Out</span></a></h4>
		</div>
	</div>

	<div class="responsive_mcol_small mcol_12">
		<?php include"inc/sidebar.php";?>

		<div class="responsive_mcol  responsive_mcol_small mcol_8">
			<div class="admin_content overflow">
				<form action="" method="POST">

				<!-- Renter Details -->
				<div class="add_property_block overflow">
				<?php
					if($renterinfo){
						while($rentInfo = $renterinfo->fetch_assoc()){ 
				?>
					<div class="property_block_title"><h2>Renter Details</h2></div>
					<div class="property_block_body overflow">
						<?php if(isset($BookAd)) echo $BookAd; ?>
						<div class="add_property_title"><p>Full Name</p></div>
						<div class="add_property_field">
							<input type="text" name="rname" value="<?php echo $rentInfo['notfName'];?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Email</p></div>
						<div class="add_property_field">
							<input type="email" name="rmail" value="<?php echo $rentInfo['notfEmail']?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Phone</p></div>
						<div class="add_property_field">
							<input type="phone" name="rphone" value="<?php echo $rentInfo['notfPhone']?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Address</p></div>
						<div class="add_property_field">
							<textarea name="raddress"><?php echo $rentInfo['notfAddress']?></textarea>
						</div>
					</div>
				<?php } } ?>
				</div>

				<!-- Owner Details -->
				<div class="add_property_block overflow">
				<?php
					if($ownerinfo){
						while($ownInfo = $ownerinfo->fetch_assoc()){ 
				?>
					<div class="property_block_title"><h2>Owner Details</h2></div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Full Name</p></div>
						<div class="add_property_field">
							<input type="text" value="<?php echo $ownInfo['firstName']." ".$ownInfo['lastName']?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Email</p></div>
						<div class="add_property_field">
							<input type="email" value="<?php echo $ownInfo['userEmail']?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Phone</p></div>
						<div class="add_property_field">
							<input type="phone" value="<?php echo $ownInfo['cellNo']?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Address</p></div>
						<div class="add_property_field">
							<textarea><?php echo $ownInfo['userAddress']?></textarea>
						</div>
					</div>
				<?php } } ?>
				</div>

				<!-- Price Details -->
				<div class="add_property_block overflow">
				<?php
					$getAd = $pro->getPropertyById($adId);
					if($getAd){
						while($ad = $getAd->fetch_assoc()){ 
				?>
					<div class="property_block_title"><h2>Price Details</h2></div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Rent Type</p></div>
						<div class="add_property_field">
							<select name="renttype">
								<option value="mo"<?php if($ad['rentType']=="mo") echo " selected";?>>Per Month</option>
								<option value="we"<?php if($ad['rentType']=="we") echo " selected";?>>Per Week</option>
							</select>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Rent (BDT)</p></div>
						<div class="add_property_field">
							<input type="text" name="adrent" value="<?php echo $ad['adRent'];?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Gas Bill</p></div>
						<div class="add_property_field">
							<input type="text" name="gasbill" value="<?php echo $ad['gasBill'];?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Electric Bill</p></div>
						<div class="add_property_field">
							<input type="text" name="electricbill" value="<?php echo $ad['electricBill'];?>"/>
						</div>
					</div>
					<div class="property_block_body overflow">
						<div class="add_property_title"><p>Service Charge</p></div>
						<div class="add_property_field">
							<input type="number" name="scharge" value="<?php echo $ad['sCharge'];?>"/>
						</div>
					</div>

					<div class="action_button overflow">
						<button type="submit" name="book_ad" onclick="return confirm('Are you sure to approve booking of this property?')">Approve Booking</button>
					</div>
				<?php } } ?>
				</div>

				</form>
			</div>
		</div>
	</div>
</div>

<?php include"inc/footer.php"; ?>