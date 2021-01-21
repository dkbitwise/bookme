<?php
$help = array();
$help['SER_VISIBILITY'] = __('Set visibility private to hide from your customer.','bookme');
$help['CAPACITY'] = __('How many customer you want in a single time period.','bookme');
$help['PADDING_TIME'] = __('Set padding time for an appointment. For example, if you want a break between two appointment for preparation of next appointment then you can use padding time. Means if your first appointment is on 9:00 - 10:00 and you set the padding time to 15 min then the next appointment will be on 10:15 - 11:15.','bookme');
$help['SER_STAFF_MEMBER'] = __('Assign employees to this service.','bookme');
$help['SER_INFO'] = __('This is the description of the service.','bookme');
$help['STAFF_VISIBILITY'] = __('To make employee invisible to your customers set the visibility to Private.','bookme');
$help['STAFF_INFO'] = __('This is the description of the employee.','bookme');
$help['STAFF_SERVICE'] = __('Assign employee to services.','bookme');
$help['BOOKING_PERIOD'] = __('Booking start and end time.','bookme');
$help['BOOKING_CUST'] = __('Add customers to the booking.','bookme');
$help['SEND_EMAIL'] = __('Send mail to the customer or not.','bookme');
$help['COUPON_CODE'] = __('Code of the coupon.','bookme');
$help['COUPON_USE_LIMIT'] = __('How many times a coupon can be used.','bookme');
$help['COUPON_DEDUCTION'] = __('If coupon discount is assigned then deduction will not work.','bookme');
$help['COUPON_SERVICE'] = __('Select services for the coupon.','bookme');
$help['APPEARANCE_BULLETS'] = __('Change frontend bullets text.','bookme');
$help['APPEARANCE_LABELS'] = __('Change frontend labels.','bookme');
$help['APPEARANCE_COLOR'] = __('Change frontend colors.','bookme');
$help['BOOKING_MSG'] = __('Change booking success message.','bookme');
$help['COMPANY_DETAILS'] = __('Company details will use in emails.','bookme');
$help['DAY_LIMIT'] = __('Set how far in the future the clients can book appointments.','bookme');
$help['GC_CLIENT_ID'] = __('The client ID obtained from the Developers Console.','bookme');
$help['GC_CLIENT_SECRET'] = __('The client secret obtained from the Developers Console.','bookme');
$help['GC_REDIRECT_URL'] = __('Enter this URL as a redirect URI in the Developers Console.','bookme');
$help['GC_2_WAY_SYNC'] = __('By default Bookme pushes new appointments and any further changes to Google Calendar. If you enable this option then Bookme will fetch events from Google Calendar and remove corresponding time slots before displaying the second step of the booking form (this may lead to a delay when users click Next at the first step).','bookme');
$help['GC_LIMIT_EVENTS'] = __('If there is a lot of events in Google Calendar sometimes this leads to a lack of memory in PHP when Bookme tries to fetch all events. You can limit the number of fetched events here. This only works when 2 way sync is enabled.','bookme');
$help['GC_EVENT_TITLE'] = __('Configure the title of Google Calendar event.','bookme');
$help['SMS_ACCOUNTSID'] = __('Get Twilio Account SID from Twilio website','bookme');
$help['SMS_AUTHTOKEN'] = __('Get Twilio Auth Token from Twilio website','bookme');
$help['SMS_PHONENO'] = __('Get Twilio Phone number from Twilio website','bookme');
$help['ADMIN_PHONENO'] = __('Admin phone number for SMS notification.','bookme');