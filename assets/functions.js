//array contains tabs to show
var bluet_tab=['bluet_style_tab','bluet_settings_tab','bluet_excluded_tab'];

for(var i=0;i<bluet_tab.length;i++){

	//remove active class from all elements
	removeClass(document.getElementById(bluet_tab[i]),'nav-tab-active');
	
	var tabular=document.getElementById(bluet_tab[i]).addEventListener('click',function(e){
		for(var i=0;i<bluet_tab.length;i++){
			removeClass(document.getElementById(bluet_tab[i]),'nav-tab-active');
		}
		
		//tab we want to show
		var tabToShow=e.target.dataset.tab
		
		bluetShowTab(tabToShow);
		
		addClass(e.target,'nav-tab-active');
	},false);
}

//begin by displaying the style div
bluetShowTab("bluet-section-style");
addClass(document.getElementById('bluet_style_tab'),'nav-tab-active');

function bluetShowTab(tabId){
	var mybluet_my_div_settings=document.getElementById('bluet-sections-div');
	var mybluet_children=mybluet_my_div_settings.childNodes;

	for(var i=0;i<mybluet_children.length-1;i++){
		mybluet_children[i].style.display='none';
	}
	
	document.getElementById(tabId).style.display='block';
}

function bluet_hide_bg(){
	if(document.getElementById("bluet_kw_no_background").checked){
		document.getElementsByClassName('bluet_tooltip')[0].style.backgroundColor='initial';
		document.getElementById('bluet_kw_bg_hide').style.display='none';
	}else{
		document.getElementsByClassName('bluet_tooltip')[0].style.backgroundColor=document.getElementsByName('bluet_kw_style[bt_kw_tt_bg_color]')[0].value;
		document.getElementById('bluet_kw_bg_hide').style.display='block';
	}
}
//
window.onload=function(){
	bluet_hide_bg();		
	document.getElementById("bluet_kw_no_background").addEventListener("change",bluet_hide_bg,false);
	
	for(var i=0;i<document.getElementsByClassName('wp-picker-holder').length;i++){
		document.getElementsByClassName('wp-picker-holder')[i].addEventListener('mousemove',function(e){
		
			if(!document.getElementById("bluet_kw_no_background").checked){
				var bluet_keyword_bg=document.getElementsByName('bluet_kw_style[bt_kw_tt_bg_color]')[0].value;
			}else{
				var bluet_keyword_bg='initial';
			}
			
			var bluet_keyword_color=document.getElementsByName('bluet_kw_style[bt_kw_tt_color]')[0].value;
			var bluet_tooltip_bg=document.getElementsByName('bluet_kw_style[bt_kw_desc_bg_color]')[0].value;
			var bluet_tooltip_color=document.getElementsByName('bluet_kw_style[bt_kw_desc_color]')[0].value;
			
			document.getElementsByClassName('bluet_tooltip')[0].style.backgroundColor=bluet_keyword_bg;
			document.getElementsByClassName('bluet_tooltip')[0].style.color=bluet_keyword_color;
			document.getElementsByClassName('bluet_tooltip_description')[0].style.backgroundColor=bluet_tooltip_bg;
			document.getElementsByClassName('bluet_tooltip_description')[0].style.color=bluet_tooltip_color;

			
		},false);
	}
}
/**/
//

// class manipulation from http://www.openjs.com/scripts/dom/class_manipulation.php
function hasClass(ele,cls) {
    return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}
 
function addClass(ele,cls) {
    if (!this.hasClass(ele,cls)) ele.className += " "+cls;
}
 
function removeClass(ele,cls) {
    if (hasClass(ele,cls)) {
        var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
        ele.className=ele.className.replace(reg,' ');
    }
}