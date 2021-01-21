/* data table */

(function($){
    var startDate, endDate;
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex, rowObj, counter) {
            var min = startDate;
            var max = endDate;
            var date = moment($(settings.aoData[dataIndex].nTr).data('date'));
            if (min == null && max == null) {
                return true;
            }
            if (min == null && date <= max) {
                return true;
            }
            if (max == null && date >= min) {
                return true;
            }
            if (date <= max && date >= min) {
                return true;
            }
            return false;
        }
    );

    var defaults = $.components.getDefaults("dataTable"), options = $.extend(!0, {}, defaults, {
        responsive: true,
        columnDefs: [
            {orderable: false, targets: [-1, -2]}
        ],
        "dom": "<'row'<'col-sm-1'l><'col-sm-3 toolbar' ><'col-sm-4'B><'col-sm-4'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [1, 'desc']
    });
    var datatable = $("#bookingTable").DataTable(options);

    $("#bookingTable_wrapper .toolbar").html('<div id="daterange" class="bookme-daterange"> <i class="fa fa-calendar"></i>&nbsp; <span></span> <b class="caret"></b></div>');


    function cb(start, end) {
        $('#daterange span').html(start.locale(bookme_object.locale).format('MMMM') + ' ' + start.locale('en-US').format('D, YYYY') + ' - ' + end.locale(bookme_object.locale).format('MMMM') + ' ' + end.locale('en-US').format('D, YYYY'));
        startDate = start;
        endDate = end;
        datatable.draw();
    }

    var picker_ranges = {};
    picker_ranges[bookme_object.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[bookme_object.today] = [moment(), moment()];
    picker_ranges[bookme_object.tomorrow] = [moment().add(1, 'days'), moment().add(1, 'days')];
    picker_ranges[bookme_object.last_7_days] = [moment().subtract(7, 'days'), moment()];
    picker_ranges[bookme_object.last_30_days] = [moment().subtract(30, 'days'), moment()];
    picker_ranges[bookme_object.this_month] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[bookme_object.next_month] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

    $('#daterange').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        ranges: picker_ranges,
        locale: {
            applyLabel: bookme_object.apply,
            cancelLabel: bookme_object.cancel,
            fromLabel: bookme_object.from,
            toLabel: bookme_object.to,
            customRangeLabel: bookme_object.custom_range,
            daysOfWeek: Array.apply(0, Array(7)).map(function (_, i) {
                return moment().weekday(i).locale(bookme_object.locale).format('dd')
            }),
            monthNames: Array.apply(0, Array(12)).map(function (_, i) {
                return moment().locale(bookme_object.locale).month(i).format('MMMM')
            }),
        }
    }, cb);

    cb(moment().startOf('month'), moment().endOf('month'));


})(jQuery);