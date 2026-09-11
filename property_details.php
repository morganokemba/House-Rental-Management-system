<?php  
	include "inc/header.php";

	if(isset($_POST['signin'])){
		header("Location:signin.php");
	}

	if(!isset($_GET['adid']) || $_GET['adid'] == NULL){
		echo "<script>window.location='index.php'</script>";
	} else {
		$adId = $_GET['adid'];
		$adimg = $pro->getPropertyImage($adId);

		$getAd = $pro->getPropertyById($adId);
		if($getAd){
			while($ad = $getAd->fetch_assoc()){
				$adId 	  = Session::set("adId", $ad['adId']);
				$ownerId  = Session::set("ownerId", $ad['userId']);
			}

			$adId 	  = Session::get("adId");
			$ownerId  = Session::get("ownerId");
			$renterId = Session::get("userId");

			if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sendmsg'])){
				// Fix: Ensure 'message' key exists to avoid undefined array key error
				if(!isset($_POST['message'])){
					$_POST['message'] = '';
				}
				$sendNotif = $ntf->notificationInsert($adId, $ownerId, $renterId, $_POST);
			}
		}
	}
?>
<!--Header Section End------------->

<div class="page_title">
	<h1 class="sub-title">Property Details</h1>
</div>

<!--Property Details Section Start-------------> 
<div class="container">
	<div class="responsive_mcol mcol_8">

	<?php
		$getAd = $pro->getPropertyById($adId);
		if($getAd){
			while($ad = $getAd->fetch_assoc()){
	?>
		<div class="property_details">
			<div class="property_title">
				<h1><?php echo $ad['adTitle'];?></h1>
				<div class="property_location">
					<p><?php echo $ad['adAddress'];?></p>
				</div>
			</div>
		</div>

		<!--Property Image Section-------------> 
		<div class="property_gallery">
			<?php if(!empty($ad['adImg'])){ ?>	
				<div><img src="<?php echo $ad['adImg'];?>" alt="property image"/></div>
			<?php } ?>
			<?php
				if($adimg){
					while($adImage = $adimg->fetch_assoc()){ ?>	
				<div>
					<img src="<?php echo $adImage['adImg'];?>" alt="property image"/>
				</div>
			<?php } } ?>
		</div>

		<div class="property_small_details">
			<h2>Hosted by <?php echo $ad['firstName']." ".$ad['lastName'];?></h2>
			<h4><img class="taka_sign" src="images/taka.png" alt="taka"/>
				<?php echo $ad['adRent'];?>/<?php echo $ad['rentType'];?> 
				<?php if(!empty($ad['adNegotiable'])){ ?><span>(negotiable)</span><?php } ?>
			</h4>
		</div>

		<div class="property_details_list">
			<h1>Details</h1>
			<div class="property_details_body">
				<!-- Property features loop -->
				<?php 
					$features = [
						'Property Type' => $ad['catName'],
						'Preferred Renter' => $ad['prefferedRenter'],
						'Available From' => $fm->formatDate($ad['adDate']),
						'Size' => $ad['adSize']." sft",
						'Floor No' => $ad['floorNo'] . (($ad['floorNo']==1)?'st':(($ad['floorNo']==2)?'nd':(($ad['floorNo']==3)?'rd':'th'))),
						'Total Unit' => $ad['totalUnit'],
						'Total Room' => $ad['totalRoom'],
						'Bedroom(s)' => $ad['totalBed'],
						'Washroom(s)' => $ad['totalBath'],
						'Attach Washroom' => $ad['attachBath'],
						'Common Washroom' => $ad['commonBath'],
						'Balconies' => $ad['totalBelcony']
					];

					foreach($features as $label => $value){ ?>
						<div class="property_feature overflow">
							<div><p><?php echo $label;?></p></div>
							<div><p><span><?php echo $value;?></span></p></div>
						</div>
				<?php } ?>

				<h3>Location</h3>
				<div class="property_feature overflow">
					<div><p>Area</p></div>
					<div><p><span><?php echo $ad['adArea'];?></span></p></div>
				</div>
				<div class="property_feature overflow">
					<div><p>Address</p></div>
					<div><p><span><?php echo $ad['adAddress'];?></span></p></div>
				</div>

				<h3>Price Details</h3>
				<div class="property_feature overflow">
					<div><p>Rent Type</p></div>
					<div><p><span><?php echo ($ad['rentType']=="mo")?"Per month":"Per week"; ?></span></p></div>
				</div>
				<div class="property_feature overflow">
					<div><p>Gas Bill</p></div>
					<div><p><span>$<?php echo $ad['gasBill'];?></span></p></div>
				</div>
				<div class="property_feature overflow">
					<div><p>Electric Bill</p></div>
					<div><p><span>$<?php echo $ad['electricBill']; echo ($ad['eBillType']=="Inc")?" (Including)":" (Excluding)"; ?></span></p></div>
				</div>
				<div class="property_feature overflow">
					<div><p>Service Charge</p></div>
					<div><p><span>$<?php echo $ad['sCharge'];?></span></p></div>
				</div>

				<h3>Facilities</h3>
				<?php 
					$facilities = [
						'Generator' => $ad['adGenerator'],
						'Lift/Elevator' => $ad['liftElevetor'],
						'Wifi Connectivity' => $ad['adWifi'],
						'Car Parking' => $ad['carParking'],
						'Open Space' => $ad['openSpace'],
						'Play Ground' => $ad['openSpace'],
						'CCTV Camera' => $ad['ccTV'],
						'Security Guard' => $ad['sGuard']
					];

					foreach($facilities as $label => $value){ ?>
						<div class="property_feature overflow">
							<div><p><?php echo $label;?></p></div>
							<div><p><span><?php echo $value;?></span></p></div>
						</div>
				<?php } ?>

				<h3>Other Details</h3>
				<?php 
					$otherDetails = [
						'Total Floor' => $ad['totalFloor'],
						'Floor Type' => $ad['floorType'],
						'Built Year' => $ad['builtYear']
					];

					foreach($otherDetails as $label => $value){ ?>
						<div class="property_feature overflow">
							<div><p><?php echo $label;?></p></div>
							<div><p><span><?php echo $value;?></span></p></div>
						</div>
				<?php } ?>
			</div>

			<h1>Description</h1>
			<div class="property_details_body property_description">
				<?php echo $ad['adDetails'];?>
			</div>
		</div>
	<?php } } ?>	

	<!--Booking Section Start-------------> 
	<div class="property_contact">
		<div class="property_contact_title">
			<h1>Book Now</h1>
			<p>Contact us for necessary information about booking</p>
		</div>

		<form action="" method="POST">
		<?php if(Session::get("userlogin") == true){ ?>
			<?php if(isset($sendNotif)){ echo $sendNotif; } ?>
			<div class="contact_body overflow">
				<div class="contact_part">
				  <label for="name"><b>Name:</b></label>
				  <input type="text" placeholder="Enter full name" name="name" required><br><br><br>
				  
				  <label for="email"><b>Email:</b></label>
				  <input type="email" placeholder="Enter email" name="email" required><br><br><br>
				  
				  <label for="phone"><b>Mobile No:</b></label>
				  <input type="phone" placeholder="Enter mobile number" name="phone" required><br><br><br>
				  
				  <label for="address"><b>Address:</b></label>
				  <textarea style="height:4em;" placeholder="Address" name="address" required></textarea><br><br><br>
				</div>
			</div>

			<div class="action_button overflow">
				<button type="submit" name="sendmsg">Book Now</button>
			</div>
		<?php } else{ ?>     
			<div class="login_button overflow">
				<button class="btn_success" type="submit" name="signin">Sign In</button>
				<span>to book this property</span>
			</div>
		<?php } ?>  
		</form>
	</div>
	<!--Booking Section End-------------> 

	</div>
</div>
<!--Property Details Section End------------->

<!--Footer Section Start-------------> 
<?php include "inc/footer.php"; ?>
<!--Footer Section End-------------> 