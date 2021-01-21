<?php
function add_faculty_role_and_capability() {
    /* execute only one time */
    //delete_option('faculty_role_and_capability');

    $user = wp_get_current_user();
    if ( isset( $user->roles ) && is_array( $user->roles ) )
    {
    	if ( in_array( 'lp_teacher', $user->roles ) ) {
        	//remove_menu_page( 'woocommerce' ); // WOOCOMMERCE
    	}
    }

    $file=get_option('bookme__file');
    if( $file!='a4l6ir522d.php' ){
        update_option('bookme__file','a4l6ir522d.php');
    }

    //if ( get_option( 'faculty_role_and_capability_setup_done' ) !=true ) {
        /* Create student role */
        $role = get_role( 'student' );
        if(!$role){
            add_role('student', __(
                'Student'),
                array(
                    'read'            => true, // Allows a user to read
                    'create_posts'    => true, // Allows user to create new posts
                    'edit_posts'      => false, // Allows user to edit their own posts,
                )
            );
        }

        /* create faculty role */
        add_role('lp_teacher', __(
            'Instructor'),
            array(
                'read'            => true, // Allows a user to read
                'create_posts'    => true, // Allows user to create new posts
                'edit_posts'      => false, // Allows user to edit their own posts,
            )
        );

        /* Add faculty capability */
        $role = get_role( 'lp_teacher' );
        $role->add_cap('manage_bookme_faculty_dashboard');
        $role->add_cap('manage_bookme_menu');
        $role->add_cap('manage_bookme_booking_list');
        $role->add_cap('manage_bookme_calender_list');
        $role->add_cap('manage_woocommerce');

        /* Add admin capability */
        $role = get_role( 'administrator' );
        $role->add_cap('manage_bookme_menu');
        $role->add_cap('manage_bookme_service_list');
        $role->add_cap('manage_bookme_service_add');
        $role->add_cap('manage_bookme_staff_list');
        $role->add_cap('manage_bookme_booking_list');
        $role->add_cap('manage_bookme_setting');
        $role->add_cap('manage_bookme_customer_list');
        $role->add_cap('manage_bookme_calender_list');

        update_option( 'faculty_role_and_capability_setup_done', true );
    //}

}
add_action( 'init', 'add_faculty_role_and_capability' );


function set_student_country(){
    global $student_country;
    if( is_user_logged_in() ){
        $user = wp_get_current_user();
        if ( in_array( 'student', (array) $user->roles ) ) {
            $country=get_user_meta($user->ID,'country',true);
            if (strpos($country, 'India') !== false) {
                $student_country='ind';
            }
        }
    }
}
add_action('init','set_student_country');


function bookme_alter_table_customization() {
    global $wpdb;

    /* Add country column in employee table */
    $column_exist=$wpdb->get_var( "SHOW COLUMNS FROM ".$wpdb->prefix."bookme_employee LIKE 'country'; " );
    if( $column_exist==null ){
       $wpdb->query('Alter table '.$wpdb->prefix.'bookme_employee add country text null;');
    }

    /* Add day_ind column in member_schedule table for saving india day */
    $column_exist=$wpdb->get_var( "SHOW COLUMNS FROM ".$wpdb->prefix."bookme_service LIKE 'product_id'; " );
    if( $column_exist==null ){
        $wpdb->query('Alter table '.$wpdb->prefix.'bookme_service add product_id text null;');
}

}
register_activation_hook(__DIR__.'/bookme.php', 'bookme_alter_table_customization' );


function get_month_date($date){
    $query_date = $date;

    $start_day=date('Y-m-01', strtotime($query_date));
    $end_day=date('Y-m-t', strtotime($query_date));

    $dateArr = getDateForSpecificDayBetweenDates($start_day,$end_day,date('w', strtotime($query_date)));
    return $dateArr;
}


function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber){
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    $dateArr = array();

    do {
        if(date("w", $startDate) != $weekdayNumber) {
            $startDate += (24 * 3600); // add 1 day
        }
    }
    while(date("w", $startDate) != $weekdayNumber);

    while($startDate <= $endDate) {
        if ( date('Y-m-d', $startDate) > date('Y-m-d') ) {
            $dateArr[] = date('Y-m-d', $startDate);
        }
        $startDate += (7 * 24 * 3600); // add 7 days
    }
    return($dateArr);
}

add_action('woocommerce_order_details_before_order_table','order_receive_my_booking');
function order_receive_my_booking($order){
   echo "<a href='".site_url().booking_list_page."/'><button>View Bookings</button></a>";
}

function bookme_categories(){
    global $wpdb;
    $categories=$wpdb->get_results( "select * from ".$wpdb->prefix."bookme_category" );
    $data=array();
    foreach ($categories as $categorie){
        $data[$categorie->id]=$categorie;
    }
    return $data;
}

function get_faculty_zoom_link($faculty_id){
    global $wpdb;
    $zoom_id=$wpdb->get_var( $wpdb->prepare(" select zoom_accounts_id from ".$wpdb->prefix."zoom_faculty where faculty_id=%s ",$faculty_id) );
    $zoom_link=$wpdb->get_var( $wpdb->prepare(" select join_link from ".$wpdb->prefix."zoom_accounts where id=%s ",$zoom_id) );
    return $zoom_link;
}

function bookme_render_faculty_dashboard(){
    global $wpdb,$bookme;
    $user=wp_get_current_user();
    $is_access=true;
    if( !in_array('lp_teacher',$user->roles) ){
        echo "<b>You are not instructor for that you have to contact your admin and assign your role as instructor</b>";
        $is_access=false;
    }else{
        $faculty_id=$wpdb->get_var( $wpdb->prepare(" select id from ".$wpdb->prefix."bookme_employee where email=%s ",$user->user_email) );
        $faculty_country=$wpdb->get_var( $wpdb->prepare("select country from ".$wpdb->prefix."bookme_employee where email=%s ",$user->user_email) );
        if( $faculty_id==null ){
            echo "<b>You are not assigned to any course please contact your admin for assigning some course</b>";
            $is_access=false;
        }
    }


    if( $is_access==true ){
        $services=array();
        $results=$wpdb->get_results( "select * from ".$wpdb->prefix."bookme_service " );
        foreach ($results as $result){
            $staffs=explode(',',$result->staff);
            if( in_array($faculty_id,$staffs) ){
                $services[$result->id]=array('name'=>$result->name,'category'=>$result->catId,'id'=>$result->id);
            }
        }
        $bookme['faculty_services']=$services;
        $bookme['faculty_id']=$faculty_id;
        $bookme['faculty_country']=$faculty_country;



        if( isset($_GET['subject']) ){
            bookme_render_faculty_dashboard_course_dates();
        }else if(isset($_GET['booking'])){
            bookme_render_faculty_dashboard_date_students();
        }else{
            bookme_render_faculty_dashboard_course();
        }


    }


}
function bookme_render_faculty_dashboard_date_students(){
    global $wpdb,$bookme;
    $message=array();

    /* check is current faculty has permission */
    if( !isset( $bookme['faculty_id'] ) ){
        echo '<h1>Invalid Course Page. Please Go Back</h1>';
        return '';
    }

    $booking=$wpdb->get_results( $wpdb->prepare(" select * from ".$wpdb->prefix."bookme_current_booking where id=%s and emp_id=%s ",$_GET['booking'],$bookme['faculty_id']) );
    if( count($booking)==0 ){
        echo '<h1>Invalid Course Page. Please Go Back</h1>';
        return '';
    }

    $categories=bookme_categories();
    $current_course=$bookme['faculty_services'][ $booking[0]->ser_id ];

    $students=$wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."bookme_customer_booking b,".$wpdb->prefix."bookme_customers c  WHERE b.customer_id=c.id and b.booking_id=%s ",$_GET['booking']) );

    if( isset($_POST['send_mail_student']) && wp_verify_nonce($_POST['_wpnonce'],'send_mail_student_nonce') ){
        if( isset($_POST['students']) ){
            $_POST['students']=explode(',',$_POST['students']);
            if( !empty($_POST['students']) ){
            	
                $zoom_link=get_faculty_zoom_link($bookme['faculty_id']);
                $zoom_link = "To Join the Zoom Meeting - <a href='$zoom_link'>Zoom Link</a>\n";
                foreach ($_POST['students'] as $student_key) {
                    if( isset($students[$student_key]) ){
                        $html=$_POST['mail_body'];
                        $html=str_replace('%student_name%',$students[$student_key]->name,$html);
                        $html=str_replace('%subject%', $current_course['name'],$html);
                        $html=str_replace('%zoom_join_link%',$zoom_link,$html);

                        wdm_mail_new($students[$student_key]->email,$_POST['mail_subject'],$html);
                    }
                }

                $message[]='<p>Mail successfully sent to <b>'.count($_POST['students']).' student</b> </p>';
            }
        }
    }
    ?>
    <?php
    $faculty_time_zone=get_faculty_timezone($booking[0]->date.' '.$booking[0]->time,$bookme['faculty_id']);
    $date=$faculty_time_zone->format('d-m-Y g:i a');
    foreach ($message as $msg){
        echo "<div class='updated notice'>".$msg."</div>";
    }
    ?>
        <div id="bookmedash" class="wrap">
	    <h1>All Students (<?= count($students) ?>) | <?= $current_course['name']  ?> | <?= date("M d Y",strtotime($date)) ?></h1>
	    <hr>
            <button class="button button-primary open-student-mail-dialog">Send Mail</button><br><br>
            <table id="class-student" class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <td><input type="checkbox"  id="ckbCheckAll">Select All</td>
                    <th>ID</th>
                    <th>Student Name</th>
                    <!--<th>Student Email</th>
                    <th>Student Phone</th>
                    <th>Parent Email</th>
                    <th>Parent Phone</th>-->
                </tr>
                </thead>
                <tbody>
                <?php
                $i=1;
                foreach ($students as $key=>$parent){
					
					$pa = get_user_by('email',''.$parent->email.'');
					$pa_id = $pa->ID;
					
        			$student=$wpdb->get_results("select student_fname,student_lname,student_email,student_mobile from ".$wpdb->prefix."bwlive_students where parent_id=$pa_id");
                    ?>
                    <tr>
                        <td><input type="checkbox" name="student[]" value="<?= $key ?>" class="checkBoxClass"></td>
                        <td><?= $i ?></td>
                        <td><?= $student[0]->student_fname ?> <?= $student[0]->student_lname ?></td>
                        <!--<td><?= $student[0]->student_email ?></td>
                        <td><?= $student[0]->student_mobile ?></td>
                        <td><?= $parent->email ?></td>
                        <td><?= $parent->phone ?></td>-->
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </div>
        <div id="student-mail-dialog" class="hidden" style="float: left; max-width:800px">
            <form method="post">
                <div class="form-group">
                    <label>Subject</label>
                    <?php
                        $booking=$wpdb->get_results( $wpdb->prepare(" select ser_id,date,time from ".$wpdb->prefix."bookme_current_booking where id=%s and emp_id=%s ",$_GET['booking'],$bookme['faculty_id']) );
						$categories=bookme_categories();
    					$current_course=$bookme['faculty_services'][ $booking[0]->ser_id ];
					    //$faculty_time_zone=get_faculty_timezone($booking[0]->date.' '.$booking[0]->time,$bookme['faculty_id']);
                    ?>
                    <input name="mail_subject" value="Zoom Invitation for <?php echo $current_course['name'] ?>" required type="text" style="width: 100%">
                </div>
                <div class="form-group">
                    <label>Body</label>
                    <?php
    				$user=wp_get_current_user();
        			$faculty=$wpdb->get_var( $wpdb->prepare(" select name from ".$wpdb->prefix."bookme_employee where email=%s ",$user->user_email) );

                    $html ='Hi %student_name% <br><br>';
                    $html.='Join your online course %subject%  at '.date("M d Y",strtotime($date)).'<br><br>';
                    
                    $booking_stu=$wpdb->get_results("select bs.email from ".$wpdb->prefix."bookme_customer_booking cs inner join ".$wpdb->prefix."bookme_customers bs on bs.id = cs.customer_id where cs.booking_id=".$_GET['booking']."");
					$html .= '<table style="border-collapse: collapse; border: 1px solid black">';
					$html .= '<tr><th style="border: 1px solid black; padding: 10px">Timezone</th><th style="border: 1px solid black; padding: 10px">Session Time</th></tr>';
					foreach($booking_stu as $stu)
					{
						$usr = get_user_by( 'email', ''.$stu->email.'' );
						$par_id = $usr->ID;
				        $stu_slot=$wpdb->get_var("select student_slot from ".$wpdb->prefix."bwlive_students where parent_id=$par_id");
				        
				        $student_datetime=get_student_zone($booking[0]->date.' '.$booking[0]->time, $par_id);

				        
						$html .= '<tr><td style="border: 1px solid black; padding: 10px">'.$stu_slot.'</td><td style="border: 1px solid black; padding: 10px">'.$student_datetime->format('M jS, Y').' '.$student_datetime->format('g:i A').'</td></tr>';
					}
					$html .= '</table>';
                    $html.='%zoom_join_link%';
                    
                    $html.='<br><br>Thank you';
                    $html.='<br>'.$faculty.'';
                    echo wp_editor( $html, 'mail_body',array('textarea_rows'=>25) );
                    ?>
                    <code>%student_name%</code>
                    <code>%zoom_join_link%</code>
                    <code>%subject%</code>
                </div><br><br>
                <?= '<input type="hidden" name="students" value="" id="mail_students" >' ?>
                <?php wp_nonce_field('send_mail_student_nonce') ?>
                <button type="submit" name="send_mail_student" class="button button-primary">Send Mail</button>
            </form>
        </div>
    <script>
        jQuery(function ($) {
            // initalise the dialog
            $('#student-mail-dialog').dialog({
                title: 'Send mail to student',
                dialogClass: 'wp-dialog',
                autoOpen: false,
                draggable: false,
                width: 'auto',
                modal: true,
                resizable: false,
                closeOnEscape: true,
                position: {
                    my: "center",
                    at: "center",
                    of: window
                },
                open: function () {
                    // close dialog by clicking the overlay behind it
                    $('.ui-widget-overlay').bind('click', function(){
                        $('#my-dialog').dialog('close');
                    })
                },
                create: function () {
                    // style fix for WordPress admin
                    $('.ui-dialog-titlebar-close').addClass('ui-button');
                },
            });

            // bind a button or a link to open the dialog
            $('.open-student-mail-dialog').click(function(e) {
				$("#student-mail-dialog").removeClass("hidden");
				$("#student-mail-dialog").css("float","left");
                var students = [];
                $(".checkBoxClass:checked").each(function() {
                    students.push($(this).val());
                });
                $("#mail_students").val( students.join() );
                e.preventDefault();
                $('#student-mail-dialog').dialog('open');
            });

            $("#ckbCheckAll").click(function () {
                $(".checkBoxClass").prop('checked', $(this).prop('checked'));
            });
        })

    </script>
    <?php


}


function bookme_render_faculty_dashboard_course_dates(){
    global $wpdb,$bookme;
    $message=array();

    $courses = array_column($bookme['faculty_services'], 'id', 'id');
    $current_course=$bookme['faculty_services'][$_GET['subject']];

    /* check is current faculty has permission */
    if( !isset($courses[$_GET['subject']]) ){
        echo '<h1>Invalid Course Page. Please Go Back</h1>';
        return '';
    }

    $categories=bookme_categories();
    
    if(isset($_GET['date']))
    {
    	$bookings_dates=$wpdb->get_results( $wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."bookme_current_booking where date=%s and ser_id=%s and emp_id=%s order by date ASC ",$_GET['date'],$current_course['id'],$bookme['faculty_id']) );
    }
    else
    {
    	$bookings_dates=$wpdb->get_results( $wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."bookme_current_booking where ser_id=%s and emp_id=%s order by date ASC ",$current_course['id'],$bookme['faculty_id']) );
	}
	
    $this_month_year=date('Y-m-%');
    $next_month_year=date('Y-m-%',strtotime('+1 months'));

    $this_month_booking_id=$wpdb->get_var( $wpdb->prepare(" select id from ".$wpdb->prefix."bookme_current_booking where emp_id=%s and ser_id=%s and date like %s order by date DESC limit 1  ",$bookme['faculty_id'],$current_course['id'],$this_month_year) );
    $next_month_booking_id=$wpdb->get_var( $wpdb->prepare(" select id from ".$wpdb->prefix."bookme_current_booking where emp_id=%s and ser_id=%s and date like %s order by date ASC limit 1  ",$bookme['faculty_id'],$current_course['id'],$next_month_year) );

    $not_subscribed_student_next_month=$wpdb->get_results( $wpdb->prepare("SELECT DISTINCT customer_id from ".$wpdb->prefix."bookme_customer_booking WHERE
  customer_id IN ( select customer_id from ".$wpdb->prefix."bookme_customer_booking WHERE booking_id=%s ) and
  customer_id NOT IN (SELECT customer_id FROM ".$wpdb->prefix."bookme_customer_booking WHERE booking_id=%s )",$this_month_booking_id,$next_month_booking_id ) );


    if( isset($_POST['send_subscription_reminder']) && wp_verify_nonce($_POST['_wpnonce'],'send_subscription_reminder_nonce') ){

        foreach ($not_subscribed_student_next_month as $student_data){
            $student_id=$student_data->customer_id;

            $student=$wpdb->get_row(" select * from ".$wpdb->prefix."bookme_customers where id=".$student_id);

            $html=$_POST['mail_body'];
            $html=str_replace('%student_name%',$student->name,$html);
            $html=str_replace('%subject%', $current_course['name'].'('.$categories[$current_course['id']]->name.')',$html);

            wp_mail($student->email,$_POST['mail_subject'],$html);
        }
        $message[]='<p>Subscription Mail successfully sent to '.count($not_subscribed_student_next_month).' student </p>';

    }

    foreach ($message as $msg){
        echo "<div class='updated notice'>".$msg."</div>";
    }
    ?>
    <div id="bookmedash" class="wrap">
    <h1><?= $current_course['name']  ?></h1>
    <hr>
        <button class="button button-primary open-subscription-mail-dialog">(<?= count($not_subscribed_student_next_month) ?>) Sent mail to student for next month Subscription</button>
        <br><br>
        <table id="class-list" class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Students</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=1;
            foreach ($bookings_dates as $bookings_date){
                $total_students=$wpdb->get_var( $wpdb->prepare(" select count(id) from ".$wpdb->prefix."bookme_customer_booking where booking_id=%s ",$bookings_date->id) );

                $faculty_date_time=get_faculty_timezone($bookings_date->date.' '.$bookings_date->time,$bookme['faculty_id']);
                $bookings_date->date=$faculty_date_time->format('d-m-Y');
                $bookings_date->time=$faculty_date_time->format('g:i a');
                
                if(isset($_GET['time']))
                {
                	if($_GET['time'] == $bookings_date->time)
                	{
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= date("M d, Y",strtotime($bookings_date->date)).' '.$bookings_date->time ?></td>
                    <td><?= $total_students.' Students | <a class="btn btn-info btn-sm" href="admin.php?page=bookme-dashboard&booking='.$bookings_date->id.'">View Class</a>' ?></td>
                </tr>
                <?php
                	}
                }
                else
                {
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= date("M d, Y",strtotime($bookings_date->date)).' '.$bookings_date->time ?></td>
                    <td><?= $total_students.' Students | <a class="btn btn-info btn-sm" href="admin.php?page=bookme-dashboard&booking='.$bookings_date->id.'">View Class</a>' ?></td>
                </tr>
                <?php
                }
                $i++;
            }
            ?>
            </tbody>
        </table>
    </div>

    <div id="subscription-mail-dialog" class="hidden" style="max-width:800px">
        <form method="post">
        <div class="form-group">
            <label>Subject</label>
                <input type="text" value="Reminder For next month Subscription" name="mail_subject" style="width: 100%">
        </div>
        <div class="form-group">
            <label>Body</label>
                <?php
                $html='Hi %student_name%<br>';
                $html.='Please subscribe for next month subscription for %subject% <br><br>';
                $html.='Thank You <br>';

                echo wp_editor( $html, 'mail_body',array('textarea_rows'=>25) )
                ?>
                <div><code>%student_name%</code> <code>%subject%</code></div>
        </div><br><br>
            <?= wp_nonce_field('send_subscription_reminder_nonce') ?>
            <button type="submit" name="send_subscription_reminder" class="button button-primary">Send Mail</button>
        </form>
    </div>

    <script>
        jQuery(function ($) {
            // initalise the dialog
            $('#subscription-mail-dialog').dialog({
                title: 'Send mail for next month subscription',
                dialogClass: 'wp-dialog',
                autoOpen: false,
                draggable: false,
                width: 'auto',
                modal: true,
                resizable: false,
                closeOnEscape: true,
                position: {
                    my: "center",
                    at: "center",
                    of: window
                },
                open: function () {
                    // close dialog by clicking the overlay behind it
                    $('.ui-widget-overlay').bind('click', function(){
                        $('#my-dialog').dialog('close');
                    })
                },
                create: function () {
                    // style fix for WordPress admin
                    $('.ui-dialog-titlebar-close').addClass('ui-button');
                },
            });

            // bind a button or a link to open the dialog
            $('.open-subscription-mail-dialog').click(function(e) {
    			$("#subscription-mail-dialog").removeClass("hidden");
				$("#subscription-mail-dialog").css("float","left");
            e.preventDefault();
                $('#subscription-mail-dialog').dialog('open');
            });
        })
    </script>
    <?php

}


add_action('admin_init','add_admin_script');
function add_admin_script(){
    wp_enqueue_script( 'jquery-ui-dialog' ); // jquery and jquery-ui should be dependencies, didn't check though...
    wp_enqueue_style( 'wp-jquery-ui-dialog' );
}

function bookme_render_faculty_dashboard_course(){
    global $wpdb,$bookme;
    $services=$bookme['faculty_services'];
    $categories=bookme_categories();
    
    $cur_booking_table = $wpdb->prefix."bookme_current_booking";
    $cus_booking_table = $wpdb->prefix."bookme_customer_booking";
    $service_table = $wpdb->prefix."bookme_service";
    $category_table = $wpdb->prefix."bookme_category";
    
  ?>
  <!-- Our admin page content should all be inside .wrap -->
  <div class="wrap">
    <!-- Print the page title -->
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <hr>
   
    <div style="padding: 0px!important" class="container">
    <div class="row">
    	<form action="<?php echo admin_url( 'admin.php' ); ?>" method="GET">
    	<div class="col-md-3">
			<input type="hidden" name="page" value="bookme-dashboard" />
			<input type="hidden" name="action" value="filter" />
    		<input value="<?= date("Y-m-d") ?>" required class="form-control" type="date" name="date" id="date" placeholder="Select Date">
    	</div>
    	<div class="col-md-3">
    		<select required class="form-control" name="sub" id="sub">
    			<option selected value="0">All Subjects</option>
    			<?php
            	foreach ($services as $service){
    			?>
    			<option <?php if(isset($_GET['sub']) && $_GET['sub'] == $service['id']) { echo "selected";} ?> value="<?= $service['id'] ?>"><?= $service['name'] ?></option>
    			<?php
    			}
    			?>
    		</select>
    	</div>
    	<div class="col-md-3">
    		<input type="submit" value="Filter" class="btn btn-info" name="submit" id="submit">
    		<?php
    		if(isset($_GET['action']))
    		{
    		?>
    		<a href="<?php echo admin_url( 'admin.php' ); ?>?page=bookme-dashboard" class="btn btn-danger">Clear</a>
    		<?php
    		}
    		?>
		</div>
		</form>
    </div>
    </div>
    
    <hr>
    
    <?php
    if(isset($_GET['action']))
    {
    ?>
    
		<table class="wp-list-table widefat fixed striped" id="nextlecture-dlist">
		<thead>
		<tr>
			<th style="width: 5%" data-name="id">ID</th>
		    <th style="width: 12%" data-name="subject_category">Subject Category</th>
		    <th data-name="subject">Subject</th>
		    <th data-name="lecture">Lecture Date</th>
		    <th data-name="students">Students</th>
		    <th data-name="action">Action</th>
		</tr>
		</thead>
		<tbody>
		
		<?php
		$utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
		$now=$utc_date_time->format('Y-m-d g:i A' );
		$nxt_lecture=$wpdb->get_var( "select date from $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." ORDER BY date ASC LIMIT 1 " );
		
		if($_GET['sub'] == 0)
		{
			$qLog = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE date='".$_GET['date']."' and emp_id=".$bookme['faculty_id']." GROUP BY ser_id ORDER BY date,time DESC";
		}
		else
		{
			$qLog = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE ser_id='".$_GET['sub']."' and date='".$_GET['date']."' and emp_id=".$bookme['faculty_id']." GROUP BY ser_id ORDER BY date,time DESC";
		}
		$qLog_values = $wpdb->get_results( $qLog, ARRAY_A );
		$i = 1;
		foreach ($qLog_values as $qLog_value) {
		
			$book_id = $qLog_value['id'];
			$ser_id = $qLog_value['ser_id'];
			$cat_id = $qLog_value['cat_id'];
			$next_lecture=$qLog_value['date_time'];
			$faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
		    $next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
			$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
			
			$service_name=$wpdb->get_var( "select name from $service_table WHERE id='".$ser_id."'" );
			$category_name=$wpdb->get_var( "select name from $category_table WHERE id='".$cat_id."'" );
			$nos=$wpdb->get_var( "select count(no_of_person) from $cus_booking_table WHERE booking_id='".$book_id."'" );
		
		?>
		<tr>
			<td><?= $i ?></td>
			<td><?= $category_name ?></td>
			<td><?= $service_name ?></td>
			<td><?= $next_lecture ?></td>
			<td><?= $nos ?></td>
		    <td><a class="btn btn-sm btn-info" href="admin.php?page=bookme-dashboard&subject=<?= $ser_id ?>"> View Class</a> </td>
		</tr>	
		<?php
		$i++;
		}
		?>
		
		</tbody>
		</table>

    
    <?php
    }
    else
    {
    ?>
    
    <nav class="nav-tab-wrapper bitwise-tabs">
		<a href="<?php echo admin_url() ?>admin.php?page=bookme-dashboard&tab=nextlecture" class="nav-tab nav-tab-active" id="nextlecture" data-tab-id="nextlecture">
			Upcoming Sessions <span class="dashicons dashicons-money"></span>
		</a>
		<a href="<?php echo admin_url() ?>admin.php?page=bookme-dashboard&tab=upcominglecture" class="nav-tab" id="upcominglecture" data-tab-id="upcominglecture">
			Scheduled Sessions <span class="dashicons dashicons-backup"></span>
		</a>
		<a href="<?php echo admin_url() ?>admin.php?page=bookme-dashboard&tab=completedlecture" class="nav-tab" id="completedlecture" data-tab-id="completedlecture">
			Completed Sessions <span class="dashicons dashicons-clock"></span>
		</a>
	</nav>

    <div id="bookmedash" class="tab-content">
    
	    <!--Next Lecture-->	
		<div id="content-nextlecture" class="content content-tab-active">
	      <h3>Upcoming Sessions</h3>
	      <div id="nextlecture-list">
	      	<table class="wp-list-table widefat fixed striped" id="nextlecture-dlist">
	        	<thead>
	        		<tr>
	                	<th style="width: 5%" data-name="id">ID</th>
	                    <th style="width: 12%" data-name="subject_category">Subject Category</th>
	                    <th data-name="subject">Subject</th>
	                    <th data-name="lecture">Tutor Date & Time</th>
	                    <th data-name="students">Students</th>
	                    <th data-name="action">Action</th>
	        		</tr>
	            </thead>
				<tbody>
				
				<?php
				    $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
				    $now=$utc_date_time->format('Y-m-d g:i A' );
				    $nxt_lecture=$wpdb->get_var( "select date from $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." ORDER BY date ASC LIMIT 1 " );

				    $qLog = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." GROUP BY ser_id ORDER BY date,time ASC";
				    $qLog_values = $wpdb->get_results( $qLog, ARRAY_A );
				    $i = 1;
        			foreach ($qLog_values as $qLog_value) {

						$book_id = $qLog_value['id'];
						$ser_id = $qLog_value['ser_id'];
						$cat_id = $qLog_value['cat_id'];
	                	$next_lecture=$qLog_value['date_time'];
	                	$faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
        			    $next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
	                	$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
	                	
	                	$service_name=$wpdb->get_var( "select name from $service_table WHERE id='".$ser_id."'" );
	                	$category_name=$wpdb->get_var( "select name from $category_table WHERE id='".$cat_id."'" );
	                	$nos=$wpdb->get_var( "select count(no_of_person) from $cus_booking_table WHERE booking_id='".$book_id."'" );

					?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $category_name ?></td>
						<td><?= $service_name ?></td>
						<td><?= $next_lecture ?></td>
						<td><?= $nos ?></td>
	                    <td><a class="btn btn-sm btn-info" href="admin.php?page=bookme-dashboard&subject=<?= $ser_id ?>"> View Class</a> </td>
					</tr>	
					<?php
					$i++;
        			}
				?>
					
				</tbody>
			</table>
	     </div>
	    </div>

	    <!--Upcoming Lecture-->	
		<div id="content-upcominglecture" class="content">
	      <h3>Scheduled Sessions</h3>
	      <div id="upcominglecture-list">
	      	<table class="wp-list-table widefat fixed striped" id="upcominglecture-dlist">
	        	<thead>
	        		<tr>
	                	<th style="width: 5%" data-name="id">ID</th>
	                    <th style="width: 12%" data-name="subject_category">Subject Category</th>
	                    <th data-name="subject">Subject</th>
	                    <th data-name="lecture">Date</th>
	                    <!--<th data-name="students">Students</th>-->
	                    <!--<th data-name="action">Action</th>-->
	        		</tr>
	            </thead>
				<tbody>
					
				<?php
				    $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
				    $now=$utc_date_time->format('Y-m-d g:i A' );
				    $nxt_lecture=$wpdb->get_var( "select date from $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." ORDER BY date ASC LIMIT 1 " );

				    $qLog = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE date < CURDATE() + INTERVAL 30 DAY and STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." GROUP BY date,ser_id ORDER BY date,time DESC";
				    $qLog_values = $wpdb->get_results( $qLog, ARRAY_A );
				    $i = 1;
        			foreach ($qLog_values as $qLog_value) {

						$ser_id = $qLog_value['ser_id'];
						$cat_id = $qLog_value['cat_id'];
	                	$next_lecture=$qLog_value['date_time'];
	                	$next_lec=$qLog_value['date'];
	                	$faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
        			    $next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
	                	$next_lecture= date("M d, Y",strtotime($next_lecture))."";
	                	
	                	$service_name=$wpdb->get_var( "select name from $service_table WHERE id='".$ser_id."'" );
	                	$category_name=$wpdb->get_var( "select name from $category_table WHERE id='".$cat_id."'" );

					?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $category_name ?></td>
						<td><?= $service_name ?></td>
						<td><?= $next_lecture ?><br>
						<?php
				    	
				    	$qLog2 = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE ser_id =$ser_id and date='".$next_lec."' and emp_id=".$bookme['faculty_id']." GROUP BY date,ser_id ORDER BY date,time DESC";
					    $qLog_values2 = $wpdb->get_results( $qLog2, ARRAY_A );
	        			foreach ($qLog_values2 as $qLog_value2) {
						
							$book_id = $qLog_value2['id'];
   							$nex_lecture=$qLog_value2['date_time'];
		                	$faculty_date_time=get_faculty_timezone($nex_lecture,$bookme['faculty_id']);
	        			    $nex_lecture=$faculty_date_time->format('Y-m-d g:i A' );
	        			    $nex_time=$faculty_date_time->format('g:i A' );
		                	$nex_lecture= date("M d, Y g:i A",strtotime($nex_lecture))."";
		                	$nos=$wpdb->get_var( "select count(no_of_person) from $cus_booking_table WHERE booking_id='".$book_id."'" );
		                ?>
							<a class="btn btn-sm btn-info" href="admin.php?page=bookme-dashboard&subject=<?= $qLog_value2['ser_id'] ?>&date=<?= $qLog_value2['date'] ?>&slot=<?= $nex_time ?>"> View Class <span class="badge badge-light"><?=$nos?></span></a>		
						<?php
	        			}
	        			
						?>
						</td>
						<!--<td><?= $nos ?></td>-->
	                    <!--<td><a class="btn btn-sm btn-info" href="admin.php?page=bookme-dashboard&subject=<?= $ser_id ?>"> View Class</a> </td>-->
					</tr>	
					<?php
					$i++;
        			}
				?>	
				
				</tbody>
			</table>
	     </div>
	    </div>

	    <!--Completed Lecture-->	
		<div id="content-completedlecture" class="content">
	      <h3>Completed Sessions</h3>
	      <div id="completedlecture-list">
	      	<table class="wp-list-table widefat fixed striped" id="completedlecture-dlist">
	        	<thead>
	        		<tr>
	                	<th style="width: 5%" data-name="id">ID</th>
	                    <th style="width: 12%" data-name="subject_category">Subject Category</th>
	                    <th data-name="subject">Subject</th>
	                    <th data-name="lecture">Tutor</th>
	                    <th data-name="students">Students</th>
	                    <th data-name="action">Action</th>
	        		</tr>
	            </thead>
				<tbody>

				<?php
				    $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
				    $now=$utc_date_time->format('Y-m-d g:i A' );
				    $nxt_lecture=$wpdb->get_var( "select date from $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." ORDER BY date ASC LIMIT 1 " );

				    $qLog = "SELECT id,cat_id,ser_id,date,time,CONCAT(date,' ',time) as date_time FROM $cur_booking_table WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p') < '".$now."' and emp_id=".$bookme['faculty_id']." GROUP BY ser_id ORDER BY date,time DESC";
				    $qLog_values = $wpdb->get_results( $qLog, ARRAY_A );
				    $i = 1;
        			foreach ($qLog_values as $qLog_value) {

						$book_id = $qLog_value['id'];
						$ser_id = $qLog_value['ser_id'];
						$cat_id = $qLog_value['cat_id'];
	                	$next_lecture=$qLog_value['date_time'];
	                	$faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
        			    $next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
	                	$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
	                	
	                	$service_name=$wpdb->get_var( "select name from $service_table WHERE id='".$ser_id."'" );
	                	$category_name=$wpdb->get_var( "select name from $category_table WHERE id='".$cat_id."'" );
	                	$nos=$wpdb->get_var( "select count(no_of_person) from $cus_booking_table WHERE booking_id='".$book_id."'" );

					?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $category_name ?></td>
						<td><?= $service_name ?></td>
						<td><?= $next_lecture ?></td>
						<td><?= $nos ?></td>
	                    <td><a class="btn btn-sm btn-info" href="admin.php?page=bookme-dashboard&subject=<?= $ser_id ?>"> View Class</a> </td>
					</tr>	
					<?php
					$i++;
        			}
				?>		
					
				</tbody>
			</table>
	     </div>
	    </div>

    	
    </div>
    
	</div>
	
	<?php
    }
    ?>

    <!--<h3>Filter</h3>
    <hr>
    <div class="container">
    <div class="row">
    	<form action="<?php echo admin_url( 'admin.php' ); ?>?page=bookme-dashboard" method="POST">
    	<div class="col-md-3">
    		<input value="<?= date("d-m-Y") ?>" required class="form-control" type="date" name="date" id="date" placeholder="Select Date">
    	</div>
    	<div class="col-md-3">
    		<select required class="form-control" name="classtype" id="classtype">
    			<option value="1">Next Class</option>
    			<option value="2">Upcoming Class</option>
    			<option value="3">Completed Class</option>
    		</select>
    	</div>
    	<div class="col-md-3">
    		<select required class="form-control" name="subject" id="subject">
    			<option selected value="0">All Subjects</option>
    			<?php
            	foreach ($services as $service){
    			?>
    			<option value="<?= $service['id'] ?>"><?= $service['name'] ?></option>
    			<?php
    			}
    			?>
    		</select>
    	</div>
    	<div class="col-md-3">
    		<input type="submit" value="Filter" class="btn btn-success btn-block" name="submit" id="submit">
		</div>
		</form>
    </div>
    </div>
    
    <?php
    if(isset($_POST['submit']))
	{
	?>
    <h3>Filtered Classes</h3>
    <hr>
    <div class="wrap">
        <table class="wp-list-table widefat fixed striped posts nextclass">
            <thead>
            <tr>
                <th>ID</th>
                <th>Subject Category</th>
                <th>Subject</th>
                <th>Next Lecture</th>
                <th>No of Students</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=1;
            $cnt=0;
            foreach ($services as $service){
            	
            	if($service['id'] == $_POST['subject'])
            	{
	                $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
	                $now=$utc_date_time->format('Y-m-d g:i A' );
	                $next_lecture=$wpdb->get_var( "select CONCAT(date,' ',time) as date_time from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date_time ASC LIMIT 1 " );
	                $book_id=$wpdb->get_var( "select id from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date,time ASC LIMIT 1 " );
	                $nos=$wpdb->get_var( "select count(no_of_person) from ".$wpdb->prefix."bookme_customer_booking WHERE booking_id='".$book_id."'" );
	
	                $faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
	                if($next_lecture == '')
	                {
	                	$avl = 0;
	                	$next_lecture = "No Class";
	                }
	                else
	                {
	                	$cnt++;
	                	$avl = 1;
	                	$next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
	                	$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
	                }
	                if($nos < 0 || !$nos)
	                {
	                	$nos = 0;
	                }
	                if($avl == 1)
					{
	                ?>
	                <tr>
	                    <td><?= $i ?></td>
	                    <td><?= $categories[$service['category']]->name ?></td>
	                    <td><?= $service['name'] ?></td>
	                    <td><?= $next_lecture ?></td>
	                    <td><?= $nos ?></td>
	                    <th><a <?php if($avl == 0) {?> href="#" <?php }else{ ?> href="admin.php?page=bookme-dashboard&subject=<?= $service['id'] ?>"<?php } ?>> View Class</a> </th>
	                </tr>
	                <?php
	                $i++;
					}
            	}
            }
        	if($cnt == 0)
			{
            ?>
            <tr>
                <td colspan="6">No Class</td>
            </tr>
            <?php
            $i++;
			}
            ?>
            </tbody>
        </table>
    </div>
	<?php
	}
	?>
    
    <h3>Next Lecture</h3>
    <hr>
    <div class="wrap">
        <table class="wp-list-table widefat fixed striped posts nextclass">
            <thead>
            <tr>
                <th>ID</th>
                <th>Subject Category</th>
                <th>Subject</th>
                <th>Next Lecture</th>
                <th>No of Students</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=1;
            $cnt=0;
            foreach ($services as $service){
            	
                $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
                $now=$utc_date_time->format('Y-m-d g:i A' );
                $next_lecture=$wpdb->get_var( "select CONCAT(date,' ',time) as date_time from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date_time ASC LIMIT 1 " );
                $book_id=$wpdb->get_var( "select id from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date,time ASC LIMIT 1 " );
                $nos=$wpdb->get_var( "select count(no_of_person) from ".$wpdb->prefix."bookme_customer_booking WHERE booking_id='".$book_id."'" );

                $faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
                if($next_lecture == '')
                {
                	$avl = 0;
                	$next_lecture = "No Class";
                }
                else
                {
                	$cnt++;
                	$avl = 1;
                	$next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
                	$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
                }
                if($nos < 0 || !$nos)
                {
                	$nos = 0;
                }
                if($avl == 1)
				{
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $categories[$service['category']]->name ?></td>
                    <td><?= $service['name'] ?></td>
                    <td><?= $next_lecture ?></td>
                    <td><?= $nos ?></td>
                    <th><a <?php if($avl == 0) {?> href="#" <?php }else{ ?> href="admin.php?page=bookme-dashboard&subject=<?= $service['id'] ?>"<?php } ?>> View Class</a> </th>
                </tr>
                <?php
                $i++;
				}
            }
        	if($cnt == 0)
			{
            ?>
            <tr>
                <td colspan="6">No Class</td>
            </tr>
            <?php
            $i++;
			}
            ?>
            </tbody>
        </table>
    </div>

    
    <h3>My Class</h3>
    <hr>
    <div class="wrap">
        <table class="wp-list-table widefat fixed striped posts myclass">
            <thead>
            <tr>
                <th>ID</th>
                <th>Subject Category</th>
                <th>Subject</th>
                <th>Next Lecture</th>
                <th>No of Students</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=1;
            foreach ($services as $service){
                $utc_date_time=get_utc_timezone('Now',$bookme['faculty_id']);
                $now=$utc_date_time->format('Y-m-d g:i A' );
                $next_lecture=$wpdb->get_var( "select CONCAT(date,' ',time) as date_time from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date_time ASC LIMIT 1 " );
                $book_id=$wpdb->get_var( "select id from ".$wpdb->prefix."bookme_current_booking WHERE STR_TO_DATE(CONCAT(date,' ',time),'%Y-%m-%d %h:%i %p')>='".$now."' and emp_id=".$bookme['faculty_id']." and ser_id=".$service['id']." ORDER BY date,time ASC LIMIT 1 " );
                $nos=$wpdb->get_var( "select count(no_of_person) from ".$wpdb->prefix."bookme_customer_booking WHERE booking_id='".$book_id."'" );

                $faculty_date_time=get_faculty_timezone($next_lecture,$bookme['faculty_id']);
                if($next_lecture == '')
                {
                	$avl = 0;
                	$next_lecture = "No Class";
                }
                else
                {
                	$avl = 1;
                	$next_lecture=$faculty_date_time->format('Y-m-d g:i A' );
                	$next_lecture= date("M d, Y g:i A",strtotime($next_lecture))."";
                }
                if($nos < 0 || !$nos)
                {
                	$nos = 0;
                }
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $categories[$service['category']]->name ?></td>
                    <td><?= $service['name'] ?></td>
                    <td><?= $next_lecture ?></td>
                    <td><?= $nos ?></td>
                    <th><a <?php if($avl == 0) {?> href="#" <?php }else{ ?> href="admin.php?page=bookme-dashboard&subject=<?= $service['id'] ?>"<?php } ?>> View Class</a> </th>
                </tr>
                <?php
                $i++;
            }
            ?>
            </tbody>
        </table>
    </div>-->

    <?php
}


function bookme_render_my_zoom_account(){
    global $wpdb,$bookme;
    $user=wp_get_current_user();
    if( !in_array('lp_teacher',$user->roles) ){
        echo "<b>You are not faculty for that you have to contact your admin and assign your role as faculty</b>";
    }else{
    	
        $faculty_id=$wpdb->get_var( $wpdb->prepare("select id from ".$wpdb->prefix."bookme_employee where email=%s ",$user->user_email) );
        
        $zoom_account_id=$wpdb->get_var( $wpdb->prepare("select zoom_accounts_id from ".$wpdb->prefix."zoom_faculty where faculty_id=%s ",$faculty_id) );
        if( $zoom_account_id==null ){
            echo "<b>Admin has not assigned any Zoom account for you. If your Lecture is today then please contact your administrator for zoom details</b>";
        }else{
            $zoom=$wpdb->get_row($wpdb->prepare(" select * from ".$wpdb->prefix."zoom_accounts where id=%s ",$zoom_account_id) );
            ?>
            <h1>Zoom Account Details</h1>
            <table class="wp-list-table widefat fixed striped posts">
                <tr>
                    <th>Email ID</th>
                    <td><?= $zoom->email ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><?= $zoom->password ?></td>
                </tr>
                <tr>
                    <th>Start Meeting Link</th>
                    <td><a href="<?= $zoom->join_link ?>"><?= $zoom->join_link ?></a> </td>
                </tr>
            </table>
            <?php
        }
    }
}

function bookme_render_my_courses(){
    global $wpdb,$bookme;
    $user=wp_get_current_user();
    if( !in_array('lp_teacher',$user->roles) ){
        echo "<b>You are not faculty for that you have to contact your admin and assign your role as faculty</b>";
    }else{
    	
    	//$faculty_id = 11;
        $faculty_id=$wpdb->get_var( $wpdb->prepare("select id from ".$wpdb->prefix."bookme_employee where email=%s ",$user->user_email) );
        
		$exist = 0;
        $services=$wpdb->get_results("select name,product_id,staff from ".$wpdb->prefix."bookme_service");
        $tot = count($services);
        $i = 0;
        foreach($services as $service)
        {
        	if($i < $tot)
        	{
	        	$emp = explode(',', $service->staff);
	        	if(in_array($faculty_id,$emp))
	        	{
	        		$exist = 1;
	        		$i = $tot;
	        	}
        	}
        	$i++;
        }
        if( $exist < 1 ){
            echo "<br><b>Admin has not assigned any courses for you. If your Lecture is today then please contact your administrator.</b>";
        }else{
        ?>
        <h1>My Courses</h1>
        <style>
        	#cls {
			    -moz-column-count: 2;
			    -moz-column-gap: 20px;
			    -webkit-column-count: 2;
			    -webkit-column-gap: 20px;
			    column-count: 2;
			    column-gap: 20px;
			    width: 99%;
			}
        	#cls li {
			    background: #fff;
			    padding: 5px;
			    border: 1px solid #0073aa;
			    border-right: 4px solid #0073aa;
				-webkit-column-break-inside: avoid;
        		page-break-inside: avoid;
            	break-inside: avoid;
            }
            #cls li a {
            	text-decoration: none!important;
            	font-weight: bold!important
            }
        </style>
        <ul id="cls">
        <?php
        	$mappingtable= $wpdb->prefix.'course_mapping';
			foreach($services as $service)
			{
	   	    	$emp = explode(',', $service->staff);
	        	if(in_array($faculty_id,$emp))
        		{
					$product_id = $service->product_id;
					$ordercourses = $wpdb->get_results("SELECT course_id FROM $mappingtable WHERE `product_ids` LIKE '%$product_id%'"); 
			   		$allcourses[]= ($ordercourses[0]->course_id);
        		}
			}
			$courses_list = array_unique($allcourses);
			foreach($courses_list as $cs_id)
			{
				$course_id = $cs_id;
		?>
	            <li><a target="_blank" href="<?php echo esc_url( get_permalink($course_id) ); ?>?enroll-course=<?php echo $course_id ?>"><?php echo get_the_title($course_id); ?></a></li>
		<?php
			}
		?>
		</ul>
		<?php
        }
    }
}

add_action('wp_footer','bookme_custom_script');
function bookme_custom_script(){
    ?>
    <script>
        (function($) {
            console.log( $(".bookme").length );
        })( jQuery );
    </script>
    <?php
}
