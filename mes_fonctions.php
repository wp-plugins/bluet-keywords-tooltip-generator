<?php
//retourne la liste des terme du post
function substr_list_array($sujet,$termes,$defs ) {
$nouv_termes=array();


	
     foreach ($termes as $k=>$mot) {
		$nouv_termes[sanitize_text_field($k)]=array($mot,0);
	 }
 
     foreach ($nouv_termes as $k=>$arrmot) {
          $nouv_termes[$k][1]+=substr_count( $sujet,$arrmot[0]); 
     }
	 
	 $ret=array();
	 
	foreach($nouv_termes as $k=>$mot){
		if($mot[1]>0){
		//htmlspecialchars pour enregister d'une manière correcte
			$ret[$mot[0]]=$defs[$k];
		}
	}

	return $ret;
}