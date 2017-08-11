<?php // encoding: utf-8

/*  Copyright 2014  
	modified by Papa Salvatore Mirko (email : mirko@primapagina.it)
	originally created by Qian Qin (email : mail@qianqin.de)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Modifications Hacks to get Wordpress work the way it should */

// modifys term form to support multilingual content
function ppqtrans_modifyTermForm($id, $name, $term) {
	global $q_config;
	echo "<script type=\"text/javascript\">\n// <![CDATA[\r\n";
	// ' workaround
	if(is_object($term)&&isset($term->name)) {
		$termname = $term->name;
	} else {
		$termname = "";
	}
	// create input fields for each language
	foreach($q_config['enabled_languages'] as $language) {
		if(isset($_GET['action']) && $_GET['action']=='edit') {
			echo ppqtrans_insertTermInput2($id, $name, $termname, $language);
		} else {
			echo ppqtrans_insertTermInput($id, $name, $termname, $language);
		}
	}
	// hide real category text
	echo "ins.style.display='none';\n";
	echo "// ]]>\n</script>\n";
}

function ppqtrans_modifyTermFormFor($term) {
	ppqtrans_modifyTermForm('name', __('Name'), $term);
	ppqtrans_modifyTermForm('tag-name', __('Name'), $term);
}

function ppqtrans_TinyMCE_init() {
	global $q_config;
	echo "<script type=\"text/javascript\">\n// <![CDATA[\n";
	echo $q_config['js']['ppqtrans_updateTinyMCE'];
	echo "</script>\n";
}

// Modifys TinyMCE to edit multilingual content
function ppqtrans_modifyRichEditor($old_content) {
	global $q_config;
	$init_editor = true;
		
	preg_match("/<textarea[^>]*id=\"([^']+)\"/",$old_content,$matches);
	$id = $matches[1];
	preg_match("/cols=\"([^\"]+)\"/",$old_content,$matches);
	$cols = $matches[1];
	// don't do anything if not editing the content
	if($id!="content") return $old_content;
	
	// don't do anything to the editor if it's not rich
	if(!user_can_richedit()) {
		//echo '<p class="updated">'.__('The qTranslate Editor could not be loaded because WYSIWYG/TinyMCE is not activated in your profile.').'</p>';
		return $old_content;
	}
	
	// remove wpautop
	if('html' != wp_default_editor()) {
		remove_filter('the_editor_content', 'wp_richedit_pre');
	}
	
	$content = "";
	$content_append = "";
	
	// create editing field for selected languages
	$qt_textarea = '<textarea id="ppqtrans_textarea_'.$id.'" name="ppqtrans_textarea_'.$id.'" tabindex="2" cols="'.$cols.'" style="display:none" onblur="ppqtrans_save(this.value);"></textarea>';
	$old_content = preg_replace('#(<textarea[^>]*>.*</textarea>)#', '$1'.$qt_textarea, $old_content);

	// do some crazy js to alter the admin view
	$content .="<script type=\"text/javascript\">\n// <![CDATA[\n";
	
	// include needed js functions
	$content .= $q_config['js']['ppqtrans_is_array'];
	$content .= $q_config['js']['ppqtrans_xsplit'];
	$content .= $q_config['js']['ppqtrans_split'];
	$content .= $q_config['js']['ppqtrans_integrate'];
	$content .= $q_config['js']['ppqtrans_use'];
	$content .= $q_config['js']['ppqtrans_assign'];
	$content .= $q_config['js']['ppqtrans_save'];
	$content .= $q_config['js']['ppqtrans_integrate_title'];
	$content .= $q_config['js']['ppqtrans_get_active_language'];
	$content .= $q_config['js']['ppqtrans_hook_on_tinyMCE'];

	$content .="function ppqtrans_editorInit1() {\n";
	$content .= $q_config['js']['ppqtrans_switch'];
	
	// insert language, visual and html buttons
	$el = ppqtrans_getSortedLanguages();
	foreach($el as $language) {
		$content .= ppqtrans_insertTitleInput($language);
	}
	$el = ppqtrans_getSortedLanguages(true);
	foreach($el as $language) {
		$content .= ppqtrans_createEditorToolbarButton($language, $id);
	}
	
	$content = apply_filters('ppqtranslate_toolbar', $content);
	
	// hide old title bar
	$content .= "document.getElementById('titlediv').style.display='none';\n";
	
	$content .="}\n";
	$content .="// ]]>\n</script>\n";
	
	$content_append .="<script type=\"text/javascript\">\n// <![CDATA[\n";
	$content_append .="function ppqtrans_editorInit2() {\n";
	
	// show default language tab
	$content_append .="document.getElementById('ppqtrans_select_".$q_config['default_language']."').className='wp-switch-editor switch-tmce switch-html';\n";
	// show default language
	$content_append .="var text = document.getElementById('".$id."').value;\n";
	$content_append .="ppqtrans_assign('ppqtrans_textarea_".$id."',ppqtrans_use('".$q_config['default_language']."',text));\n";
	
	$content_append .="}\n";

	$content_append .="function ppqtrans_editorInit3() {\n";
	// make tinyMCE and mediauploader get the correct data
	$content_append .=$q_config['js']['ppqtrans_tinyMCEOverload'];
	$content_append .=$q_config['js']['ppqtrans_wpActiveEditorOverload'];
	$content_append .="}\n";
	$content_append .=$q_config['js']['ppqtrans_editorInit'];
	if($init_editor) {
		$content_append .=$q_config['js']['ppqtrans_wpOnload'];
	} else {
		$content_append .="var qtmsg = document.getElementById('ppqtrans_imsg');\n";
		$content_append .="var et = document.getElementById('wp-".$id."-editor-tools');\n";
		$content_append .="et.parentNode.insertBefore(qtmsg, et);\n";
	}
	$content_append = apply_filters('ppqtranslate_modify_editor_js', $content_append);
	$content_append .="// ]]>\n</script>\n";
	
	return $content.$old_content.$content_append;
}

function ppqtrans_modifyExcerpt() {
	global $q_config;
	echo "<script type=\"text/javascript\">\n// <![CDATA[\n";
	echo "if(jQuery('#excerpt').size()>0) {";
	echo $q_config['js']['ppqtrans_is_array'];
	echo $q_config['js']['ppqtrans_xsplit'];
	echo $q_config['js']['ppqtrans_split'];
	echo $q_config['js']['ppqtrans_integrate'];
	echo $q_config['js']['ppqtrans_switch_postbox'];
	echo $q_config['js']['ppqtrans_use'];
	$el = ppqtrans_getSortedLanguages();
	foreach($el as $language) {
		echo ppqtrans_createTitlebarButton('postexcerpt', $language, 'excerpt', 'ppqtrans_switcher_postexcerpt_'.$language);
		echo ppqtrans_createTextArea('postexcerpt', $language, 'excerpt', 'ppqtrans_switcher_postexcerpt_'.$language);
	}
	echo "ppqtrans_switch_postbox('postexcerpt','excerpt','".$q_config['default_language']."', false);"; 
	echo "jQuery('#excerpt').hide();";
	echo "}";
	echo "// ]]>\n</script>\n";
}

function ppqtrans_createTitlebarButton($parent, $language, $target, $id) {
	global $q_config;
	$html = "
		jQuery('#".$parent." .handlediv').after('<div class=\"ppqtranslate_lang_div\" id=\"".$id."\"><img alt=\"".$language."\" title=\"".$q_config['language_name'][$language]."\" src=\"".WP_CONTENT_URL.'/'.$q_config['flag_location'].$q_config['flag'][$language]."\" /></div>');
		jQuery('#".$id."').click(function() {ppqtrans_switch_postbox('".$parent."','".$target."','".$language."');});
		";
	return $html;
}

function ppqtrans_createTextArea($parent, $language, $target, $id) {
	global $q_config;
	$html = "
		jQuery('#".$target."').after('<textarea name=\"ppqtrans_textarea_".$target."_".$language."\" id=\"ppqtrans_textarea_".$target."_".$language."\"></textarea>');
		jQuery('#ppqtrans_textarea_".$target."_".$language."').attr('cols', jQuery('#".$target."').attr('cols'));
		jQuery('#ppqtrans_textarea_".$target."_".$language."').attr('rows', jQuery('#".$target."').attr('rows'));
		jQuery('#ppqtrans_textarea_".$target."_".$language."').attr('tabindex', jQuery('#".$target."').attr('tabindex'));
		jQuery('#ppqtrans_textarea_".$target."_".$language."').blur(function() {ppqtrans_switch_postbox('".$parent."','".$target."',false);});
		jQuery('#ppqtrans_textarea_".$target."_".$language."').val(ppqtrans_use('".$language."',jQuery('#".$target."').val()));
		";
	return $html;
}

function ppqtrans_insertTermInput($id,$name,$term,$language){
	global $q_config;
	$html ="
		var il = document.getElementsByTagName('input');
		var d =  document.createElement('div');
		var l = document.createTextNode('".$name." (".$q_config['language_name'][$language].")');
		var ll = document.createElement('label');
		var i = document.createElement('input');
		var ins = null;
		for(var j = 0; j < il.length; j++) {
			if(il[j].id=='".$id."') {
				ins = il[j];
				break;
			}
		}
		i.type = 'text';
		i.id = i.name = ll.htmlFor ='ppqtrans_term_".$language."';
	";
	if(isset($q_config['term_name'][$term][$language])) {
	$html .="
		i.value = '".addslashes(htmlspecialchars_decode($q_config['term_name'][$term][$language], ENT_NOQUOTES))."';
		";
	} else {
	$html .="
		i.value = ins.value;
		";
	}
	if($language == $q_config['default_language']) {
		$html .="
			i.onchange = function() { 
				var il = document.getElementsByTagName('input');
				var ins = null;
				for(var j = 0; j < il.length; j++) {
					if(il[j].id=='".$id."') {
						ins = il[j];
						break;
					}
				}
				ins.value = document.getElementById('ppqtrans_term_".$language."').value;
			};
			";
	}
	$html .="
		ins = ins.parentNode;
		d.className = 'form-field form-required';
		ll.appendChild(l);
		d.appendChild(ll);
		d.appendChild(i);
		ins.parentNode.insertBefore(d,ins);
		";
	return $html;	
}

function ppqtrans_insertTermInput2($id,$name,$term,$language){
	global $q_config;
	$html ="
		var tr = document.createElement('tr');
		var th = document.createElement('th');
		var ll = document.createElement('label');
		var l = document.createTextNode('".$name." (".$q_config['language_name'][$language].")');
		var td = document.createElement('td');
		var i = document.createElement('input');
		var ins = document.getElementById('".$id."');
		i.type = 'text';
		i.id = i.name = ll.htmlFor ='ppqtrans_term_".$language."';
	";
	if(isset($q_config['term_name'][$term][$language])) {
	$html .="
		i.value = '".addslashes(htmlspecialchars_decode($q_config['term_name'][$term][$language], ENT_QUOTES))."';
		";
	} else {
	$html .="
		i.value = ins.value;
		";
	}
	if($language == $q_config['default_language']) {
		$html .="
			i.onchange = function() { 
				var il = document.getElementsByTagName('input');
				var ins = null;
				for(var j = 0; j < il.length; j++) {
					if(il[j].id=='".$id."') {
						ins = il[j];
						break;
					}
				}
				ins.value = document.getElementById('ppqtrans_term_".$language."').value;
			};
			";
	}
	$html .="
		ins = ins.parentNode.parentNode;
		tr.className = 'form-field form-required';
		th.scope = 'row';
		th.vAlign = 'top';
		ll.appendChild(l);
		th.appendChild(ll);
		tr.appendChild(th);
		td.appendChild(i);
		tr.appendChild(td);
		ins.parentNode.insertBefore(tr,ins);
		";
	return $html;
}

function ppqtrans_insertTitleInput($language){
	global $q_config;
	$html ="
		var td = document.getElementById('titlediv');
		var qtd = document.createElement('div');
		var h = document.createElement('h3');
		var l = document.createTextNode('".__("Title", 'qtranslate')." (".$q_config['language_name'][$language].")');
		var tw = document.createElement('div');
		var ti = document.createElement('input');
		var slug = document.getElementById('edit-slug-box');
		
		ti.type = 'text';
		ti.id = 'ppqtrans_title_".$language."';
		ti.tabIndex = '1';
		ti.value = ppqtrans_use('".$language."', document.getElementById('title').value);
		ti.onchange = ppqtrans_integrate_title;
		ti.className = 'ppqtrans_title_input';
		h.className = 'ppqtrans_title';
		tw.className = 'ppqtrans_title_wrap';
		
		qtd.className = 'postarea';
		
		h.appendChild(l);
		tw.appendChild(ti);
		qtd.appendChild(h);
		qtd.appendChild(tw);";
	if($q_config['default_language'] == $language)
		$html.="if(slug) qtd.appendChild(slug);";
	$html.="
		td.parentNode.insertBefore(qtd,td);
		
		";
	return $html;	
}

function ppqtrans_createEditorToolbarButton($language, $id, $js_function = 'switchEditors.go', $label = ''){
	global $q_config;
	$html = "
		var bc = document.getElementById('wp-".$id."-editor-tools');
		var mb = document.getElementById('wp-".$id."-media-buttons');
		var ls = document.createElement('a');
		var l = document.createTextNode('".(($label==='')?$q_config['language_name'][$language]:$label)."');
		ls.id = 'ppqtrans_select_".$language."';
		ls.className = 'wp-switch-editor';
		ls.onclick = function() { ".$js_function."('".$id."','".$language."'); };
		ls.appendChild(l);
		bc.insertBefore(ls,mb);
		";
	return $html;
}
?>
