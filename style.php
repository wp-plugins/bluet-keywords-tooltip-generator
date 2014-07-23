<?php

function lebleut_ttg_tooltip_style(){
	$opt=get_option('lebleut_ttg_settings');

	$tc=$opt[0];
	$tbgc=$opt[1];

	$ttc=$opt[2];
	$ttbgc=$opt[3];

	?>
	<style>

	.tooltip{
   			display: inline;
    		position: relative;
			color:<?php echo($tc); ?>;
			background-color: <?php echo($tbgc); ?>;
			padding: 0 4px;
			border-radius: 5px;
		}
		
		.tooltip:hover:after{		
			color:<?php echo($ttc); ?>;
			background-color:<?php echo($ttbgc); ?>;
    		border-radius: 5px;
    		bottom: 26px;
    		content: attr(title);
    		left: 20%;
    		padding: 5px 15px;
    		position: absolute;
    		z-index: 9999;
    		width: 220px;
		}
		
		.tooltip:hover:before{
    		border: solid;
    		border-color: <?php echo($ttbgc); ?> transparent;
    		border-width: 6px 6px 0 6px;
    		bottom: 20px;
    		content: "";
    		left: 50%;
    		position: absolute;
    		z-index: 9999;
		}
	</style>

	<?php
}