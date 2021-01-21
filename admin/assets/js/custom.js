jQuery(document).ready(function ($) {
	
    $(document).on( 'click', '.nav-tab-wrapper.bitwise-tabs a', function() {
        var tab = $(this).data('tab-id');

        $('.nav-tab').removeClass('nav-tab-active');
        $('.content').removeClass('content-tab-active');
        
        $('#'+tab).addClass('nav-tab-active');
        $('#content-'+tab).addClass('content-tab-active');
        return false;
    });
    
    $('#nextlecture-dlist').DataTable();

    $('#nextlecture').on( 'click', function () {
        $('#nextlecture-dlist').DataTable().columns.adjust().responsive.recalc();
        $('#nextlecture-dlist').css("width", '100%');
    });
    $('#upcominglecture').on( 'click', function () {
        $('#upcominglecture-dlist').DataTable().columns.adjust().responsive.recalc();
        $('#upcominglecture-dlist').css("width", '100%');
    });
    $('#completedlecture').on( 'click', function () {
        $('#completedlecture-dlist').DataTable().columns.adjust().responsive.recalc();
        $('#completedlecture-dlist').css("width", '100%');
    });

    $('#class-list').DataTable();
    $('#class-student').DataTable();
    
    alertify.logPosition("top right").closeLogOnClick(true);
    var loader = '<div class="preloader"><div class="cssload-speeding-wheel"></div></div>';

    /*jQuery(document).on("click", ".dropdown-toggle", function (e) {
        $(this).parents('.bootstrap-select').toggleClass('open');
    });*/

    /*Services Ajax Function*/
    $(document).on("click", "[data-tag=list-delete]", function (e) { /* Delete category */
        e.stopPropagation();
        var $item = $(this).closest('.cat-edit');
        var id = $item.data('catid');
        bootbox.dialog({
            message: bookme_object.del_category,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'delete_cat',
                            'del_cat': id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            if (response == 1) {
                                $item.remove();
                                alertify.success(bookme_object.category_deleted);
                                $('.danger-loader').removeClass('bookme-progress');
                            } else {
                                alertify.error(bookme_object.cat_del_problem);
                                $('.danger-loader').removeClass('bookme-progress');
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }).on("click", "#save_cat", function (e) { /* Save category */
        var name = jQuery("#name").val();
        if (name == '') {
            alertify.error(bookme_object.cat_error);
            jQuery("#name").focus();
            return false;
        }
        else {
            var data = {
                'action': 'bookme_admin_action',
                'call': 'save_cat',
                'name': name
            };
            jQuery("#save_cat").addClass('bookme-progress');
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                if (response == 'exist') {
                    alertify.error(bookme_object.cat_exist);
                    jQuery("#save_cat").removeClass('bookme-progress');
                } else if (response == 'db_error') {
                    alertify.error(bookme_object.cat_save_problem);
                    jQuery("#save_cat").removeClass('bookme-progress');
                } else if (response == 0) {
                    alertify.error(bookme_object.cat_save_problem);
                    jQuery("#save_cat").removeClass('bookme-progress');
                } else {
                    var cat_tpl = '<div class="list-group-item cat-edit" data-catid="' + response + '"> <div class="list-content"> <span class="item-right">0</span> <span class="list-text">' + name + '</span> <div class="item-actions"> <span class="btn btn-pure btn-icon" data-toggle="list-editable"><i class="icon md-edit" aria-hidden="true"></i></span> <span class="btn btn-pure btn-icon" data-tag="list-delete"><i class="icon md-delete" aria-hidden="true"></i></span> </div></div><div class="list-editable"> <div class="form-group form-material"> <input type="text" class="form-control empty" name="label" data-bind=".list-text" value="' + name + '" id="new_name"> <select data-plugin="selectpicker" id="catState"> <option selected>' + bookme_object.validtext + '</option> <option>' + bookme_object.invalidtext + '</option> </select> <input type="hidden" class="form-control" value="' + response + '" id="catID" /><button type="button" class="input-editable-close icon md-check-circle" data-save="save-cat" aria-label="Save" aria-expanded="true"></button> <button type="button" class="input-editable-close icon md-close" data-toggle="list-editable-close" aria-label="Close" aria-expanded="true"></button> </div></div></div>';
                    jQuery("#ajax-cat-section").append(cat_tpl);
                    alertify.success(bookme_object.cat_saved);
                    jQuery("#save_cat").removeClass('bookme-progress');
                    jQuery("#name").val('');
                    jQuery("#addcatmodal").modal("hide");
                    jQuery('[data-plugin=selectpicker]').selectpicker('refresh');
                }
            });
        }
    }).on("click", "[data-save=save-cat]", function (e) { /* Edit category */
        e.stopPropagation();
        var $item = $(this).closest('.cat-edit'),
            cat_name = $item.find("#new_name").val(),
            cat_id = $item.find("#catID").val(),
            catState = $item.find("#catState").val(),
            $content = $item.find(".list-content"),
            $editable = $item.find(".list-editable");

        if (catState == '') {
            catState = 'valid';
        }
        var data = {
            'action': 'bookme_admin_action',
            'call': 'edit_cat',
            'cat_name': cat_name,
            'cat_id': cat_id,
            'catState': catState
        };
        $(this).addClass('bookme-progress');

        jQuery.post(bookme_object.ajax_url, data, function (response) {
            if (response == 1) {
                $item.find('.list-text').text(cat_name);
                alertify.success(bookme_object.cat_edited);
                $item.find('.bookme-progress').removeClass('bookme-progress');
                $content.show();
                $editable.hide();
                $item.removeClass('no-action');
                if (catState == 'invalid') {
                    $item.addClass('bg-danger');
                } else {
                    $item.removeClass('bg-danger');
                }
            } else if (response == 'exist') {
                alertify.error(bookme_object.cat_exist);
                $item.find('.bookme-progress').removeClass('bookme-progress');
            } else {
                alertify.error(bookme_object.cat_edit_problem);
                $item.find('.bookme-progress').removeClass('bookme-progress');
            }
        });
    }).on("click", ".list-group-item", function (e) { /* Fetch services */
        if ($(this).hasClass('no-action')) {
            return;
        }
        var cat_id = $(this).data('catid');
        var data = {
            'action': 'bookme_admin_action',
            'call': 'fetch_services',
            'cat_id': cat_id
        };
        $('#ajax-services').append(loader);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            if (response == 0) {
                alertify.error(bookme_object.ser_fetch_problem);
                $('#ajax-services').find('.preloader').remove();
            } else {
                $('#ajax-services').html(response);
            }
        });
    }).on("click", "#edit_service", function (e) { /* Edit service */
        alertify.logPosition("bottom right");
        var form = $('#edit_ser_form').serialize();
        var cat = $("#category").val();
        var title = $("#title").val();
        var loader = $(this);
        if (title == "") {
            alertify.error(bookme_object.ser_title_error);
            $("#title").focus();
        } else if (cat == 0) {
            alertify.error(bookme_object.cat_select_error);
            $("#category").focus();
        } else {
            loader.addClass('bookme-progress');
            $.post(bookme_object.ajax_url + '?action=bookme_admin_action&call=edit_service', form, function (response) {
                loader.removeClass('bookme-progress');
                if (response == 0) {
                    alertify.error(bookme_object.ser_save_problem);
                } else {
                    response = jQuery.parseJSON(response);
                    if (response.response == 'added') {
                        alertify.success(bookme_object.ser_saved);
                        $('#edit_ser_form').append('<input type="hidden" name="ser_id" value="' + response.id + '" id="ser_id">');

                    } else if (response.response == 'edited') {
                        alertify.success(bookme_object.ser_edited);
                    } else {
                        alertify.error(bookme_object.ser_save_problem);
                    }
                }

            });
        }
    }).on("click", "#del_service", function (e) { /* Delete service */
        var id = $('#ser_id').val();
        var ser_id = [];
        ser_id.push(id);
        bookme_del_service(ser_id);

    }).on("click", '#del_service_array', function (e) { /* Delete service */
        var ser_id = jQuery('#ajax-services').find('input[type=checkbox]:checked').map(function () {
            return $(this).closest('[data-toggle="slidePanel"]').data('serid');
        }).get();
        bookme_del_service(ser_id);
        $('.selectable-all').attr('checked', false);

    }).on("click", '#addBreak', function (e) { /* Add break */
        alertify.logPosition("top right");

        var id = $(this).data('key');
        var loader = $(this);

        var start = $("#schedule_start_" + id).val();
        var end = $("#schedule_end_" + id).val();
        var break_start = $("#break_start_" + id).val();
        var break_end = $("#break_end_" + id).val();

        if (start == '' || end == '') {
            alertify.error(bookme_object.select_schedule_time);
            return false;
        }

        var time = timeFormatter(start);
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12) hrs = hrs + 12;
        if (format == "AM" && hrs == 12) hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10) hours = "0" + hours;
        if (mnts < 10) minutes = "0" + minutes;
        var s1 = hours + ":" + minutes;
        var time = timeFormatter(end);
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12) hrs = hrs + 12;
        if (format == "AM" && hrs == 12) hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10) hours = "0" + hours;
        if (mnts < 10) minutes = "0" + minutes;
        var s2 = hours + ":" + minutes;
        var time = timeFormatter(break_start);
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12) hrs = hrs + 12;
        if (format == "AM" && hrs == 12) hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10) hours = "0" + hours;
        if (mnts < 10) minutes = "0" + minutes;
        var b1 = hours + ":" + minutes;
        var time = timeFormatter(break_end);
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12) hrs = hrs + 12;
        if (format == "AM" && hrs == 12) hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10) hours = "0" + hours;
        if (mnts < 10) minutes = "0" + minutes;
        var b2 = hours + ":" + minutes;

        if (s1 > b1 || s2 < b2) {
            alertify.error(bookme_object.select_currect_time);
            return false;
        }
        if (break_start == '') {
            alertify.error(bookme_object.select_start_time);
            return false;
        }
        if (break_end == '') {
            alertify.error(bookme_object.select_end_time);
            return false;
        }
        if (b1 > b2) {
            alertify.error(bookme_object.select_currect_time);
            return false;
        }
        loader.addClass('bookme-progress');
        $('.webUiPopover').webuiPopover('hide');
        var btn = '<button type="button" class="btn btn-info"> ' + break_start + ' - ' + break_end + ' </button> <button title="' + bookme_object.delete_break + '" type="button" id="delete_break" class="btn btn-info" data-key="' + id + '"><span>×</span> </button><input type="hidden" value="' + break_start + '" name="break_start[' + id + '][]"><input type="hidden" value="' + break_end + '" name="break_end[' + id + '][]">';
        $("#break_btn_" + id).html(btn);
        loader.removeClass('bookme-progress');

    }).on("click", '#delete_break', function (e) { /* Delete break */
        var key = $(this).data('key');
        $('.webui-popover').remove();
        var btn = '<button type="button" class="btn btn-info webUiPopover" data-trigger="click" data-style="info" data-animation="fade" data-title="Add break" data-width="300" data-height="auto"> ' + bookme_object.add_break + ' </button> <div class="webui-popover-content"> <div class="input-daterange"> <div class="input-box"> <div class="input-group"> <input id="break_start_' + key + '" type="text" class="form-control ui-timepicker-input schedule-bookme-time-picker" autocomplete="off" placeholder="' + bookme_object.starttext + '"> </div><div class="input-group"> <span class="input-group-addon">-</span> <input id="break_end_' + key + '" type="text" class="form-control ui-timepicker-input schedule-bookme-time-picker" autocomplete="off" placeholder="' + bookme_object.endtext + '"> </div></div><div class="input-btn"> <button type="button" class="btn btn-floating btn-success btn-xs waves-effect waves-float waves-light" data-key="' + key + '" id="addBreak"><i class="icon md-check" aria-hidden="true"></i></button> </div></div></div>';
        $(this).parent().html(btn);
        $('.webUiPopover').webuiPopover({closeable: true, dismissible: false});
        $('.schedule-bookme-time-picker').timepicker({'step': 15, 'timeFormat': timeformat});

    }).on("click", '#emp_img_uploader', function (e) { /* Upload employee img */
        e.preventDefault();
        var image = wp.media({
            title: bookme_object.upload_image,
            multiple: false
        }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#emp_img_url').val(image_url);
                $('#emp_img_uploader').find('img').attr('src', image_url);
            });

    }).on("click", "#save_emp_data", function (e) { /* Edit service */
        alertify.logPosition("bottom right");
        var title = $("#bookme-full-name").val();
        var email = $("#bookme-email").val();
        var phone = $("#bookme-phone").val();
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var loader = $(this);
        if (title == "") {
            alertify.error(bookme_object.enter_emp_name);
            jQuery("#bookme-full-name").focus();
        } else if (email == "") {
            alertify.error(bookme_object.enter_emp_email);
            jQuery("#bookme-email").focus();
        } else if (!regex.test(email)) {
            alertify.error(bookme_object.invalid_email);
            jQuery("#bookme-email").focus();
        } else if (!$("#bookme-phone").intlTelInput("isValidNumber")) {
            alertify.error(bookme_object.invalid_phone);
            jQuery("#bookme-phone").focus();
        } else {
            var form = $('#emp_data_form').serializeArray();
            $.each(form, function (key, data) {
                if (this.name == "phone")
                    this.value = jQuery("#bookme-phone").intlTelInput("getNumber");
            });
            loader.addClass('bookme-progress');
            jQuery.post(bookme_object.ajax_url + '?action=bookme_admin_action&call=save_emp_data', form, function (response) {
                loader.removeClass('bookme-progress');
                if (response == 0) {
                    alertify.error(bookme_object.emp_save_problem);
                } else {
                    response = jQuery.parseJSON(response);
                    if (response.response == 'added') {
                        alertify.success(bookme_object.emp_saved);
                        $('#emp_data_form').append('<input type="hidden" id="schedule_emp" value="' + response.id + '" name="emp_id">');
                    } else if (response.response == 'edited') {
                        alertify.success(bookme_object.emp_edited);
                    } else {
                        alertify.error(bookme_object.emp_save_problem);
                    }
                }

            });
        }
    }).on("click", "#del_member", function (e) { /* Delete member */
        var id = $('#schedule_emp').val();
        var mem_id = [];
        mem_id.push(id);
        bookme_del_member(mem_id);

    }).on("click", '#del_member_array', function (e) { /* Delete member */
        var mem_id = jQuery('.del_emp_checkbox').find('input[type=checkbox]:checked').map(function () {
            return $(this).attr('id');
        }).get();
        bookme_del_member(mem_id);
        $('.selectable-all').attr('checked', false);

    }).on('keyup', '.live-search-box', function () {
        var searchTerm = $(this).val().toLowerCase();
        $('#ajax-services').find('tr').each(function () {
            if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

    }).on('keyup', '#member-live-search', function () {
        var searchTerm = $(this).val().toLowerCase();
        $('#ajax_members').find('tr').each(function () {
            if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

    }).on('change', '#provider_name', function () {
        var empid = $(this).val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'get_services_by_emp_id',
            'empid': empid
        };
        $.post(bookme_object.ajax_url, data, function (response) {
            if (response != 0) {
                $('#edit_ser').empty().append(response);
            }
        });
    }).on('change', '#edit_ser', function () {
        var capacity = $('#edit_ser').find('option:selected').data('capacity');
        if (capacity == 0) {
            $('#customer_capacity').fadeOut();
        } else {
            $('#panel_capacity').text(capacity);
            $('#customer_capacity').fadeIn();
            if (parseInt($('#panel_total_customer').text()) >= parseInt($('#panel_capacity').text())) {
                alertify.error(bookme_object.ser_cap_full);
                $('#add_customer_div').hide();
            }
        }
    }).on('change', '#edit_ser, #time_start', function () {
        var duration = $('#edit_ser').find('option:selected').data('duration');
        var time = parseInt($('#time_start').val()) + parseInt(duration);
        $('#time_end').val(time);

    }).on('change', '#provider_name, #bookingEditdate, #time_start, #time_end', function () {
        alertify.logPosition("bottom right");
        var start_t = $('#time_start').val();
        var end_t = $('#time_end').val();
        var date = $('#bookingEditdate').val();
        var emp_id = $('#provider_name').val();
        var emp_name = $('#provider_name').find('option:selected').text();
        var booking_id = $('#bookingId').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'check_for_appointment',
            'start_t': start_t,
            'end_t': end_t,
            'emp_id': emp_id,
            'booking_id': booking_id,
            'date': date
        };
        $.post(bookme_object.ajax_url, data, function (response) {
            if (response != 0) {
                alertify.error(emp_name + " " + bookme_object.is_booked);
            }
        });
    }).on('change', '#time_end', function () {
        var duration = $('#edit_ser').find('option:selected').data('duration');
        var time = parseInt($('#time_start').val()) + parseInt(duration);
        if ($('#time_end').val() != time) {
            alertify.error(bookme_object.period_not_match);
        }
    }).on("click", ".delete_customer_cb", function (e) { /* remove customer from booking list */
        var id = $(this).data("id");
        $('[data-plugin=selectpicker] option[value=' + id + ']').prop('disabled', false);
        $('[data-plugin=selectpicker]').selectpicker('render');
        $(this).parent().parent().remove();
        $('#panel_total_customer').text(parseInt($('#panel_total_customer').text()) - 1);
        $('#add_customer_div').show();

    }).on("click", "#add_customer_cb", function (e) { /* add customer in booking list */
        if (parseInt($('#panel_total_customer').text()) < parseInt($('#panel_capacity').text())) {
            var cust_id = $('[data-plugin=selectpicker]').val();
            if ($('[data-plugin=selectpicker] option[value=' + cust_id + ']').prop('disabled') != undefined) {
                var cust = $('[data-plugin=selectpicker] option:selected').text();
                var html = '<div class="row margin-bottom-5"> <div class="col-md-7"> <span class="text-primary">' + cust + '</span> </div><div class="col-md-5 text-right"> <span class="btn btn-inverse disabled bookme_person"> <i class="fa fa-user"></i>&Cross;1 </span> <button class="btn btn-inverse btn-default delete_customer_cb" data-id="' + cust_id + '" style="color: red"> <i class="fa fa-trash font-size-15"></i> </button><input type="hidden" name="customers[]" value="' + cust_id + '"><input type="hidden" name="person[]" value="1"> </div></div>';
                $('[data-plugin=selectpicker] option[value=' + cust_id + ']').prop('disabled', true);
                $('[data-plugin=selectpicker]').selectpicker('render');
                $('#customer_list').append(html);
                $('#panel_total_customer').text(parseInt($('#panel_total_customer').text()) + 1);
                if (parseInt($('#panel_total_customer').text()) >= parseInt($('#panel_capacity').text())) {
                    $('#add_customer_div').hide();
                }
            }
        } else {
            alertify.error(bookme_object.ser_cap_full);
        }

    }).on("click", "#save_booking", function (e) { /* save booking */
        alertify.logPosition("bottom right").closeLogOnClick(true);
        var form = $('#booking_form').serialize();
        var ser = $("#edit_ser").val();
        var date = $("#bookingEditdate").val();
        var values = [];
        $("[name='customers[]']").each(function () {
            values.push($(this).val());
        });
        var emp_name = $('#provider_name').find('option:selected').text();
        var loader = $(this);
        if (ser == 0) {
            alertify.error(bookme_object.select_ser_error);
            jQuery("#edit_ser").focus();
        } else if (values.length <= 0) {
            alertify.error(bookme_object.add_cust_error);
        } else if (date == '') {
            alertify.error(bookme_object.booking_date_error);
        } else {
            loader.addClass('bookme-progress');
            jQuery.post(bookme_object.ajax_url + '?action=bookme_admin_action&call=save_booking', form, function (response) {
                loader.removeClass('bookme-progress');
                if (response == 0) {
                    alertify.error(bookme_object.booking_save_problem);
                } else {
                    //response = jQuery.parseJSON(response);
                    location.reload();
                    if (response.response == 'added') {
                        alertify.success(bookme_object.booking_saved);
                        $('#booking_form').append('<input type="hidden" id="bookingId" value="' + response.id + '" name="bookingId">');
                    } else if (response.response == 'edited') {
                        alertify.success(bookme_object.booking_edited);
                    } else if (response.response == 'full') {
                        alertify.error(bookme_object.ser_cap_full);
                    } else if (response.response == 'booked') {
                        alertify.error(emp_name + " " + bookme_object.is_booked);
                    } else {
                        alertify.error(bookme_object.booking_save_problem);
                    }
                }
            });
        }

    }).on("click", "#del_booking", function (e) { /* Delete booking */
        var id = $('#bookingId').val();
        var b_id = [];
        b_id.push(id);
        bookme_del_booking(b_id);

    }).on("click", '#del_booking_array', function (e) { /* Delete booking */
        var b_id = jQuery('.del_booking_checkbox').find('input[type=checkbox]:checked').map(function () {
            return $(this).attr('id');
        }).get();
        bookme_del_booking(b_id);
        $('.selectable-all').attr('checked', false);

    }).on("click", '#edit_customer', function (e) { /* edit customer */
        alertify.logPosition("bottom right");
        var title = $("#custName").val();
        var email = $("#custEmail").val();
        var phone = $("#custPhone").val();
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var loader = $(this);
        if (title == "") {
            alertify.error(bookme_object.enter_cust_name);
            jQuery("#custName").focus();
        } else if (phone == "") {
            alertify.error(bookme_object.enter_cust_phone);
            jQuery("#custPhone").focus();
        } else if (!$("#custPhone").intlTelInput("isValidNumber")) {
            alertify.error(bookme_object.invalid_phone);
            jQuery("#custPhone").focus();
        } else if (email == "") {
            alertify.error(bookme_object.enter_cust_email);
            jQuery("#custEmail").focus();
        } else if (!regex.test(email)) {
            alertify.error(bookme_object.invalid_email);
            jQuery("#custEmail").focus();
        } else {
            var form = $('#save_cust_details').serializeArray();
            $.each(form, function (key, data) {
                if (this.name == "custPhone")
                    this.value = jQuery("#custPhone").intlTelInput("getNumber");
            });
            loader.addClass('bookme-progress');
            jQuery.post(bookme_object.ajax_url + '?action=bookme_admin_action&call=save_cust_data', form, function (response) {
                loader.removeClass('bookme-progress');
                if (response == 0) {
                    alertify.error(bookme_object.cust_save_problem);
                } else {
                    response = jQuery.parseJSON(response);
                    if (response.response == 'added') {
                        alertify.success(bookme_object.cust_saved);
                        $('#save_cust_details').append('<input type="hidden" value="' + response.id + '" name="custId">');
                    } else if (response.response == 'edited') {
                        alertify.success(bookme_object.cust_edited);
                    } else {
                        alertify.error(bookme_object.cust_save_problem);
                    }
                }

            });
        }
    }).on("click", "#del_customer", function (e) { /* Delete customer */
        var id = $('#custId').val();
        var cust_id = [];
        cust_id.push(id);
        bookme_del_customer(cust_id);

    }).on("click", '#del_customer_array', function (e) { /* Delete customer */
        var cust_id = jQuery('.del_cust_checkbox').find('input[type=checkbox]:checked').map(function () {
            return $(this).attr('id');
        }).get();
        bookme_del_customer(cust_id);
        $('.selectable-all').attr('checked', false);

    }).on("click", '#del_payment_array', function (e) { /* Delete payment */
        var pay_id = jQuery('.del_payment_checkbox').find('input[type=checkbox]:checked').map(function () {
            return $(this).attr('id');
        }).get();
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_payments,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_payments',
                            'pay_id': pay_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            $('.danger-loader').removeClass('bookme-progress');
                            if (response == 1) {
                                alertify.success(bookme_object.payment_deleted);
                                actionBtn.hide();
                                var table = $("#paymentTable").DataTable();
                                $.each(pay_id, function (index, item) {
                                    table.row($('#payment_id_' + item)).remove().draw();
                                });
                            } else {
                                alertify.error(bookme_object.payment_del_problem);
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        });
        $('.selectable-all').attr('checked', false);

    }).on("click", '#print_invoice', function (e) { /* print invoice */
        var w = window.open();
        var content = $('#invoice_for_print').html();
        content += '<style>.table,td,th{border: 1px solid #eee;border-collapse: collapse;text-align: left;padding: 10px;width: 500px;} .table-responsive{margin: 0 auto; margin-bottom: 16.5px; border: 1px solid #e0e0e0; width: fit-content; text-align: center;}</style>';
        w.document.write(content);
        w.print();
        w.close();

    }).on("click", '#save_coupon', function (e) { /* create coupon */
        alertify.logPosition("bottom right");
        var form = $('#coupon_create_form').serialize();
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url + '?action=bookme_admin_action&call=save_coupon', form, function (response) {
            loader.removeClass('bookme-progress');
            if (response == 0) {
                alertify.error(bookme_object.coupon_create_problem);
            } else {
                response = jQuery.parseJSON(response);
                if (response.response == 'added') {
                    alertify.success(bookme_object.coupon_saved);
                    $('#coupon_create_form').append('<input type="hidden" value="' + response.id + '" name="coupon_id" id="coupon_id">');
                } else if (response.response == 'edited') {
                    alertify.success(bookme_object.coupon_edited);
                } else {
                    alertify.error(bookme_object.coupon_create_problem);
                }
            }

        });
    }).on("click", "#comp_payment_btn", function (e) { /* Complete payment status */
        alertify.logPosition("bottom right");
        var id = $(this).data("id");
        var data = {
            'action': 'bookme_admin_action',
            'call': 'complete_payment',
            'payment_id': id
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            if (response == 0) {
                alertify.error(bookme_object.payment_complete_problem);
            } else {
                alertify.success(bookme_object.payment_completed);
                $('#comp_payment').remove();
                location.reload();
            }

        });
    }).on("click", "#del_coupon", function (e) { /* Delete coupon */
        var id = $('#coupon_id').val();
        var coupon_id = [];
        coupon_id.push(id);
        bookme_del_coupon(coupon_id);

    }).on("click", "#del_coupon_array", function (e) { /* Delete coupons */
        var coupon_id = jQuery('.del_coupon_checkbox').find('input[type=checkbox]:checked').map(function () {
            return $(this).attr('id');
        }).get();
        bookme_del_coupon(coupon_id);
        $('.selectable-all').attr('checked', false);

    }).on("click", "#booking_custom_save", function (e) { /* Save custom fields */
        var data = [];
        var loader = $(this);
        var $error = 0;
        jQuery(".input_fields").each(function (index) {
            var $this = $(this),
                field = {};
            switch ($this.attr('id')) {
                case 'checkboxGroup':
                case 'radioGroup':
                case 'dropDown':
                    field.items = [];
                    $this.find('ul.input_fields_wrap_items li').each(function () {
                        if ($(this).find('input').val() != "") {
                            field.items.push($(this).find('input').val());
                        } else {
                            $error = 1;
                            return false;
                        }
                    });
                case 'textArea':
                case 'textField':
                case 'textContent':
                    if ($(this).find('input').val() != "") {
                        field.type = $this.attr('id');
                        field.label = $this.find('.inputlblOne').val();
                        field.required = $this.find('.ab-required').prop('checked');
                        field.position = index;
                    } else {
                        $error = 1;
                        return false;
                    }
            }
            data.push(field);
        });
        if ($error != 1) {
            loader.addClass('bookme-progress');
            $.ajax({
                type: 'POST',
                url: bookme_object.ajax_url,
                data: {action: 'bookme_admin_action', 'call': 'save_custom_fields', fields: JSON.stringify(data)},
                complete: function () {
                    alertify.success(bookme_object.custom_field_saved);
                    loader.removeClass('bookme-progress');
                }
            });
        } else {
            alertify.error(bookme_object.label_required);
            return false;
        }
    }).on("click", "#emailnotification", function (e) { /* Save notification */
        var sender_name = jQuery('#bookme_email_sender_name').val();
        var sender_email = jQuery('#bookme_email_sender_email').val();
        var customer_mail = jQuery('#email_customer').prop('checked');
        var employee_mail = jQuery('#email_employee').prop('checked');
        var admin_mail = jQuery('#email_admin').prop('checked');
        var customer_subject = jQuery('#customer_subject').val();
        var employee_subject = jQuery('#employee_subject').val();
        var admin_subject = jQuery('#admin_subject').val();
        var customer_msg, employee_msg, admin_msg;
        if (jQuery("#wp-emessage_customer-wrap").hasClass("tmce-active")){
            customer_msg = tinymce.editors[0].getContent();
        }else{
            customer_msg = jQuery('#emessage_customer').val();
        }
        if (jQuery("#wp-emessage_employee-wrap").hasClass("tmce-active")){
            employee_msg = tinymce.editors[1].getContent();
        }else{
            employee_msg = jQuery('#emessage_employee').val();
        }
        if (jQuery("#wp-emessage_admin-wrap").hasClass("tmce-active")){
            admin_msg = tinymce.editors[2].getContent();
        }else{
            admin_msg = jQuery('#emessage_admin').val();
        }

        var data = {
            'action': 'bookme_admin_action',
            'call': 'insert_email_notification',
            'sender_name': sender_name,
            'sender_email': sender_email,
            'customer_mail': customer_mail,
            'employee_mail': employee_mail,
            'admin_mail': admin_mail,
            'customer_subject': customer_subject,
            'employee_subject': employee_subject,
            'admin_subject': admin_subject,
            'customer_msg': customer_msg,
            'employee_msg': employee_msg,
            'admin_msg': admin_msg
        };
        var loader = $(this);
        loader.addClass('bookme-progress').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress').prop('disabled', false);
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.email_saved);
            }
        });

    }).on("click", "#smsnotification", function (e) { /* Save SMS notification */
        var account_sid = jQuery('#bookme_sms_accountsid').val();
        var auth_token = jQuery('#bookme_sms_authtoken').val();
        var phone_no = jQuery('#bookme_sms_phone_no').intlTelInput("getNumber");
        var admin_phone_no = jQuery('#bookme_admin_phone_no').intlTelInput("getNumber");
        var customer_sms = jQuery('#sms_customer').prop('checked');
        var employee_sms = jQuery('#sms_employee').prop('checked');
        var admin_sms = jQuery('#sms_admin').prop('checked');
        var customer_msg = jQuery('#message_customer').val();
        var employee_msg = jQuery('#message_employee').val();
        var admin_msg = jQuery('#message_admin').val();

        var error = 0;

        if (phone_no != "") {
            if (!$("#bookme_sms_phone_no").intlTelInput("isValidNumber")) {
                jQuery("#bookme_sms_phone_no").focus();
                alertify.error(bookme_object.invalid_phone);
                error = 1;
                return false;
            }
        }

        if (admin_phone_no != "") {
            if (!$("#bookme_admin_phone_no").intlTelInput("isValidNumber")) {
                jQuery("#bookme_admin_phone_no").focus();
                alertify.error(bookme_object.invalid_phone);
                error = 1;
                return false;
            }
        }

        var data = {
            'action': 'bookme_admin_action',
            'call': 'insert_sms_notification',
            'account_sid': account_sid,
            'auth_token': auth_token,
            'phone_no': phone_no,
            'admin_phone_no': admin_phone_no,
            'customer_sms': customer_sms,
            'employee_sms': employee_sms,
            'admin_sms': admin_sms,
            'customer_msg': customer_msg,
            'employee_msg': employee_msg,
            'admin_msg': admin_msg
        };
        var loader = $(this);
        loader.addClass('bookme-progress').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress').prop('disabled', false);
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.sms_saved);
            }
        });

    }).on("click", "#appearance_btn_bullet", function (e) { /* Save appearance bullets */
        var b1 = jQuery('input[name=bullet1]').val();
        var b2 = jQuery('input[name=bullet2]').val();
        var bcart = jQuery('input[name=bullet_cart]').val();
        var b3 = jQuery('input[name=bullet3]').val();
        var b4 = jQuery('input[name=bullet4]').val();
        var b5 = jQuery('input[name=bullet5]').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_appearance_bullets',
            'bullet1': b1,
            'bullet2': b2,
            'bullet_cart': bcart,
            'bullet3': b3,
            'bullet4': b4,
            'bullet5': b5
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.appearance_bullet_edited);
            }
            if (msg == 2) {
                alertify.success(bookme_object.appearance_bullet_saved);
            }
        });
    }).on("click", "#appearance_btn", function (e) { /* Save appearance labels */
        var v1 = jQuery('input[name=category]').val();
        var v2 = jQuery('input[name=service]').val();
        var v3 = jQuery('input[name=employee]').val();
        var v4 = jQuery('input[name=number_of_person]').val();
        var v5 = jQuery('input[name=availability]').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_appearance_labels',
            'category': v1,
            'service': v2,
            'employee': v3,
            'number_of_person': v4,
            'availability': v5
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.appearance_label_edited);
            }
            if (msg == 2) {
                alertify.success(bookme_object.appearance_label_saved);
            }

        });
    }).on("click", "#appearance_color_btn", function (e) { /* Save appearance Colors */
        var color = jQuery('input[name=appearance_color]').val();
        var txtcolor = jQuery('input[name=text_color]').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_appearance_colors',
            'booking_color': color,
            'booking_colortxt': txtcolor
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.appearance_color_edited);
            }
            if (msg == 2) {
                alertify.success(bookme_object.appearance_color_saved);
            }
        });
    }).on("click", "#message_btn", function (e) { /* Save appearance booking msg */
        var mess = jQuery('input[name=booking_msg]').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_appearance_msg',
            'booking_mes': mess
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            var msg = jQuery.trim(response);
            if (msg == 1) {
                alertify.success(bookme_object.appearance_msg_edited);
            }
            if (msg == 2) {
                alertify.success(bookme_object.appearance_msg_saved);
            }
        });
    }).on("click", "#booking_company", function (e) { /* Save Company data */
        var companyName = jQuery('#bookme_co_name').val();
        var companyAddress = jQuery('#bookme_co_address').val();
        var companyPhone = jQuery('#bookme_co_phone').intlTelInput("getNumber");
        var companyWebsite = jQuery('#bookme_co_website').val();

        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_company_data',
            'companyName': companyName,
            'companyAddress': companyAddress,
            'companyPhone': companyPhone,
            'companyWebsite': companyWebsite
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            if (response != 0) {
                alertify.success(bookme_object.company_saved);
            }
        });
    }).on("click", "#save_gen_settings", function (e) { /* Enable coupons */
        var coupan = jQuery('#coupan_code').val();
        var day_limit = jQuery('#bookme_day_limit').val();
        if (day_limit == parseInt(day_limit, 10)) {
            var data = {
                'action': 'bookme_admin_action',
                'call': 'save_gen_settings',
                'coupan': coupan,
                'day_limit': day_limit
            };
            var loader = $(this);
            loader.addClass('bookme-progress');
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                loader.removeClass('bookme-progress');
                if (response != 0) {
                    alertify.success(bookme_object.gen_setting_saved);
                }
            });
        } else {
            alertify.error(bookme_object.day_limit_not_number);
        }
    }).on("click", "#booking_cart_save", function (e) { /* Enable cart */
        var cart = jQuery('#bookme_enable_cart').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_cart_settings',
            'cart': cart
        };
        var loader = $(this);
        loader.addClass('bookme-progress').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress').prop('disabled', false);
            if (response != 0) {
                alertify.success(bookme_object.gen_setting_saved);
            }
        });
    }).on("click", "#booking_payments", function (e) { /* Save payment details */
        var pmt_currency = jQuery('#bookme_pmt_currency').val();
        var pmt_local = jQuery('#bookme_pmt_local').val();
        var pmt_paypal = jQuery('#bookme_pmt_paypal').val();
        var pmt_paypal_api_username = jQuery('#bookme_pmt_paypal_api_username').val();
        var pmt_paypal_api_password = jQuery('#bookme_pmt_paypal_api_password').val();
        var pmt_paypal_api_signature = jQuery('#bookme_pmt_paypal_api_signature').val();
        var pmt_paypal_sandbox = jQuery('#bookme_pmt_paypal_sandbox').val();
        var pmt_stripe = jQuery('#bookme_pmt_stripe').val();
        var pmt_stripe_secret = jQuery('#bookme_pmt_stripe_secret').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_payment_details',
            'payment_pmt': 'payment_pmt',
            'pmt_currency': pmt_currency,
            'pmt_local': pmt_local,
            'pmt_paypal': pmt_paypal,
            'pmt_paypal_api_username': pmt_paypal_api_username,
            'pmt_paypal_api_password': pmt_paypal_api_password,
            'pmt_paypal_api_signature': pmt_paypal_api_signature,
            'pmt_paypal_sandbox': pmt_paypal_sandbox,
            'pmt_stripe': pmt_stripe,
            'pmt_stripe_secret_key': pmt_stripe_secret
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-progress');
            if (response != 0) {
                alertify.success(bookme_object.payment_details_saved);
            }
        });

    }).on("click", "#bookme_dayoff_nav_year_back", function () {
        bookme_print_cal(parseInt($('#bookme_dayoff_nav_year').val()) - 1);

    }).on("click", "#bookme_dayoff_nav_year_next", function () {
        bookme_print_cal(parseInt($('#bookme_dayoff_nav_year').val()) + 1);

    }).on("click", "#save_dayoff", function () {
        var $target = $('.bookme-cal-wrap');
        var key = jQuery(this).data('date');
        var day_off = $('#dayoff_' + key).prop('checked');
        var day_off_repeat = $('#dayoff_repeat_' + key).prop('checked');
        var staff_id = $('#emp_id').val();
        var id = ($('#dayoff_date_' + key).data('id') != undefined) ? $('#dayoff_date_' + key).data('id') : 0;
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_dayoff',
            'day_off': day_off,
            'day_off_repeat': day_off_repeat,
            'date': key,
            'staff_id': staff_id,
            'id': id
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        loader.prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.prop('disabled', false);
            loader.removeClass('bookme-progress');
            if (response != 0) {
                var year = key.split('-');
                days_off = JSON.parse(response);
                $('.bookme-cal-webUiPopover').webuiPopover('destroy');
                bookme_print_cal(year[2]);
                alertify.success(bookme_object.saved_daysoff);
            } else {
                alertify.error(bookme_object.save_daysoff_problem);
            }
        });

    }).on("change", "[id^=dayoff_]", function () {
        var id = $(this).attr('id');
        id = id.replace("dayoff_", "");
        $('#dayoff_repeat_' + id).prop("disabled", !$(this).prop("checked"));

    }).on("click", "#booking_woocommerce", function () {
        var enable = jQuery('#bookme_enable_woo').val();
        var product = jQuery('#bookme_woo_product').val();
        var cart_data = jQuery('#bookme_cart_data').val();
        var cart_data_text = jQuery('#bookme_cart_data_text').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_woocommerce',
            'enable': enable,
            'product': product,
            'cart_data': cart_data,
            'cart_data_text': cart_data_text
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        loader.prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.prop('disabled', false);
            loader.removeClass('bookme-progress');
            if (response == 1) {
                alertify.success(bookme_object.saved_woocommerce);
            } else {
                alertify.error(bookme_object.save_woocommerce_problem);
            }
        });

    }).on("click", "#booking_google_calendar", function () {
        var gc_client_id = jQuery('#bookme_gc_client_id').val();
        var gc_client_secret = jQuery('#bookme_gc_client_secret').val();
        var gc_2_way_sync = jQuery('#bookme_gc_2_way_sync').val();
        var gc_limit_events = jQuery('#bookme_gc_limit_events').val();
        var gc_event_title = jQuery('#bookme_gc_event_title').val();
        var data = {
            'action': 'bookme_admin_action',
            'call': 'save_google_calendar',
            'gc_client_id': gc_client_id,
            'gc_client_secret': gc_client_secret,
            'gc_2_way_sync': gc_2_way_sync,
            'gc_limit_events': gc_limit_events,
            'gc_event_title': gc_event_title
        };
        var loader = $(this);
        loader.addClass('bookme-progress');
        loader.prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.prop('disabled', false);
            loader.removeClass('bookme-progress');
            if (response == 1) {
                alertify.success(bookme_object.saved_gc);
            } else {
                alertify.error(bookme_object.save_gc_problem);
            }
        });

    });


    function bookme_del_service(ser_id) {
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_service,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_services',
                            'ser_id': ser_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            if (response == 1) {
                                alertify.success(bookme_object.ser_deleted);
                                $('.danger-loader').removeClass('bookme-progress');
                                $.slidePanel.hide();
                                actionBtn.hide();
                                $.each(ser_id, function (index, item) {
                                    $('[data-serid="' + item + '"]').remove();
                                });

                            } else {
                                alertify.error(bookme_object.ser_del_problem);
                                $('.danger-loader').removeClass('bookme-progress');
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }

    function bookme_del_member(mem_id) {
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_employee,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_members',
                            'emp_id': mem_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            $('.danger-loader').removeClass('bookme-progress');
                            if (response == 1) {
                                alertify.success(bookme_object.emp_deleted);
                                $.slidePanel.hide();
                                actionBtn.hide();
                                $.each(mem_id, function (index, item) {
                                    $('#mem_row_' + item).remove();
                                });
                            } else {
                                alertify.error(bookme_object.emp_del_problem);
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }

    function bookme_del_booking(b_id) {
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_booking,
            buttons: {
                success: {
                    label: bookme_object.deletetext,
                    className: "btn-danger danger-loader",
                    type: "alert",
                    callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_bookings',
                            'id': b_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            if (response == 1) {
                                alertify.success(bookme_object.booking_deleted);
                                $('.danger-loader').removeClass('bookme-progress');
                                $.slidePanel.hide();
                                actionBtn.hide();
                                var table = $("#bookingTable").DataTable();
                                $.each(b_id, function (index, item) {
                                    table.row($('#booking_id_' + item)).remove().draw();
                                });

                            } else {
                                alertify.error(bookme_object.booking_del_problem);
                                $('.danger-loader').removeClass('bookme-progress');
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }

    function bookme_del_customer(cust_id) {
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_customer,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_customers',
                            'cust_id': cust_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            $('.danger-loader').removeClass('bookme-progress');
                            if (response == 1) {
                                alertify.success(bookme_object.cust_deleted);
                                $.slidePanel.hide();
                                actionBtn.hide();
                                var table = $("#customerTable").DataTable();
                                $.each(cust_id, function (index, item) {
                                    table.row($('#cust_id_' + item)).remove().draw();
                                });
                            } else {
                                alertify.error(bookme_object.cust_del_problem);
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }

    function bookme_del_coupon(coupon_id) {
        alertify.logPosition("bottom right");
        bootbox.dialog({
            message: bookme_object.del_coupon,
            buttons: {
                success: {
                    label: bookme_object.deletetext, className: "btn-danger danger-loader", callback: function () {
                        var data = {
                            'action': 'bookme_admin_action',
                            'call': 'del_coupon',
                            'coupon_id': coupon_id
                        };
                        $('.danger-loader').addClass('bookme-progress');
                        var actionBtn = $(".site-action").actionBtn().data("actionBtn");
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            bootbox.hideAll();
                            $('.danger-loader').removeClass('bookme-progress');
                            if (response == 1) {
                                alertify.success(bookme_object.coupon_deleted);
                                $.slidePanel.hide();
                                actionBtn.hide();
                                var table = $("#coupon_table").DataTable();
                                $.each(coupon_id, function (index, item) {
                                    table.row($('#coupon_id_' + item)).remove().draw();
                                });
                            } else {
                                alertify.error(bookme_object.coupon_del_problem);
                                actionBtn.hide();
                            }
                        });
                        return false;
                    }
                }
            }
        })
    }

    $('[data-catid="0"]').trigger('click');

    /* Calender page */
    var calendar = jQuery('.bookme-booking-calendar').fullCalendar({
        header: {
            left: 'title',
            center: 'agendaDay,agendaWeek,month',
            right: 'prev,next today'
        },
        editable: false,
        firstDay: 1, //  1(Monday) this can be changed to 0(Sunday) for the USA system
        selectable: true,
        defaultView: 'month',
        droppable: false, // this allows things to be dropped onto the calendar !!!
        displayEventEnd: true,
        timeFormat: 'hh:mm a',
        eventRender: function (calEvent, $event) {
            var body = calEvent.title;
            if (calEvent.desc) {
                body += calEvent.desc;
            }
            $event.find('.fc-title').html(body);
        },
        eventAfterRender: function (calEvent, $calEventList, calendar) {
            $calEventList.each(function () {
                var $calEvent = $(this);
                $calEvent.css({'cursor':'pointer'});
                var titleHeight = $calEvent.find('.fc-title').height(),
                    origHeight = $calEvent.outerHeight(),
                    origWidth = $calEvent.outerWidth();
                var z_index = $calEvent.zIndex();
                if (origHeight < titleHeight) {
                    // Mouse handlers.
                    $calEvent.on('mouseenter', function () {
                        $calEvent.removeClass('fc-short')
                            .css({'z-index': 64, bottom: '', height: ''});
                    }).on('mouseleave', function () {
                        $calEvent.css({'z-index': z_index, height: origHeight});
                    });
                }
                $calEvent.on('click', function () {
                    if($calEvent.hasClass('bookme-event-big')){
                        $calEvent.removeClass('bookme-event-big').css({'z-index': z_index, height: origHeight, width: origWidth});
                    } else {
                        $calEvent.removeClass('fc-short').addClass('bookme-event-big').css({'z-index': 64, bottom: '', height: '', width: '150px'});
                    }
                });
            });
        }
    });
    $(document).on("click", ".empClickevent", function (e) { /* Get calender by emp id */
        var emp = jQuery(this).attr('data-id');
        emp = (emp == 0) ? undefined : emp;
        var data = {
            'action': 'bookme_admin_action',
            'call': 'get_calender_for_emp',
            'emp_id': emp
        };
        $('#my-tab-content .preloader').show();
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            calendar.fullCalendar('removeEvents');
            calendar.fullCalendar('addEventSource', response);
            $('#my-tab-content .preloader').hide();
        });
    });
    $('#empClickevent_all').trigger('click');


    /* Setting page */
    $('#bookme_co_phone, #bookme_sms_phone_no, #bookme_admin_phone_no').intlTelInput({
        preferredCountries: ["us", "br", "gb", "in"],
        initialCountry: "auto",
        geoIpLookup: function (callback) {
            $.get('https://ipinfo.io', function () {
            }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        }
    });

    if (jQuery('#bookme_pmt_paypal').val() == 'ec') {
        jQuery('.bookme-paypal').show();
    } else {
        jQuery('.bookme-paypal').hide();
    }

    if (jQuery('#bookme_pmt_stripe').val() == 'enabled') {
        jQuery('.bookme-stripe').show();
    } else {
        jQuery('.bookme-stripe').hide();
    }

    jQuery('#bookme_pmt_paypal').change(function () {
        if (jQuery('#bookme_pmt_paypal').val() == 'ec') {
            jQuery('.bookme-paypal').show();
        } else {
            jQuery('.bookme-paypal').hide();
        }
    });

    jQuery('#bookme_pmt_stripe').change(function () {
        if (jQuery('#bookme_pmt_stripe').val() == 'enabled') {
            jQuery('.bookme-stripe').show();
        } else {
            jQuery('.bookme-stripe').hide();
        }
    });
});

function bookme_print_cal(year) {
    var date = new Date();
    var cal = "";
    if (year <= 200) {
        year += 1900;
    }

    $('#bookme_dayoff_nav_year').val(year);
    $('.bookme-cal-wrap').html('');
    var months = Array.apply(0, Array(12)).map(function (_, i) {
        return moment().month(i).locale(bookme_object.locale).format('MMMM')
    });
    var weekdays = Array.apply(0, Array(7)).map(function (_, i) {
        return moment().weekday(i).locale(bookme_object.locale).format('dd')
    });
    var days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if (year % 4 == 0 && year != 1900) {
        days_in_month[1] = 29;
    }
    for (var month = 0; month < 12; month++) {
        var total = days_in_month[month];
        var beg_j = date;
        beg_j.setDate(1);
        if (beg_j.getDate() == 2) {
            beg_j.setDate(0);
        }
        beg_j.setMonth(month);
        beg_j.setYear(year);
        beg_j = beg_j.getDay();

        cal = '';
        cal += '<div class="col-md-4"><table class="bookme-dayoff-cal"><tbody><tr><th colspan="7"><strong>' + months[month] + '</strong></th></tr>';
        cal += '<tr class="bookme-dayoff-cal-weeks">';
        for (var i = 0; i < weekdays.length; i++) {
            cal += '<th>' + weekdays[i] + '</th>';
        }
        cal += '</tr><tr>';
        var week = 0;
        var month_back = ((month - 1) >= 0) ? month - 1 : 11;
        for (i = 1; i <= beg_j; i++) {
            cal += '<td class="cal_days_bef_aft">' + (days_in_month[month_back] - beg_j + i) + '</td>';
            week++;
        }
        for (i = 1; i <= total; i++) {
            if (week == 0) {
                cal += '<tr>';
            }
            var cur_date = i + '-' + (month + 1) + '-' + year;

            var tpl = "<div><p><label><input type='checkbox' id='dayoff_" + cur_date + "'>&nbsp; We are not working this day.</label></p>";
            tpl += "<p><label><input type='checkbox' id='dayoff_repeat_" + cur_date + "' disabled>&nbsp; Repeat every year.</label></p>";
            tpl += "<button type='button' class='btn btn-info' id='save_dayoff' data-date='" + cur_date + "'>Save</button></div>";

            cal += '<td id="dayoff_date_' + cur_date + '" class="bookme-cal-webUiPopover" data-style="info" data-title="' + cur_date + '" data-content="' + tpl + '">' + i + '</td>';

            week++;
            if (week == 7) {
                cal += '</tr>';
                week = 0;
            }
        }
        for (i = 1; week != 0; i++) {
            cal += '<td class="cal_days_bef_aft">' + i + '</td>';
            week++;
            if (week == 7) {
                cal += '</tr>';
                week = 0;
            }
        }
        cal += '</tbody></table></div>';

        $('.bookme-cal-wrap').append(cal);
    }

    $('.bookme-cal-webUiPopover').webuiPopover('destroy').webuiPopover({
        closeable: true,
        dismissible: false,
        placement: 'auto',
        trigger: 'click',
        animation: 'fade',
        width: 300,
        height: 'auto',
        cache: false
    });
    drawHolidays(year);

}

function drawHolidays(year) {
    $('.bookme-cal-wrap').find('.bookme-dayoff').removeClass('bookme-dayoff').data('id', null);
    $('.bookme-cal-wrap').find('.bookme-dayoff-repeat').removeClass('bookme-dayoff-repeat');
    for (var i in days_off) {
        if (days_off.hasOwnProperty(i)) {
            $('.bookme-cal-wrap').find(getHolidaySelector(days_off[i]))
                .addClass('bookme-dayoff')
                .addClass(days_off[i].hasOwnProperty('y') ? '' : 'bookme-dayoff-repeat')
                .data('id', i).attr('data-content', '');

            var title = $('.bookme-cal-wrap').find(getHolidaySelector(days_off[i])).data('title');
            var cur_date = days_off[i].d + '-' + days_off[i].m + '-' + year;
            var checked = days_off[i].hasOwnProperty('y') ? '' : 'checked';
            var tpl = "<div><p><label><input type='checkbox' id='dayoff_" + cur_date + "' checked>&nbsp; We are not working this day.</label></p>";
            tpl += "<p><label><input type='checkbox' id='dayoff_repeat_" + cur_date + "' " + checked + ">&nbsp; Repeat every year.</label></p>";
            tpl += "<button type='button' class='btn btn-info' id='save_dayoff' data-date='" + cur_date + "'>Save</button></div>";

            $('.bookme-cal-wrap').find(getHolidaySelector(days_off[i])).webuiPopover('destroy').webuiPopover({
                closeable: true,
                dismissible: false,
                placement: 'auto',
                trigger: 'click',
                animation: 'fade',
                width: 300,
                height: 'auto',
                content: tpl,
                cache: false
            });

        }
    }
}

function getHolidaySelector(day) {
    return 'td[id^=dayoff_date_' + day.d + '-' + day.m + '-' + (day.hasOwnProperty('y') ? (day.y + ']') : ']');
}

function timeFormatter(dateTime) {
    var date = new Date("1/1/2018 " + dateTime);
    if (date.getHours() >= 12) {
        var hour = parseInt(date.getHours()) - 12;
        var amPm = "PM";
    } else {
        var hour = date.getHours();
        var amPm = "AM";
    }
    var time = hour + ":" + date.getMinutes() + " " + amPm;
    return time;
}

(function( $ ) {

    /*$('#upcominglecture').click(function(){
    	$('#upcominglecture-dlist').DataTable({            
    		paging : true,
            destroy : true
    	});
    });
    
    $('#completedlecture').click(function(){
	    $('#completedlecture-dlist').DataTable();
    });*/
    
})( jQuery );
