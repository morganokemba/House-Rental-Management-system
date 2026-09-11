<?php
	include "inc/header.php";

	if(Session::get("userlogin") != true){
		echo "<script>window.location='signin.php'</script>";
	}

	$userId = Session::get("userId");

	if(isset($_GET['ownerid']) && isset($_GET['adid'])){
		$ownerId  = $_GET['ownerid'];
		$adId 	 = $_GET['adid'];

		$cancel = $bk->cancelBooking($userId, $ownerId, $adId);
		if(isset($cancel)){
			echo $cancel;
		}
	}
?>

<div class="container">
	<div class="mcol_12 admin_page_title">
		<div class="page_title overflow">
			<h1 class="sub-title">My Booking List</h1>
		</div>
	</div>

	<div class="responsive_mcol_small mcol_12">
		<div class="responsive_mcol responsive_mcol_small mcol_12">
			<div class="admin_content overflow overflow_x">
				<div class="admin_property_table">

					<!-- TABLE FULL WIDTH -->
					<?php
						$getblist = $bk->getBookingList();
						$hasBooking = false;
						if($getblist){
							while($booking = $getblist->fetch_assoc()){
								if($booking['renterId'] == $userId){
									$hasBooking = true;
									break;
								}
							}
						}

						if(!$hasBooking){
							echo "<p style='text-align:center; font-weight:bold; padding:20px;'>No property booked</p>";
						} else {
					?>
						<table id="example" class="display" style="width:100%">
						<thead>
							<tr>
								<th>No</th>
								<th>Property</th>
								<th>Category</th>
								<th>Booking Date</th>
								<th>Total Price</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$getblist->data_seek(0); // reset pointer
								$i = 0;
								while($booking = $getblist->fetch_assoc()){
									if($booking['renterId'] == $userId){
										$i++;
							?>
							<tr>
								<td><?php echo $i; ?></td>

								<td width="20%">
									<img src="<?php echo $booking['adImg']; ?>" style="width:80px; height:60px;">
									<p><?php echo $booking['adTitle']; ?></p>
								</td>

								<td><?php echo $booking['catName']; ?></td>

								<td><?php echo $fm->formatDate($booking['bookingDate']); ?></td>

								<td>
									<?php
										$total = $booking['adRent'] + $booking['gasBill'] + $booking['electricBill'] + $booking['sCharge'];
										echo $total . " / " . $booking['rentType'];
									?>
								</td>

								<td>
									<?php
										if($booking['bookingStatus'] == 0){
											echo "<span style='color:orange;'>Pending</span>";
										}else{
											echo "<span style='color:green;'>Approved</span>";
										}
									?>
								</td>

								<td>
									<a onclick="return confirm('Cancel this booking?')" 
									   href="?ownerid=<?php echo $booking['ownerId']; ?>&adid=<?php echo $booking['adId']; ?>">
										<button class="btn_delete">
											<i class="fa-solid fa-trash-can"></i>
										</button>
									</a>
								</td>
							</tr>
							<?php } } ?>
						</tbody>
						</table>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>

<?php include "inc/footer.php"; ?>