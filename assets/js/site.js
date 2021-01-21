// call this from the developer console and you can control both instances
var $ = jQuery.noConflict();
window.jQuery = $;

var calendars = {};

$(document).ready( function($) {

  // assuming you've got the appropriate language files,
  // bookme will respect whatever moment's language is set to.
  // moment.lang('ru');

  // here's some magic to make sure the dates are happening this month.
  var thisMonth = moment().format('YYYY-MM');

  var eventArray = [
    { startDate: thisMonth + '-10', endDate: thisMonth + '-14', title: 'Multi-Day Event' },
    { startDate: thisMonth + '-21', endDate: thisMonth + '-23', title: 'Another Multi-Day Event' }
  ];

  // the order of the click handlers is predictable.
  // direct click action callbacks come first: click, nextMonth, previousMonth, nextYear, previousYear, or today.
  // then onMonthChange (if the month changed).
  // finally onYearChange (if the year changed).


  calendars.bookme1 = $('.cal1').bookme({
    events: eventArray,
    /*constraints: {
       startDate: start_date,
       endDate: end_date
    },*/
    clickEvents: {
      click: function(target) {
        console.log(target);
        if($(target.element).hasClass('inbookme-active')) {
          console.log('not a valid datepicker date.');
        } else {
          console.log('VALID datepicker date.');
        }
      },
      nextMonth: function() {
        console.log('next month.');
      },
      previousMonth: function() {
        console.log('previous month.');
      },
      onMonthChange: function() {
        console.log('month changed.');
      },
      nextYear: function() {
        console.log('next year.');
      },
      previousYear: function() {
        console.log('previous year.');
      },
      onYearChange: function() {
        console.log('year changed.');
      }
    },
    multiDayEvents: {
      startDate: 'startDate',
      endDate: 'endDate'
    },
    showAdjacentMonths: true,
    adjacentDaysChangeMonth: false
  });

  // calendars.bookme2 = $('.cal2').bookme({
  //   template: $('#template-calendar').html(),
  //   events: eventArray,
  //   startWithMonth: moment().add('month', 1),
  //   clickEvents: {
  //     click: function(target) {
  //       console.log(target);
  //     }
  //   }
  // });

  // bind both bookmes to the left and right arrow keys
  $(document).keydown( function(e) {
    if(e.keyCode == 37) {
      // left arrow
      calendars.bookme1.back();
      calendars.bookme2.back();
    }
    if(e.keyCode == 39) {
      // right arrow
      calendars.bookme1.forward();
      calendars.bookme2.forward();
    }
  });

});