<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Use this to tell the user the status of them sending a message
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */
?>
<div style="height:400px;padding:40px;text-align:center;">
<h3>
<?php if($success){?>
<img src="<?php echo url::base()?>plugins/usermsg/img/mail-sent.png"/><br/>
<?php } else {?>
<img src="<?php echo url::base()?>plugins/usermsg/img/mail-error.png"/><br/>
<?php } echo $msg;?>
</h3>
<h3>

<a href="<?php echo url::base()?>profile/user/<?php echo $username;?>">Back to profile</a></h3>


</div>
