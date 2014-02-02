<?php
if(!empty($_REQUEST['data']))
{
	$data=base64_decode($_REQUEST['data']);
	$data_array=array();
	$data_array=explode('@#@#@@#',$data);
	$user_data=$data_array[0];
	$user_phone=$data_array[1];
	$user_phone_words = chunk_split($user_phone,1," ");	
}
  header("content-type: text/xml");
?>
<Response><Gather action="<?=WEBSITE_URL;?>complete_call.php?user_phone=<?php echo $user_phone?>"><Say>A customer at the number <?php echo $user_phone_words?> is calling. <?php echo $user_data?></Say></Gather></Response>