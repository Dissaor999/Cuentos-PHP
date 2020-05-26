    <?php
    if((isset($this->isLoginScreen)) && $this->isLoginScreen=true) {

    }else { ?>

        </div>
        </div>
        <!-- end:: Body -->

        <!-- begin::Footer -->
        <footer class="m-grid__item		m-footer ">
            <div class="m-container m-container--fluid m-container--full-height m-page__container">
                <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                    <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
							<span class="m-footer__copyright">
								2017 &copy; <?= APP_NAME ?>
							</span>
                    </div>
                    <div class="m-stack__item m-stack__item--right m-stack__item--middle m-stack__item--first">
                        <ul class="m-footer__nav m-nav m-nav--inline m--pull-right">
                            <li class="m-nav__item">
                                <a href="mailto:soporte@acornstudio.es" class="m-nav__link">
										<span class="m-nav__link-text">
											Soporte
										</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end::Footer -->
        <?php
    }
    ?>
    <!-- /#wrapper -->

    </div>



    <!--begin::Base Scripts -->
    <script src="<?=ADMIN_LAYOUT_PATH?>assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
    <script src="<?=ADMIN_LAYOUT_PATH?>assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
    <!--end::Base Scripts -->
    <!--begin::Page Vendors -->
    <script src="<?=ADMIN_LAYOUT_PATH?>assets/vendors/custom/fullcalendar/fullcalendar.bundle.js" type="text/javascript"></script>
    <!--end::Page Vendors -->
    <!--begin::Page Snippets -->
    <script src="<?=ADMIN_LAYOUT_PATH?>assets/app/js/dashboard.js" type="text/javascript"></script>
    <!--end::Page Snippets -->

    <script>
        function addProgress(form){

            $(form).find("#progress").remove();
            $(form).find("#response").remove();

            $(form).prepend('<div id="response"></div>').prepend('<div class="progress" id="progress"><div class="bar" id="bar"></div><div class="percent" id="percent">0%</div></div>');


        }

        $(document).ready(function() {
            $(".ajaxform").each(function(){
                $form = $(this);
                $($form).ajaxForm({
                    beforeSubmit: function() {
                        if (!$($form).valid()) {return false;}

                        addProgress($form);


                    },
                    beforeSerialize:function($Form, options){

                        return true;
                    },
                    beforeSend: function() {
                        if (!$($form).valid()) {return false;};
                        $($form).find('#status').html("");
                        var percentVal = '0%';
                        $($form).find('#bar').css("width",percentVal);
                        $($form).find('#percent').html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        $($form).find('#bar').css("width",percentVal);
                        $($form).find('#percent').html(percentVal);
                    },
                    success: function() {
                        var percentVal = '100%';
                        $($form).find('#bar').css("width",percentVal);
                        $($form).find('#percent').html(percentVal);
                    },
                    complete: function(xhr) {
                        var datos  =xhr.responseText;
                        var jsondata = $.parseJSON(datos);
                        if(jsondata.error==0) {
                            $($form).find('#response').html('<p class="alert alert-success">'+jsondata.msg+"</p>").slideDown("slow").delay(4000);

                            if(jsondata.redirect) setTimeout(function() {window.location.href=jsondata.redirect;	},2000);
                        }
                        else {
                            var html = '<div class="alert alert-danger"><strong>'+jsondata.msg+'</strong>';
                            if(jsondata.errors && jsondata.errors.length>0) {
                                html+='<ul>';
                                $.each(jsondata.errors, function(k,error) {
                                    html+='<li>'+error+'</li>';
                                });
                                html+='</ul>';
                            }
                            html+='</div>';
                            $($form).find('#response').html(html).slideDown("slow");
                        }
                        $('.loader-overlay').delay(2000).fadeOut(300);
                    }
                });
            });
        });


    </script>

    <!-- View JS -->
    <?php
    if(sizeof($this->_js)>0) {
        foreach($this->_js as $js) {
            echo '<script src="'.$js.'"></script>';
        }
    }
    ?>


    </body>
    <!-- end::Body -->
</html>


