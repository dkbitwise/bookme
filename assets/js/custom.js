jQuery(document).ready(function ($) {
    var calender_date = [];
    var tooltip = [];
    var auto_fill = false;

    $(document).on('click', '.notavailable', function () {
        swal("Class is full!!", "This class is full. Please choose other available time slots");
    });

    if (status == 'init') {
        cal_sdate = moment(cal_sdate, "YYYY-MM-DD");
        var sdate = cal_sdate.format("YYYY-MM-DD");
        var cal_edate = cal_sdate.add('days', day_limit);
        var edate = cal_edate.format("YYYY-MM-DD");
        var data = {
            'action': 'bookme_user_action',
            'call': 'get_step_1',
            'get_data': bookme_var,
            'auto_fill': auto_fill
        };
        $.post(bookme_object.ajax_url, data, function (response) {
            if (response != 0) {
                $('#bookme_container').html(response);
                if ($('#bookme_employee').is('input:hidden')) {
                    bookme_get_calender();
                }
                $('.cal1').bookme({
                    constraints: {
                        startDate: sdate,
                        endDate: edate
                    },
                    multiDayEvents: {
                        startDate: 'startDate',
                        endDate: 'endDate'
                    },
                    showAdjacentMonths: true,
                    adjacentDaysChangeMonth: false
                });

                if (auto_fill == false) {
                    var cat = $('#bookme_category').val();
                    var ser = $('#bookme_service').val();
                    if (cat != '' && ser != '') {
                        bookme_get_calender();
                    }
                    auto_fill = true;
                }

            }
        });
    } else {
        if (status == 'success') {
            var heading = bookme_object.appointment_booked;
            var message = bookme_object.appointment_booked_msg;
        } else {
            var heading = bookme_object.appointment_not_booked;
            var message = status;
        }

        var data = {
            'action': 'bookme_user_action',
            'call': 'get_step_5',
            'message': message,
            'heading': heading
        };
        jQuery.post(bookme_object.ajax_url, data, function (result) {
            jQuery("#bookme_container").html(result);
        });
    }

    $(document).on('change', '#bookme_category', function () {
        var cat_id = $(this).val();
        $(".bookme-tooltiptext").removeClass('tipshow');
        $(".bookme-tooltiptext").html('');
        $(".scriptdiv").html('');
        $(".cat_error").hide();
        $("#bookme_category").removeClass("borederColor");
        if (cat_id) {
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_services_by_cat_id',
                'cat_id': cat_id
            };
            $('#bookme_service').siblings('legend').find('span.select-loader').fadeIn();
            $.post(bookme_object.ajax_url, data, function (response) {
                if (response != 0) {
                    $('#bookme_service').html(response);
                }
                $('#bookme_service').siblings('legend').find('span.select-loader').fadeOut();
            });
        }
    }).on('change', '#bookme_service', function () {
        var ser_id = $(this).val();
        $(".bookme-tooltiptext").removeClass('tipshow');
        $(".bookme-tooltiptext").html('');
        $("#bookme_service").removeClass("borederColor");
        $(".ser_error").hide();
        if (ser_id) {
            $("#bookme_employee").removeClass("borederColor");
            $(".emp_error").hide();
            bookme_get_calender();
        }
    }).on('change', '#bookme_employee', function () {


    }).on("click", ".bookme-previous-button, .bookme-next-button", function () {
        var month_withoutdate = jQuery('.month').data('month');
        var cat = $('#bookme_category').val();
        var ser = $('#bookme_service').val();
        var emp = $('#bookme_employee').val();
        var per = ($('#bookme_person').val() != undefined) ? $('#bookme_person').val() : 1;
        if (cat != "" && ser != "" && emp != "" && per != "" && month_withoutdate != "") {
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_the_calender',
                'cat': cat,
                'ser': ser,
                'emp': emp,
                'per': per,
                'btnpre': 'btnpre',
                'm_w_date': month_withoutdate
            };
            jQuery('.column_right_grid').hide();
            jQuery('.column_right_grid_loading').show();
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                if (response != 0) {
                    response = jQuery.parseJSON(response);
                    calender_date = response.cdate;
                    tooltip = response.tooltip;
                    $('.bookme').find('.day').each(function () {
                        if (!$(this).hasClass("past") && !$(this).hasClass("adjacent-month")) {
                            var caldate = $(this).find(".day-contents").data('date');
                            var indx = calender_date.indexOf(caldate);
                            var tipval = tooltip[indx];
                            if (indx != -1) {
                                if (tipval == bookme_object.not_available || tipval == bookme_object.zero_available || tipval == bookme_object.today_holiday) {
                                    $(this).addClass('booked');
                                } else {
                                    $(this).removeClass('booked');
                                }
                            }
                        }
                    });
                }
                $('.column_right_grid').show();
                $('.column_right_grid_loading').hide();
            });
        }
    }).on('click', '.day-contents', function () {
        jQuery(".day").removeClass('today');
        jQuery(".calendar-day-" + jQuery(this).attr("data-date")).addClass('today');
        jQuery("#date").empty();
        jQuery("#date").val(jQuery(this).attr("data-date"));
        jQuery(".date_error").hide();

    }).on("click", "#bookme_step1", function () {
        var cat = $('#bookme_category').val();
        var ser = $('#bookme_service').val();
        var emp = $('#bookme_employee').val();
        var emp = $('#bookme_employee').val();
        var date = jQuery("#date").val();

        if (jQuery("#bookme_student").is("select")) {
            var stu = $('#bookme_student :selected').val();
        } else if (jQuery("#bookme_student").is("input")) {
            var stu = $('#bookme_student').val();
        }

        var per = jQuery("#bookme_person").val();

        if (cat == "") {
            jQuery(".cat_error").show();
            jQuery("#bookme_category").addClass("borederColor");
            jQuery("#bookme_category").focus();
            return false;
        } else {

            jQuery(".cat_error").hide();
        }
        if (ser == "") {
            jQuery(".ser_error").show();
            jQuery("#bookme_service").addClass("borederColor");
            jQuery("#bookme_service").focus();
            return false;
        } else {
            jQuery(".ser_error").hide();
        }
        if (emp == "") {
            jQuery(".emp_error").show();
            jQuery("#bookme_employee").addClass("borederColor");
            jQuery("#bookme_employee").focus();
            return false;
        } else {
            jQuery(".emp_error").hide();
        }
        if (date == "") {
            jQuery(".date_error").show();
            return false;
        }


        if (per != '') {
            per = (per != undefined) ? per : 1;
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_2',
                'cat_a': cat,
                'ser_a': ser,
                'emp_a': emp,
                'date_a': date,
                'person': per,
                'student': stu
            };
            if (cat != "" && ser != "" && emp != "" && date != "") {
                var loader = jQuery(this);
                loader.addClass('bookme-loader').prop('disabled', true);
                jQuery.post(bookme_object.ajax_url, data, function (response) {
                    jQuery("#formDiv").hide();
                    jQuery("#showStep").show();
                    jQuery("#showStep").html(response);
                    loader.removeClass('bookme-loader').prop('disabled', false);
                    // Initialize scrollers.
                    $('#time_slot_scroll').TrackpadScrollEmulator();
                    scrollTo($("#bookme_container"));
                });
            }

        }

    }).on("click", ".bookme_step2", function () {
        if ($('table.bookme_cart_table').length) {
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_3',
                'cart': 1
            };
        } else {
            var id = $(this).data('key');
            var time1 = jQuery(".appoints" + id).val();
            var time2 = jQuery(".appointe" + id).val();
            var token = jQuery("input[name=access_token]").val();
            var faculty = jQuery(".faculty_" + id).val();
            var serviceid = jQuery("#bookme_service").val();
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_3',
                'access_token': token,
                'time1_a': time1,
                'time2_a': time2,

                'faculty': faculty,
                'serviceid': serviceid
            };
        }

        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            jQuery("#formDiv").hide();
            jQuery("#showStep").html(response);
            $("#bookme_container #phone").intlTelInput({
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
            scrollTo($("#bookme_container"));
        });

    }).on("click", "#bookme_get_cart", function () {
        var data = {
            'action': 'bookme_user_action',
            'call': 'get_step_cart'
        };
        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            jQuery("#formDiv").hide();
            jQuery("#showStep").html(response).show();
            loader.removeClass('bookme-loader').prop('disabled', false);
            scrollTo($("#bookme_container"));
        });

    }).on("click", "#bookme_delete_cart", function () {
        var key = jQuery(this).data('key');
        var data = {
            'action': 'bookme_user_action',
            'call': 'delete_cart',
            'key': key
        };
        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-loader').prop('disabled', false);
            if (response != 0) {
                jQuery('#cart_row_' + key).remove();
                jQuery('#bookme_cart_total').html(response);
            }
        });

    }).on("click", "#bookme_book_more", function () {
        var data = {
            'action': 'bookme_user_action',
            'call': 'get_step_1',
            'cart': '1'
        };
        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        $.post(bookme_object.ajax_url, data, function (response) {
            loader.removeClass('bookme-loader').prop('disabled', false);
            if (response != 0) {
                $('#bookme_container').html(response);
                if ($('#bookme_employee').is('input:hidden')) {
                    bookme_get_calender();
                }
                $('.cal1').bookme({
                    constraints: {
                        startDate: sdate,
                        endDate: edate
                    },
                    multiDayEvents: {
                        startDate: 'startDate',
                        endDate: 'endDate'
                    },
                    showAdjacentMonths: true,
                    adjacentDaysChangeMonth: false
                });
                scrollTo($("#bookme_container"));
            }
        });

    }).on("click", "#bookme_step3", function () {


        $('.bookme-error').hide();

        var name = jQuery("#pname").val();

        var mail = jQuery("#bookme_container #email").val();
        try {
            var phone = jQuery("#phone").intlTelInput("getNumber");
        } catch (e) {
            var phone = jQuery("#phone").val();
        }
        var note = jQuery("#notes").val();
        var payment = $('input[name=payment]:checked').val();
        var cart = jQuery("input[name=bookme_cart]").val();
        if (cart == '1') {
            var token = 1;
        } else {
            var token = jQuery("input[name=access_token]").val();
        }
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var error = 0;


        if (name == "") {
            jQuery(".error1").show();
            jQuery("#pname").addClass("borederColor");
            jQuery("#pname").focus();
            error = 1;
            return false;
        }

        if (mail == "") {
            jQuery(".error2").show();
            jQuery("#email").addClass("borederColor");
            jQuery("#email").focus();
            error = 1;
            return false;
        }

        if (!regex.test(mail)) {
            jQuery(".error2").html(bookme_object.invalid_email).show();
            jQuery("#email").addClass("borederColor");
            jQuery("#email").focus();
            error = 1;
            return false;
        }


        var text_a = [];

        var area_a = [];

        var content = [];
        jQuery('#bookme_container .content').each(function () {
            var field = {};
            field.name = $(this).data('name');
            field.value = $(this).val().trim();
            if ($(this).data('req') && field.value == '') {
                error = 1;
                $(this).siblings('.bookme-error').show().focus();
                return false;
            }
            content.push(field);
        });

        var check_a = [];
        jQuery('.bookme-check').each(function () {
            var field = {};
            field.name = $(this).data('name');
            field.value = jQuery('[data-name="' + field.name + '"]:checked').map(function () {
                return $(this).val().trim();
            }).get();
            if ($(this).data('req') && field.value.length === 0) {
                error = 1;
                $(this).siblings('.bookme-error').show().focus();
                return false;
            }
            check_a.push(field);
        });


        var radio_a = [];
        jQuery('.bookme-radio:checked').each(function () {
            var field = {};
            field.name = $(this).data('name');
            field.value = $(this).val().trim();
            if ($(this).data('req') && field.value == '') {
                error = 1;
                $(this).siblings('.bookme-error').show().focus();
                return false;
            }
            radio_a.push(field);
        });

        var select_a = [];
        jQuery('.bookme-select').each(function () {
            var field = {};
            field.name = $(this).data('name');
            field.value = $(this).val().trim();
            if ($(this).data('req') && field.value == '') {
                error = 1;
                $(this).siblings('.bookme-error').show().focus();
                return false;
            }
            select_a.push(field);
        });

        var data = {
            'action': 'bookme_user_action',
            'call': 'save_session_data',
            'access_token': token,
            'name_a': name,
            'mail_a': mail,

            'note_a': note,
            'text_a': JSON.stringify(text_a),
            'area_a': JSON.stringify(area_a),
            'content': JSON.stringify(content),
            'check_a': JSON.stringify(check_a),
            'radio_a': JSON.stringify(radio_a),
            'selects': JSON.stringify(select_a)

        };
        if (name != "" && mail != "" && error != 1) {
            var loader = jQuery(this);
            loader.addClass('bookme-loader').prop('disabled', true);
            jQuery.post(bookme_object.ajax_url, data, function (response) {


                if (response == 1) {
                    if (payment == 'locally' || payment == undefined) {
                        var data = {
                            'action': 'bookme_user_action',
                            'call': 'book_customer',
                            'access_token': token,
                            'payment': payment
                        };
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            jQuery("#formDiv").hide();
                            if (response == 1) {
                                var mms = jQuery(".hidmsg").val();
                                if (mms == '') {
                                    var message = bookme_object.appointment_booked_msg;
                                } else {
                                    var message = mms;
                                }
                                var data = {
                                    'action': 'bookme_user_action',
                                    'call': 'get_step_5',
                                    'message': message
                                };
                                jQuery.post(bookme_object.ajax_url, data, function (result) {
                                    jQuery("#showStep").html(result);
                                    scrollTo($("#bookme_container"));
                                });
                            }
                            if (response == 0) {
                                var message = bookme_object.appointment_book_problem;
                                jQuery(".bookme-js-error").html(message).show().focus();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            }
                            if (response == 2) {
                                var message = bookme_object.appointment_exist;
                                jQuery(".bookme-js-error").html(message).show().focus();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            }
                            if (response == 3) {
                                var message = bookme_object.appointment_full;
                                jQuery(".bookme-js-error").html(message).show().focus();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            }
                            if (response == 4) {
                                var message = bookme_object.appointment_holiday;
                                jQuery(".bookme-js-error").html(message).show().focus();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            }

                        });
                    } else if (payment == 'PayPal') {
                        $('#paypal_form').submit();

                    } else if (payment == 'Stripe') {
                        var stripe_card_number = jQuery("#card_number_stripe").val(),
                            stripe_card_cvc = jQuery("#card_cvc_stripe").val(),
                            stripe_card_exp_month = jQuery("#card_date_stripe_month").val(),
                            stripe_card_exp_year = jQuery("#card_date_stripe_year").val();
                        var data = {
                            'action': 'bookme_user_action',
                            'call': 'stripe_payment',
                            'access_token': token,
                            'card[number]': stripe_card_number,
                            'card[cvc]': stripe_card_cvc,
                            'card[exp_month]': stripe_card_exp_month,
                            'card[exp_year]': stripe_card_exp_year
                        };
                        jQuery.post(bookme_object.ajax_url, data, function (response) {
                            if (response == 1) {
                                var mms = jQuery(".hidmsg").val();

                                if (mms == '') {
                                    var message = bookme_object.appointment_booked_msg;
                                } else {
                                    var message = mms;
                                }
                                var data = {
                                    'action': 'bookme_user_action',
                                    'call': 'get_step_5',
                                    'message': message
                                };
                                jQuery.post(bookme_object.ajax_url, data, function (result) {
                                    jQuery("#showStep").html(result);
                                    scrollTo($("#bookme_container"));
                                });
                            } else if (response == 0) {
                                var message = bookme_object.appointment_book_problem;
                                jQuery(".bookme-js-error").html(message).show();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            } else if (response == 2) {
                                var message = bookme_object.appointment_exist;
                                jQuery(".bookme-js-error").html(message).show();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            } else if (response == 3) {
                                var message = bookme_object.appointment_full;
                                jQuery(".bookme-js-error").html(message).show();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            } else if (response == 4) {
                                var message = bookme_object.appointment_holiday;
                                jQuery(".bookme-js-error").html(message).show();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            } else {
                                jQuery(".bookme-js-error").html(response).show();
                                loader.removeClass('bookme-loader').prop('disabled', false);
                            }
                        });
                    }
                } else if (response == 2) {
                    var data = {
                        'action': 'bookme_user_action',
                        'call': 'woo_add_to_cart',
                        'access_token': token
                    };
                    jQuery.post(bookme_object.ajax_url, data, function (response) {
                        if (response == 0) {
                            var message = bookme_object.appointment_book_problem;
                            jQuery(".bookme-js-error").html(message).show();
                            loader.removeClass('bookme-loader').prop('disabled', false);
                        } else if (response == 2) {
                            var message = bookme_object.appointment_exist;
                            jQuery(".bookme-js-error").html(message).show();
                            loader.removeClass('bookme-loader').prop('disabled', false);
                        } else if (response == 3) {
                            var message = bookme_object.appointment_full;
                            jQuery(".bookme-js-error").html(message).show();
                            loader.removeClass('bookme-loader').prop('disabled', false);
                        } else if (response == 4) {
                            var message = bookme_object.appointment_holiday;
                            jQuery(".bookme-js-error").html(message).show();
                            loader.removeClass('bookme-loader').prop('disabled', false);
                        } else if (response == 5) {
                            var message = bookme_object.appointment_in_cart;
                            jQuery(".bookme-js-error").html(message).show();
                            loader.removeClass('bookme-loader').prop('disabled', false);
                        } else {
                            window.location = response;
                        }
                    });
                }
            });
        }

    }).on("click", "#check_coupan_code", function () {
        var applied_coupan = jQuery('#apply_coupan_code').val();
        var cart = jQuery("input[name=bookme_cart]").val();
        if (cart == '1') {
            var data = {
                'action': 'bookme_user_action',
                'call': 'check_coupan',
                'cart': cart,
                'applied_coupan': applied_coupan
            };
        } else {
            var token = jQuery("input[name=access_token]").val();
            var data = {
                'action': 'bookme_user_action',
                'call': 'check_coupan',
                'access_token': token,
                'applied_coupan': applied_coupan
            };
        }
        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        jQuery.post(bookme_object.ajax_url, data, function (result) {
            loader.removeClass('bookme-loader').prop('disabled', false);
            var trimStr = jQuery.trim(result);
            if (trimStr == 1) {
                jQuery('.mess').html(bookme_object.wrong_coupon).css('color', '#ef5350').fadeIn();
            } else if (cart == '1') {
                jQuery('.coupan_codes').val(applied_coupan);
                jQuery('#bookme_coupon_apply_btn').remove();
                jQuery('#bookme_coupon_box').html('<td>' + bookme_object.coupon_applied + '</td><td>' + applied_coupan + '</td>');
                jQuery('#bookme_payment_total #bookme_payment_price').html(trimStr);
                jQuery('.appbtn').fadeOut();
            } else {
                var prarr = trimStr.split("_");
                jQuery('.coupan_codes').val(applied_coupan);
                jQuery('#bookme_coupon_apply_btn').remove();
                jQuery('#bookme_coupon_box').html('<td>' + bookme_object.discount + '</td><td>' + prarr[2] + '</td>');
                jQuery('#bookme_payment_total #bookme_payment_price').html(prarr[0]);
                jQuery('#desc_price').val(prarr[1]);
                jQuery('.appbtn').fadeOut();
            }

        });

    }).on("keyup", "#apply_coupan_code", function () {
        jQuery('.mess').html(' ').fadeOut();
    }).on("click", ".bookme_backbtn", function (e) {
        e.preventDefault();
        id = $(this).data("key");
        var loader = jQuery(this);
        loader.addClass('bookme-loader').prop('disabled', true);
        if (id == 2) {
            jQuery("#formDiv").show();
            jQuery("#showStep").hide();
            loader.removeClass('bookme-loader').prop('disabled', false);
            scrollTo($("#bookme_container"));
        }
        if (id == 3) {
            var access_token = jQuery("input[name=access_token]").val();
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_2',
                'access_token': access_token
            };
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                jQuery("#formDiv").hide();
                loader.removeClass('bookme-loader').prop('disabled', false);
                jQuery("#showStep").html(response);
                // Initialize scrollers.
                $('#time_slot_scroll').TrackpadScrollEmulator();
                scrollTo($("#bookme_container"));
            });
        }
        if (id == 'cart') {
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_cart'
            };
            var loader = jQuery(this);
            loader.addClass('bookme-loader').prop('disabled', true);
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                jQuery("#formDiv").hide();
                jQuery("#showStep").html(response).show();
                loader.removeClass('bookme-loader').prop('disabled', false);
                scrollTo($("#bookme_container"));
            });
        }
        if (id == 4) {
            var category_s = jQuery("input[name=category]").val();
            var service_s = jQuery("input[name=service]").val();
            var employee_s = jQuery("input[name=employee]").val();
            var person = jQuery("input[name=person]").val();
            var date_s = jQuery("input[name=date]").val();
            var appoint1_s = jQuery("input[name=appoint1]").val();
            var appoint2_s = jQuery("input[name=appoint2]").val();
            var pname_s = jQuery("input[name=pname]").val();
            var email_s = jQuery("input[name=email]").val();
            var phone_s = jQuery("input[name=phone]").val();
            var note_s = jQuery("input[name=notes]").val();
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_step_3',
                'cat_a': category_s,
                'ser_a': service_s,
                'emp_a': employee_s,
                'person': person,
                'date_a': date_s,
                'time1_a': appoint1_s,
                'time2_a': appoint2_s,
                'pname_s': pname_s,
                'email_s': email_s,
                'phone_s': phone_s,
                'note_s': note_s
            };
            jQuery.post(bookme_object.ajax_url, data, function (response) {
                jQuery("#formDiv").hide();
                loader.removeClass('bookme-loader').prop('disabled', false);
                jQuery("#showStep").html(response);
                scrollTo($("#bookme_container"));
            });
        }
    }).on('change', '.payment_method', function () {
        var t = $("div.payment_box." + $(this).attr("ID"));
        $(this).is(":checked") && !t.is(":visible") && ($("div.payment_box").filter(":visible").slideUp(250), $(this).is(":checked") && $("div.payment_box." + $(this).attr("ID")).slideDown(250))

    }).on('click', '#bookme_add_coupon', function (e) {
        e.preventDefault();
        if ($('#bookme_coupon_apply_btn').is(":visible")) {
            $('#bookme_coupon_apply_div').slideUp(250).show();
            setTimeout(
                function () {
                    $('#bookme_coupon_apply_btn').hide()
                }, 250);
        } else {
            $('#bookme_coupon_apply_btn').show();
            $('#bookme_coupon_apply_div').hide().slideDown(250);
        }
    });

    $("#bookme_container").on("mouseenter", ".day-contents", function () {
        var tipcat = $("#bookme_category").val();
        var tipser = $("#bookme_service").val();
        var tipemp = $("#bookme_employee").val();
        var tipper = $("#bookme_person").val();
        var caldate = $(this).attr("data-date");
        if (tipcat != "" && tipser != "" && tipemp != "" && caldate != "") {

            var cname = $(this).parent("td");
            if (!cname.hasClass("past") && !cname.hasClass("adjacent-month")) {
                var indx = calender_date.indexOf(caldate);
                var tipval = tooltip[indx];
                var v = jQuery(this).siblings("span");
                if (indx != -1) {
                    v.addClass("tipshow");
                    v.removeClass("tiphide");
                    v.text(tipval);
                } else {
                    v.addClass("tiphide");
                    v.removeClass("tipshow");
                }
            }
        }
    }).on("mouseout", ".day-contents", function () {
        $(this).removeClass("calnotfound");
        $(this).removeClass("calfound");
    }).on("click", ".day-contents", function () {

        var tipcat = $("#bookme_category").val();
        var tipser = $("#bookme_service").val();
        var tipemp = $("#bookme_employee").val();
        var tipper = $("#bookme_person").val();
        var caldatec = $(this).attr("data-date");
        if (tipcat != "" && tipser != "" && tipemp != "" && caldatec != "") {
            var cname = $(this).parent("td");
            if (!cname.hasClass("past") && !cname.hasClass("adjacent-month")) {
                var indxx = calender_date.indexOf(caldatec);
                if (indxx != -1) {
                    var tipvall = tooltip[indxx];
                    if (tipvall == bookme_object.not_available || tipvall == bookme_object.zero_available || tipvall == bookme_object.today_holiday) {
                        jQuery("#show-me-next").hide();
                    } else {
                        jQuery("#show-me-next").show();
                    }
                } else {
                    jQuery("#show-me-next").hide();
                }
            }
        }
    });

    function bookme_get_calender() {
        var month_withoutdate = jQuery('.month').data('month');
        var cat = $('#bookme_category').val();
        var ser = $('#bookme_service').val();
        var emp = '0';
        var per = ($('#bookme_person').val() != undefined) ? $('#bookme_person').val() : 1;
        if (emp != "") {
            var data = {
                'action': 'bookme_user_action',
                'call': 'get_the_calender',
                'cat': cat,
                'ser': ser,
                'emp': emp,
                'per': per,
                'm_w_date': month_withoutdate
            };
            $('.column_right_grid').hide();
            $('.column_right_grid_loading').show();
            calender_date = [];
            tooltip = [];
            $.post(bookme_object.ajax_url, data, function (response) {
                if (response != 0) {
                    response = jQuery.parseJSON(response);
                    calender_date = response.cdate;
                    tooltip = response.tooltip;
                    $('.bookme').find('.day').each(function () {
                        if (!$(this).hasClass("past") && !$(this).hasClass("adjacent-month")) {
                            var caldate = $(this).find(".day-contents").data('date');
                            var indx = calender_date.indexOf(caldate);
                            var tipval = tooltip[indx];
                            if (indx != -1) {
                                if (tipval == bookme_object.not_available || tipval == bookme_object.zero_available || tipval == bookme_object.today_holiday) {
                                    $(this).addClass('booked');
                                } else {
                                    $(this).removeClass('booked');
                                }
                            }
                        }
                    });
                }
                $('.column_right_grid').show();
                $('.column_right_grid_loading').hide();
            });
        }
    }

    function scrollTo($elem) {
        var elemTop = $elem.offset().top;
        var scrollTop = $(window).scrollTop();
        if (elemTop < $(window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
            $('html,body').animate({scrollTop: (elemTop - 24)}, 500);
        }
    }
});
