<script>
    (function($) {
        "use strict";

        var tsize = "";
        var tsizeText = "";
        var tcolor = "";
        var tcolorText = "";



        $(document).ready(function() {

            if ($('.size-name option').length > 0) {
                getSize1();
            }

            if ($('.color-name option').length > 0) {
                $('.color-name').each(function() {
                    $(this).css('background-color', $(this).val());
                });
            }

            // Check Clicks :)
            $(".checkclicks").on("change", function() {
                if (this.checked) {
                    $('.tsize').prop('required', true);
                    $(this).parent().parent().parent().parent().next().removeClass('showbox');
                } else {
                    $('.tsize').prop('required', false);
                    $(this).parent().parent().parent().parent().next().addClass('showbox');
                }
            });
            // Check Clicks Ends :)

            // Check Clickc :)
            $(".checkclickc").on("change", function() {
                if (this.checked) {
                    $('.tcolor').prop('required', true);
                    $(this).parent().parent().parent().parent().next().removeClass('showbox');
                } else {
                    $('.tcolor').prop('required', false);
                    $(this).parent().parent().parent().parent().next().addClass('showbox');
                }
            });
            // Check Clickc Ends :)


            // Product Measure :)

            $("#product_measure").on("change", function() {
                var val = $(this).val();
                $('#measurement').val(val);
                if (val == "Custom") {
                    $('#measurement').val('');
                    $('#measure').show();
                } else {
                    $('#measure').hide();
                }
            });

            // Product Measure Ends :)

        });

        // TAGIT

        $("#metatags").tagit({
            fieldName: "meta_tag[]",
            allowSpaces: true
        });

        $("#tags").tagit({
            fieldName: "tags[]",
            allowSpaces: true
        });
        // TAGIT ENDS


        // Remove White Space


        function isEmpty(el) {
            return !$.trim(el.html())
        }


        // Remove White Space Ends

        // Size Section

        $("#size-btn").on('click', function() {

            $("#size-section").append('' +
                '<div class="size-area">' +
                '<span class="remove size-remove"><i class="fas fa-times"></i></span>' +
                '<div  class="row">' +
                '<div class="col-md-4 col-sm-4">' +
                '<label>' +
                '{{ __('Size Name') }} :' +
                '<span>{{ __('(eg. S,M,L,XL,3XL,4XL)') }}</span>' +
                '</label>' +
                `<input type="text" name="size[]" class="input-field tsize"
															placeholder="{{ __('Enter Product Size') }}"
															value="" required="">` +
                '</div>' +
                '<div class="col-md-4 col-sm-4">' +
                '<label>' +
                '{{ __('Size Qty') }} :' +
                '<span>{{ __('(Quantity of this size)') }}</span>' +
                '</label>' +
                '<input type="number" name="size_qty[]" required class="input-field" placeholder="{{ __('Size Qty') }}" value="1" min="1">' +
                '</div>' +
                '<div class="col-md-4 col-sm-4">' +
                '<label>' +
                '{{ __('Size Price') }} :' +
                '<span>{{ __('(Added with base price)') }}</span>' +
                '</label>' +
                '<input type="number" name="size_price[]" required class="input-field" placeholder="{{ __('Size Price') }}" value="0" min="0">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '');


        });

        $(document).on('click', '.size-remove', function() {

            $(this.parentNode).remove();
            if (isEmpty($('#size-section'))) {

                $("#size-section").append('' +
                    '<div class="size-area">' +
                    '<span class="remove size-remove"><i class="fas fa-times"></i></span>' +
                    '<div  class="row">' +
                    '<div class="col-md-3 col-sm-6">' +
                    '<label>' +
                    '{{ __('Size Name') }} :' +
                    '<span>{{ __('(eg. S,M,L,XL,3XL,4XL)') }}</span>' +
                    '</label>' +
                    `<select name="size[]" class="input-field size-name">${tsizeText}</select>` +
                    '</div>' +
                    '<div class="col-md-3 col-sm-6">' +
                    '<label>' +
                    '{{ __('Size Qty') }} :' +
                    '<span>{{ __('(Quantity of this size)') }}</span>' +
                    '</label>' +
                    '<input type="number" name="size_qty[]" required class="input-field" placeholder="{{ __('Size Qty') }}" value="1" min="1">' +
                    '</div>' +
                    '<div class="col-md-3 col-sm-6">' +
                    '<label>' +
                    '{{ __('Size Price') }} :' +
                    '<span>{{ __('(Added with base price)') }}</span>' +
                    '</label>' +
                    '<input type="number" name="size_price[]" required class="input-field" placeholder="{{ __('Size Price') }}" value="0" min="0">' +
                    '</div>' +
                    '<div class="col-md-3 col-sm-6">' +
                    '<label>' +
                    '{{ __('Size Color') }} :' +
                    '<span>' +
                    '{{ __('(Select color of this size)') }}' +
                    '</span>' +
                    '</label>' +
                    `<select name="color[]" class="input-field color-name" style="background-color:${tcolor[0]}">${tcolorText}</select>` +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '');


            }


        });



        $(document).on('change', '.color-name', function() {
            $(this).css('background-color', $(this).val());
        });




        $("#color-btn").on('click', function() {

            $("#color-section").append(`
            <div class="size-area">
                                                            <span class="remove size-remove"><i
                                                                    class="fas fa-times"></i></span>
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12">
                                                                    <label>
                                                                        {{ __('Color') }} :
                                                                    </label>
																	<div class="color-area">
																		<div class="input-group colorpicker-component cp">
																			<input type="text" name="color_all[]" value="#000000" class="input-field cp tcolor"/>
																			<span class="input-group-addon"><i></i></span>
																		</div>
																	</div>
                                                                </div>

                                                                


                                                            </div>
                                                        </div>
            
            `);
            $('.cp').colorpicker();

        });


        $(document).on('click', '.color-remove', function() {

            $(this.parentNode).remove();
            if (isEmpty($('#color-section'))) {

                $("#color-section").append(`
            <div class="size-area">
                    <span class="remove size-remove"><i
                            class="fas fa-times"></i></span>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <label>
                                {{ __('Color') }} :
                                
                            </label>
                            <div class="color-area">
                                <div class="input-group colorpicker-component cp">
                                    <input type="text" name="color_all[]" value="#000000" class="input-field cp tcolor"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <label>
                                {{ __('Color Price') }} :
                                <span>
                                    {{ __('(Added with base price)') }}
                                </span>
                            </label>
                            <input type="number" name="color_price[]" required
                                class="input-field"
                                placeholder="{{ __('Color Price') }}"
                                value="" min="0">
                        </div>


                    </div>
                </div>
            
            `);
                $('.cp').colorpicker();
            }



        });

        // Color Section Ends



        // Feature Section

        $("#feature-btn").on('click', function() {

            $("#feature-section").append('' +
                '<div class="feature-area">' +
                '<span class="remove feature-remove"><i class="fas fa-times"></i></span>' +
                '<div  class="row">' +
                '<div class="col-lg-6">' +
                '<input type="text" name="features[]" class="input-field" placeholder="{{ __('Enter Your Keyword') }}">' +
                '</div>' +
                '<div class="col-lg-6">' +
                '<div class="input-group colorpicker-component cp">' +
                '<input type="text" name="colors[]" value="#000000" class="input-field cp"/>' +
                '<span class="input-group-addon"><i></i></span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '');
            $('.cp').colorpicker();
        });

        $(document).on('click', '.feature-remove', function() {

            $(this.parentNode).remove();
            if (isEmpty($('#feature-section'))) {

                $("#feature-section").append('' +
                    '<div class="feature-area">' +
                    '<span class="remove feature-remove"><i class="fas fa-times"></i></span>' +
                    '<div  class="row">' +
                    '<div class="col-lg-6">' +
                    '<input type="text" name="features[]" class="input-field" placeholder="{{ __('Enter Your Keyword') }}">' +
                    '</div>' +
                    '<div class="col-lg-6">' +
                    '<div class="input-group colorpicker-component cp">' +
                    '<input type="text" name="colors[]" value="#000000" class="input-field cp"/>' +
                    '<span class="input-group-addon"><i></i></span>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '');
                $('.cp').colorpicker();
            }

        });

        // Feature Section Ends
        // Type Check

        $('#type_check').on('change', function() {
            var val = $(this).val();
            if (val == 1) {
                $('.row.file').css('display', 'flex');
                $('.row.file').find('input[type=file]').prop('required', true);
                $('.row.link').find('textarea').val('').prop('required', false);
                $('.row.link').hide();
            } else {
                $('.row.file').hide();
                $('.row.link').css('display', 'flex');
                $('.row.file').find('input[type=file]').prop('required', false);
                $('.row.link').find('textarea').prop('required', true);
            }

        });

        // Type Check Ends



        // License Section

        $("#license-btn").on('click', function() {

            $("#license-section").append('' +
                '<div class="license-area">' +
                '<span class="remove license-remove"><i class="fas fa-times"></i></span>' +
                '<div  class="row">' +
                '<div class="col-lg-6">' +
                '<input type="text" name="license[]" class="input-field" placeholder="{{ __('License Key') }}" required="">' +
                '</div>' +
                '<div class="col-lg-6">' +
                '<input type="number" name="license_qty[]" min="1" class="input-field" placeholder="{{ __('License Quantity') }}" value="1">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '');
        });

        $(document).on('click', '.license-remove', function() {

            $(this.parentNode).remove();
            if (isEmpty($('#license-section'))) {

                $("#license-section").append('' +
                    '<div class="license-area">' +
                    '<span class="remove license-remove"><i class="fas fa-times"></i></span>' +
                    '<div  class="row">' +
                    '<div class="col-lg-6">' +
                    '<input type="text" name="license[]" class="input-field" placeholder="{{ __('License Key') }}" required="">' +
                    '</div>' +
                    '<div class="col-lg-6">' +
                    '<input type="number" name="license_qty[]" min="1" class="input-field" placeholder="{{ __('License Quantity') }}" value="1">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '');
            }

        });

        // License Section Ends

        $("#size-check").change(function() {
            if (this.checked) {
                $("#size-display").show();
                $("#stckprod").hide();
            } else {
                $("#size-display").hide();
                $("#stckprod").show();

            }
        });

        $("#whole_check").change(function() {
            if (this.checked) {
                $("#whole-section input").prop('required', true);
            } else {
                $("#whole-section input").prop('required', false);
            }
        });


        // Whole Sell Section

        $("#whole-btn").on('click', function() {

            if (whole_sell > $("[name='whole_sell_qty[]']").length) {
                $("#whole-section").append('' +
                    '<div class="feature-area">' +
                    '<span class="remove whole-remove"><i class="fas fa-times"></i></span>' +
                    '<div  class="row">' +
                    '<div class="col-lg-6">' +
                    '<input type="number" name="whole_sell_qty[]" class="input-field" placeholder="{{ __('Enter Quantity') }}" min="0">' +
                    '</div>' +
                    '<div class="col-lg-6">' +
                    '<input type="number" name="whole_sell_discount[]" class="input-field" placeholder="{{ __('Enter Discount Percentage') }}" min="0">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '');
            }

        });

        $(document).on('click', '.whole-remove', function() {

            $(this.parentNode).remove();
            if (isEmpty($('#whole-section'))) {

                $("#whole-section").append('' +
                    '<div class="feature-area">' +
                    '<span class="remove whole-remove"><i class="fas fa-times"></i></span>' +
                    '<div  class="row">' +
                    '<div class="col-lg-6">' +
                    '<input type="number" name="whole_sell_qty[]" class="input-field" placeholder="{{ __('Enter Quantity') }}" min="0">' +
                    '</div>' +
                    '<div class="col-lg-6">' +
                    '<input type="number" name="whole_sell_discount[]" class="input-field" placeholder="{{ __('Enter Discount Percentage') }}" min="0">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '');
            }

        });

        // Whole Sell Section Ends


    })(jQuery);
</script>
