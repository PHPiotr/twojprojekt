<? if ($page_dir !== "zaloguj" && $page_dir !== "tp" && $page_dir !== 'error'): ?>
        <div class="row-fluid" style="margin-top:20px">
                <div class="span12 well">
                        <div class="span3 offset9">
                                <ul class="nav" style="margin-bottom:0">
                                        <li><a rel="nofollow" href="/" <? if ($page_dir !== 'index'): ?>class="text-warning"<? else: ?>class="text-error"<? endif; ?>>Start</a></li>
                                        <? foreach ($this->categories as $kategoria): ?>
                                                <li><a rel="nofollow" <? if (isset($this->category['url_kategorii']) && $this->category['url_kategorii'] === $kategoria['url_kategorii']): ?>class="text-error"<? else: ?>class="text-warning"<? endif; ?> href="/oferta/<?= $kategoria['url_kategorii'] ?><?= isset($this->category['url_kategorii']) && $this->category['url_kategorii'] === $kategoria['url_kategorii'] ? '/' : null; ?>"><?= $kategoria['nazwa_kategorii'] ?></a></li>
                                        <? endforeach; ?>
                                        <li><a rel="nofollow" class="contact text-warning" data-content="<?php echo $kontakt; ?>" data-toggle="popover" data-placement="top" data-original-title="<img src='/media/img/ico/logo-twojprojekt-f7f7f7.jpg' alt='Dane kontaktowe' />">Kontakt</a></li>
                                        <li class="foot-scrollup"><a rel="nofollow" href="#" class="scrollup text-warning">Góra strony</a></li>
                                </ul>
                        </div>
                </div>
        </div>
        <script type="text/javascript">

                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-43436611-1']);
                _gaq.push(['_setDomainName', 'twojprojekt.com.pl']);
                _gaq.push(['_setAllowLinker', true]);
                _gaq.push(['_trackPageview']);

                (function() {
                        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();

        </script>
<? endif; ?>
<div style="font-size:0.8em;color:silver">
        &copy; <a href="http://piotrkowalski.eu" style="color:silver" target="_blank">Profi</a>
</div>
</div>

<a href="#" class="scrollup-fixed-btn btn btn-danger scrollup right-btn" style="display:none;position:fixed;bottom:20px;right:0">Do góry</a>
<script src="http://twojprojekt.com.pl/media/js/jquery.js"></script>
<? if ($page === 'ustawienia'): ?>
        <script src="http://twojprojekt.com.pl/media/js/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
                tinyMCE.init({
                        mode : "textareas",
                        theme : "advanced",
                        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
                        plugins : "autolink,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                        theme_advanced_font_sizes: "8px,9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,31px,32px",
                        font_size_style_values: "8px,9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,31px,32px",
                        theme_advanced_blockformats: 'p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp',
                        /*formatselect,*/
                        theme_advanced_buttons1 : "sizeselect,fontsizeselect,fontselect,bold,italic,underline,strikethrough,link,unlink,anchor,forecolor,cut,copy,paste,pastetext,|,justifyleft,justifycenter,justifyright,justifyfull,|,code,|,fullscreen,ltr,rtl",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,print",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr",
                        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,
                            
                        theme_advanced_fonts : "Andale Mono=andale mono,times;"+"Helvetica,sans-serif;"+
                                "Arial=arial;"+
                                "Tahoma=tahoma;"+
                                "Corda Light=CordaLight,sans-serif;"+
                                "Courier New=courier_newregular,courier;"+
                                "Flexo Caps=FlexoCapsDEMORegular;"+                 
                                "Lucida Console=lucida_consoleregular,courier;"+
                                "Georgia=georgia,palatino;"+
                                "Helvetica=helvetica;"+
                                "Impact=impactregular,chicago;"+
                                "Museo Slab=MuseoSlab500Regular,sans-serif;"+                   
                                "Museo Sans=MuseoSans500Regular,sans-serif;"+
                                "Oblik Bold=OblikBoldRegular;"+
                                "Sofia Pro Light=SofiaProLightRegular;"+                    
                                "Symbol=webfontregular;"+
                                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                                "Terminal=terminal,monaco;"+
                                "Tikal Sans Medium=TikalSansMediumMedium;"+
                                "Times New Roman=times new roman,times;"+
                                "Trebuchet MS=trebuchet ms,geneva;"+
                                "Verdana=verdana,geneva;"+
                                "Webdings=webdings;"+
                                "Michroma=Michroma;"+
                                "Paytone One=Paytone One, sans-serif;"+
                                "Andalus=andalusregular, sans-serif;"+
                                "Arabic Style=b_arabic_styleregular, sans-serif;"+
                                "Andalus=andalusregular, sans-serif;"+
                                "KACST_1=kacstoneregular, sans-serif;"+
                                "Mothanna=mothannaregular, sans-serif;"+
                                "Nastaliq=irannastaliqregular, sans-serif;",
                        		
                        force_br_newlines : true,
                        force_p_newlines : false,
                            
                        template_external_list_url : "lists/template_list.js",
                        external_link_list_url : "lists/link_list.js",
                        external_image_list_url : "lists/image_list.js",
                        media_external_list_url : "lists/media_list.js",

                        template_replace_values : {
                                username : "Twój Projekt",
                                staffid : "991234"
                        }
                });
        </script>
<? endif; ?>
<script src="http://twojprojekt.com.pl/media/js/shadowbox/shadowbox.js"></script>
<script src="http://twojprojekt.com.pl/media/js/bootstrap.min.js"></script>

<script src="http://twojprojekt.com.pl/media/js/script.js"></script>
<? if (is_file("templates/{$page_dir}/{$page_dir}.js")): ?>
        <script src="/templates/<?= $page_dir ?>/<?= $page_dir ?>.js"></script>
<? endif; ?>
</body>
</html>
