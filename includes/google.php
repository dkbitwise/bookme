<?php

/* Include google api */
include_once plugin_dir_path(__FILE__) . '/lib/google/autoload.php';

function bookme_create_auth_url($id, $bookme_gc_client)
{
    $bookme_gc_client->setRedirectUri(admin_url('admin.php?page=bookme-staff'));
    $bookme_gc_client->addScope('https://www.googleapis.com/auth/calendar');
    $bookme_gc_client->setState(strtr(base64_encode($id), '+/=', '-_,'));
    $bookme_gc_client->setApprovalPrompt('force');
    $bookme_gc_client->setAccessType('offline');

    return $bookme_gc_client->createAuthUrl();
}

function bookme_get_calendar_list($service) {
    $calendarList = $service->calendarList->listCalendarList();
    $result = array();
    while ( true ) {
        /** @var \Google_Service_Calendar_CalendarListEntry $calendarListEntry */
        foreach ( $calendarList->getItems() as $calendarListEntry ) {
            if ( in_array( $calendarListEntry->getAccessRole(), array( 'writer', 'owner' ) ) ) {
                $result[ $calendarListEntry->getId() ] = array(
                    'primary' => $calendarListEntry->getPrimary(),
                    'summary' => $calendarListEntry->getSummary(),
                );
            }
        }
        $pageToken = $calendarList->getNextPageToken();
        if ( $pageToken ) {
            $optParams    = array( 'pageToken' => $pageToken );
            $calendarList = $service->calendarList->listCalendarList( $optParams );
        } else {
            break;
        }
    }
    return $result;
}

function bookme_get_event_data(array $event_data){
    $gc_event = new Google_Service_Calendar_Event();

    $wptimezone = null;

    if ( $timezone = get_option( 'timezone_string' ) ) {
        // If site timezone string exists, return it.
        $wptimezone = $timezone;
    } else {
        // Otherwise return offset.
        $gmt_offset = get_option( 'gmt_offset' );
        $wptimezone = sprintf( '%s%02d:%02d', $gmt_offset >= 0 ? '+' : '-', abs( $gmt_offset ), abs( $gmt_offset ) * 60 % 60 );
    }
    $startTime = new DateTime($event_data['start'], new DateTimeZone($wptimezone));
    $start_datetime = new Google_Service_Calendar_EventDateTime();
    $start_datetime->setDateTime(
        $startTime->format('Y-m-d\TH:i:sP')
    );

    $endTime = new DateTime($event_data['end'], new DateTimeZone($wptimezone));
    $end_datetime = new Google_Service_Calendar_EventDateTime();
    $end_datetime->setDateTime(
        $endTime->format('Y-m-d\TH:i:sP')
    );

    $description  = __( 'Service', 'bookme' ) . ': ' . $event_data['service'] . PHP_EOL . PHP_EOL;
    foreach($event_data['name'] as $key => $value){
        $description .= sprintf(
            "%s: %s\n%s: %s\n%s: %s\n",
            __( 'Name',  'bookme' ), $event_data['name'][$key],
            __( 'Email', 'bookme' ), $event_data['email'][$key],
            __( 'Phone', 'bookme' ), $event_data['phone'][$key]
        );
        $description .= PHP_EOL;
    }

    $title = strtr( bookme_get_settings('bookme_gc_event_title','{service_name}'), array(
        '{service_name}' => $event_data['service'],
        '{category_name}'   => $event_data['category'],
        '{employee_name}' => $event_data['employee'],
        '{customer_name}'   => implode( ', ', $event_data['name'] )
    ) );

    $gc_event->setStart( $start_datetime );
    $gc_event->setEnd( $end_datetime );
    $gc_event->setSummary( $title );
    $gc_event->setDescription( $description );

    $extended_property = new Google_Service_Calendar_EventExtendedProperties();
    $extended_property->setPrivate( array(
        'customer_id'      => json_encode($event_data['customer_id']),
        'service_id'     => $event_data['service_id'],
        'booking_id' => $event_data['booking_id'],
    ) );
    $gc_event->setExtendedProperties( $extended_property );

    return $gc_event;
}


function bookme_get_calendar_events($bookme_gc_client, $dates){
    $gc_events = array();

    $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
    $google_calendar_id = 'primary';
    $gc_calendar = $bookme_gc_service->calendarList->get($google_calendar_id);
    $gc_access = $gc_calendar->getAccessRole();
    $limit_events = bookme_get_settings('bookme_gc_limit_events', 50);

    $wptimezone = null;

    if ($timezone = get_option('timezone_string')) {
        // If site timezone string exists, return it.
        $wptimezone = $timezone;
    } else {
        // Otherwise return offset.
        $gmt_offset = get_option('gmt_offset');
        $wptimezone = sprintf('%s%02d:%02d', $gmt_offset >= 0 ? '+' : '-', abs($gmt_offset), abs($gmt_offset) * 60 % 60);
    }
    $startTime = new DateTime($dates, new DateTimeZone($wptimezone));
    $timeMin = $startTime->format('Y-m-d\TH:i:sP');
    $timeMax = $startTime->add(new DateInterval('P1D'))->format('Y-m-d\TH:i:sP');

    $events = $bookme_gc_service->events->listEvents($google_calendar_id, array(
        'singleEvents' => true,
        'orderBy' => 'startTime',
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'maxResults' => $limit_events,
    ));

    while (true) {
        foreach ($events->getItems() as $event) {
            if ($event->getStatus() !== 'cancelled' && ($event->getTransparency() === null || $event->getTransparency() === 'opaque')) {
                if ($gc_access != 'freeBusyReader') {
                    $ext_properties = $event->getExtendedProperties();
                    if ($ext_properties !== null) {
                        $private = $ext_properties->private;
                        if ($private !== null && array_key_exists('service_id', $private)) {
                            continue;
                        }
                    }
                }

                $event_start = $event->getStart();
                $event_end = $event->getEnd();

                if ($event_start->dateTime == null) {
                    // All day event.
                    $event_start_date = new DateTime($event_start->date, new DateTimeZone($gc_calendar->getTimeZone()));
                    $event_end_date = new DateTime($event_end->date, new DateTimeZone($gc_calendar->getTimeZone()));
                } else {
                    // Regular event.
                    $event_start_date = new DateTime($event_start->dateTime);
                    $event_end_date = new DateTime($event_end->dateTime);
                }

                // Convert to WP time zone.
                $event_start_date = date_timestamp_set(date_create($wptimezone), $event_start_date->getTimestamp());
                $event_end_date = date_timestamp_set(date_create($wptimezone), $event_end_date->getTimestamp());

                $gc_events[] = array(
                    'start_date' => $event_start_date->format('Y-m-d H:i:s'),
                    'end_date' => $event_end_date->format('Y-m-d H:i:s')
                );
            }
        }

        if (!$limit_events && $events->getNextPageToken()) {
            $events = $bookme_gc_service->events->listEvents($google_calendar_id, array(
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'pageToken' => $events->getNextPageToken()
            ));
        } else {
            break;
        }
    }
    return $gc_events;
}