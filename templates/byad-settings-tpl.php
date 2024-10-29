<div class="wrap">
    <h2><?php _e('Before You Are Dead Countdown', 'byad-countdown');?></h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('byad-countdown-group'); ?>
        <?php @do_settings_fields('byad-countdown-group'); ?>

        <?php do_settings_sections('byad-countdown'); ?>

        <?php @submit_button(); ?>
    </form>
</div>