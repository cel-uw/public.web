<?php 
	// defining available vars
	
	/* @var $subscriptionObj Subscription */
	$subscriptionObj 	= $subscription['subscriptionObj'];
	
	/* @var $paymentObj Payment */
	$paymentObj 		= $subscription['paymentObj'];
	
	/* @var $purchaserObj Purchaser */
	$purchaserObj 		= $subscription['purchaserObj'];
	
	// array of Subscriber objects
	$subscribers_arr 	= $subscription['subscribers_arr'];
?>


<?php print l('<< Back to Rosters','5d-courses-admin/rosters/subscriptions')?>

<h2>Purchaser Info</h2>
<div class="purchaser_info">
	<?php 
	$out .= '<span class="label">First Name: </span>'.$purchaserObj->getProfile_first_name().'<br>';
	$out .= '<span class="label">Last Name: </span>'.$purchaserObj->getProfile_last_name().'<br>';
	$out .= '<span class="label">Preferred Name: </span>'.$purchaserObj->getPreferred_name().'<br>';
	$out .= '<span class="label">Current Position/Title: </span>'.$purchaserObj->getProfile_current_position().'<br>';
	$out .= '<span class="label">Daytime Phone: </span>'.$purchaserObj->getDaytime_phone().'<br>';
	$out .= '<span class="label">School/District/ Organization: </span>'.$purchaserObj->getProfile_organization().'<br>';
	echo $out;
	?>
</div>
<br>
<h2>Subscription Info</h2>
<div class="purchaser_info">
	<?php 
	$out = '<span class="label">Start Date: </span>' . date("F d Y", strtotime($subscriptionObj->getStart_date()) ) . '<br>';
	$out .= '<span class="label">Expiration Date: </span>'.date("F d Y", strtotime($subscriptionObj->getExpire_date()) ).'<br>';
	$out .= '<span class="label">Nubmber of Licenses: </span>'.$subscriptionObj->getLicences_qty().'<br>';
	$out .= '<span class="label">Total Price: </span>'. fuse_money_format( $paymentObj->getAmount() ).'<br>';
	
	
	// PO code
	if($paymentObj->getPayment_type() == 'po'){
		$out .= '<span class="label">Purchase Order #: </span>'.array_get( unserialize($paymentObj->getPayment_data()), 'po_number').'<br/>';
	}
	echo $out;
	?>
</div>
<br><br>
<?php 
if(!empty($subscribers_arr))
{
	$out = '<h2>Subscribers Info</h2>';
	$i=1;
	foreach ($subscribers_arr as $subscriberObj)
	{
// 		$subscriberObj = $subscriberRepo->getSubscriberValidateBySubscriberObj($subscriber);
		// 				print_r($subscriberObj);
		$out .= '<div class="subscriber_info">';
		$out .= '<h3>Subscriber '.$i.'</h3>';
		$out .= '<span class="label">Email: </span>'.$subscriberObj->getMail().'<br>';
		$out .= '<span class="label">School/Organization: </span>'.$subscriberObj->getProfile_organization().'<br>';
		$out .= '<span class="label">First Name: </span>'.$subscriberObj->getProfile_first_name().'<br>';
		$out .= '<span class="label">Last Name: </span>'.$subscriberObj->getProfile_last_name().'<br>';
		$out .= '</div>';
		$i++;
	}
	print $out;
}
?>