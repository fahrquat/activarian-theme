<script type="text/javascript">
$(document).ready(function(){

	//find the checkbox
	$('input[type="checkbox"][value="<?php echo $org_id; ?>"]').parent().remove();
	<?php if($role_id != 0){?>
	$('input[type="checkbox"][value="<?php echo $role_id; ?>"]').parent().remove();
	<?php }?>
	
	
});

function orgclick()
{
	//figure out the current selection
	var id = $("#orgs").val();
	$("#orgscheck").val(id);
	
	
	
}

</script>