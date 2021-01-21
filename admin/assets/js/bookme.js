
/* app-contact */
!function (document, window, $) {
    "use strict";
    window.AppContacts = App.extend({
        handleAction: function () {
            var actionBtn = $(".site-action").actionBtn().data("actionBtn"), $selectable = $("[data-selectable]");
            $(".site-action-toggle", ".site-action").on("click", function (e) {
                var $selected = $selectable.asSelectable("getSelected");
                0 === $selected.length && ($("#slidepanel-show").trigger('click'), e.stopPropagation())
            }),
                $selectable.on("asSelectable::change", function (e, api, checked) {
                    checked ? actionBtn.show() : actionBtn.hide()
                })
        }, handleEdit: function () {
            $(document).on("click", "[data-toggle=edit]", function () {
                var $button = $(this), $panel = $button.parents(".slidePanel"), $form = $panel.find(".user-info");
                $button.toggleClass("active"), $form.toggleClass("active")
            }), $(document).on("slidePanel::afterLoad", function (e, api) {
                $.components.init("material", api.$panel)
            }), $(document).on("change", ".user-info .form-group", function (e) {
                var $input = $(this).find("input"), $span = $(this).siblings("span");
                $span.html($input.val())
            })
        }, handleListItem: function () {
            $("#addcatModelToggle").on("click", function (e) {
                $("#addcatmodal").modal("show"), e.stopPropagation()
            })
        }, run: function (next) {
            this.handleAction(), this.handleEdit(), this.handleListItem(), $("#addlabelForm").modal({show: !1}), $("#addUserForm").modal({show: !1}), next()
        }
    }), $(document).ready(function () {
        AppContacts.run()
    });

    /* selective */
    $(document).ready(function () {
        var selected_count = 0;
        var all_members_count = 5;
        //var not_all = 0;
        var members = [
                {id: "All", name: "All Staff", img: "global/portraits/allstaff.png"},
                {id: "uid_1", name: "Herman Beck", img: "global/portraits/1.jpg"},
                {id: "uid_2", name: "Mary Adams", img: "global/portraits/2.jpg"},
                {id: "uid_3", name: "Caleb Richards", img: "global/portraits/3.jpg"},
                {id: "uid_4", name: "June Lane", img: "global/portraits/4.jpg"}
            ]
            ,
            selected = [
                {id: "All", name: "All Staff", img: "global/portraits/allstaff.png"},
                {id: "uid_1", name: "Herman Beck", img: "global/portraits/1.jpg"},
                {id: "uid_2", name: "Mary Adams", img: "global/portraits/2.jpg"},
                {id: "uid_3", name: "Caleb Richards", img: "global/portraits/3.jpg"},
                {id: "uid_4", name: "June Lane", img: "global/portraits/4.jpg"}
            ];
        var a;
        $('[data-plugin="jquery-selective"]').selective({
            namespace: "addMember",
            local: members,
            selected: selected,
            buildFromHtml: !1,
            tpl: {
                optionValue: function (data) {
                    return data.id
                },
                frame: function () {
                    return '<div class="' + this.namespace + '">' + this.options.tpl.items.call(this) +
                        '<div class="' + this.namespace + '-trigger">' + this.options.tpl.triggerButton.call(this) + '<div class="' + this.namespace + '-trigger-dropdown">' + this.options.tpl.list.call(this) + "</div></div></div>"
                },
                triggerButton: function () {
                    return '<div class="'+this.namespace+'-trigger-button"><i class="md-plus"></i></div>'
                },
                list: function() {
                    return '<ul class="'+this.namespace+'-list"></ul>';
                },
                listItem: function (data) {
                    return '<li class="' + this.namespace + '-list-item" id="' + this.options.tpl.optionValue.call(this, data) + '"><img class="avatar" src="' + data.img + '">' + data.name + '</li>'
                },
                item: function (data) {
                    return '<li class="' + this.namespace + '-item" id="imgbox' + this.options.tpl.optionValue.call(this, data) + '"><img class="avatar" src="' + data.img + '">' + this.options.tpl.itemRemove.call(this) + "</li>"
                },
                itemRemove: function () {
                    return '<span class="' + this.namespace + '-remove"><i class="md-close-circle"></i></span>'
                },
                option: function (data) {
                    return '<option value="' + this.options.tpl.optionValue.call(this, data) + '">' + data.name + "</option>"
                }
            },
            onAfterSelected: function() {
                a = this;
                selected_count++;
            },
            onAfterUnselected: function() {
                selected_count--;
            }
        });

        if(all_members_count == selected_count){
            $.each(members, function(index, item) {
                if(item.id != 'All')
                    $('.addMember-item#imgbox'+item.id).remove();
            });
        }

        $('.addMember-list-item#All').on('click', function(e){
            if($(this).hasClass('addMember-selected')){
                e.stopPropagation();
                $.each(members, function(index, item) {
                    a.unselect($('.addMember-list-item#'+item.id));
                    $('#selective option[value="'+item.id+'"]').remove();
                });
                $('.addMember-item').remove();

            }else{
                $('.addMember-item').remove();
                $.each(members, function(index, item) {
                    if(!($('.addMember-list-item#'+item.id).hasClass('addMember-selected'))){
                        a.select($('.addMember-list-item#'+item.id));
                        if(item.id != 'All')
                            $('.addMember-item#imgbox' + item.id).remove();
                    }
                });
            }
        });

        $('.addMember-list-item').on('click', function(e){
            if($(this).hasClass('addMember-selected')) {
                var id = $(this).attr('id');
                if (id != 'All') {
                    e.stopPropagation();
                    a.unselect($(this));
                    $('#selective option[value="'+id+'"]').remove();
                    $('.addMember-item#imgbox' + id).remove();
                    if(all_members_count-selected_count == 1){
                        a.unselect($('.addMember-list-item#All'));
                        $('#selective option[value="All"]').remove();
                        $('.addMember-item').remove();
                        $.each(members, function(index, item) {
                            if(item.id != id && item.id != 'All')
                                a.itemAdd(item);
                        });
                    }
                }
            }else{

                if(selected_count == all_members_count-2){
                    e.stopPropagation();
                    a.select($(this));
                    $('.addMember-item').remove();
                    a.select($('.addMember-list-item#All'));
                }
            }
        });

        $('.addMember-items').on('click','#imgboxAll .addMember-remove', function(e){
            e.stopPropagation();
            $.each(members, function(index, item) {
                a.unselect($('.addMember-list-item#'+item.id));
                $('#selective option[value="'+item.id+'"]').remove();
            });
            selected_count = 0;
        });


        var defaults = $.components.getDefaults("dataTable"), options = $.extend(!0, {}, defaults, {
            dom: 'Bftrip',
            "columnDefs": [
                { "orderable": false, "targets": [ 7, 8 ] }
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('#customerTable').DataTable(options);

        options = $.extend(!0, {}, defaults, {
            dom: 'Bftrip',
            "columnDefs": [
                { "orderable": false, "targets": [ 9,10 ] }
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $('#paymentTable').DataTable(options);

        options = $.extend(!0, {}, defaults, {
            dom: 'Bftrip',
            "columnDefs": [
                { "orderable": false, "targets": [ 6,7 ] }
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $('#coupon_table').DataTable(options);

        /* tooltip-popover */
        $('.webUiPopover').webuiPopover();

        /* tooltipster */
        $('.tooltipster').tooltipster({
            theme: 'tooltipster-borderless',
            plugins: ['follower'],
            maxWidth: 300,
            delay: 100
        });
    });
}(document, window, jQuery);
