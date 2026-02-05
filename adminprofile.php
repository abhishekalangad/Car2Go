<?php
// session_start();
include 'db_connect.php';
include 'aheader.php';
if(isset($_SESSION['l_id']))
{
  $l_id =$_SESSION['l_id'];
  //var_dump($l_id);


}
?>
<HTML><BODY>
<div class="Servicebottom">
	<div class="layer">
	<div class="col-md-5">
	</div>
	<div class="col-md-7 Servicebottomtext">
		<h3>Fast and Safe Service</h3>
		<h3>Premium Cars</h3>
		<h3>Car Services </h3>
		<!-- <p>Ut enim ad minima veniam, quis nostrum 
			exercitationem ullam corporis suscipit laboriosam, 
			nisi ut aliquid ex ea commodi consequatur? Quis autem consequatur? Quis autem 
			vel eum iure reprehenderit qui in ea voluptate velit vel eum iure reprehenderit qui in ea voluptate velit 
			esse quam nihil molestiae consequatur, vel quidolorem eum fugiat quo voluptas nulla pariatur.</p> -->
		<h4><i class="fa fa-taxi" aria-hidden="true"></i>International Quality Service</h4>
		<h4><i class="fa fa-car" aria-hidden="true"></i>Verified Drivers</h4>
	</div>
	<div class="clearfix"></div>
	</div>
</div>
<?php

include 'footer.php';
?>