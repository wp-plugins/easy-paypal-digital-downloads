<?php

$options = get_option('wpepdd_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }

?>

<script>
<?php
if ($value['notices'] == "1") {
?>
alert("Payment cancelled");
<?php
}
?>
window.onload = function(){
if(window.opener){
window.close();
} 
else{
if(top.dg.isOpen() == true){
top.dg.closeFlow();
return true;
}
}           
};                             
</script>