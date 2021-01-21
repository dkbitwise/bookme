<?php
add_action('wp_ajax_nopriv_bookme_user_action', 'bookme_user_ajax_action');
add_action('wp_ajax_bookme_user_action', 'bookme_user_ajax_action');

function bookme_user_ajax_action()
{
    if (isset($_POST['call'])) {
        if ($_POST['call'] == 'get_step_1') {
            bookme_get_step_1();
        }
        if ($_POST['call'] == 'get_services_by_cat_id') {
            bookme_get_services_by_cat_id($_POST['cat_id']);
        }
        if ($_POST['call'] == 'get_emp_by_ser_id') {
            bookme_get_emp_by_ser_id($_POST['ser_id']);
        }
        if ($_POST['call'] == 'get_capacity_by_ser_id') {
            bookme_get_capacity_by_ser_id($_POST['ser_id']);
        }
        if ($_POST['call'] == 'get_the_calender') {
            bookme_get_the_calender();
        }
        if ($_POST['call'] == 'get_step_2') {
            bookme_get_step_2();
        }
        if ($_POST['call'] == 'get_step_3') {
        	
        	$service = $_POST['serviceid'];
        	if(isset($dates)){
        	 $day = date('l', strtotime($dates));
        	}
                    global $wpdb;
                    $table_book_service = $wpdb->prefix . 'bookme_service';
                    $table_all_employee = $wpdb->prefix . 'bookme_employee';
                    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
                    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
                    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';

                    $resultS = $wpdb->get_results("SELECT capacity,duration,paddingBefore,name,staff FROM $table_book_service WHERE id=$service");
                    $resultS[0]->staff = str_replace('All,', '', $resultS[0]->staff);
                    $emp = explode(',', $resultS[0]->staff);
                    $resultemployee = $wpdb->get_results("SELECT id,name, google_data FROM $table_all_employee WHERE id IN (" . $resultS[0]->staff . ") ");
                    
                    if(isset($dates)){
                    $date = explode('-', $dates);
                    
                    $monthNum = $date[1];
                    
                    $dateObj = DateTime::createFromFormat('!m', $monthNum);
                    //$monthName = $dateObj->format('F');
                     if(isset($dates)){
                    $monthName = date_i18n('F', strtotime($dates));
                     }
                    $k = 1;
                    $employee=array();
                            $selected_date = $date[0] . '-' . date('m', strtotime($monthName)) . '-' . $date[2];
                    }
                            foreach ($resultemployee as $key => $resulte) {
                            	$resultE = array(0 => $resulte);
                                 $employee[] = $resulte->id;
                            }
                    
					$newarrayemp = array_rand($employee);
				 $randomempl = $employee[$newarrayemp]; 
                    
                    
            if (isset($_POST['faculty']) && isset($_SESSION['bookme'])) {
                $_POST['faculty'] = $randomempl;
                $_SESSION['bookme'][$_POST['access_token']]['employee'] = $_POST['faculty'];
            }
            bookme_get_step_3();
        }
        if ($_POST['call'] == 'get_step_cart') {
            bookme_get_step_cart();
        }
        if ($_POST['call'] == 'delete_cart') {
            bookme_delete_cart();
        }
        if ($_POST['call'] == 'check_coupan') {
            bookme_check_coupan();
        }
        if ($_POST['call'] == 'woo_add_to_cart') {
            bookme_woo_add_to_cart(); // includes/payment/woocommerce.php
        }
        if ($_POST['call'] == 'stripe_payment') {
            bookme_stripe_payment(); // includes/payment/stripe.php
        }
        if ($_POST['call'] == 'book_customer') {
            bookme_book_customer();
        }
        if ($_POST['call'] == 'get_step_5') {
            bookme_get_step_5();
        }
        if ($_POST['call'] == 'save_session_data') {
            bookme_save_session_data();
        }
        wp_die();
    }
}

function bookme_get_step_1()
{
    if (get_option('bookme_initial')) {
        global $wpdb;
        //var_dump($_SESSION);
        $hide = $_SESSION['bookme']['hide'];
        $hide = explode(',', $hide);
        $cat_id = $_SESSION['bookme']['cat_id'];
        $ser_id = $_SESSION['bookme']['ser_id'];
        $mem_id = $_SESSION['bookme']['mem_id'];
        $show_person = $_SESSION['bookme']['show_person'];
        $i = 1;
        $cat_hide = (in_array('categories', $hide) && $cat_id != '');
        $ser_hide = (in_array('services', $hide) && $ser_id != '');
        $mem_hide = (in_array('employees', $hide) && $mem_id != '');
        $person_hide = ($show_person == 0);
        $class1 = ($cat_hide && $ser_hide && $mem_hide && $person_hide) ? 'hidden' : 'bookme-col-xs-12 bookme-col-sm-6 bookme-col-md-6';
        $class2 = ($cat_hide && $ser_hide && $mem_hide && $person_hide) ? 'bookme-col-xs-12' : 'bookme-col-xs-12 bookme-col-sm-6 bookme-col-md-6';
        ?>
        <input type='hidden' id="chk_hd" value='0'/>
        <input type='hidden' id="chk_hd1" value='0'/>
        <div id="formDiv">
            <form>
                <div class="bookme-header">
                    <div class="bookme-container">

					<div class="btn-group" role="group">
					  <a href="/live-courses/" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Courses</a>
					  <a href="<?php echo $_SESSION['ref'] ?>" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Packages</a>
					</div>

					<div class="bookme-bs-wizard" style="border-bottom:0;">
                            <?php $b = 1;
                            $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
                            ?>
                            <div
                                    class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-active">
                                <div class="text-center bookme-bs-wizard-stepnum">
                                    <?php echo $b; ?>. Start Date<?php /*echo bookme_get_table_appearance('bullet1', 'bullet', __('Start Date', 'bookme'));*/ ?>
                                </div>
                                <div class="bookme-progress">
                                    <div class="bookme-progress-bar"></div>
                                </div>
                                <span class="bookme-bs-wizard-dot selectcolor"></span>
                                <?php $b++; ?>
                            </div>

                            <div
                                    class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                                <!-- bookme-complete -->
                                <div class="text-center bookme-bs-wizard-stepnum">
                                    <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet2', 'bullet', __('Time', 'bookme')); ?>
                                </div>
                                <div class="bookme-progress">
                                    <div class="bookme-progress-bar"></div>
                                </div>
                                <a href="#" class="bookme-bs-wizard-dot"></a>
                                <?php $b++; ?>
                            </div>
                            <?php if ($cart_enable) { ?>
                                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-disabled">
                                    <!-- bookme-complete -->
                                    <div class="text-center bookme-bs-wizard-stepnum">
                                        <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet_cart', 'bullet', __('Cart', 'bookme')); ?>
                                    </div>
                                    <div class="bookme-progress">
                                        <div class="bookme-progress-bar"></div>
                                    </div>
                                    <a href="#" class="bookme-bs-wizard-dot"></a>
                                    <?php $b++; ?>
                                </div>
                            <?php } ?>

                            <div
                                    class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                                <!-- bookme-complete -->
                                <div class="text-center bookme-bs-wizard-stepnum">
                                    <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet3', 'bullet', __('Sessions', 'bookme')); ?>
                                </div>
                                <div class="bookme-progress">
                                    <div class="bookme-progress-bar"></div>
                                </div>
                                <a href="#" class="bookme-bs-wizard-dot"></a>
                                <?php $b++; ?>
                            </div>

                            <div
                                    class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                                <!-- bookme-active -->
                                <div class="text-center bookme-bs-wizard-stepnum">
                                    <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet5', 'bullet', __('Done', 'bookme')); ?>
                                </div>
                                <div class="bookme-progress">
                                    <div class="bookme-progress-bar"></div>
                                </div>
                                <a href="#" class="bookme-bs-wizard-dot"></a>
                                <?php $b++; ?>
                            </div>
                        </div>
                
                        <!--<a href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>-->
                        <div class="bookme-row">
                            <div class="bookme-col-xs-12 bookme-col-md-12 bookme-col-lg-12 bookme-form-style-5">
                                <div class="<?php echo $class1; ?>">
                                	<fieldset>
                                	<?php
                                    $time_zone=get_user_meta(get_current_user_id(),'timezone',true);
                                    if( $time_zone ){
                                        //echo get_timezone_list( $time_zone );
                                        echo "<b>Your Time Zone : </b>";
                                        echo $time_zone;
                                    }else{
					$time_zone = 'US/Central';
                                        //echo 'Your Timezone is not set';
                                        echo "<b>Your Time Zone : </b>";
                                        echo $time_zone;
                                    }

                                    ?>
                                	</fieldset>
                                    <?php if ($mem_hide) {
                                        ?>
                                        <input type="hidden" name="employee" id="bookme_employee"
                                               value="<?php echo $mem_id; ?>">
                                        <input type="hidden" name="service" id="bookme_service"
                                               value="<?php echo $ser_id; ?>">
                                        <input type="hidden" name="category" id="bookme_category"
                                               value="<?php echo $cat_id; ?>">
    <input type="hidden" name="category1" id="bookme_category"
                                               value="<?php echo $cat_id; ?>">                                        <?php
                                    } else { ?>
                                        <?php
                                        if ($ser_hide) {
                                            ?>
                                            <input type="hidden" name="service" id="bookme_service"
                                                   value="<?php echo $ser_id; ?>">
                                            <input type="hidden" name="category" id="bookme_category"
                                                   value="<?php echo $cat_id; ?>">
                                            <?php
                                        } else {
                                            if ($cat_hide) {
                                                ?>
                                                <input type="hidden" name="category" id="bookme_category"
                                                       value="<?php echo $cat_id; ?>">
                                                <?php
                                            } else { ?>
                                                <fieldset>
                                                    <legend>
                                                        <?php
                                                    	$table = $wpdb->prefix . 'bwlive_students';
                                                    	$sql = "SELECT student_id,student_fname,student_lname FROM $table where parent_id=".get_current_user_id();
                                                    	$results = $wpdb->get_results($sql);

                                                        $student = bookme_get_table_appearance('student', 'label', __('Student', 'bookme'));
														if(count($results) <= 1)
														{
                                                        ?>
                                                        <span class="number selectcolor"><?php echo $i; ?></span> <?php echo $student; ?>
														<?php
														}
														else
														{
														?>
                                                        <span class="number selectcolor"><?php echo $i; ?></span> Select Student
														<?php
														}
														?>
													</legend>
                                                    	<?php

                                                    	if(count($results)==1){
                                                    		?>
                                                    			<input class="bookme-has-error-none"  name="student" type="text" value="<?php echo $results[0]->student_fname.' '.$results[0]->student_lname; ?>" readonly >
                                                    	
                                                    		<input id="bookme_student" class="bookme-has-error-none"   type="hidden" value="<?php echo $results[0]->student_id; ?>" readonly >
                                                    		<?php
                                                    		
                                                    	}else{
                                                    	?>
                                                    	<select id="bookme_student" class="bookme-has-error-none"
                                                            name="student"
                                                            required="required">
                                                        	<!--<option value="">
                                                            	<?php _e('Select', 'bookme');
                                                            	echo ' ' . $student; ?>
                                                        	</option>-->
								<?php
	                                                        foreach ($results as $result) {
								?>
<option value="<?php echo $result->student_id; ?>" class="hide-if-no-js"><?php echo $result->student_fname; ?> <?php echo $result->student_lname; ?></option>
								<?php
								}
								?>
							</select>
							<?php }?>
						</fieldset>
						<?php
						$i++;
						?>
                                                <fieldset>
                                                    <legend>
                                                        <?php
                                                        $cat = bookme_get_table_appearance('category', 'label', __('Category', 'bookme'));
                                                        ?>
                                                        <span class="number selectcolor"><?php echo $i; ?></span> <?php echo $cat; ?>
                                                    </legend>
                                                    <?php

                                                    $table = $wpdb->prefix . 'bookme_category';
                                                    $sql = "SELECT id,name FROM $table where status='valid'";
                                                    $results = $wpdb->get_results($sql);
                                                    
                                                     foreach ($results as $result) {
                                                            $sel = '';
                                                            if (isset($_POST['get_data']) && $_POST['auto_fill'] == 'false') {
                                                                if (isset($_POST['get_data']['category']) && $_POST['get_data']['category'] == $result->name) {
                                                                    $sel = 'selected';
                                                                    $cat_id_au = $result->id;
                                                                    ?>
                                                                     <input type="text" readonly   class="hide-if-no-js" value="<?php echo $result->name; ?>" />

 <input  id="bookme_category"  name="category" type="hidden"  value="<?php echo $result->id; ?>" class="hide-if-no-js"  />
<?php
                                                                }
                                                            }
                                                            
                                                           
                                                       } ?>
                                                   
                                                   <?php /* <select id="bookme_category" name="category" class="bookme-has-error-none"
                                                            
                                                            required="required">
                                                        <option value="">
                                                            <?php _e('Select', 'bookme');
                                                            echo ' ' . $cat; ?>
                                                        </option>
                                                        <?php

                                                        foreach ($results as $result) {
                                                            $sel = '';
                                                            if (isset($_POST['get_data']) && $_POST['auto_fill'] == 'false') {
                                                                if (isset($_POST['get_data']['category']) && $_POST['get_data']['category'] == $result->name) {
                                                                    $sel = 'selected';
                                                                    $cat_id_au = $result->id;
                                                                }
                                                            }
                                                            ?>
                                                            <option <?= $sel ?> value="<?php echo $result->id; ?>"
                                                                                class="hide-if-no-js"><?php echo $result->name; ?></option>
                                                        <?php } ?>
                                                    </select> 
                                                    <label class="cat_error bookme-error">
                                                        <?php _e('Please select', 'bookme');
                                                        echo ' ' . $cat; ?>
                                                    </label>*/?>
                                                </fieldset>
                                                <?php $i++;
                                            } ?>
                                            <fieldset>
                                                <legend>
                                                    <span class="number selectcolor"><?php echo $i; ?></span> <?php echo "Course"; ?>
                                                    <span class="select-loader btn-xs bookme-loader"
                                                          style="display: none"></span>
                                                </legend>
                                                    <?php
                                                    $serapp = bookme_get_table_appearance('service', 'label', __('Service', 'bookme'));
                                                    //echo $serapp;
                                                   
                                                     if ($cat_hide) {
                                                        bookme_get_services_by_cat_id($cat_id);
                                                    } else {
                                                        ?>
                                                      
                                                           
                                                        <?php

                                                        if (isset($_POST['get_data']) && $_POST['auto_fill'] == 'false') {
                                                            $sel = '';
                                                            if (isset($cat_id_au)) {
                                                            	
                                                            	
                                                                $serices = $wpdb->get_results($wpdb->prepare(" select * from " . $wpdb->prefix . "bookme_service where catId=%s ", $cat_id_au));
                                                                if (!empty($serices)) {
                                                                    foreach ($serices as $serice) {
                                                                        $sel = '';
                                                                        if (isset($_POST['get_data']['service']) && $_POST['get_data']['service'] == $serice->name) {
                                                                            $sel = 'selected';
                                                                            ?>
                                                                     <input type="text" readonly   class="hide-if-no-js" value="<?php echo $serice->name; ?>" />

 <input  id="bookme_service"  name="service" type="hidden"  value="<?php echo $serice->id; ?>" class="hide-if-no-js"  />
<?php
                                                                        }
                                                                 }
                                                                }
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                <?php /*<select id="bookme_service"  name="service"
                                                        required="required">
                                                    <?php
                                                    if ($cat_hide) {
                                                        bookme_get_services_by_cat_id($cat_id);
                                                    } else {
                                                        ?>
                                                        <option value=""><?php _e('Select', 'bookme');
                                                            echo ' ' . $serapp; ?></option>
                                                        <?php

                                                        if (isset($_POST['get_data']) && $_POST['auto_fill'] == 'false') {
                                                            $sel = '';
                                                            if (isset($cat_id_au)) {
                                                            	
                                                            	
                                                                $serices = $wpdb->get_results($wpdb->prepare(" select * from " . $wpdb->prefix . "bookme_service where catId=%s ", $cat_id_au));
                                                                if (!empty($serices)) {
                                                                    foreach ($serices as $serice) {
                                                                        $sel = '';
                                                                        if (isset($_POST['get_data']['service']) && $_POST['get_data']['service'] == $serice->name) {
                                                                            $sel = 'selected';
                                                                        }
                                                                        echo "<option " . $sel . " value='" . $serice->id . "'>" . $serice->name . "</option>";
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    }
                                                    ?>

                                                </select> */?>
                                                <label class="ser_error bookme-error">
                                                    <?php _e('Please select', 'bookme');
                                                    echo ' ' . $serapp; ?>
                                                </label>
                                            </fieldset>
                                            <?php $i++;
                                        } ?>
                                        <fieldset style="display: none">
                                            <legend>
                                                <span class="number selectcolor"><?php echo $i; ?></span>
                                                <?php
                                                $empapp = bookme_get_table_appearance('employee', 'label', __('Employee', 'bookme'));
                                                echo $empapp;
                                                ?>
                                                <span class="select-loader btn-xs bookme-loader"
                                                      style="display: none"></span>
                                            </legend>
                                            <select id="bookme_employee" name="employee" class="employee_select"
                                                    required="required" style="display: none;">
                                                <option value="1" selected></option>
                                            </select>
                                            <label class="emp_error bookme-error">
                                                <?php _e('Please select', 'bookme');
                                                echo ' ' . $empapp; ?>
                                            </label>
                                        </fieldset>
                                        <!--<fieldset>
                                            <p style="    font-size: 15px;
    line-height: 28px;    text-align: justify;">
                                                All classes are weekly at the same day and time. Minimum commitment is
                                                to schedule all remaining classes for that month. Subsequently, if the
                                                student continues, payment will be monthly for the number of classes the
                                                course is offered for each month. The number of classes each month could
                                                vary depending on the month and if any holidays fall on the specific
                                                date of your class.
                                            </p>
                                        </fieldset>-->
                                        <?php $i++;
                                    } ?>
                                    <?php if ($person_hide) {
                                        ?>
                                        <input type="hidden" id="bookme_person" value="1">
                                        <?php
                                    } else { ?>
                                        <fieldset>
                                            <legend><span class="number selectcolor"><?php echo $i; ?></span>
                                                <?php
                                                echo bookme_get_table_appearance('number_of_person', 'label', __('Number of person', 'bookme'));
                                                ?>
                                                <span class="select-loader btn-xs bookme-loader"
                                                      style="display: none"></span>
                                            </legend>
                                            <select id="bookme_person" name="">
                                                <?php
                                                if ($ser_hide) {
                                                    bookme_get_capacity_by_ser_id($ser_id);
                                                }
                                                ?>
                                            </select>
                                        </fieldset>
                                        <label class="per_error bookme-error"></label>
                                        <?php $i++;
                                    } ?>
                                </div>
                                <div class="<?php echo $class2; ?>">
                                    <label
                                            class="date_error bookme-error"><?php _e('Please select date', 'bookme'); ?> </label>
                                    <?php
                                    /*$time_zone=get_user_meta(get_current_user_id(),'timezone',true);
                                    if( $time_zone ){
                                        //echo get_timezone_list( $time_zone );
                                        echo "<b>&nbsp;&nbsp;Preferred time zone : </b>";
                                        echo $time_zone;
                                    }else{
                                        echo 'Your Timezone is not set';
                                    }*/

                                    ?>

                                    <fieldset class="bookme-mar-pad">
                                        <div class="bookme-calender">
                                            <?php if ($class2 != 'bookme-col-xs-12') { ?>
                                                <legend><span
                                                            class="number selectcolor"><?php echo $i-1; ?></span> <?php echo bookme_get_table_appearance('availability', 'label', __('Availability', 'bookme')); ?>
                                                </legend>
                                            <?php } ?>
                                            <input type="hidden" id="date" name="date" class="date"/>

                                            <div class="column_right_grid">
                                                <div class="cal1">
                                                    <div class="bookme">
                                                        <!--Here dynamic calender comes with bookme.js display:none;-->
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="column_right_grid_loading" style="display:none;">
                                                <div class="loader-color bookme-loader"></div>
                                            </div>


                                            <section id="show-me-next" style="display: none;">
                                                <button type="button" id="bookme_step1"
                                                        class="width-100 coffe button selectcolorbtn"><?php _e('Next', 'bookme'); ?>
                                                </button>
                                            </section>
                                    </fieldset>
                                </div>
                                <?php if ($cart_enable && count($_SESSION['bookme']['cart']) > 0 && isset($_POST['cart'])) { ?>
                                    <div class="bookme-col-xs-12 bookme-mar-pad">
                                        <button class="bookme_cart_btn selectcolor" id="bookme_get_cart">
                                            <img src="<?php echo plugins_url('../assets/images/cart.png', __FILE__) ?>"
                                                 alt="">
                                        </button>
                                    </div>
                                <?php } else {
                                    unset($_SESSION['bookme']['cart']);
                                } ?>
                                <p style=" font-size: 15px;line-height: 28px;    text-align: justify;clear:both;">All sessions are weekly on the same day and time.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="scriptdiv">
        </div>
        <div id="showStep">
        </div><?php
    } else {
        ?>
        <h3><?php _e('Your product is not verified. Please enter purchase code to verify your product.', 'bookme'); ?></h3>
        <button type="button" class="width-100 coffe button selectcolorbtn"
                onclick="window.location.href='<?php echo admin_url('admin.php?page=bookme-settings'); ?>'"><?php _e('Verify Purchase Code', 'bookme'); ?>
        </button>
        <?php
    }
}

function get_ind_time($us_time)
{
    $ind = date('l h:i a', strtotime('+12 hour +30 minute', strtotime($us_time)));
    return array(date('l', strtotime($ind)), date('h:i a', strtotime($ind)));
}

function get_ind_date($in_time)
{
    $us = date('Y-m-d g:i a', strtotime('+12 hour +30 minute', strtotime($in_time)));
    return array(date('Y-m-d', strtotime($us)), date('g:i a', strtotime($us)));
}

function get_usa_date($us_time)
{
    $us = date('Y-m-d g:i a', strtotime('-12 hour -30 minute', strtotime($us_time)));
    return array(date('Y-m-d', strtotime($us)), date('g:i a', strtotime($us)));
}

function get_student_zone($date_time_utc, $stuid){
	
//	echo $date_time_utc;
    $timezone=get_user_meta( $stuid,'timezone',true );
    if(!$timezone)
    {
    	$timezone = 'UTC';
    }
    
    
    if( $timezone ){
    	
   
        $student_date_time=(new DateTime($date_time_utc, new DateTimeZone('UTC')))->setTimezone( new DateTimeZone($timezone) );
       //$student_date_time=(new DateTime($date_time_utc, new DateTimeZone($timezone)) );
          
      //print_r($student_date_time);
        return $student_date_time;
    }else{
        throw new Exception("Bookme registration user has not selected timezone");
    }
}


function get_student_timezone($date_time_utc){
	
//	echo $date_time_utc;
    $timezone=get_user_meta( get_current_user_id(),'timezone',true );
    if(!$timezone)
    {
    	$timezone = 'UTC';
    }
    
    
    if( $timezone ){
    	
   
        $student_date_time=(new DateTime($date_time_utc, new DateTimeZone('UTC')))->setTimezone( new DateTimeZone($timezone) );
       //$student_date_time=(new DateTime($date_time_utc, new DateTimeZone($timezone)) );
          
      //print_r($student_date_time);
        return $student_date_time;
    }else{
        throw new Exception("Bookme registration user has not selected timezone");
    }
}

function get_utc_timezone($date_time_student,$faculty_id=null){
    if( $faculty_id==null ){
        $timezone=get_user_meta( get_current_user_id(),'timezone',true );
    	if(!$timezone)
    	{
        	$timezone = 'UTC';
    	}
        if( $timezone ){
        	
            $utc_date_time=(new DateTime($date_time_student, new DateTimeZone($timezone)))->setTimezone( new DateTimeZone('UTC') );
            return $utc_date_time;
        }else{
            throw new Exception("Bookme registration user has not selected timezone");
        }
    }else{
        global $wpdb;
        $timezone=$wpdb->get_var( $wpdb->prepare(" select country from ".$wpdb->prefix."bookme_employee where id=%s ",$faculty_id) );
        if( $timezone ){
            $utc_date_time=(new DateTime($date_time_student, new DateTimeZone($timezone)))->setTimezone( new DateTimeZone('UTC') );
            return $utc_date_time;
        }else{
            throw new Exception("Bookme registration user has not selected timezone");
        }
    }

}

function get_faculty_timezone($date_time_utc,$faculty_id){
    global $wpdb;
    $timezone=$wpdb->get_var( $wpdb->prepare(" select country from ".$wpdb->prefix."bookme_employee where id=%s ",$faculty_id) );
   $utc_date_time=(new DateTime($date_time_utc, new DateTimeZone('UTC')))->setTimezone( new DateTimeZone($timezone) );
     
   // $utc_date_time=(new DateTime($date_time_utc, new DateTimeZone($timezone)) );
    return $utc_date_time;
}



function bookme_get_the_calender(){
    global $wpdb;
    $m_w_date = $_POST['m_w_date'];
    $category = $_POST['cat'];
    $service = $_POST['ser'];
    $employee = (isset($_POST['emp'])) ? $_POST['emp'] : '';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $resultS = $wpdb->get_results("SELECT capacity,duration,paddingBefore,name,staff FROM $table_book_service WHERE id=$service");
    $emp = explode(',', $resultS[0]->staff);
    $cdate = array();
    $tooltip = array();
    global $student_country;

    if (!empty($m_w_date)) {
        $arr_d = explode(" ", $m_w_date);
        $month = date('m', strtotime($arr_d[0], 1));
        $year = $arr_d[1];
        $startdate = 01;
        $chkdte = date('Y-m-d', strtotime($year . '-' . $month . '-' . '01'));
        $converted = date('Y-m', strtotime($chkdte));

    } else {
        $startdate = date('d', current_time('timestamp'));
        $month = date('m', current_time('timestamp'));
        $year = date('Y', current_time('timestamp'));
        $converted = date('Y-m', current_time('timestamp'));
    }
    //$day_in_month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($converted)), $year);
    $day_in_month = date('t', mktime(0, 0, 0, date('m', strtotime($converted)), 1, $year));

    for ($jc = $startdate; $jc <= $day_in_month; $jc++) {
        $dates = date('Y-m-d', strtotime($converted . '-' . $jc));
        $day = date('l', strtotime($dates));
        $bstart = "";
        $bend = "";


        if (!empty($emp)) {

            $duration = $resultS[0]->duration;
            $paddingTime = $resultS[0]->paddingBefore;
            $ttlslots = 0;
            $tip = '';
            foreach ($emp as $employee) {

                /* TODO:ANAND-time_conversion fetch faculty days & convert into student timezone  */
                $temp_resultTime = $wpdb->get_results("SELECT * FROM $table_member_schedule WHERE emp_id=$employee  ");

                $resultTime=array();
                foreach ($temp_resultTime as $r_key => $r_data) {
                    $s_date=get_student_timezone($r_data->day.' '.$r_data->schedule_start);
                    $n_date=get_student_timezone($r_data->day.' '.$r_data->schedule_end);

                    $temp_resultTime[$r_key]->schedule_start = $s_date->format('l g:i a');
                    $temp_resultTime[$r_key]->schedule_end = $n_date->format('l g:i a');

                    if( $r_data->break_start!='' ){
                        $b_start=get_student_timezone($r_data->day.' '.$r_data->break_start);
                        $b_end=get_student_timezone($r_data->day.' '.$r_data->break_end);
                        $temp_resultTime[$r_key]->break_start = $b_start->format('l g:i a');
                        $temp_resultTime[$r_key]->break_end = $b_end->format('l g:i a');
                    }

                    if( $day==$s_date->format('l') || $day==$n_date->format('l') ){
                        $resultTime[]= $temp_resultTime[$r_key];
                    }

                }


                $numR = $wpdb->num_rows;
                if ($numR >= 1) {

                    $gc_events = array();
                    /* Google Calendar integration */
                    if (bookme_get_settings('bookme_gc_2_way_sync', 1)) {
                        if (bookme_get_settings('bookme_gc_client_id') != null) {
                            $google_data = $wpdb->get_var("SELECT google_data FROM $table_all_employee WHERE id=" . $employee);
                            if ($google_data) {
                                include_once plugin_dir_path(__FILE__) . '/google.php';
                                $bookme_gc_client = new Google_Client();
                                $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                try {
                                    $bookme_gc_client->setAccessToken($google_data);
                                    if ($bookme_gc_client->isAccessTokenExpired()) {
                                        $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                        $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                    }
                                    $gc_events = bookme_get_calendar_events($bookme_gc_client, $dates);
                                } catch (Exception $e) {

                                }
                            }
                        }
                    }

                    foreach ($resultTime as $resultTime_s) {
                        $scheduleTime1 = $resultTime_s->schedule_start;
                        $scheduleTime2 = $resultTime_s->schedule_end;
                        $breakTime1 = $resultTime_s->break_start;
                        $breakTime2 = $resultTime_s->break_end;
                        $bstart = strtotime($breakTime1);
                        $bend = strtotime($breakTime2);
                        $start = strtotime($scheduleTime1);
                        $end = strtotime($scheduleTime2);
                        $k = 1;
                        $breakDiff = ($bend - $bstart);

                        if (!empty($bstart) and !empty($bend)) {
                            /* if day has break */

                            for ($j = $start; $j <= $bstart - $duration; $j = $j + $duration + $paddingTime) {
                                $apptstart = date('g:i A', $j);
                                $apptend = date('g:i A', $j + $duration);
                                if ($dates == current_time('Y-m-d')) {
                                    if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                        continue;
                                    }
                                }
                                if( date('l', $j)!=$day ){
                                    continue;
                                }

                                /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                $utc_dates=$utc_date_time->format('Y-m-d');

                                $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
                                $book = 0;
                                foreach ($rowcount as $sql) {
                                    /* convert date utc to student for compare */
                                    $student_date_time=get_student_timezone($dates.' '.$sql->time);
                                    $sql->time=$student_date_time->format('g:i A');

                                    $time = strtotime(date('g:i A', strtotime($sql->time)));
                                    $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                        $book = 1;
                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                        $book = 1;
                                    }
                                }

                                if ($book == 0) {
                                    foreach ($gc_events as $gc_event) {
                                        $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                        $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                        if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                            $book = 1;
                                        } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                            $book = 1;
                                        }
                                    }
                                }

                                if ($book == 0) {

                                    /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                    $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                    $utc_dates=$utc_date_time->format('Y-m-d');
                                    $utc_appstart=$utc_date_time->format('g:i a');

                                    $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart' and b.duration = '$duration'");
                                    if (empty($countAppoint[0]->sump)) {
                                        $booked = 0;
                                    } else {
                                        $booked = $countAppoint[0]->sump;
                                    }
                                    $available = $resultS[0]->capacity - $booked;
                                    if ($available > 0) {
                                        if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {
                                            foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                    if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                    $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                    $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                        $cart_book = 1;
                                                        break;
                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($cart_book == 0) {
                                            $ttlslots++;
                                            $k++;
                                        }
                                    }
                                }
                            }
                            for ($l = $start; $l <= $end - $duration; $l = $l + $duration + $paddingTime) {
                                $apptstart = date('g:i A', $l);
                                $apptend = date('g:i A', $l + $duration);
                                if ($dates == current_time('Y-m-d')) {
                                    if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                        continue;
                                    }
                                }
                                if( date('l', $l)!=$day ){
                                    continue;
                                }

                                /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                $utc_dates=$utc_date_time->format('Y-m-d');

                                $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
                                $book = 0;
                                foreach ($rowcount as $sql) {
                                    /* convert date utc to student for compare */
                                    $student_date_time=get_student_timezone($dates.' '.$sql->time);
                                    $sql->time=$student_date_time->format('g:i A');

                                    $time = strtotime(date('g:i A', strtotime($sql->time)));
                                    $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                        $book = 1;
                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                        $book = 1;
                                    }
                                }

                                if ($book == 0) {
                                    foreach ($gc_events as $gc_event) {
                                        $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                        $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                        if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                            $book = 1;
                                        } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                            $book = 1;
                                        }
                                    }
                                }

                                if ($book == 0) {

                                    /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                    $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                    $utc_dates=$utc_date_time->format('Y-m-d');
                                    $utc_appstart=$utc_date_time->format('g:i a');

                                    $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart' and b.duration = '$duration'");
                                    if (empty($countAppoint[0]->sump)) {
                                        $booked = 0;
                                    } else {
                                        $booked = $countAppoint[0]->sump;
                                    }
                                    $available = $resultS[0]->capacity - $booked;
                                    if ($available > 0) {
                                        if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {
                                            foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                    if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                    $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                    $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                        $cart_book = 1;
                                                        break;
                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($cart_book == 0) {
                                            $ttlslots++;
                                            $k++;
                                        }
                                    }
                                }
                            }
                            $tip = $ttlslots . " " . __('Available', 'bookme');


                        }
                        else {
                            for ($j = $start; $j <= $end - $duration; $j = $j + $duration + $paddingTime) {
                                $apptstart = date('g:i A', $j);
                                $apptend = date('g:i A', $j + $duration);
                                if ($dates == current_time('Y-m-d')) {
                                    if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                        continue;
                                    }
                                }
                                if( date('l', $j)!=$day ){
                                    continue;
                                }

                                /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                $utc_dates=$utc_date_time->format('Y-m-d');
                                $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
                                $book = 0;
                                foreach ($rowcount as $sql) {
                                    /* convert date utc to student for compare */
                                    $student_date_time=get_student_timezone($dates.' '.$sql->time);
                                    $sql->time=$student_date_time->format('g:i A');

                                    $time = strtotime(date('g:i A', strtotime($sql->time)));
                                    $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                        $book = 1;
                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                        $book = 1;
                                    }
                                }

                                if ($book == 0) {
                                    foreach ($gc_events as $gc_event) {
                                        $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                        $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                        if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                            $book = 1;
                                        } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                            $book = 1;
                                        }
                                    }
                                }

                                if ($book == 0) {

                                    /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                    $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                    $utc_dates=$utc_date_time->format('Y-m-d');
                                    $utc_appstart=$utc_date_time->format('g:i a');

                                    $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart' and b.duration = '$duration'");
                                    if (empty($countAppoint[0]->sump)) {
                                        $booked = 0;
                                    } else {
                                        $booked = $countAppoint[0]->sump;
                                    }
                                    $available = $resultS[0]->capacity - $booked;
                                    if ($available > 0) {
                                        $cart_book = 0;
                                        if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {
                                            foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                    if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                    $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                    $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                        $cart_book = 1;
                                                        break;
                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                        $cart_book = 1;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($cart_book == 0) {
                                            $ttlslots++;
                                            $k++;
                                        }
                                    }
                                }
                            }
                            $tip = $ttlslots . " " . __('Available', 'bookme');
                        }
                    }


                } else {
                    if ($tip == 'Not Available' || $tip == '') {
                        $tip = __('Not Available', 'bookme');
                    }

                }

            }


        } else {
            $tip = __('Not Available', 'bookme');
        }

        if( $tip=='' ){
            $tip = __('Not Available', 'bookme');
        }
        $cdate[] = $dates;
        $tooltip[] = $tip;

    }

    $table_holidays = $wpdb->prefix . 'bookme_holidays';
    $result = $wpdb->get_results("SELECT COUNT(id) as total_f,holi_date FROM $table_holidays WHERE staff_id IN (" . implode(',', $emp) . ") group by holi_date ");
    foreach ($result as $holiday) {
        $i = array_search($holiday->holi_date, $cdate);

        if ($holiday->total_f == count($emp)) {
            /* Holiday for all employee */
            $tooltip[$i] = __('Holiday', 'bookme');
        } else {
            $faculty = str_replace(' Available', '', $tooltip[$i]);
            $available_f = $faculty - $holiday->total_f;
            $tooltip[$i] = $available_f . ' Available';
        }


    }
    
    $table_genholidays = $wpdb->prefix . 'custom_holidays';
    $result_gen = $wpdb->get_results("SELECT COUNT(id) as total_f,date FROM $table_genholidays group by date ");
    foreach ($result_gen as $holiday_gen) {

		$dt = $holiday_gen->date;
		if(in_array($dt,$cdate))
		{
			$ki = array_search($dt, $cdate);
            $tooltip[$ki] = __('Holiday', 'bookme');
		}

	}

    $res['cdate'] = $cdate;
    $res['tooltip'] = $tooltip;
    echo json_encode($res);
}

function bookme_get_step_2()
{
    ?>
    <div class="bookme-header">
        <div class="bookme-container">
        
			<div class="btn-group" role="group">
			  <a href="/live-courses/" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Courses</a>
			  <a href="<?php echo $_SESSION['ref'] ?>" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Packages</a>
			</div>

            <div class="bookme-bs-wizard bookme-row" style="border-bottom:0;">
                <?php $b = 1;
                $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
                ?>
                <div
                        class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b;?>. Start Date<?php /*echo bookme_get_table_appearance('bullet1', 'bullet', __('Start Date', 'bookme'));*/ ?>
                    </div>
                    <div class="bookme-progress selectcolor">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="2"></a>
                    <?php $b++; ?>
                </div>

                <div
                        class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-active">
                    <!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet2', 'bullet', __('Time', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>
                <?php if ($cart_enable) { ?>
                    <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-disabled"><!-- bookme-complete -->
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet_cart', 'bullet', __('Cart', 'bookme')); ?>
                        </div>
                        <div class="bookme-progress">
                            <div class="bookme-progress-bar"></div>
                        </div>
                        <a href="#" class="bookme-bs-wizard-dot"></a>
                        <?php $b++; ?>
                    </div>
                <?php } ?>

                <div
                        class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                    <!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet3', 'bullet', __('Sessions', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot"></a>
                    <?php $b++; ?>
                </div>

                <div
                        class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                    <!-- bookme-active -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet5', 'bullet', __('Done', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot"></a>
                    <?php $b++; ?>
                </div>
            </div>
			<!--<a href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>-->
            <div class="bookme-row">

                    <?php
                    $access_token = null;
                    if (isset($_POST['emp_a'])) {
                        $dates = $_POST['date_a'];
                        $category = $_POST['cat_a'];
                        $service = $_POST['ser_a'];
                        $employee = $_POST['emp_a'];
                        $person = $_POST['person'];
                        $student = $_POST['student'];

                        $access_token = uniqid();

                        $_SESSION['bookme'][$access_token]['date'] = $dates;
                        $_SESSION['bookme'][$access_token]['category'] = $category;
                        $_SESSION['bookme'][$access_token]['service'] = $service;
                        $_SESSION['bookme'][$access_token]['employee'] = $employee;
                        $_SESSION['bookme'][$access_token]['person'] = $person;
                        $_SESSION['bookme'][$access_token]['student'] = $student;
                    } else if (!isset($_POST['access_token'])) {
                        if ($cart_enable) {
                            $cart = end($_SESSION['bookme']['cart']);
                            $_SESSION['bookme']['back_cart'] = key($_SESSION['bookme']['cart']);
                            $dates = $cart['date'];
                            $category = $cart['category'];
                            $service = $cart['service'];
                            $employee = $cart['employee'];
                            $person = $cart['person'];
                            $student = $cart['student'];
                        } else {
                            echo 0;
                            exit;
                        }
                    } else {
                        $access_token = $_POST['access_token'];
                        $dates = $_SESSION['bookme'][$access_token]['date'];
                        $category = $_SESSION['bookme'][$access_token]['category'];
                        $service = $_SESSION['bookme'][$access_token]['service'];
                        $employee = $_SESSION['bookme'][$access_token]['employee'];
                        $person = $_SESSION['bookme'][$access_token]['person'];
                        $student = $_SESSION['bookme'][$access_token]['student'];
                    }

                    $day = date('l', strtotime($dates));
                    global $wpdb;
                    $table_book_pay = $wpdb->prefix . 'bookme_payments';
                    $table_book_service = $wpdb->prefix . 'bookme_service';
                    $table_all_employee = $wpdb->prefix . 'bookme_employee';
                    $table_all_customer = $wpdb->prefix . 'customers';
                    $table_holiday_employee = $wpdb->prefix . 'bookme_holidays';
                    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
                    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
                    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';

                    $resultS = $wpdb->get_results("SELECT capacity,duration,paddingBefore,name,staff FROM $table_book_service WHERE id=$service");
                    $resultS[0]->staff = str_replace('All,', '', $resultS[0]->staff);
                    $emp = explode(',', $resultS[0]->staff);
                    
                    foreach($emp as $em)
                    {
                    	
                    	$total_bookings = $wpdb->get_results("SELECT cbb.payment_id FROM $table_current_booking cb
                    	inner join $table_customer_booking cbb on cbb.booking_id = cb.id 
                    	WHERE cb.emp_id = $em group by cbb.payment_id order by cbb.payment_id desc");

						if(count($total_bookings) >= 1)
						{
							$payid = $total_bookings[0]->payment_id;
						}
						else
						{
							$payid = 0;
						}
						
						$bks [] = array("emp_id"=>$em, "bookings"=> count($total_bookings), "last_book"=> $payid);

                    }

					$sort = array();
					foreach($bks as $k=>$v) {
					   $sort['bookings'][$k] = $v['bookings'];
					   $sort['last_book'][$k] = $v['last_book'];
					}
					array_multisort($sort['bookings'], SORT_ASC,$sort['last_book'], SORT_ASC,$bks);

					foreach($bks as $bk)
					{
						foreach ($bk as $key => $value){
							if($key === 'emp_id')
							{
								$tutors[] = $value;
							}
						}
					}
					$tutor_list = implode(",",$tutors);

					//Added by Vignesh R
                    $resultS_hol = $wpdb->get_results("SELECT emp_id FROM $table_member_schedule WHERE day='$day' and emp_id IN (" . $resultS[0]->staff . ")");

                    $resultemployee = $wpdb->get_results("SELECT id,name, google_data FROM $table_all_employee WHERE id IN (" . $resultS_hol[0]->emp_id . ") ");
                    //$resultemployee = $wpdb->get_results("SELECT id,name, google_data FROM $table_all_employee WHERE id IN (" . $tutor_list . ") ");

                    $date = explode('-', $dates);
                    $monthNum = $date[1];
                    $dateObj = DateTime::createFromFormat('!m', $monthNum);
                    //$monthName = $dateObj->format('F');
                    $monthName = date_i18n('F', strtotime($dates));

                    $gc_events = array();
                    /* Google Calendar integration */
                    $resultE = array(0 => $resultemployee[0]);
                    if (bookme_get_settings('bookme_gc_2_way_sync', 1)) {
                        if (bookme_get_settings('bookme_gc_client_id') != null) {
                            $google_data = $resultE[0]->google_data;
                            if ($google_data) {
                                include_once plugin_dir_path(__FILE__) . '/google.php';
                                $bookme_gc_client = new Google_Client();
                                $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                try {
                                    $bookme_gc_client->setAccessToken($google_data);
                                    if ($bookme_gc_client->isAccessTokenExpired()) {
                                        $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                        $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                    }
                                    $gc_events = bookme_get_calendar_events($bookme_gc_client, $dates);
                                } catch (Exception $e) {

                                }
                            }
                        }
                    }
                    ?>
				<?php
				
					$service=$_SESSION['bookme'][$access_token]['service'];

					$resultavail = $wpdb->get_results("SELECT price,name,product_id FROM $table_book_service WHERE id=$service");
                    $product = wc_get_product($resultavail[0]->product_id);
                    
				
				?>
                    <div class="packagedetails"><div class="subjectdetails"><strong>Subject: </strong><?php echo $resultS[0]->name;?></div>
                    <div class="studentdetailsnew"><strong>Date: </strong><?php echo date("M d, Y",strtotime($dates));?> (<?php echo date("l",strtotime($dates));?>)</div></div>
                <div class="bookme-form-style-5">

                    <div class="bookme-calender bookme-pad-20">
                        <div class="bookme-aval-time" style="display: block;">
                            <?php if ($access_token != null) { ?>
                                <input type="hidden" name="access_token" value="<?php echo $access_token; ?>">
                            <?php }

				// $location = WC_Geolocation::geolocate_ip();
            			 //$cou = $location['country'];
				 ?>
                            
                                    <?php
                                    $month_dates = get_month_date($dates);
                                    $month_dates_f = array();
                                    //echo $date[2] . ' ' . $monthName . ' ' . $date[0];
					 $today = $dates; 
					// $today = strtotime("-1 day",strtotime($dates));
			
				

					$sp = 0;
				//	echo '<table style="margin-bottom: 0px!important"><tr>';
					for($i=1; $i<=$product->get_meta( 'custom_text_field_title'); $i++)
					{
    					    $sp++;
    					   
    					$monthonly=date('M', strtotime($today));
    					 //$today = date('Y-m-d',$today);
    					 $month_dates_f[] = date('d M Y', strtotime($today)); 
    					 $month_dates_fd[] = date('Y-m-d', strtotime($today)); 
					    
                     // echo '<td style="font-size: 12px">'.date('d M Y', strtotime($today)).'</td>';
                      $repeat = strtotime("+7 day",strtotime($today));
					    $today = date('Y-m-d',$repeat);
if($sp == 5) {
      //  echo '</tr><tr>';
        $sp = 0;
    }
						
				//	echo '<br>';
					}
                                //    foreach ($month_dates as $date) {
				//	 if(date('Y-m-d',strtotime($date))>=$_SESSION['bookme'][$access_token]['date']){  //Condition to avoid past dates updated by suresh on 5-8-2020
                                  //      $month_dates_f[] = date('d M Y', strtotime($date));
				//	}
                                  //  }

                                    

                                    //echo implode(' | ', $month_dates_f);
                                    ?>
                                
                            
                            <?php
                            $_SESSION['bookme'][$access_token]['month_date'] = $month_dates_f;
                            $_SESSION['bookme'][$access_token]['month_date_fd'] = $month_dates_fd;
							$dts = implode("','",$month_dates_fd);
							$dts = "'".$dts."'";

                            $parent = wp_get_current_user();
							$parent_email = $parent->user_email;
							$parent_id = $wpdb->get_var($wpdb->prepare(" select id from " . $wpdb->prefix . "bookme_customers where email=%s", $parent_email));

                            $k = 1;
                            $newstrarttime=array();
							$newsendtime=array();
                            $selected_date = $date[0] . '-' . date('m', strtotime($monthName)) . '-' . $date[2];
                            
                            $ik=0;
                            foreach ($resultemployee as $key => $resulte) {
                            	
                                $resultE = array(0 => $resulte);
                                $employee = $resulte->id;
                                //echo $employee;
                                //echo "<Br>";
                                $is_holiday = $wpdb->get_var($wpdb->prepare(" select count(id) from " . $wpdb->prefix . "bookme_holidays where staff_id=%s and holi_date=%s", $employee, $selected_date));
                                if ($is_holiday != 1) {
                                	
                                	
                                    ?>
                                    
                 

                                    <div id="time_slot_scroll" class="tse-scrollable">
                                        <div class="tse-content">
                                            <?php
                                            $bstart = "";
                                            $bend = "";
                                            if (in_array($employee, $emp)) {
                                                $duration = 4500;
                                                //$paddingTime = $resultS[0]->paddingBefore;
                                                //$paddingTime =300;
                                                /*Modified By Vignesh*/
                                                $paddingTime =900;
                                                global $student_country;
//
                                                /* TODO:ANAND-time_conversion fetch faculty days & convert into student timezone  */
												
                                                $temp_resultTime = $wpdb->get_results("SELECT * FROM $table_member_schedule WHERE emp_id=$employee");
												//echo "select $table_current_booking.time from $table_current_booking  inner join $table_customer_booking on $table_customer_booking.booking_id = $table_current_booking.id where $table_current_booking.date in ($dts) and $table_customer_booking.customer_id=$parent_id";
                                                $results=array();
                                                foreach ($temp_resultTime as $r_key => $r_data) {
                                                //print_r(date('d-m-Y',strtotime($month_dates_f[0])));
                                                //echo date('d-m-Y',strotime($month_dates_f[0]));
                                                
                                                if($day==$r_data->day){
                                                    $s_date=get_student_timezone($selected_date.' '.$r_data->schedule_start);
                                                    $n_date=get_student_timezone($selected_date.' '.$r_data->schedule_end);
                                                   
                                                   
                                                    $temp_resultTime[$r_key]->schedule_start = $s_date->format('l g:i a');
                                                    $temp_resultTime[$r_key]->schedule_end = $n_date->format('l g:i a');


                                                    if( $r_data->break_start!='' ){
                                                        $b_start=get_student_timezone($selected_date.' '.$r_data->break_start);
                                                        $b_end=get_student_timezone($selected_date.' '.$r_data->break_end);

                                                        $temp_resultTime[$r_key]->break_start = $b_start->format('l g:i a');
                                                        $temp_resultTime[$r_key]->break_end = $b_end->format('l g:i a');
                                                    }
												
                                                    if( $day==$s_date->format('l') || $day==$n_date->format('l') ){
                                                    
                                                        $results[]= $temp_resultTime[$r_key];
                                                    }
                                                }

                                                }
                                               
                                 
                                                
                                                $numR = $wpdb->num_rows;
                                                if ($numR >= 1) {
                                                    $resultTime=array();
                                                    foreach ( $results as $result ){
                                                        $resultTime[0]=$result;
                                                   $scheduleTime1 = $resultTime[0]->schedule_start;
                                                     $scheduleTime2 = $resultTime[0]->schedule_end;
                                                    $breakTime1 = $resultTime[0]->break_start;
                                                    $breakTime2 = $resultTime[0]->break_end;
                                                    $bstart = strtotime($breakTime1);
                                                    $bend = strtotime($breakTime2);
                                                    $start = strtotime($scheduleTime1);
                                                    $end = strtotime($scheduleTime2);


                                                    if (!empty($bstart) and !empty($bend)) {
                                                        /* if day has break */

                                                        for ($j = $start; $j <= $bstart - $duration; $j = $j + $duration + $paddingTime) {
                                                        	
                                                             $apptstart = date('g:i A', $j);
                                                            $apptend = date('g:i A', $j + $duration);
                                                            if ($dates == current_time('Y-m-d')) {
                                                                if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                                                    continue;
                                                                }
                                                            }
                                                                //if( date('l', $j)!=$day ){
                                                               //     continue;
                                                              //  }

                                                            /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                                            $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                            $utc_dates=$utc_date_time->format('Y-m-d');
														
                                                            $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
                                                            $book = 0;
                                                            
                                                            
                                                            //	if (in_array($s_date->format('g:i a'), $temp_resultTime))
                                                    //	{
												//	echo "Match found";
												//			 }
                                                            foreach ($rowcount as $sql) {
                                                                /* convert date utc to student for compare */
                                                                $student_date_time=get_student_timezone($dates.' '.$sql->time);
                                                                $sql->time=$student_date_time->format('g:i A');

                                                                $time = strtotime(date('g:i A', strtotime($sql->time)));
                                                                $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                    $book = 1;
                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                    $book = 1;
                                                                }
                                                            }

                                                            if ($book == 0) {
                                                                foreach ($gc_events as $gc_event) {
                                                                    $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                                                    $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                        $book = 1;
                                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                        $book = 1;
                                                                    }
                                                                }
                                                            }

                                                            if ($book == 0) {

                                                                /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                                
                                                                
                                                                $utc_dates=$utc_date_time->format('Y-m-d');
                                                                $utc_appstart=$utc_date_time->format('g:i a');

                                                                $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart' and b.duration = '$duration'");

                                                            	$all_bk = $wpdb->get_results("select $table_current_booking.time from $table_current_booking  inner join $table_customer_booking on $table_customer_booking.booking_id = $table_current_booking.id where $table_current_booking.date in ($dts) and $table_customer_booking.customer_id=$parent_id group by $table_current_booking.time");
                                                            	$all_booked = array();
                                                            	foreach($all_bk as $all_time)
                                                            	{
                                                            		$tm = $all_time->time;
                                                            		array_push($all_booked,$tm);
                                                            	}

                                                                if (empty($countAppoint[0]->sump)) {
                                                                    $booked = 0;
                                                                } else {
                                                                    $booked = $countAppoint[0]->sump;
                                                                }
                                                                
                                                                 $available = $resultS[0]->capacity - $booked;
                                                                if ($available > 0) {
                                                                    $cart_book = 0;
                                                                    if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {

                                                                        foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                                            if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                                                if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                                $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                                                $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    }


			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				
				if(''.$_SESSION['bookme'][$access_token]['student'].'' == ''.$cart_item['bookme']['student'].'')
				{
					foreach($month_dates_f as $dt)
					{	
						if(in_array($dt, $cart_item['bookme']['month_date']) && $cart_item['bookme']['appointstart'] == $apptstart)
						{
							$cart_book = 1;
						}
					}
				}

			}
                                                                    if ($cart_book == 0 && !in_array("".$utc_appstart."", $all_booked)) {
                                                                    	
                                                                    
                                                                        ?>
                                                                        <div class="ts bookmeClearFix aaa">
                                                                            <span class="ts-time">
                                                                                <input type="hidden"
                                                                                       class="appoints<?php echo $k; ?>"
                                                                                       value="<?php echo $apptstart; ?>">
                                                                                <input type="hidden"
                                                                                       class="appointe<?php echo $k; ?>"
                                                                                       value="<?php echo $apptend; ?>">
                                                                                <input type="hidden"
                                                                                       class="faculty_<?php echo $k; ?>"
                                                                                       value="<?php echo $employee; ?>">
                                                                                <span><i class="fa fa-clock-o"
                                                                                         aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $j); ?>
                                                                                    – <?php echo date_i18n(get_option('time_format'), $j + $duration); ?></span>
                                                                            
                                                                            </span>
                                                                            <span class="ts-bookappo">
                                                                                <button class=" coffe button selectcolor bookme_step2"
                                                                                        type="submit"
                                                                                        id="book_appointment"
                                                                                        data-key="<?php echo $k; ?>">

                                                                                    <span class="button-ts"><i
                                                                                                class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $j) ?>
                                                                                        – <?php echo date_i18n(get_option('time_format'), $j + $duration); ?></span>
                                                                                    <span
                                                                                            class="button-text loading<?php echo $k; ?>"><?php _e('Enroll', 'bookme'); ?></span>
                                                                                </button>
                                                                            </span>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                                $k++;
                                                            }
                                                        
                                                        	
                                                        	
                                                        	
                                                        }
                                                        

                                                        for ($l = $bend; $l <= $end - $duration; $l = $l + $duration + $paddingTime) {
                                                            $apptstart = date('l g:i A', $l);
                                                            $apptend = date('g:i A', $l + $duration);
                                                            if ($dates == current_time('Y-m-d')) {
                                                                if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                                                    continue;
                                                                }
                                                            }
                                                                //if( date('l', $l)!=$day ){
                                                              //      continue;
                                                             //   }

                                                            /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                                            $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                            $utc_dates=$utc_date_time->format('Y-m-d');

                                                            $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
                                                            $book = 0;
                                                            foreach ($rowcount as $sql) {
                                                                $time = strtotime(date('g:i A', strtotime($sql->time)));
                                                                $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                    $book = 1;
                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                    $book = 1;
                                                                }
                                                            }

                                                            if ($book == 0) {
                                                                foreach ($gc_events as $gc_event) {
                                                                    $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                                                    $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                        $book = 1;
                                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                        $book = 1;
                                                                    }
                                                                }
                                                            }

                                                            if ($book == 0) {

                                                                /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                                $utc_dates=$utc_date_time->format('Y-m-d');
                                                                $utc_appstart=$utc_date_time->format('g:i a');

                                                                $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart' and b.duration = '$duration'");

                                                            	$all_bk = $wpdb->get_results("select $table_current_booking.time from $table_current_booking  inner join $table_customer_booking on $table_customer_booking.booking_id = $table_current_booking.id where $table_current_booking.date in ($dts) and $table_customer_booking.customer_id=$parent_id group by $table_current_booking.time");
                                                            	$all_booked = array();
                                                            	foreach($all_bk as $all_time)
                                                            	{
                                                            		$tm = $all_time->time;
                                                            		array_push($all_booked,$tm);
                                                            	}

                                                                if (empty($countAppoint[0]->sump)) {
                                                                    $booked = 0;
                                                                } else {
                                                                    $booked = $countAppoint[0]->sump;
                                                                }
                                                                $available = $resultS[0]->capacity - $booked;
                                                                //if ($available > 0) {
                                                                    $cart_book = 0;
                                                                    if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {

                                                                        foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                                            if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                                                if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                                $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                                                $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    }


			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				
				if(''.$_SESSION['bookme'][$access_token]['student'].'' == ''.$cart_item['bookme']['student'].'')
				{
					foreach($month_dates_f as $dt)
					{	
						if(in_array($dt, $cart_item['bookme']['month_date']) && $cart_item['bookme']['appointstart'] == $apptstart)
						{
							$cart_book = 1;
						}
					}
				}

			}
                                                                    
                                                                    if ($cart_book == 0 && !in_array($utc_appstart, $all_booked)) {
                                                                    	
                                                                    	
                                                                        ?>
                                                                        <div class="ts bookmeClearFix bbb">
                                                                            <span class="ts-time">
                                                                                <input type="hidden"
                                                                                       class="appoints<?php echo $k; ?>"
                                                                                       value="<?php echo $apptstart; ?>">
                                                                                <input type="hidden"
                                                                                       class="appointe<?php echo $k; ?>"
                                                                                       value="<?php echo $apptend; ?>">
                                                                                <input type="hidden"
                                                                                       class="faculty_<?php echo $k; ?>"
                                                                                       value="<?php echo $employee; ?>">
                                                                                <span><i class="fa fa-clock-o"
                                                                                         aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $l); ?>
                                                                                    – <?php echo date_i18n(get_option('time_format'), $l + $duration); ?></span>
                                                                                <?php if ($resultS[0]->capacity > 1) { ?>
                                                                                    <span
                                                                                            class="ts-available"><?php echo '(' . __('Seats available', 'bookme') . ' : ' . $available . ')'; ?></span>
                                                                                <?php } ?>
                                                                            </span>
                                                                            <span class="ts-bookappo">
                                                                            	<?php if($available==0){?>
                                                                                <button <?php if($available==0){?>  style="background: grey;"<?php }?> class="<?php if($available==0){?> notavailable <?php }?> coffe button selectcolor ccc"                                                                                       type="submit"
                                                                                       
                                                                                        data-key="<?php echo $k; ?>">
                                                                                    <span class="button-ts"><i
                                                                                                class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $l) ?>
                                                                                        – <?php echo date_i18n(get_option('time_format'), $l + $duration); ?></span><span
                                                                                            class="button-text loading<?php echo $k; ?>"><?php _e('Enroll', 'bookme'); ?></span>
                                                                                </button>
                                                                                <?php }else{?>
                                                                                <button class=" coffe button selectcolor bookme_step2"
                                                                                        type="submit"
                                                                                        id="book_appointment"
                                                                                        data-key="<?php echo $k; ?>">
                                                                                    <span class="button-ts"><i
                                                                                                class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $l); ?>
                                                                                        – <?php echo date_i18n(get_option('time_format'), $l + $duration); ?></span><span
                                                                                            class="button-text loading<?php echo $k; ?>"><?php _e('Enroll', 'bookme'); ?></span>
                                                                                </button>
                                                                                <?php }?>
                                                                            </span>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                //}
                                                                $k++;
                                                            }
                                                        }

                                                    } else {
                                                        ?>
                                                        <?php
													
														$istart=1;
    										for ($j = $start; $j <= $end - $duration; $j = $j + $duration + $paddingTime) {
                                                        
                                                       	$tz = $_SESSION['tzone'];
														$st = new DateTime();
														$st->setTimestamp($j);
														$apptstart = $st->format('g:i A');

														$ed = new DateTime();
														$ed->setTimestamp($j+$duration);
														$apptend = $ed->format('g:i A');

                                                         //$apptstart = date('g:i A', $j);
                                                            
                                                            //$apptend = date('g:i A', $j + $duration);
                                                            if ($dates == current_time('Y-m-d')) {
                                                                if (strtotime(current_time('g:i A')) > strtotime($apptstart)) {
                                                                    continue;
                                                                }
                                                            }
                                                                if( date('l', $j)!=$day ){
                                                                    continue;
                                                                }

                                                            /* TODO:ANAND-time_conversion  hide time if same lecture time for other course  */
                                                            $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                            $utc_dates=$utc_date_time->format('Y-m-d');
                                                            
                                                            $rowcount = $wpdb->get_results("SELECT time,duration FROM $table_current_booking WHERE ser_id!='$service' and emp_id='$employee' and date='$utc_dates'");
															
                                                            $book = 0;
                                                            foreach ($rowcount as $sql) {
                                                                /* convert date utc to student for compare */
                                                                $student_date_time=get_student_timezone($dates.' '.$sql->time);
                                                                $sql->time=$student_date_time->format('g:i A');

                                                                $time = strtotime(date('g:i A', strtotime($sql->time)));
                                                                $etime = strtotime(date('g:i A', strtotime($sql->time) + $sql->duration));

                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                    $book = 1;
                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                    $book = 1;
                                                                }
                                                            }

                                                            if ($book == 0) {
                                                                foreach ($gc_events as $gc_event) {
                                                                    $time = strtotime(date('g:i A', strtotime($gc_event['start_date'])));
                                                                    $etime = strtotime(date('g:i A', strtotime($gc_event['end_date'])));
                                                                    if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                        $book = 1;
                                                                    } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                        $book = 1;
                                                                    }
                                                                }
                                                            }
                                                            	
                                                          //if($istart>3 && $istart<10){
                                                        //			 $book=1;
                                                        			 
                                                        //			 $j=$j+450;
                                                    	//	 } 
                                                    		
											if ($book == 0) {

                                                                /* TODO:ANAND-time_conversion convert timezone to student for displaying count appointment  */
                                                                $utc_date_time=get_utc_timezone($dates.' '.$apptstart);
                                                                $utc_dates=$utc_date_time->format('Y-m-d');
                                                                $utc_appstart=$utc_date_time->format('g:i a');
                                                                $utc_appst=$utc_date_time->format('g:i A');
//echo "SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$apptstart' and b.duration = '$duration'";
                                                                $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$utc_dates' and b.time = '$utc_appstart'");

                                                            	$all_bk = $wpdb->get_results("select $table_current_booking.time from $table_current_booking  inner join $table_customer_booking on $table_customer_booking.booking_id = $table_current_booking.id where $table_current_booking.date in ($dts) and $table_customer_booking.customer_id=$parent_id group by $table_current_booking.time");
                                                            	$all_booked = array();
                                                            	foreach($all_bk as $all_time)
                                                            	{
                                                            		$tm = $all_time->time;
                                                            		array_push($all_booked,$tm);
                                                            	}
																
                                                                if (empty($countAppoint[0]->sump)) {
                                                                    $booked = 0;
                                                                } else {
                                                                    $booked = $countAppoint[0]->sump;
                                                                }
                                                                $available = $resultS[0]->capacity - $booked;
                                                                //if ($available > 0) {
                                                                    $cart_book = 0;
                                                                    if (isset($_SESSION['bookme']['cart']) && count($_SESSION['bookme']['cart']) > 0) {
                                                                        foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                                            if ($cart['employee'] == $employee && $cart['date'] == $dates) {
                                                                                if ($cart['service'] == $service && $cart['time_s'] == $apptstart) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                                $time = strtotime(date('g:i A', strtotime($cart['time_s'])));
                                                                                $etime = strtotime(date('g:i A', strtotime($cart['time_e'])));
                                                                                if (($time >= strtotime($apptstart) && $time < strtotime($apptend)) || ($etime > strtotime($apptstart) && $etime <= strtotime($apptend))) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                } else if ((strtotime($apptstart) >= $time && strtotime($apptstart) < $etime) || (strtotime($apptend) > $time && strtotime($apptend) <= $etime)) {
                                                                                    $cart_book = 1;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    }


			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				
				if(''.$_SESSION['bookme'][$access_token]['student'].'' == ''.$cart_item['bookme']['student'].'')
				{
					foreach($month_dates_f as $dt)
					{	
						if(in_array($dt, $cart_item['bookme']['month_date']) && $cart_item['bookme']['appointstart'] == $apptstart)
						{
							$cart_book = 1;
						}
					}
				}

			}

                                                                    if ($cart_book == 0 && !in_array($utc_appstart, $all_booked)) {
                                                                    
                                                                        ?>
                                                                        <div class="ts bookmeClearFix ccc">
                                                                            <span class="ts-time">
                                                                                <input type="hidden"
                                                                                       class="appoints<?php echo $k; ?>"
                                                                                       value="<?php echo $apptstart; ?>">
                                                                                <input type="hidden"
                                                                                       class="appointe<?php echo $k; ?>"
                                                                                       value="<?php echo $apptend; ?>">
                                                                                <input type="hidden"
                                                                                       class="faculty_<?php echo $k; ?>"
                                                                                       value="<?php echo $employee; ?>">
                                                                                <span><i class="fa fa-clock-o"
                                                                                         aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $j); ?>
                                                                                    – <?php echo date_i18n(get_option('time_format'), $j + $duration); ?></span>

                                                                                <?php if ($resultS[0]->capacity > 1) { ?>
                                                                                    <span
                                                                                            class="ts-available"><?php echo '(' . __('Seats available', 'bookme') . ' : ' . $available . ')'; ?></span>
                                                                                <?php } ?>

                                                                            </span>
                                                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
                                                                            <span class="ts-bookappo">
                                                                            	<?php if($available==0){?>
                                                                                <button <?php if($available==0){?>  style="background: grey;"<?php }?> class="<?php if($available==0){?> notavailable <?php }?> coffe button selectcolor ccc"                                                                                       type="submit"
                                                                                       
                                                                                        data-key="<?php echo $k; ?>">
                                                                                    <span class="button-ts"><i
                                                                                                class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $j) ?>
                                                                                        – <?php echo date_i18n(get_option('time_format'), $j + $duration); ?></span><span
                                                                                            class="button-text loading<?php echo $k; ?>"><?php _e('Enroll', 'bookme'); ?></span>
                                                                                </button>
                                                                                <?php }else{?>
                                                                                  <button  class="coffe button selectcolor bookme_step2 ccc"  type="submit"
                                                                                        id="book_appointment"
                                                                                        data-key="<?php echo $k; ?>">
                                                                                    <span class="button-ts"><i
                                                                                                class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i>&nbsp;&nbsp;<?php echo date_i18n(get_option('time_format'), $j) ?>
                                                                                        – <?php echo date_i18n(get_option('time_format'), $j + $duration); ?></span><span
                                                                                            class="button-text loading<?php echo $k; ?>"><?php _e('Enroll', 'bookme'); ?></span>
                                                                                </button>
                                                                                <?php }?>
                                                                            </span>
                                                                           
                                                                        </div>
                                                                        <?php
                                                                    //}
                                                                    }
                                                                //}
                                                                $k++;
                                                        
                                                            }
                                                        
                                                        $newstrarttime[]=$apptstart;
                                    						$newendtime[]=$apptend;
                                    						
                                    						++$istart;
                                                        }
                                                        
                                                        ?>

                                                        <?php
                                                    }
                                                    
                                                    
                                                    }

                                                } else {
                                                    _e('No record found.', 'bookme');
                                                }
                                            } else {
                                                _e('No record found.', 'bookme');
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                    
                                }
                            $ik++;	
                           if($ik==1){
                            	break;
                            }	
                           //echo '<br>';
                            }
                            
                            
                            ?>

                        </div>
                    </div>
                    <div class="bookme-back-steps">
                        <button class="coffe button selectcolor textcolor bookme_backbtn"
                                data-key="2"><?php _e('Back', 'bookme'); ?></button>
                        <?php if ($cart_enable && count($_SESSION['bookme']['cart']) > 0) { ?>
                            <button class="bookme_cart_btn selectcolor" id="bookme_get_cart">
                                <img src="<?php echo plugins_url('../assets/images/cart.png', __FILE__) ?>" alt="">
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function bookme_get_step_3()
{
	

    $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
    if ($cart_enable && !isset($_POST['cart'])) {
        bookme_get_step_cart();
    } else {
        ?>
        <div class="bookme-header">
        	
            <div class="bookme-container">

					<div class="btn-group" role="group">
					  <a href="/live-courses/" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Courses</a>
					  <a href="<?php echo $_SESSION['ref'] ?>" class="btn btn-primary"><i class="fa fa-chevron-circle-left"></i> Back To Packages</a>
					</div>
           
                <div class="bookme-bs-wizard bookme-row" style="border-bottom:0;">
                    <?php $b = 1; ?>
                    <div
                            class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>. Start Date<?php /*echo bookme_get_table_appearance('bullet1', 'bullet', __('Start Date', 'bookme'));*/ ?>
                        </div>
                        <div class="bookme-progress selectcolor">
                            <div class="bookme-progress-bar selectcolor"></div>
                        </div>
                        <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="2"></a>
                        <?php $b++; ?>
                    </div>

                    <div
                            class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                        <!-- bookme-complete -->
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet2', 'bullet', __('Time', 'bookme')); ?>
                        </div>
                        <div class="bookme-progress selectcolor">
                            <div class="bookme-progress-bar selectcolor"></div>
                        </div>
                        <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="3"></a>
                        <?php $b++; ?>
                    </div>
                    <?php if ($cart_enable) { ?>
                        <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-complete"><!-- bookme-complete -->
                            <div class="text-center bookme-bs-wizard-stepnum">
                                <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet_cart', 'bullet', __('Cart', 'bookme')); ?>
                            </div>
                            <div class="bookme-progress selectcolor">
                                <div class="bookme-progress-bar selectcolor"></div>
                            </div>
                            <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="cart"></a>
                            <?php $b++; ?>
                        </div>
                    <?php } ?>

                    <div
                            class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-active">
                        <!-- bookme-complete -->
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet3', 'bullet', __('Sessions', 'bookme')); ?>
                        </div>
                        <div class="bookme-progress">
                            <div class="bookme-progress-bar selectcolor"></div>
                        </div>
                        <span class="bookme-bs-wizard-dot selectcolor"></span>
                        <?php $b++; ?>
                    </div>

                    <div
                            class="<?php echo $cart_enable ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-disabled">
                        <!-- bookme-active -->
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>. <?php echo bookme_get_table_appearance('bullet5', 'bullet', __('Done', 'bookme')); ?>
                        </div>
                        <div class="bookme-progress">
                            <div class="bookme-progress-bar"></div>
                        </div>
                        <a href="#" class="bookme-bs-wizard-dot"></a>
                        <?php $b++; ?>
                    </div>
                </div>
                <?php
                global $wpdb;
                $access_token = 1;
                $messag = bookme_get_table_appearance('booking_message', 'message');
                if (!isset($_POST['cart'])) {
                    $appointstart = $_POST['time1_a'];
                    $appointend = $_POST['time2_a'];
                    $access_token = $_POST['access_token'];

                    $_SESSION['bookme'][$_POST['access_token']]['time_s'] = $appointstart;
                    $_SESSION['bookme'][$_POST['access_token']]['time_e'] = $appointend;

                    $dates = $_SESSION['bookme'][$access_token]['date'];
                    $person = $_SESSION['bookme'][$access_token]['person'];
                    $service = $_SESSION['bookme'][$access_token]['service'];
                    $employee = $_SESSION['bookme'][$access_token]['employee'];
                    $student = $_SESSION['bookme'][$access_token]['student'];

                    $appointstart = $_SESSION['bookme'][$access_token]['time_s'];


                    $table_book_service = $wpdb->prefix . 'bookme_service';
                    $table_all_employee = $wpdb->prefix . 'bookme_employee';
                    $table_holidays = $wpdb->prefix . 'bookme_holidays';
                    $resultS = $wpdb->get_results("SELECT price,name,product_id FROM $table_book_service WHERE id=$service");

                    $resultE = $wpdb->get_results("SELECT name FROM $table_all_employee WHERE id=$employee");
                    $holidayresult = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
                    $holidaytable= $wpdb->prefix.'custom_holidays';
                    $commonholidays = $wpdb->get_results("SELECT * FROM $holidaytable");
                    $holidaycheck=$_SESSION['bookme'][$access_token]['month_date'];
                    $totalholiday=array();
                    $commonholidayreason=array();
                    
                    $totalcommonholidaynew=array();
                    $totalcommonholiday=array();
                    foreach($holidaycheck as $customdates){
                    	
                    	$booking_date = date("Y-m-d", strtotime($customdates));
                     foreach ($holidayresult as $holidaynew) {
		        		if ($holidaynew->holi_date == $booking_date){
		        			
		            	$totalholiday[]=$holidaynew->holi_date;
		            	array_push($totalcommonholidaynew,$holidaynew->holi_date);
		            	
		        		}
					 }
					   foreach ($commonholidays as $holidaycommon) {
		        		if ($holidaycommon->date == $booking_date){
		        			
		            	$totalcommonholiday[]=$holidaycommon->date;
		            	$commonholidayreason[]=$holidaycommon->name;
		            	
		            	array_push($totalcommonholidaynew,$holidaycommon->date);
		            	
		        		}
					 }
                    }
                 
                  
                    if(count($totalcommonholidaynew)>0){
                    for($i=1;$i<=count($totalcommonholidaynew);$i++){
                     $customdates= date("Y-m-d", strtotime($customdates));
                    
                    $pDate = strtotime($customdates .'+ '.$i.' week');
				
					array_push($holidaycheck,date('d M Y',$pDate));
                    }
                    }
                    $date = explode('-', $dates);
                    $monthNum = $date[1];
                    $dateObj = DateTime::createFromFormat('!m', $monthNum);
                    //$monthName = $dateObj->format('M');
                    $monthName = date_i18n('M', strtotime($dates));

                    $_SESSION['bookme'][$access_token]['price'] = $resultS[0]->price;
                }
               $product = wc_get_product($resultS[0]->product_id);
               $table_custom_fields = $wpdb->prefix . 'bookme_custom_field';
               $resultField = $wpdb->get_results("SELECT * FROM $table_custom_fields order by position asc");
                ?>
                <!--<a href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>-->
               <div class=="packagedetails"><div class="subjectdetails"><strong>Subject: </strong><?php echo $resultS[0]->name; ?> <br>
               <!--<strong>Number of classes: <?php echo $person * $product->get_meta( 'custom_text_field_title');?></strong>-->
                              
                                                
                                                <?php
                                                $rowcount = bookme_get_settings('enable_coupan', 'No');
                                                if (isset($_POST['cart'])) {
                                                    $totalPrice = 0;
                                                    $table_book_service = $wpdb->prefix . 'bookme_service';
                                                    foreach ($_SESSION['bookme']['cart'] as $cart) {
                                                        $service = $cart['service'];
                                                        $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id=$service");
													    $product1 = wc_get_product($resultS[0]->product_id);
                                                        $totalPrice += $product1->get_price() * $cart['person'];
                                                        //$totalPrice += $resultS[0]->price * $cart['person'];
                                                    }
                                                    if ($rowcount == 'Yes') { ?>
                                                        <div class="cart-subtotal">
                                                            <?php// _e('Subtotal', 'bookme'); ?>
                                                            
                                                    <span><?php
                                                        if (!isset($_POST['cart'])) {
                                                           // echo bookme_formatPrice($resultS[0]->price * $person * $total_bookings);
							//	echo bookme_formatPrice($product->get_price() * $person * $product->get_meta( 'custom_text_field_title'));
                                                        } else {
                                                          //  echo bookme_formatPrice($totalPrice * $product->get_meta( 'custom_text_field_title'));
                                                        }
                                                        ?></span></div>
                                                            
                                                    <?php }
                                                } else { ?>
                                                    <div class="cart-subtotal">
                                                       <?php // _e('Subtotal', 'bookme'); ?>
                                                        <td>
                                                    <span><?php
                                                        //echo bookme_formatPrice($resultS[0]->price * $person*$total_bookings);
                                                       // echo wc_price($product->get_price() * $person * $product->get_meta( 'custom_text_field_title'));
                                                        ?></span>
                                                     </div>   
                                                    
                                                    <?php
                                                }
                                                if (isset($_SESSION['bookme'][$access_token]['discount']['for_show'])) {
                                                    ?>
                                                   
                                                    <?php
                                                } else {
                                                    ?>
                                                    <?php

                                                    if ($rowcount == 'Yes') {
                                                        ?>
                                                       
                                                     
                                                    <?php }
                                                }
                                                ?>
                                               
                                                    <strong><?php _e('Total Cost: ', 'bookme'); ?></strong>
                                                    <span
                                                                    id="bookme_payment_price">
                                                                <?php
                                                                if (!isset($_POST['cart'])) {
                                                                    // echo bookme_formatPrice(isset($_SESSION['bookme'][$access_token]['discount']['price']) ? $_SESSION['bookme'][$access_token]['discount']['price'] : $resultS[0]->price*$total_bookings);
                                                                    echo wc_price($product->get_price() * $person * $product->get_meta( 'custom_text_field_title'));
                                                                } else {
                                                                    echo bookme_formatPrice($totalPrice);
                                                                    foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
                                                                        if (isset($_SESSION['bookme']['cart'][$key]['off_price'])) {
                                                                            unset($_SESSION['bookme']['cart'][$key]['dic_price']);
                                                                            unset($_SESSION['bookme']['cart'][$key]['off_price']);
                                                                            unset($_SESSION['bookme']['cart'][$key]['disc_code']);
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                </span>
                                                        
                                                        </div>
                                                   
               
               <?php if (isset($_POST['cart'])) {
               }else{
               	
               	
               }
               if (bookme_get_settings('pmt_local', 'enabled') != 'disabled' || bookme_get_settings('pmt_paypal', 'disabled') != 'disabled' || bookme_get_settings('pmt_stripe', 'disabled') != 'disabled' || (class_exists('WooCommerce') && (bookme_get_settings('enable_woocommerce', 0) != 0))) { ?>
                							<div class="studentdetailsnew">
                							
                							<?php
    								        $table = $wpdb->prefix . 'bwlive_students';
											$stu_id = $_SESSION['bookme'][$access_token]['student'];
                                        	$sql = "SELECT student_id,student_fname,student_lname,student_email FROM $table where student_id='".$stu_id."' and parent_id=".get_current_user_id();
                                        	$results = $wpdb->get_results($sql);
                                        	echo $results[0]->student_fname;
                                        	echo " ";
                                        	echo $results[0]->student_lname;
                                        	echo "<br>";
                                        	echo $results[0]->student_email;
											?>
                								
                								
                                                    <input type="hidden" readonly name="pname" id="pname"
                                                           placeholder="<?php _e('Your Name', 'bookme'); ?>"
                                                           value="<?php if (isset($_POST['pname_s'])) {
                                                               echo $_POST['pname_s'];
                                                           } ?>">   
                                                  
                                                    <input type="hidden" readonly name="email" id="email"
                                                           placeholder="<?php _e('t', 'bookme'); ?>"
                                                           value="<?php if (isset($_POST['email_s'])) {
                                                               echo $_POST['email_s'];
                                                           } ?>"> 
                                                  
         <input type="hidden" readonly name="phones" id="phones" value="<?php if (isset($_POST['phone_s'])) {
                                                               echo $_POST['phone_s'];
                                                           } ?>">
                                                           </div>
              <?php }?> 
              </div>
                <div class="bookme-row">
                    <div class="bookme-form-style-5">
                        <div class="bookme-row bookme-pad-20">
                            <div class="<?php echo (bookme_get_settings('pmt_local', 'enabled') != 'disabled' || bookme_get_settings('pmt_paypal', 'disabled') != 'disabled' || bookme_get_settings('pmt_stripe', 'disabled') != 'disabled' || (class_exists('WooCommerce', false) && (bookme_get_settings('enable_woocommerce', 0) != 0))) ? 'col-md-6 bookme-col-sm-12 bookme-col-xs-12' : 'bookme-col-xs-12'; ?>" style="width:100%">

                                <?php if (!isset($_POST['cart'])) { ?>
                                    <input type="hidden" name="access_token" value="<?php echo $access_token; ?>">
                                <?php } else { ?>
                                    <input type="hidden" name="bookme_cart" value="1">
                                <?php } ?>
                                <input type="hidden" class="hidmsg" name="hidmsg" value="<?php echo $messag; ?>">
                                <input type="hidden" class="coupan_codes" name="coupan_codes">
                                <input type="hidden" id="desc_price" name="desc_price">
                                <input type="hidden" class="hidmsg" name="student" value="<?php echo $student; ?>">

                                <div class="bookme-calender">
                                    <div class="bookme-aval-time">
                                    	
                                    	<p style="text-align: center!important; font-weight: bold; font-size: 19px;"><?php echo date("D", strtotime($holidaycheck[0])) ?> <?php  echo  date_i18n(get_option('time_format'), strtotime($appointstart)) . " to ".date_i18n(get_option('time_format'), strtotime($appointend));?> (<?php echo $_SESSION['tzone']; ?>)</p>
                                        
										 <ul class="availabledates">
                <?php
                
               $month_dates_new = array();
                foreach($holidaycheck as $fulldates){
                
                 $booking_date1= date("Y-m-d", strtotime($fulldates));
                 $booking_day= date("D", strtotime($fulldates));
                 $fulldates= date("M d, Y", strtotime($fulldates));

                if(in_array($booking_date1,$totalholiday)){ ?>
                <li class="notenroll"><span class="notenrolldates"><i style="padding-right:5px;" class="fa fa-calendar" aria-hidden="true"></i><?php echo $fulldates;?></span><span class="enrolltime"><i style="padding-right:5px;" class="fa fa-clock-o" aria-hidden="true"></i><?php echo $booking_day ?> <?php  echo  date_i18n(get_option('time_format'), strtotime($appointstart)) . " to ".date_i18n(get_option('time_format'), strtotime($appointend));?></span></li>
                		
                <?php }elseif(in_array($booking_date1,$totalcommonholiday)){
                	
                ?>
                <li class="notenrollcommon"><span class="notenrolldates"><i style="padding-right:5px;" class="fa fa-calendar" aria-hidden="true"></i><?php echo $fulldates;?></span><span class="enrolltime"><i style="padding-right:5px;" class="fa fa-clock-o" aria-hidden="true"></i><?php echo $booking_day ?> <?php  echo  date_i18n(get_option('time_format'), strtotime($appointstart)) . " to ".date_i18n(get_option('time_format'), strtotime($appointend));?></span></li>
                		
                <?php 	}else{ 
                $month_dates_new[] = date('d M Y', strtotime($fulldates)); 
                ?>
                	
                	<li class="enroll"><span class="enrolldates"><i style="padding-right:5px;" class="fa fa-calendar" aria-hidden="true"></i><?php echo $fulldates;?></span><span class="enrolltime"><i style="padding-right:5px;" class="fa fa-clock-o" aria-hidden="true"></i><?php echo $booking_day ?> <?php  echo  date_i18n(get_option('time_format'), strtotime($appointstart)) . " to ".date_i18n(get_option('time_format'), strtotime($appointend));?></span></li>
                	
               <?php }
                ?>
                	
                	
               <?php }
                $_SESSION['bookme'][$access_token]['month_date'] = $month_dates_new;
                ?>
                </ul>
                <div class="notavailabletext"><div class="leaveindicate"></div><span style="padding-left: 5px;font-weight: bold;">Tutor not available</span></div>
                 <?php if(count($commonholidayreason)){?>
                  <div class="notavailabletext"><div class="holidayindicate"></div><span style="padding-left: 5px;font-weight: bold;">Holiday(s): <?php if(count($commonholidayreason)>0){ foreach($commonholidayreason as $holidaycomm){ echo $holidaycomm.' ';} }?></span></div>
                    <?php }

                                       
                                   
                                        if ($wpdb->num_rows > 0) {
                                            $j = 5;
                                            foreach ($resultField as $rowdata) {
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'textField') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span> <?php echo $rowdata->field_name; ?>
                                                            </legend>
                                                            <input type="text" name="" class="bookme-text"
                                                                   data-name="<?php echo base64_encode($rowdata->field_name); ?>"
                                                                   data-req="<?php echo $rowdata->required; ?>"
                                                                   placeholder="<?php echo ucwords($rowdata->field_name); ?>">
                                                            <label
                                                                    class="bookme-error"><?php _e('This field is required.', 'bookme'); ?></label>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'textArea') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span> <?php echo $rowdata->field_name; ?>
                                                            </legend>
                                                            <textarea name="" class="bookme-textArea"
                                                                      data-name="<?php echo base64_encode($rowdata->field_name); ?>"
                                                                      data-req="<?php echo $rowdata->required; ?>"
                                                                      placeholder="<?php echo ucwords($rowdata->field_name); ?>"></textArea>
                                                            <label
                                                                    class="bookme-error"><?php _e('This field is required.', 'bookme'); ?></label>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'textContent') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span>
                                                            </legend>
                                                            <div><?php echo $rowdata->field_name; ?></div>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'checkboxGroup') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span> <?php echo $rowdata->field_name; ?>
                                                            </legend>
                                                            <?php
                                                            $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                                            foreach ($resultOption as $poti) {
                                                                ?>
                                                                <input type="checkbox"
                                                                       data-name="<?php echo base64_encode($rowdata->field_name); ?>"
                                                                       class="bookme-checkMe <?php echo str_replace(' ', '', $rowdata->field_name); ?>"
                                                                       name=""
                                                                       value="<?php echo $poti->field_name ?>">
                                                                <span
                                                                        class="txt-name"><?php echo $poti->field_name ?></span>
                                                                <br>
                                                                <?php
                                                            }
                                                            ?>
                                                            <input type="hidden" class="bookme-check"
                                                                   data-name="<?php echo base64_encode($rowdata->field_name); ?>"
                                                                   data-req="<?php echo $rowdata->required; ?>">
                                                            <label
                                                                    class="bookme-error"><?php _e('This field is required.', 'bookme'); ?></label>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'radioGroup') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span> <?php echo $rowdata->field_name; ?>
                                                            </legend>
                                                            <?php
                                                            $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                                            foreach ($resultOption as $poti) {
                                                                echo '<input type="radio" name="radio' . $j . '"   class="bookme-radio"  value="' . $poti->field_name . '" data-name="' . base64_encode($rowdata->field_name) . '" data-req="' . $rowdata->required . '"> ' . ucwords($poti->field_name) . '<br>';

                                                            }
                                                            ?>
                                                            <label
                                                                    class="bookme-error"><?php _e('This field is required.', 'bookme'); ?></label>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                                if (!empty($rowdata->field_type) and $rowdata->field_type == 'dropDown') {
                                                    ?>
                                                    <div class="row">
                                                        <fieldset class="bookme-mar-bot-10 bookme-pad-5">
                                                            <legend class="small"><span
                                                                        class="number selectcolor"><?php echo $j; ?></span> <?php echo $rowdata->field_name; ?>
                                                            </legend>


                                                            <select type="text" name="" class="bookme-select"
                                                                    data-name="<?php echo base64_encode($rowdata->field_name); ?>"
                                                                    data-req="<?php echo $rowdata->required; ?>">
                                                                <option
                                                                        value=""><?php echo __('Select', 'bookme') . ' ' . $rowdata->field_name; ?></option>
                                                                <?php
                                                                $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                                                foreach ($resultOption as $poti) { ?>
                                                                    <option
                                                                            value="<?php echo $poti->field_name; ?>"><?php echo $poti->field_name; ?> </option>
                                                                <?php }
                                                                ?>
                                                            </select>
                                                            <label
                                                                    class="bookme-error"><?php _e('This field is required.', 'bookme'); ?></label>
                                                        </fieldset>
                                                    </div>
                                                    <?php $j++;
                                                }
                                            }
                                        } ?>
                                    </div>
                                </div>
                                <p class="bookme-js-error"></p>
                            </div>
                            <?php if (bookme_get_settings('pmt_local', 'enabled') != 'disabled' || bookme_get_settings('pmt_paypal', 'disabled') != 'disabled' || bookme_get_settings('pmt_stripe', 'disabled') != 'disabled' || (class_exists('WooCommerce') && (bookme_get_settings('enable_woocommerce', 0) != 0))) { ?>
                                <div class="row col-md-6 bookme-col-sm-12 bookme-col-xs-12"
                                     style="padding-left: 20px">
                                    <div class="bookme-aval-time order-review">
                                       

                                        <div>
                                   
                                           
                                            <?php if (bookme_get_settings('enable_woocommerce', 0) != 1) { ?>
                                                <div class="bookme-aval-time">
                                                    <h2 class="payment-heading"><?php _e('Choose your payment method', 'bookme'); ?></h2>
                                                    <ul>
                                                        <?php if (bookme_get_settings('pmt_local', 'enabled') != 'disabled') { ?>
                                                            <li>
                                                                <input id="payment_method_local" type="radio"
                                                                       class="input-radio payment_method"
                                                                       name="payment"
                                                                       value="locally" checked="checked">

                                                                <label
                                                                        for="payment_method_local"><?php _e('Locally', 'bookme'); ?></label>

                                                                <div class="payment_box payment_method_local"
                                                                     style="display: block;">
                                                                    <p><?php _e('I will pay locally on meeting place.', 'bookme'); ?></p>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (bookme_get_settings('pmt_paypal', 'disabled') != 'disabled') { ?>
                                                            <li>
                                                                <input id="payment_method_paypal" type="radio"
                                                                       class="input-radio payment_method"
                                                                       name="payment"
                                                                       value="PayPal" <?php echo (bookme_get_settings('pmt_local', 'enabled') == 'disabled') ? 'checked' : ''; ?>>
                                                                <label
                                                                        for="payment_method_paypal"><?php _e('Paypal', 'bookme'); ?></label>

                                                                <div class="payment_box payment_method_paypal"
                                                                     style="<?php echo (bookme_get_settings('pmt_local', 'enabled') == 'disabled') ? '' : 'display: none;'; ?>">
                                                                    <p><?php _e('Instant payment using paypal', 'bookme'); ?></p>
                                                                </div>
                                                                <section id="show-me-paypal" style="display: none;">
                                                                    <form id="paypal_form" method="post">
                                                                        <input type="hidden" name="bookme_action"
                                                                               value="paypal_init">
                                                                        <input type="hidden" name="pay_access_token"
                                                                               value="<?php echo $access_token; ?>">

                                                                    </form>
                                                                </section>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (bookme_get_settings('pmt_stripe', 'disabled') != 'disabled') { ?>
                                                            <li>

                                                                <input id="payment_method_stripe" type="radio"
                                                                       class="input-radio payment_method"
                                                                       name="payment"
                                                                       value="Stripe" <?php echo ((bookme_get_settings('pmt_local', 'enabled') == 'disabled') && (bookme_get_settings('pmt_paypal', 'disabled') == 'disabled')) ? 'checked' : ''; ?>>
                                                                <label
                                                                        for="payment_method_stripe"><?php _e('Instant payment', 'bookme'); ?></label>

                                                                <div class="payment_box payment_method_stripe"
                                                                     style="<?php echo ((bookme_get_settings('pmt_local', 'enabled') == 'disabled') && (bookme_get_settings('pmt_paypal', 'disabled') == 'disabled')) ? '' : 'display: none;'; ?>">
                                                                    <p>
                                                                        <img
                                                                                src="<?php echo plugins_url('../assets/images/cards.png', __FILE__); ?>">
                                                                    </p>
                                                                    <div class="bookme_card_number">
                                                                        <label
                                                                                for="card_number_stripe"><?php _e('Card Number', 'bookme'); ?></label>
                                                                        <input type="text" name="card_number_stripe"
                                                                               id="card_number_stripe">
                                                                    </div>
                                                                    <div>
                                                                        <div class="bookme_card_cvc">
                                                                            <label
                                                                                    for="card_cvc_stripe"><?php _e('CVC', 'bookme'); ?></label>
                                                                            <input type="text"
                                                                                   name="card_cvc_stripe"
                                                                                   id="card_cvc_stripe" size="3">
                                                                        </div>
                                                                        <div class="bookme_card_dates">
                                                                            <label
                                                                                    for="card_date_stripe_month"><?php _e('Expiration Date', 'bookme'); ?></label>
                                                                            <select name="card_date_stripe_month"
                                                                                    id="card_date_stripe_month">
                                                                                <?php for ($i = 1; $i <= 12; ++$i) : ?>
                                                                                    <option
                                                                                            value="<?php echo $i ?>"><?php printf('%02d', $i) ?></option>
                                                                                <?php endfor ?>
                                                                            </select>
                                                                            <select name="card_date_stripe_year"
                                                                                    id="card_date_stripe_year">
                                                                                <?php for ($i = date('Y'); $i <= date('Y') + 10; ++$i) : ?>
                                                                                    <option
                                                                                            value="<?php echo $i ?>"><?php echo $i ?></option>
                                                                                <?php endfor ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="bookme-row">
                            <div class="col-md-12">
                                <div class="bookme-back-steps">
                                    <button class="coffe button selectcolor textcolor bookme_backbtn"
                                            data-key="<?php echo $cart_enable ? 'cart' : 3; ?>"><?php _e('Back', 'bookme'); ?></button>
                                    <button class="coffe button selectcolor textcolor nextBtn3" id="bookme_step3"
                                            type="submit"
                                    ><?php _e('Next', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                jQuery("input[name=pname]").keyup(function () {
                    jQuery("#pname").removeClass("borederColor");
                    jQuery(".error1").hide();
                });
                jQuery("input[name=email]").keyup(function () {
                    jQuery("#email").removeClass("borederColor");
                    jQuery(".error2").hide();
                });
                jQuery("input[name=phone]").keyup(function () {
                    jQuery("#phone").removeClass("borederColor");
                    jQuery(".error3").hide();
                });

            });
        </script>
        <?php
    }
}

function bookme_get_step_cart()
{
    ?>
    <div class="bookme-header">
        <div class="bookme-container">
        <a style="float:left;"  href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>
            <div class="bookme-bs-wizard bookme-row" style="border-bottom:0;">
                <?php $b = 1;
                ?>
                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-complete">
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . Start Date<?php /*echo bookme_get_table_appearance('bullet1', 'bullet', __('Start Date', 'bookme'));*/ ?>
                    </div>
                    <div class="bookme-progress selectcolor">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="2"></a>
                    <?php $b++; ?>
                </div>

                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-complete"><!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet2', 'bullet', __('Time', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress selectcolor">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot selectcolor bookme_backbtn" data-key="3"></a>
                    <?php $b++; ?>
                </div>
                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-active"><!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet_cart', 'bullet', __('Cart', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>

                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-disabled"><!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet3', 'bullet', __('Sessions', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot"></a>
                    <?php $b++; ?>
                </div>

                <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-disabled"><!-- bookme-active -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet5', 'bullet', __('Done', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar"></div>
                    </div>
                    <a href="#" class="bookme-bs-wizard-dot"></a>
                    <?php $b++; ?>
                </div>
            </div>
            <?php
            if (isset($_POST['access_token'])) {
                $appointstart = $_POST['time1_a'];
                $appointend = $_POST['time2_a'];
                $access_token = $_POST['access_token'];

                $_SESSION['bookme'][$access_token]['time_s'] = $appointstart;
                $_SESSION['bookme'][$access_token]['time_e'] = $appointend;

                // assign value in cart
                $_SESSION['bookme']['cart'][] = $_SESSION['bookme'][$access_token];
            } else if (isset($_SESSION['bookme']['back_cart'])) {
                $_SESSION['bookme']['cart'][$_SESSION['bookme']['back_cart']]['time_s'] = $_POST['time1_a'];
                $_SESSION['bookme']['cart'][$_SESSION['bookme']['back_cart']]['time_e'] = $_POST['time2_a'];
            }
            ?>
            <div class="bookme-row">
                <div class="bookme-form-style-5">
                    <div class="bookme-row bookme-pad-20">
                        <p><?php _e('A list of services selected for booking.', 'bookme'); ?></p>
                        <p><?php _e("Click 'BOOK MORE' if you want to book more services.", 'bookme'); ?></p>
                        <button class="coffe button selectcolor textcolor"
                                id="bookme_book_more"><?php _e('BOOK MORE', 'bookme'); ?></button>
                        <table class="bookme_cart_table">
                            <tr>
                                <th><?php _e("Date", 'bookme'); ?></th>
                                <th><?php _e("Time", 'bookme'); ?></th>
                                <th><?php echo bookme_get_table_appearance('employee', 'label', __('Employee', 'bookme')); ?></th>
                                <th><?php _e("Price", 'bookme'); ?></th>
                                <th><?php echo bookme_get_table_appearance('service', 'label', __('Service', 'bookme')); ?></th>
                                <th><?php _e("Actions", 'bookme'); ?></th>
                            </tr>
                            <?php
                            $price = 0;
                            global $wpdb;
                            $table_book_service = $wpdb->prefix . 'bookme_service';
                            $table_all_employee = $wpdb->prefix . 'bookme_employee';
                            foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
                                $resultS = $wpdb->get_results("SELECT price,name FROM $table_book_service WHERE id='" . $cart['service'] . "'");
                                $resultE = $wpdb->get_results("SELECT name FROM $table_all_employee WHERE id='" . $cart['employee'] . "'");
                                $price += $resultS[0]->price * $cart['person'];
                                ?>
                                <tr id="cart_row_<?php echo $key; ?>">
                                    <td><?php echo date_i18n(get_option('date_format'), strtotime($cart['date'])); ?></td>
                                    <td><?php echo date_i18n(get_option('time_format'), strtotime($cart['time_s'])); ?></td>
                                    <td><?php echo $resultE[0]->name; ?></td>
                                    <td><?php echo bookme_formatPrice($resultS[0]->price * $cart['person']); ?></td>
                                    <td><?php echo $resultS[0]->name; ?></td>
                                    <td>
                                        <button class="bookme_cart_btn selectcolor" id="bookme_delete_cart"
                                                data-key="<?php echo $key; ?>">
                                            <img
                                                    src="<?php echo plugins_url('../assets/images/delete.png', __FILE__) ?>"
                                                    alt="">
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            <tr>
                                <th colspan="3"><?php _e('Total:', 'bookme'); ?></th>
                                <th colspan="3"
                                    id="bookme_cart_total"><?php echo bookme_formatPrice($price); ?></th>
                            </tr>
                        </table>
                    </div>
                    <div class="bookme-row bookme-pad-20">
                        <div class="col-md-12">
                            <div class="bookme-back-steps">
                                <button class="coffe button selectcolor textcolor bookme_backbtn"
                                        data-key="3"><?php _e('Back', 'bookme'); ?></button>
                                <button class="coffe button selectcolor textcolor nextBtn2 bookme_step2"
                                        id="bookme_step2"
                                        type="submit"
                                ><?php _e('Next', 'bookme'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function bookme_delete_cart()
{
    if (isset($_POST['key'])) {
        if (isset($_SESSION['bookme']['cart'][$_POST['key']])) {
            $totalprice = 0;
            $price = 0;
            global $wpdb;
            $table_book_service = $wpdb->prefix . 'bookme_service';
            foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
                $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id='" . $cart['service'] . "'");
                $totalprice += $resultS[0]->price * $cart['person'];
                if ($_POST['key'] == $key) {
                    $price = $resultS[0]->price * $cart['person'];
                }
            }
            unset($_SESSION['bookme']['cart'][$_POST['key']]);
            echo bookme_formatPrice($totalprice - $price);
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }
}

function bookme_check_coupan()
{
    global $wpdb;
    $table_coupans = $wpdb->prefix . 'bookme_coupons';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $code = $_POST['applied_coupan'];
    if (!isset($_POST['cart'])) {
        $access_token = $_POST['access_token'];
        $price = $_SESSION['bookme'][$access_token]['price'];
        $per = $_SESSION['bookme'][$access_token]['person'];
        $total = $price * $per * count($_SESSION['bookme'][$access_token]['month_date']);
        $id = $_SESSION['bookme'][$access_token]['service'];
        $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and find_in_set($id,ser_id) <> 0");
        if ($wpdb->num_rows >= 1) {
            if ($resultOption[0]->coupon_used_limit < $resultOption[0]->usage_limit) {
                $dicprice = 0;
                if ($resultOption[0]->discount != 0) {
                    $dicprice = $total - ($resultOption[0]->discount * $total) / 100;
                    $_SESSION['bookme'][$access_token]['discount']['for_show'] = $resultOption[0]->discount . "%";
                    $_SESSION['bookme'][$access_token]['discount']['off_price'] = ($resultOption[0]->discount * $total) / 100;
                    $_SESSION['bookme'][$access_token]['discount']['price'] = $dicprice;
                    $_SESSION['bookme'][$access_token]['disc_code'] = $code;
                    echo bookme_formatPrice($dicprice) . '_' . (($resultOption[0]->discount * $total) / 100) . '_' . $resultOption[0]->discount . "%";
                } else {
                    $dicprice = $total - $resultOption[0]->deduction;
                    $_SESSION['bookme'][$access_token]['discount']['for_show'] = bookme_formatPrice($resultOption[0]->deduction);
                    $_SESSION['bookme'][$access_token]['discount']['off_price'] = $resultOption[0]->deduction;
                    $_SESSION['bookme'][$access_token]['discount']['price'] = $dicprice;
                    $_SESSION['bookme'][$access_token]['disc_code'] = $code;
                    echo bookme_formatPrice($dicprice) . '_' . $resultOption[0]->deduction . '_' . bookme_formatPrice($resultOption[0]->deduction);
                }
            } else {
                echo '1';
            }
        } else {
            echo '1';
        }
    } else {
        $price = 0;
        $dic = 0;
        $coupon_limit = array();
        foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
            $c = 0;
            $service = $cart['service'];
            $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id='$service'");
            $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and find_in_set($service,ser_id) <> 0");
            $total = $resultS[0]->price * $cart['person'];
            if ($wpdb->num_rows >= 1) {
                if ($resultOption[0]->coupon_used_limit < $resultOption[0]->usage_limit) {
                    if (!isset($coupon_limit[$resultOption[0]->id])) {
                        $coupon_limit[$resultOption[0]->id] = $resultOption[0]->usage_limit - 1;
                    } elseif ($coupon_limit[$resultOption[0]->id] == 0) {
                        $price += $total;
                        continue;
                    } else {
                        $coupon_limit[$resultOption[0]->id] -= 1;
                    }
                    $dic = 1;
                    $c = 1;
                    if ($resultOption[0]->discount != 0) {
                        $_SESSION['bookme']['cart'][$key]['dic_price'] = $total - ($resultOption[0]->discount * $total) / 100;
                        $_SESSION['bookme']['cart'][$key]['off_price'] = ($resultOption[0]->discount * $total) / 100;
                        $_SESSION['bookme']['cart'][$key]['disc_code'] = $code;
                    } else {
                        $_SESSION['bookme']['cart'][$key]['dic_price'] = $total - $resultOption[0]->deduction;
                        $_SESSION['bookme']['cart'][$key]['off_price'] = $resultOption[0]->deduction;
                        $_SESSION['bookme']['cart'][$key]['disc_code'] = $code;
                    }
                }
            }
            if ($c) {
                $price += $_SESSION['bookme']['cart'][$key]['dic_price'];
            } else {
                $price += $total;
            }
        }
        if ($dic == 1) {
            $_SESSION['bookme'][1]['price'] = $price;
            echo bookme_formatPrice($price);
        } else {
            echo '1';
        }
    }
}

function bookme_save_session_data()
{
	
	
    $name = $_POST['name_a'];
    $name = isset($_POST['name_a']) ? $_POST['name_a'] : '';
    $email = $_POST['mail_a'];
       $email = isset($_POST['mail_a']) ? $_POST['mail_a'] : '';
   // $phone = $_POST['phone_a'];
      $phone = isset($_POST['phone_a']) ? $_POST['phone_a'] : '';
   // $notes = $_POST['note_a'];
    $notes = isset($_POST['note_a']) ? $_POST['note_a'] : '';

    $custom_text = isset($_POST['text_a']) ? $_POST['text_a'] : array();
    $custom_textarea = isset($_POST['area_a']) ? $_POST['area_a'] : array();
    $custom_content = isset($_POST['content']) ? $_POST['content'] : array();
    $custom_checkbox = isset($_POST['check_a']) ? $_POST['check_a'] : array();
    $custom_radio = isset($_POST['radio_a']) ? $_POST['radio_a'] : array();
    $custom_select = isset($_POST['selects']) ? $_POST['selects'] : array();

    $access_token = $_POST['access_token'];

    $_SESSION['bookme'][$access_token]['name'] = $name;
    $_SESSION['bookme'][$access_token]['email'] = $email;
    $_SESSION['bookme'][$access_token]['phone'] = $phone;
    $_SESSION['bookme'][$access_token]['notes'] = $notes;

    $_SESSION['bookme'][$access_token]['custom_text'] = json_decode(wp_unslash($custom_text), true);
    $_SESSION['bookme'][$access_token]['custom_textarea'] = json_decode(wp_unslash($custom_textarea), true);
    $_SESSION['bookme'][$access_token]['custom_content'] = json_decode(wp_unslash($custom_content), true);
    $_SESSION['bookme'][$access_token]['custom_checkbox'] = json_decode(wp_unslash($custom_checkbox), true);
    $_SESSION['bookme'][$access_token]['custom_radio'] = json_decode(wp_unslash($custom_radio), true);
    $_SESSION['bookme'][$access_token]['custom_select'] = json_decode(wp_unslash($custom_select), true);

    if (bookme_get_settings('enable_woocommerce', 0) != 0 && class_exists('WooCommerce', false)) {
        echo 2;
    } else {
        echo 1;
    }

}

function bookme_book_customer()
{
    global $wpdb;

    $table_coupans = $wpdb->prefix . 'bookme_coupons';
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table = $wpdb->prefix . 'bookme_category';
    $table_enotification = $wpdb->prefix . 'bookme_email_notification';
    $table_sms_notification = $wpdb->prefix . 'bookme_sms_notification';
    $table_current_booking_fields = $wpdb->prefix . 'bookme_current_booking_fields';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_customers = $wpdb->prefix . 'bookme_customers';
    $table_payments = $wpdb->prefix . 'bookme_payments';
    $table_holidays = $wpdb->prefix . 'bookme_holidays';
    $table_customer_booking_ref = $wpdb->prefix . 'bookme_customer_booking_ref';

    $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
    if ($cart_enable) {
        $access_token = 1;
        $name = $_SESSION['bookme'][$access_token]['name'];
        $student = $_SESSION['bookme'][$access_token]['student'];
        $email = $_SESSION['bookme'][$access_token]['email'];
        $phone = $_SESSION['bookme'][$access_token]['phone'];
        $notes = $_SESSION['bookme'][$access_token]['notes'];

        $custom_text = $_SESSION['bookme'][$access_token]['custom_text'];
        $custom_textarea = $_SESSION['bookme'][$access_token]['custom_textarea'];
        $custom_content = $_SESSION['bookme'][$access_token]['custom_content'];
        $custom_checkbox = $_SESSION['bookme'][$access_token]['custom_checkbox'];
        $custom_radio = $_SESSION['bookme'][$access_token]['custom_radio'];
        $custom_select = $_SESSION['bookme'][$access_token]['custom_select'];
        add_filter('wp_mail_content_type', 'bookme_set_html_mail_content_type');

        $error = '';

        foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
            $code = isset($cart['disc_code']) ? $cart['disc_code'] : 0;
            $dic_price = isset($cart['off_price']) ? $cart['off_price'] : '';
            $appointstart = $cart['time_s'];
            $appointend = $cart['time_e'];
            $category = $cart['category'];
            $service = $cart['service'];
            $employee = $cart['employee'];
            $dates = $cart['date'];
            $person = $cart['person'];

            $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id='$service'");
            $price = $resultS[0]->price;

            $dt = new DateTime($dates);
            $booking_date = $dt->format('Y-m-d');

            $hodiday = 0;

            $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
            foreach ($result as $holiday) {
                if ($holiday->holi_date == $booking_date) {
                    $hodiday = 1;
                }
            }

            if ($hodiday == 0) {
                $rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $dates . "' and b.time = '" . $appointstart . "' and c.email='" . $email . "'");
                if ($rowcount >= 1) {
                    $error = '2';
                } else {
                    $booked = 0;
                    $resultSs = $wpdb->get_results("SELECT capacity,duration,name FROM $table_book_service WHERE id=$service and catId=$category");
                    $capacity = $resultSs[0]->capacity;
                    $duration = $resultSs[0]->duration;
                    $servname = $resultSs[0]->name;

                    $resultE = $wpdb->get_results("SELECT name,email,google_data,phone FROM $table_all_employee WHERE id=" . $employee);
                    $employee_name = $resultE[0]->name;
                    $employee_email = $resultE[0]->email;
                    $employee_phone = $resultE[0]->phone;
                    $google_data = $resultE[0]->google_data;

                    $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
                    if (empty($countAppoint[0]->sump)) {
                        $booked = 0;
                    } else {
                        $booked = $countAppoint[0]->sump;
                    }
                    $avl = 0;
                    $avl = $capacity - $booked;
                    if ($person <= $avl) {
                        if ($wpdb->insert($table_customers, array(
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone,
                            'notes' => $notes
                        ))
                        ) {
                            $customer_id = $wpdb->insert_id;
                            $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$dates' and time = '$appointstart'");
                            $gc_event_id = null;
                            if ($wpdb->num_rows > 0) {
                                $booking_id = $booking_result[0]->id;
                                $gc_event_id = $booking_result[0]->google_event_id;
                            } else {
                                $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $dates, 'time' => $appointstart, 'duration' => $duration));
                                $booking_id = $wpdb->insert_id;
                            }
                            if (isset($_POST['payment'])) {
                                $wpdb->insert($table_payments, array(
                                    'created' => current_time('mysql'),
                                    'type' => 'locally',
                                    'price' => $price,
                                    'discount_price' => $dic_price,
                                    'status' => 'pending'
                                ));
                                $payment_id = $wpdb->insert_id;
                            } else {
                                $payment_id = 0;
                            }

                            $wpdb->insert($table_customer_booking, array(
                                'customer_id' => $customer_id,
                                'booking_id' => $booking_id,
                                'payment_id' => $payment_id,
                                'no_of_person' => $person,
                                'status' => 'Approved'
                            ));
                            $customer_booking_id = $wpdb->insert_id;

                            $wpdb->insert($table_customer_booking_ref, array(
                                'customer_id' => $customer_id,
                                'student_id' => $student,
                                'booking_id' => $booking_id,
                                'cus_booking_id' => $customer_booking_id
                            ));

                            $limit = 0;
                            if ($code) {
                                $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and find_in_set($service,ser_id) <> 0");
                                if ($wpdb->num_rows >= 1) {
                                    $limit = $resultOption[0]->coupon_used_limit + 1;
                                    $wpdb->update($table_coupans, array('coupon_used_limit' => $limit), array('id' => $resultOption[0]->id), array('%s'), array('%d'));
                                }
                            }

                            $resultcompanyName = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyName'");
                            $resultcompanyAddress = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyAddress'");
                            $resultcompanyPhone = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyPhone'");
                            $resultcompanyWebsite = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyWebsite'");

                            $company_name = $resultcompanyName[0]->book_value;
                            $company_address = $resultcompanyAddress[0]->book_value;
                            $company_phone = $resultcompanyPhone[0]->book_value;
                            $company_website = $resultcompanyWebsite[0]->book_value;

                            $endtime = strtotime($appointstart);
                            $appointment_time = date_i18n(get_option('time_format'), strtotime($appointstart));
                            $appointment_end_time = date_i18n(get_option('time_format'), $endtime + $duration);

                            $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category . "");
                            $category_name = $results[0]->name;
                            $service_name = $servname;

                            $booking_date = date_i18n(get_option('date_format'), strtotime($dates));
                            $ttl_person = $person;
                            $customer_name = ucfirst($name);
                            $customer_email = $email;
                            $customer_phone = $phone;
                            $customer_note = $notes;
                            $custom_fields_text = '';
                            $custom_fields_html = '';


                            foreach ($custom_text as $custom) {
                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), $custom['value']
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), $custom['value']
                                    );
                                }
                            }
                            foreach ($custom_textarea as $custom) {

                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), $custom['value']
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), $custom['value']
                                    );
                                }
                            }
                            foreach ($custom_content as $custom) {

                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), $custom['value']
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), $custom['value']
                                    );
                                }
                            }
                            foreach ($custom_select as $custom) {

                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), $custom['value']
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), $custom['value']
                                    );
                                }
                            }
                            foreach ($custom_radio as $custom) {

                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), $custom['value']
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), $custom['value']
                                    );
                                }
                            }
                            foreach ($custom_checkbox as $custom) {

                                $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => implode(',', $custom['value']), 'status' => 'valid'));

                                if ($custom['value'] != '') {
                                    $custom_fields_text .= sprintf(
                                        "%s: %s\n",
                                        base64_decode($custom['name']), implode(',', $custom['value'])
                                    );

                                    $custom_fields_html .= sprintf(
                                        '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                        base64_decode($custom['name']), implode(',', $custom['value'])
                                    );
                                }
                            }
                            if ($custom_fields_html != '') {
                                $custom_fields_html = "<table cellspacing=0 cellpadding=0 border=0>$custom_fields_html</table>";
                            }


                            // send email and sms
                            $headers = array('From: ' . bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')) . ' <' . bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')) . '>');

                            if (bookme_get_settings('email_customer', 'true') == 'true') {
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $subject = $reslabl[0]->email_value;
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->email_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                wdm_mail_new($customer_email, $subject, $message, $headers);
                            }

                            if (bookme_get_settings('email_employee', 'true') == 'true') {
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $subject = $reslabl[0]->email_value;
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->email_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                wdm_mail_new($employee_email, $subject, $message, $headers);
                            }

                            if (bookme_get_settings('email_admin', 'true') == 'true') {
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $subject = $reslabl[0]->email_value;
                                $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->email_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                wdm_mail_new(get_bloginfo('admin_email'), $subject, $message, $headers);
                            }

                            remove_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');

                            if (bookme_get_settings('sms_customer', 'true') == 'true') {
                                $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='customer_msg' and key_type='customer_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->sms_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                bookme_send_sms_twilio($customer_phone, $message);
                            }

                            if (bookme_get_settings('sms_employee', 'true') == 'true') {
                                $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='employee_msg' and key_type='employee_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->sms_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                bookme_send_sms_twilio($employee_phone, $message);
                            }

                            if (bookme_get_settings('sms_admin', 'true') == 'true') {
                                $admin_phone = bookme_get_settings('bookme_admin_phone_no');
                                if ($admin_phone) {
                                    $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='admin_msg' and key_type='admin_confirm'";
                                    $reslabl = $wpdb->get_results($sqlblbc);
                                    $message = $reslabl[0]->sms_value;

                                    $message = str_replace("{booking_time}", $appointment_time, $message);
                                    $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                    $message = str_replace("{booking_date}", $booking_date, $message);
                                    $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                    $message = str_replace("{customer_name}", $customer_name, $message);
                                    $message = str_replace("{customer_email}", $customer_email, $message);
                                    $message = str_replace("{customer_phone}", $customer_phone, $message);
                                    $message = str_replace("{customer_note}", $customer_note, $message);
                                    $message = str_replace("{company_name}", $company_name, $message);
                                    $message = str_replace("{company_address}", $company_address, $message);
                                    $message = str_replace("{company_phone}", $company_phone, $message);
                                    $message = str_replace("{company_website}", $company_website, $message);
                                    $message = str_replace("{employee_name}", $employee_name, $message);
                                    $message = str_replace("{category_name}", $category_name, $message);
                                    $message = str_replace("{service_name}", $service_name, $message);
                                    $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                    $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                    bookme_send_sms_twilio($admin_phone, $message);
                                }
                            }

                            /* Google Calendar integration */
                            if (bookme_get_settings('bookme_gc_client_id') != null) {
                                if ($google_data) {
                                    include_once plugin_dir_path(__FILE__) . '/google.php';
                                    $bookme_gc_client = new Google_Client();
                                    $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                    $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                    try {
                                        $bookme_gc_client->setAccessToken($google_data);
                                        if ($bookme_gc_client->isAccessTokenExpired()) {
                                            $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                            $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                        }
                                        $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                        $google_calendar_id = 'primary';
                                        $gc_calendar = $bookme_gc_service->calendarList->get($google_calendar_id);
                                        $gc_access = $gc_calendar->getAccessRole();
                                        if (in_array($gc_access, array('writer', 'owner'))) {
                                            if ($gc_event_id == null) {
                                                $event_data = array(
                                                    'start' => $dates . ' ' . $appointstart,
                                                    'end' => $dates . ' ' . $appointend,
                                                    'name' => array($name),
                                                    'email' => array($email),
                                                    'phone' => array($phone),
                                                    'service' => $service_name,
                                                    'category' => $category_name,
                                                    'employee' => $employee_name,
                                                    'service_id' => $service,
                                                    'customer_id' => array($customer_id),
                                                    'booking_id' => $booking_id
                                                );
                                                $gc_event = bookme_get_event_data($event_data);

                                                $createdEvent = $bookme_gc_service->events->insert($google_calendar_id, $gc_event);

                                                $event_id = $createdEvent->getId();
                                                if ($event_id) {
                                                    $wpdb->update($table_current_booking, array('google_event_id' => $event_id), array('id' => $booking_id), array('%s'), array('%d'));
                                                }
                                            } else {
                                                $customer_result = $wpdb->get_results("select customer_id from $table_customer_booking where booking_id = $booking_id");
                                                $customer_ids = array();
                                                $name = array();
                                                $email = array();
                                                $phone = array();
                                                foreach ($customer_result as $customer_id) {
                                                    $customer_ids[] = $customer_id->customer_id;
                                                    $customer_data = $wpdb->get_results("select name,email,phone from $table_customers where id = $customer_id->customer_id");
                                                    $name[] = $customer_data[0]->name;
                                                    $email[] = $customer_data[0]->email;
                                                    $phone[] = $customer_data[0]->phone;
                                                }

                                                $event_data = array(
                                                    'start' => $dates . ' ' . $appointstart,
                                                    'end' => $dates . ' ' . $appointend,
                                                    'name' => $name,
                                                    'email' => $email,
                                                    'phone' => $phone,
                                                    'service' => $service_name,
                                                    'category' => $category_name,
                                                    'employee' => $employee_name,
                                                    'service_id' => $service,
                                                    'customer_id' => $customer_ids,
                                                    'booking_id' => $booking_id
                                                );
                                                $gc_event = bookme_get_event_data($event_data);
                                                $bookme_gc_service->events->update($google_calendar_id, $gc_event_id, $gc_event);
                                            }
                                        }
                                    } catch (Exception $e) {

                                    }
                                }
                            }

                        } else {
                            $error = '0';
                        }
                    } else {
                        $error = '3';
                    }
                }
            } else {
                $error = '4';
            }
        }
        if ($error != '') {
            echo $error;
        } else {
            unset($_SESSION['bookme']);
            echo '1';
        }
    } else {
        $access_token = $_POST['access_token'];
        $code = isset($_SESSION['bookme'][$access_token]['disc_code']) ? $_SESSION['bookme'][$access_token]['disc_code'] : '';
        $dic_price = isset($_SESSION['bookme'][$access_token]['discount']['off_price']) ? $_SESSION['bookme'][$access_token]['discount']['off_price'] : '';
        $appointstart = $_SESSION['bookme'][$access_token]['time_s'];
        $appointend = $_SESSION['bookme'][$access_token]['time_e'];
        $category = $_SESSION['bookme'][$access_token]['category'];
        $service = $_SESSION['bookme'][$access_token]['service'];
        $employee = $_SESSION['bookme'][$access_token]['employee'];
        $dates = $_SESSION['bookme'][$access_token]['date'];
        $person = $_SESSION['bookme'][$access_token]['person'];

        $name = $_SESSION['bookme'][$access_token]['name'];
        $email = $_SESSION['bookme'][$access_token]['email'];
        $phone = $_SESSION['bookme'][$access_token]['phone'];
        $notes = $_SESSION['bookme'][$access_token]['notes'];
        $price = $_SESSION['bookme'][$access_token]['price'];

        $custom_text = $_SESSION['bookme'][$access_token]['custom_text'];
        $custom_textarea = $_SESSION['bookme'][$access_token]['custom_textarea'];
        $custom_content = $_SESSION['bookme'][$access_token]['custom_content'];
        $custom_checkbox = $_SESSION['bookme'][$access_token]['custom_checkbox'];
        $custom_radio = $_SESSION['bookme'][$access_token]['custom_radio'];
        $custom_select = $_SESSION['bookme'][$access_token]['custom_select'];

        add_filter('wp_mail_content_type', 'bookme_set_html_mail_content_type');


        $booking_date = date("Y-m-d", strtotime($dates));

        $hodiday = 0;

        $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
        foreach ($result as $holiday) {
            if ($holiday->holi_date == $booking_date) {
                $hodiday = 1;
            }
        }

        if ($hodiday == 0) {
            $rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $dates . "' and b.time = '" . $appointstart . "' and c.email='" . $email . "'");
            if ($rowcount >= 1) {
                echo '2';
            } else {
                $booked = 0;
                $resultSs = $wpdb->get_results("SELECT capacity,duration,name FROM $table_book_service WHERE id=$service and catId=$category");
                $capacity = $resultSs[0]->capacity;
                $duration = $resultSs[0]->duration;
                $servname = $resultSs[0]->name;

                $resultE = $wpdb->get_results("SELECT name,email,google_data,phone FROM $table_all_employee WHERE id=" . $employee);
                $employee_name = $resultE[0]->name;
                $employee_email = $resultE[0]->email;
                $employee_phone = $resultE[0]->phone;
                $google_data = $resultE[0]->google_data;

                $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
                if (empty($countAppoint[0]->sump)) {
                    $booked = 0;
                } else {
                    $booked = $countAppoint[0]->sump;
                }
                $avl = 0;
                $avl = $capacity - $booked;
                if ($person <= $avl) {
                    if ($wpdb->insert($table_customers, array(
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'notes' => $notes
                    ))
                    ) {
                        $customer_id = $wpdb->insert_id;

                        $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$dates' and time = '$appointstart'");
                        $gc_event_id = null;
                        if ($wpdb->num_rows > 0) {
                            $booking_id = $booking_result[0]->id;
                            $gc_event_id = $booking_result[0]->google_event_id;
                        } else {
                            $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $dates, 'time' => $appointstart, 'duration' => $duration));
                            $booking_id = $wpdb->insert_id;
                        }
                        if (isset($_POST['payment'])) {
                            $wpdb->insert($table_payments, array(
                                'created' => current_time('mysql'),
                                'type' => 'locally',
                                'price' => $price,
                                'discount_price' => $dic_price,
                                'status' => 'pending'
                            ));
                            $payment_id = $wpdb->insert_id;
                        } else {
                            $payment_id = 0;
                        }


                        $wpdb->insert($table_customer_booking, array(
                            'customer_id' => $customer_id,
                            'booking_id' => $booking_id,
                            'payment_id' => $payment_id,
                            'no_of_person' => $person,
                            'status' => 'Approved'
                        ));
                        $customer_booking_id = $wpdb->insert_id;
                        $limit = 0;
                        if (isset($_SESSION['bookme'][$access_token]['disc_code'])) {
                            $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and ser_id='$service'");
                            if ($wpdb->num_rows >= 1) {
                                $limit = $resultOption[0]->coupon_used_limit + 1;
                                $wpdb->update($table_coupans, array('coupon_used_limit' => $limit), array('coupon_code' => $code, 'ser_id' => $service,), array('%s'), array('%s', '%d'));
                            }

                        }

                        $resultcompanyName = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyName'");
                        $resultcompanyAddress = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyAddress'");
                        $resultcompanyPhone = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyPhone'");
                        $resultcompanyWebsite = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyWebsite'");

                        $company_name = $resultcompanyName[0]->book_value;
                        $company_address = $resultcompanyAddress[0]->book_value;
                        $company_phone = $resultcompanyPhone[0]->book_value;
                        $company_website = $resultcompanyWebsite[0]->book_value;

                        $endtime = strtotime($appointstart);
                        $appointment_time = date_i18n(get_option('time_format'), strtotime($appointstart));
                        $appointment_end_time = date_i18n(get_option('time_format'), $endtime + $duration);

                        $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category . "");
                        $category_name = $results[0]->name;
                        $service_name = $servname;

                        $booking_date = date_i18n(get_option('date_format'), strtotime($dates));
                        $ttl_person = $person;
                        $customer_name = ucfirst($name);
                        $customer_email = $email;
                        $customer_phone = $phone;
                        $customer_note = $notes;
                        $custom_fields_text = '';
                        $custom_fields_html = '';

                        foreach ($custom_text as $custom) {
                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), $custom['value']
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), $custom['value']
                                );
                            }
                        }
                        foreach ($custom_textarea as $custom) {

                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), $custom['value']
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), $custom['value']
                                );
                            }
                        }
                        foreach ($custom_content as $custom) {

                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), $custom['value']
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), $custom['value']
                                );
                            }
                        }
                        foreach ($custom_select as $custom) {

                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), $custom['value']
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), $custom['value']
                                );
                            }
                        }
                        foreach ($custom_radio as $custom) {

                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), $custom['value']
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), $custom['value']
                                );
                            }
                        }
                        foreach ($custom_checkbox as $custom) {

                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => implode(',', $custom['value']), 'status' => 'valid'));

                            if ($custom['value'] != '') {
                                $custom_fields_text .= sprintf(
                                    "%s: %s\n",
                                    base64_decode($custom['name']), implode(',', $custom['value'])
                                );

                                $custom_fields_html .= sprintf(
                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                    base64_decode($custom['name']), implode(',', $custom['value'])
                                );
                            }
                        }
                        if ($custom_fields_html != '') {
                            $custom_fields_html = "<table cellspacing=0 cellpadding=0 border=0>$custom_fields_html</table>";
                        }

                        $headers = array('From: ' . bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')) . ' <' . bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')) . '>');

                        if (bookme_get_settings('email_customer', 'true') == 'true') {
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $subject = $reslabl[0]->email_value;
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $message = $reslabl[0]->email_value;

                            $message = str_replace("{booking_time}", $appointment_time, $message);
                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                            $message = str_replace("{booking_date}", $booking_date, $message);
                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                            $message = str_replace("{customer_name}", $customer_name, $message);
                            $message = str_replace("{customer_email}", $customer_email, $message);
                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                            $message = str_replace("{customer_note}", $customer_note, $message);
                            $message = str_replace("{company_name}", $company_name, $message);
                            $message = str_replace("{company_address}", $company_address, $message);
                            $message = str_replace("{company_phone}", $company_phone, $message);
                            $message = str_replace("{company_website}", $company_website, $message);
                            $message = str_replace("{employee_name}", $employee_name, $message);
                            $message = str_replace("{category_name}", $category_name, $message);
                            $message = str_replace("{service_name}", $service_name, $message);
                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                            wp_mail($customer_email, $subject, $message, $headers);
                        }

                        if (bookme_get_settings('email_employee', 'true') == 'true') {
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $subject = $reslabl[0]->email_value;
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $message = $reslabl[0]->email_value;

                            $message = str_replace("{booking_time}", $appointment_time, $message);
                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                            $message = str_replace("{booking_date}", $booking_date, $message);
                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                            $message = str_replace("{customer_name}", $customer_name, $message);
                            $message = str_replace("{customer_email}", $customer_email, $message);
                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                            $message = str_replace("{customer_note}", $customer_note, $message);
                            $message = str_replace("{company_name}", $company_name, $message);
                            $message = str_replace("{company_address}", $company_address, $message);
                            $message = str_replace("{company_phone}", $company_phone, $message);
                            $message = str_replace("{company_website}", $company_website, $message);
                            $message = str_replace("{employee_name}", $employee_name, $message);
                            $message = str_replace("{category_name}", $category_name, $message);
                            $message = str_replace("{service_name}", $service_name, $message);
                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                            wp_mail($employee_email, $subject, $message, $headers);
                        }

                        if (bookme_get_settings('email_admin', 'true') == 'true') {
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $subject = $reslabl[0]->email_value;
                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $message = $reslabl[0]->email_value;

                            $message = str_replace("{booking_time}", $appointment_time, $message);
                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                            $message = str_replace("{booking_date}", $booking_date, $message);
                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                            $message = str_replace("{customer_name}", $customer_name, $message);
                            $message = str_replace("{customer_email}", $customer_email, $message);
                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                            $message = str_replace("{customer_note}", $customer_note, $message);
                            $message = str_replace("{company_name}", $company_name, $message);
                            $message = str_replace("{company_address}", $company_address, $message);
                            $message = str_replace("{company_phone}", $company_phone, $message);
                            $message = str_replace("{company_website}", $company_website, $message);
                            $message = str_replace("{employee_name}", $employee_name, $message);
                            $message = str_replace("{category_name}", $category_name, $message);
                            $message = str_replace("{service_name}", $service_name, $message);
                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                            wp_mail(get_bloginfo('admin_email'), $subject, $message, $headers);
                        }

                        remove_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');


                        if (bookme_get_settings('sms_customer', 'true') == 'true') {
                            $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='customer_msg' and key_type='customer_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $message = $reslabl[0]->sms_value;

                            $message = str_replace("{booking_time}", $appointment_time, $message);
                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                            $message = str_replace("{booking_date}", $booking_date, $message);
                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                            $message = str_replace("{customer_name}", $customer_name, $message);
                            $message = str_replace("{customer_email}", $customer_email, $message);
                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                            $message = str_replace("{customer_note}", $customer_note, $message);
                            $message = str_replace("{company_name}", $company_name, $message);
                            $message = str_replace("{company_address}", $company_address, $message);
                            $message = str_replace("{company_phone}", $company_phone, $message);
                            $message = str_replace("{company_website}", $company_website, $message);
                            $message = str_replace("{employee_name}", $employee_name, $message);
                            $message = str_replace("{category_name}", $category_name, $message);
                            $message = str_replace("{service_name}", $service_name, $message);
                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                            $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                            bookme_send_sms_twilio($customer_phone, $message);
                        }

                        if (bookme_get_settings('sms_employee', 'true') == 'true') {
                            $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='employee_msg' and key_type='employee_confirm'";
                            $reslabl = $wpdb->get_results($sqlblbc);
                            $message = $reslabl[0]->sms_value;

                            $message = str_replace("{booking_time}", $appointment_time, $message);
                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                            $message = str_replace("{booking_date}", $booking_date, $message);
                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                            $message = str_replace("{customer_name}", $customer_name, $message);
                            $message = str_replace("{customer_email}", $customer_email, $message);
                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                            $message = str_replace("{customer_note}", $customer_note, $message);
                            $message = str_replace("{company_name}", $company_name, $message);
                            $message = str_replace("{company_address}", $company_address, $message);
                            $message = str_replace("{company_phone}", $company_phone, $message);
                            $message = str_replace("{company_website}", $company_website, $message);
                            $message = str_replace("{employee_name}", $employee_name, $message);
                            $message = str_replace("{category_name}", $category_name, $message);
                            $message = str_replace("{service_name}", $service_name, $message);
                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                            $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                            bookme_send_sms_twilio($employee_phone, $message);
                        }

                        if (bookme_get_settings('sms_admin', 'true') == 'true') {
                            $admin_phone = bookme_get_settings('bookme_admin_phone_no');
                            if ($admin_phone) {
                                $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='admin_msg' and key_type='admin_confirm'";
                                $reslabl = $wpdb->get_results($sqlblbc);
                                $message = $reslabl[0]->sms_value;

                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                $message = str_replace("{booking_date}", $booking_date, $message);
                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                $message = str_replace("{customer_name}", $customer_name, $message);
                                $message = str_replace("{customer_email}", $customer_email, $message);
                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                $message = str_replace("{customer_note}", $customer_note, $message);
                                $message = str_replace("{company_name}", $company_name, $message);
                                $message = str_replace("{company_address}", $company_address, $message);
                                $message = str_replace("{company_phone}", $company_phone, $message);
                                $message = str_replace("{company_website}", $company_website, $message);
                                $message = str_replace("{employee_name}", $employee_name, $message);
                                $message = str_replace("{category_name}", $category_name, $message);
                                $message = str_replace("{service_name}", $service_name, $message);
                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                bookme_send_sms_twilio($admin_phone, $message);
                            }
                        }


                        /* Google Calendar integration */
                        if (bookme_get_settings('bookme_gc_client_id') != null) {
                            if ($google_data) {
                                include_once plugin_dir_path(__FILE__) . '/google.php';
                                $bookme_gc_client = new Google_Client();
                                $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                try {
                                    $bookme_gc_client->setAccessToken($google_data);
                                    if ($bookme_gc_client->isAccessTokenExpired()) {
                                        $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                        $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                    }
                                    $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                    $google_calendar_id = 'primary';
                                    $gc_calendar = $bookme_gc_service->calendarList->get($google_calendar_id);
                                    $gc_access = $gc_calendar->getAccessRole();
                                    if (in_array($gc_access, array('writer', 'owner'))) {
                                        if ($gc_event_id == null) {
                                            $event_data = array(
                                                'start' => $dates . ' ' . $appointstart,
                                                'end' => $dates . ' ' . $appointend,
                                                'name' => array($name),
                                                'email' => array($email),
                                                'phone' => array($phone),
                                                'service' => $service_name,
                                                'category' => $category_name,
                                                'employee' => $employee_name,
                                                'service_id' => $service,
                                                'customer_id' => array($customer_id),
                                                'booking_id' => $booking_id
                                            );
                                            $gc_event = bookme_get_event_data($event_data);

                                            $createdEvent = $bookme_gc_service->events->insert($google_calendar_id, $gc_event);

                                            $event_id = $createdEvent->getId();
                                            if ($event_id) {
                                                $wpdb->update($table_current_booking, array('google_event_id' => $event_id), array('id' => $booking_id), array('%s'), array('%d'));
                                            }
                                        } else {
                                            $customer_result = $wpdb->get_results("select customer_id from $table_customer_booking where booking_id = $booking_id");
                                            $customer_ids = array();
                                            $name = array();
                                            $email = array();
                                            $phone = array();
                                            foreach ($customer_result as $customer_id) {
                                                $customer_ids[] = $customer_id->customer_id;
                                                $customer_data = $wpdb->get_results("select name,email,phone from $table_customers where id = $customer_id->customer_id");
                                                $name[] = $customer_data[0]->name;
                                                $email[] = $customer_data[0]->email;
                                                $phone[] = $customer_data[0]->phone;
                                            }

                                            $event_data = array(
                                                'start' => $dates . ' ' . $appointstart,
                                                'end' => $dates . ' ' . $appointend,
                                                'name' => $name,
                                                'email' => $email,
                                                'phone' => $phone,
                                                'service' => $service_name,
                                                'category' => $category_name,
                                                'employee' => $employee_name,
                                                'service_id' => $service,
                                                'customer_id' => $customer_ids,
                                                'booking_id' => $booking_id
                                            );
                                            $gc_event = bookme_get_event_data($event_data);
                                            $bookme_gc_service->events->update($google_calendar_id, $gc_event_id, $gc_event);
                                        }
                                    }
                                } catch (Exception $e) {

                                }
                            }
                        }

                        unset($_SESSION['bookme']);
                        echo '1';

                    } else {
                        echo '0';
                    }
                } else {
                    echo '3';
                }
            }
        } else {
            echo '4';
        }
    }
}

function bookme_get_step_5($msg = null)
{
    $msg = isset($_POST['message']) ? $_POST['message'] : $msg;
    $heading = isset($_POST['heading']) ? $_POST['heading'] : __('Appointment booked.', 'bookme');
    ?>
    <div class="bookme-header">
        <div class="bookme-container">
        <a href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>
            <div class="bookme-bs-wizard bookme-row" style="border-bottom:0;">
                <?php $b = 1;
                $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
                ?>
                <div
                        class="<?php echo bookme_get_settings('bookme_enable_cart', 0) ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . Start Date<?php /*echo bookme_get_table_appearance('bullet1', 'bullet', __('Start Date', 'bookme'));*/ ?>
                    </div>
                    <div class="bookme-progress selectcolor">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>

                <div
                        class="<?php echo bookme_get_settings('bookme_enable_cart', 0) ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                    <!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet2', 'bullet', __('Time', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>
                <?php if (bookme_get_settings('bookme_enable_cart', 0)) { ?>
                    <div class="bookme-col-xs-2 bookme-bs-wizard-step bookme-complete"><!-- bookme-complete -->
                        <div class="text-center bookme-bs-wizard-stepnum">
                            <?php echo $b; ?>
                            . <?php echo bookme_get_table_appearance('bullet_cart', 'bullet', __('Cart', 'bookme')); ?>
                        </div>
                        <div class="bookme-progress">
                            <div class="bookme-progress-bar selectcolor"></div>
                        </div>
                        <span class="bookme-bs-wizard-dot selectcolor"></span>
                        <?php $b++; ?>
                    </div>
                <?php } ?>

                <div
                        class="<?php echo bookme_get_settings('bookme_enable_cart', 0) ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-complete">
                    <!-- bookme-complete -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet3', 'bullet', __('Sessions', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>

                <div
                        class="<?php echo bookme_get_settings('bookme_enable_cart', 0) ? 'bookme-col-xs-2' : 'bookme-col-xs-3'; ?> bookme-bs-wizard-step bookme-active">
                    <!-- bookme-active -->
                    <div class="text-center bookme-bs-wizard-stepnum">
                        <?php echo $b; ?>
                        . <?php echo bookme_get_table_appearance('bullet5', 'bullet', __('Done', 'bookme')); ?>
                    </div>
                    <div class="bookme-progress">
                        <div class="bookme-progress-bar selectcolor"></div>
                    </div>
                    <span class="bookme-bs-wizard-dot selectcolor"></span>
                    <?php $b++; ?>
                </div>
            </div>
<a href="<?php echo site_url();?>/live-courses/"><i class="fa fa-chevron-circle-left"></i>Back To Coursess</a>
            <div class="bookme-row">
                <div class="bookme-form-style-5">
                    <div class="bookme-calender bookme-pad-20">
                        <div class="bookme-aval-time">
                            <h2><?php echo $heading; ?> </h2>

                            <p class="bm-row bookme-success-msg">
                                <?php
                                echo $msg;
                                ?></p>
                        </div>
                        <div align="right">
                            <button type="button" class="coffe button selectcolor textcolor"
                                    onClick="window.parent.location='//' + location.host + location.pathname"><?php _e('New Booking', 'bookme'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

