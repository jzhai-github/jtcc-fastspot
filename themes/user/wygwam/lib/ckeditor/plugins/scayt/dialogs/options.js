﻿/*
 Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.dialog.add("scaytcheck",function(l){function A(){return"undefined"!=typeof document.forms["optionsbar_"+b]?document.forms["optionsbar_"+b].options:[]}function B(a,b){if(a){var e=a.length;if(void 0==e)a.checked=a.value==b.toString();else for(var d=0;d<e;d++)a[d].checked=!1,a[d].value==b.toString()&&(a[d].checked=!0)}}function q(a){f.getById("dic_message_"+b).setHtml('\x3cspan style\x3d"color:red;"\x3e'+a+"\x3c/span\x3e")}function r(a){f.getById("dic_message_"+b).setHtml('\x3cspan style\x3d"color:blue;"\x3e'+
a+"\x3c/span\x3e")}function t(a){a=String(a);a=a.split(",");for(var b=0,e=a.length;b<e;b+=1)f.getById(a[b]).$.style.display="inline"}function u(a){a=String(a);a=a.split(",");for(var b=0,e=a.length;b<e;b+=1)f.getById(a[b]).$.style.display="none"}function v(a){f.getById("dic_name_"+b).$.value=a}var w=!0,h,f=CKEDITOR.document,b=l.name,n=CKEDITOR.plugins.scayt.getUiTabs(l),g,x=[],y=0,p=["dic_create_"+b+",dic_restore_"+b,"dic_rename_"+b+",dic_delete_"+b],z=["mixedCase","mixedWithDigits","allCaps","ignoreDomainNames"];
g=l.lang.scayt;var D=[{id:"options",label:g.optionsTab,elements:[{type:"html",id:"options",html:'\x3cform name\x3d"optionsbar_'+b+'"\x3e\x3cdiv class\x3d"inner_options"\x3e\t\x3cdiv class\x3d"messagebox"\x3e\x3c/div\x3e\t\x3cdiv style\x3d"display:none;"\x3e\t\t\x3cinput type\x3d"checkbox" name\x3d"options"  id\x3d"allCaps_'+b+'" /\x3e\t\t\x3clabel style \x3d "display: inline" for\x3d"allCaps" id\x3d"label_allCaps_'+b+'"\x3e\x3c/label\x3e\t\x3c/div\x3e\t\x3cdiv style\x3d"display:none;"\x3e\t\t\x3cinput name\x3d"options" type\x3d"checkbox"  id\x3d"ignoreDomainNames_'+
b+'" /\x3e\t\t\x3clabel style \x3d "display: inline" for\x3d"ignoreDomainNames" id\x3d"label_ignoreDomainNames_'+b+'"\x3e\x3c/label\x3e\t\x3c/div\x3e\t\x3cdiv style\x3d"display:none;"\x3e\t\x3cinput name\x3d"options" type\x3d"checkbox"  id\x3d"mixedCase_'+b+'" /\x3e\t\t\x3clabel style \x3d "display: inline" for\x3d"mixedCase" id\x3d"label_mixedCase_'+b+'"\x3e\x3c/label\x3e\t\x3c/div\x3e\t\x3cdiv style\x3d"display:none;"\x3e\t\t\x3cinput name\x3d"options" type\x3d"checkbox"  id\x3d"mixedWithDigits_'+
b+'" /\x3e\t\t\x3clabel style \x3d "display: inline" for\x3d"mixedWithDigits" id\x3d"label_mixedWithDigits_'+b+'"\x3e\x3c/label\x3e\t\x3c/div\x3e\x3c/div\x3e\x3c/form\x3e'}]},{id:"langs",label:g.languagesTab,elements:[{type:"html",id:"langs",html:'\x3cdiv class\x3d"inner_langs"\x3e\t\x3cdiv class\x3d"messagebox"\x3e\x3c/div\x3e\t   \x3cdiv style\x3d"float:left;width:45%;margin-left:5px;" id\x3d"scayt_lcol_'+b+'" \x3e\x3c/div\x3e   \x3cdiv style\x3d"float:left;width:45%;margin-left:15px;" id\x3d"scayt_rcol_'+
b+'"\x3e\x3c/div\x3e\x3c/div\x3e'}]},{id:"dictionaries",label:g.dictionariesTab,elements:[{type:"html",style:"",id:"dictionaries",html:'\x3cform name\x3d"dictionarybar_'+b+'"\x3e\x3cdiv class\x3d"inner_dictionary" style\x3d"text-align:left; white-space:normal; width:320px; overflow: hidden;"\x3e\t\x3cdiv style\x3d"margin:5px auto; width:95%;white-space:normal; overflow:hidden;" id\x3d"dic_message_'+b+'"\x3e \x3c/div\x3e\t\x3cdiv style\x3d"margin:5px auto; width:95%;white-space:normal;"\x3e        \x3cspan class\x3d"cke_dialog_ui_labeled_label" \x3eDictionary name\x3c/span\x3e\x3cbr\x3e\t\t\x3cspan class\x3d"cke_dialog_ui_labeled_content" \x3e\t\t\t\x3cdiv class\x3d"cke_dialog_ui_input_text"\x3e\t\t\t\t\x3cinput id\x3d"dic_name_'+
b+'" type\x3d"text" class\x3d"cke_dialog_ui_input_text" style \x3d "height: 25px; background: none; padding: 0;"/\x3e\t\t\x3c/div\x3e\x3c/span\x3e\x3c/div\x3e\t\t\x3cdiv style\x3d"margin:5px auto; width:95%;white-space:normal;"\x3e\t\t\t\x3ca style\x3d"display:none;" class\x3d"cke_dialog_ui_button" href\x3d"javascript:void(0)" id\x3d"dic_create_'+b+'"\x3e\t\t\t\t\x3c/a\x3e\t\t\t\x3ca  style\x3d"display:none;" class\x3d"cke_dialog_ui_button" href\x3d"javascript:void(0)" id\x3d"dic_delete_'+b+'"\x3e\t\t\t\t\x3c/a\x3e\t\t\t\x3ca  style\x3d"display:none;" class\x3d"cke_dialog_ui_button" href\x3d"javascript:void(0)" id\x3d"dic_rename_'+
b+'"\x3e\t\t\t\t\x3c/a\x3e\t\t\t\x3ca  style\x3d"display:none;" class\x3d"cke_dialog_ui_button" href\x3d"javascript:void(0)" id\x3d"dic_restore_'+b+'"\x3e\t\t\t\t\x3c/a\x3e\t\t\x3c/div\x3e\t\x3cdiv style\x3d"margin:5px auto; width:95%;white-space:normal;" id\x3d"dic_info_'+b+'"\x3e\x3c/div\x3e\x3c/div\x3e\x3c/form\x3e'}]},{id:"about",label:g.aboutTab,elements:[{type:"html",id:"about",style:"margin: 5px 5px;",html:'\x3cdiv\x3e\x3cdiv id\x3d"scayt_about_'+b+'"\x3e\x3c/div\x3e\x3c/div\x3e'}]}],F={title:g.title,
minWidth:360,minHeight:220,onShow:function(){var a=this;a.data=l.fire("scaytDialog",{});a.options=a.data.scayt_control.option();a.chosed_lang=a.sLang=a.data.scayt_control.sLang;if(a.data&&a.data.scayt&&a.data.scayt_control){var b=0;w?a.data.scayt.getCaption(l.langCode||"en",function(e){0<b++||(h=e,E.apply(a),C.apply(a),w=!1)}):C.apply(a);a.selectPage(a.data.tab)}else alert("Error loading application service"),a.hide()},onOk:function(){var a=this.data.scayt_control;a.option(this.options);a.setLang(this.chosed_lang);
a.refresh()},onCancel:function(){var a=A(),f;for(f in a)a[f].checked=!1;a="undefined"!=typeof document.forms["languagesbar_"+b]?document.forms["languagesbar_"+b].scayt_lang:[];B(a,"")},contents:x};CKEDITOR.plugins.scayt.getScayt(l);for(g=0;g<n.length;g++)1==n[g]&&(x[x.length]=D[g]);1==n[2]&&(y=1);var E=function(){function a(a){var c=f.getById("dic_name_"+b).getValue();if(!c)return q(" Dictionary name should not be empty. "),!1;try{var d=a.data.getTarget().getParent(),e=/(dic_\w+)_[\w\d]+/.exec(d.getId())[1];
l[e].apply(null,[d,c,p])}catch(G){q(" Dictionary error. ")}return!0}var m=this,e=m.data.scayt.getLangList(),d=["dic_create","dic_delete","dic_rename","dic_restore"],g=[],k=[],c;if(y){for(c=0;c<d.length;c++)g[c]=d[c]+"_"+b,f.getById(g[c]).setHtml('\x3cspan class\x3d"cke_dialog_ui_button"\x3e'+h["button_"+d[c]]+"\x3c/span\x3e");f.getById("dic_info_"+b).setHtml(h.dic_info)}if(1==n[0])for(c in z)d="label_"+z[c],g=f.getById(d+"_"+b),"undefined"!=typeof g&&"undefined"!=typeof h[d]&&"undefined"!=typeof m.options[z[c]]&&
(g.setHtml(h[d]),g.getParent().$.style.display="block");d='\x3cp\x3e\x3cimg src\x3d"'+window.scayt.getAboutInfo().logoURL+'" /\x3e\x3c/p\x3e\x3cp\x3e'+h.version+window.scayt.getAboutInfo().version.toString()+"\x3c/p\x3e\x3cp\x3e"+h.about_throwt_copy+"\x3c/p\x3e";f.getById("scayt_about_"+b).setHtml(d);d=function(a,b){var c=f.createElement("label");c.setAttribute("for","cke_option"+a);c.setStyle("display","inline");c.setHtml(b[a]);m.sLang==a&&(m.chosed_lang=a);var d=f.createElement("div"),e=CKEDITOR.dom.element.createFromHtml('\x3cinput class \x3d "cke_dialog_ui_radio_input" id\x3d"cke_option'+
a+'" type\x3d"radio" '+(m.sLang==a?'checked\x3d"checked"':"")+' value\x3d"'+a+'" name\x3d"scayt_lang" /\x3e');e.on("click",function(){this.$.checked=!0;m.chosed_lang=a});d.append(e);d.append(c);return{lang:b[a],code:a,radio:d}};if(1==n[1]){for(c in e.rtl)k[k.length]=d(c,e.ltr);for(c in e.ltr)k[k.length]=d(c,e.ltr);k.sort(function(a,b){return b.lang>a.lang?-1:1});e=f.getById("scayt_lcol_"+b);d=f.getById("scayt_rcol_"+b);for(c=0;c<k.length;c++)(c<k.length/2?e:d).append(k[c].radio)}var l={dic_create:function(a,
b,c){var d=c[0]+","+c[1],e=h.err_dic_create,f=h.succ_dic_create;window.scayt.createUserDictionary(b,function(a){u(d);t(c[1]);f=f.replace("%s",a.dname);r(f)},function(a){e=e.replace("%s",a.dname);q(e+"( "+(a.message||"")+")")})},dic_rename:function(a,b){var c=h.err_dic_rename||"",d=h.succ_dic_rename||"";window.scayt.renameUserDictionary(b,function(a){d=d.replace("%s",a.dname);v(b);r(d)},function(a){c=c.replace("%s",a.dname);v(b);q(c+"( "+(a.message||"")+" )")})},dic_delete:function(a,b,c){var d=c[0]+
","+c[1],e=h.err_dic_delete,f=h.succ_dic_delete;window.scayt.deleteUserDictionary(function(a){f=f.replace("%s",a.dname);u(d);t(c[0]);v("");r(f)},function(a){e=e.replace("%s",a.dname);q(e)})}};l.dic_restore=m.dic_restore||function(a,b,c){var d=c[0]+","+c[1],e=h.err_dic_restore,f=h.succ_dic_restore;window.scayt.restoreUserDictionary(b,function(a){f=f.replace("%s",a.dname);u(d);t(c[1]);r(f)},function(a){e=e.replace("%s",a.dname);q(e)})};k=(p[0]+","+p[1]).split(",");c=0;for(e=k.length;c<e;c+=1)if(d=f.getById(k[c]))d.on("click",
a,this)},C=function(){var a=this;if(1==n[0])for(var g=A(),e=0,d=g.length;e<d;e++){var h=g[e].id,k=f.getById(h);if(k&&(g[e].checked=!1,1==a.options[h.split("_")[0]]&&(g[e].checked=!0),w))k.on("click",function(){a.options[this.getId().split("_")[0]]=this.$.checked?1:0})}1==n[1]&&(g=f.getById("cke_option"+a.sLang),B(g.$,a.sLang));y&&(window.scayt.getNameUserDictionary(function(a){a=a.dname;u(p[0]+","+p[1]);a?(f.getById("dic_name_"+b).setValue(a),t(p[1])):t(p[0])},function(){f.getById("dic_name_"+b).setValue("")}),
r(""))};return F});