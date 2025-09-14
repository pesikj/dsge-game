<script type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});


function nacteni_clanku() {
    var id_ekonomiky = document.getElementById("id_ekonomiky").value;

    if (window.XMLHttpRequest) {
        xmlhttp_nacteni_clanku=new XMLHttpRequest();
    }
    xmlhttp_nacteni_clanku.onreadystatechange = zpracovani_nactenych_dat;
    xmlhttp_nacteni_clanku.open("GET","data_pro_ajax/nacteni_clanku.php?id_ekonomiky=" + id_ekonomiky,true);
    xmlhttp_nacteni_clanku.send();
}

function zpracovani_nactenych_dat() {
    if (xmlhttp_nacteni_clanku.readyState==4 && xmlhttp_nacteni_clanku.status==200) {
         ajaxLoad(xmlhttp_nacteni_clanku.responseText);
    }
}

function ajaxLoad(text) {
	var ed = tinyMCE.get('clanek');

	// Do you ajax call here, window.setTimeout fakes ajax call
	ed.setProgressState(1); // Show progress
	window.setTimeout(function() {
		ed.setProgressState(0); // Hide progress
		ed.setContent(text);
	}, 3000);
}

function ajaxSave() {
	var ed = tinyMCE.get('clanek');

	// Do you ajax call here, window.setTimeout fakes ajax call
	ed.setProgressState(1); // Show progress
	window.setTimeout(function() {
		ed.setProgressState(0); // Hide progress
		alert(ed.getContent());
	}, 3000);
}
</script>


<?php
class Formular_editor extends Formular_administratora {
    //put your code here

    public function generuj_formular_editor() {
        $generator_formularu = new Generator_formularu();
        $prekladac = $GLOBALS['prekladac'];

        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        $pageURL = str_replace("&", "&amp;", $pageURL);

        ?>
        <form method="post" action="<?php echo $pageURL ?>">
            <table>
                <tr>
                    <td>
                        id_ekonomiky
                    </td>
                    <td>
                        <select id="id_ekonomiky" name="id_ekonomiky" onchange="nacteni_clanku()">
                            <option></option>
                            <?php
                                echo $this->vygeneruj_vyber_ekonomiky();
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <textarea name="clanek" style="width:100%; height: 500px;"></textarea>
            <table width="100%">
                <tr>
                    <td>
                        <input type="submit" value="<?php echo $prekladac->vloz_retezec('formular_potvrdit') ?>" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="typ_formulare" value="editor_clanku" />
        </form>
        <?php
    }

    function __construct() {
        parent::__construct();
    }

}
?>