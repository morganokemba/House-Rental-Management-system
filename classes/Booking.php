<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');

class Booking{
	private $db;
	private $fm;
	
	public function __construct(){
		$this->db = new Database();
		$this->fm = new Format();
	}
	
/* ================= BOOK PROPERTY ================= */
	
	public function bookProperty($renterId, $ownerId, $adId, $nftId, $data, $isAdmin = false){

		$adId = mysqli_real_escape_string($this->db->link, $this->fm->validation($adId));
		$renterId = mysqli_real_escape_string($this->db->link, $this->fm->validation($renterId));
		$ownerId = mysqli_real_escape_string($this->db->link, $this->fm->validation($ownerId));
		$nftId = mysqli_real_escape_string($this->db->link, $this->fm->validation($nftId));

		$renterName = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['rname']));
		$renterMail = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['rmail']));
		$renterPhone = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['rphone']));
		$renterAddress = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['raddress']));
		$rentType = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['renttype']));
		$adRent = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['adrent']));
		$gasBill = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['gasbill']));
		$electricBill = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['electricbill']));
		$sCharge = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['scharge']));

		// ✅ Determine status: 1 if admin booking, 0 if renter request
		$bookingStatus = $isAdmin ? 1 : 0;

		// 🔥 PREVENT DUPLICATE BOOKING
		$checkQuery = "SELECT * FROM tbl_booking 
					   WHERE adId = '$adId' AND renterId = '$renterId'";
		$check = $this->db->select($checkQuery);

		if($check){
			return "<div class='alert alert_danger'>You already booked this property!</div>";
		}

		if(empty($adId) || empty($renterId) || empty($ownerId) || empty($renterName) || empty($renterMail)){
			return "<div class='alert alert_danger'>Error! Field must not be empty</div>";
		} else{

			$query = "INSERT INTO tbl_booking(
				adId, renterId, renterName, renterMail, renterPhone, renterAddress,
				ownerId, rentType, adRent, gasBill, electricBill, sCharge, bookingStatus
			) VALUES(
				'$adId', '$renterId', '$renterName', '$renterMail', '$renterPhone',
				'$renterAddress', '$ownerId', '$rentType', '$adRent',
				'$gasBill', '$electricBill', '$sCharge', '$bookingStatus'
			)";

			$booked = $this->db->insert($query);

			if($booked){

				$updquery = "UPDATE tbl_ad
							 SET adStatus = '2'
							 WHERE adId = '$adId'";
				$updstatus = $this->db->update($updquery);

				if($nftId != 0){
					$delquery = "DELETE FROM tbl_notification 
								 WHERE notfId = '$nftId' AND adId = '$adId'";
					$this->db->delete($delquery);
				}

				return "<div class='alert alert_success'>Booking approved successfully!</div>";

			} else{
				return "<div class='alert alert_danger'>Something went wrong!</div>";
			}
		}
	}

/* ================= CANCEL BOOKING ================= */

	function cancelBooking($renterId, $ownerId, $adId){

		$adId = mysqli_real_escape_string($this->db->link, $this->fm->validation($adId));
		$renterId = mysqli_real_escape_string($this->db->link, $this->fm->validation($renterId));
		$ownerId = mysqli_real_escape_string($this->db->link, $this->fm->validation($ownerId));

		if(empty($adId) || empty($renterId) || empty($ownerId)){
			return "<div class='alert alert_danger'>Error! Data not found</div>";
		} else{

			$delquery = "DELETE FROM tbl_booking 
						 WHERE adId = '$adId' 
						 AND renterId = '$renterId' 
						 AND ownerId = '$ownerId'";

			$delbooking = $this->db->delete($delquery);

			if($delbooking){
				$updquery = "UPDATE tbl_ad
							 SET adStatus = '1'
							 WHERE adId = '$adId' 
							 AND userId = '$ownerId'";
				$this->db->update($updquery);

				return "<div class='alert alert_success'>Booking cancelled successfully!</div>";
			} else{
				return "<div class='alert alert_danger'>Something went wrong!</div>";
			}
		}
	}

/* ================= GET SINGLE PROPERTY ================= */

	public function getSingleProperty($adId){
		$adId = mysqli_real_escape_string($this->db->link, $this->fm->validation($adId));
		$query = "SELECT * FROM tbl_ad WHERE adId = '$adId'";
		return $this->db->select($query);
	}

/* ================= OTHER FUNCTIONS ================= */

	function getBookedPropertyById($adId, $renterId, $ownerId){
		$query = "SELECT rentType, adRent, gasBill, electricBill, sCharge 
		          FROM tbl_booking
		          WHERE renterId = '$renterId' 
		          AND ownerId = '$ownerId' 
		          AND adId = '$adId'";
		return $this->db->select($query);
	}

	public function getRenterInfo($renterId, $nftId){
		$query = "SELECT * FROM tbl_notification
		          WHERE renterId = '$renterId' AND notfId = '$nftId'";
		return $this->db->select($query);
	}

	public function getOwnerInfo($ownerId){
		$query = "SELECT * FROM tbl_user WHERE userId = '$ownerId'";
		return $this->db->select($query);
	}

	public function getBookingList(){		
		$query = "SELECT tbl_booking.*, tbl_ad.adTitle, tbl_ad.adImg, tbl_category.catName, tbl_booking.bookingStatus
		          FROM tbl_booking 
		          INNER JOIN tbl_ad ON tbl_booking.adId = tbl_ad.adId 
		          INNER JOIN tbl_category ON tbl_ad.catId = tbl_category.catId";
		return $this->db->select($query);
	}

	function getBookingRenter($renterId, $ownerId, $adId){
		$query = "SELECT renterName, renterMail, renterPhone, renterAddress 
		          FROM tbl_booking 
		          WHERE adId = '$adId' 
		          AND renterId = '$renterId' 
		          AND ownerId = '$ownerId'";
		return $this->db->select($query);
	}

	public function getBookingListById($userId){	
		$query = "SELECT tbl_booking.*, tbl_ad.adTitle, tbl_ad.adImg, tbl_category.catName, tbl_booking.bookingStatus
		          FROM tbl_booking 
		          INNER JOIN tbl_ad ON tbl_booking.adId = tbl_ad.adId 
		          INNER JOIN tbl_category ON tbl_ad.catId = tbl_category.catId 
		          WHERE tbl_booking.ownerId = '$userId'";
		return $this->db->select($query);
	}

	public function getNewBooking($userId){
		$query = "SELECT * FROM tbl_booking 
		          WHERE ownerId = '$userId' AND bookingStatus = '0'";
		return $this->db->select($query);
	}

	function updateBookingStatus($userId){
		$query = "UPDATE tbl_booking
		          SET bookingStatus = '1' 
		          WHERE bookingStatus = '0' 
		          AND ownerId = '$userId'";
		return $this->db->update($query);
	}
}
?>