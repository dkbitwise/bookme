<?php
$colour = bookme_get_table_appearance('booking_color', 'color');
$colourtxt = bookme_get_table_appearance('booking_colortxt', 'color');

if (!empty($colour)) {
?>
<style>
    .selectcolor {  background: <?php echo $colour;?> !important;  }
    .selectcolorbtn {  background: <?php echo $colour;?> !important;  border: 1px solid <?php echo $colour;?> !important;  color: <?php echo $colourtxt;?> !important;  }
    .loader-color.bookme-loader:before {  border-left-color: <?php echo $colour;?> !important;  border-right-color: <?php echo $colour;?> !important;  border-top-color: <?php echo $colour;?> !important;  }
    .bookme .bookme-controls {  background: <?php echo $colour;?> !important;  }
    .today {  background: <?php echo $colour;?> !important;  }
    .cal1 .bookme .bookme-table tr .day:hover {  background-color: <?php echo $colour;?> !important;  }
    .cal1 .bookme .bookme-table tr .next-month:hover {  background-color: <?php echo $colour;?>;  }
    span.number.selectcolor {  color: <?php echo $colourtxt;?> !important;  }
    .month {  color: <?php echo $colourtxt;?> !important;  }
    .bookme-bs-wizard > .bookme-bs-wizard-step > .bookme-bs-wizard-dot:after {  background: <?php echo $colourtxt;?> !important;  }
</style>
<?php
}
?>
<div id="bookme_container"><div class="column_right_grid_loading"><div class="loader-color bookme-loader"></div></div></div>
<?php
    if(isset($_GET['status'])){
        $status = 'success';
    }else if(isset($_GET['bookme_action']) && $_GET['bookme_action'] == 'error'){
        $status = $_GET['error_msg'];
    }else{
        $status = 'init';
    }
    $day_limit = bookme_get_settings('bookmeDayLimit',365);
?>
<script>var status = '<?php echo $status; ?>'; var cal_sdate = '<?php echo date( 'Y-m-d', current_time( 'timestamp' ) ); ?>'; var day_limit = <?php echo $day_limit ?>; var current_lang = '<?php echo get_locale() ?>';</script>