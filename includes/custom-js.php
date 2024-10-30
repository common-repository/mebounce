<script>
jQuery(document).ready(function($) {
    var _ouibounce = ouibounce(document.getElementById('mebounce-modal'), {
        aggressive: <?php echo $aggressive; ?>,
        <?php echo $chain_output; ?>
        timer: 0,
        sitewide: true,
    });

    $('.underlay').on('click', function() {
        $('#mebounce-modal').hide();
        //_ouiBounce.disable({ cookieExpire: 5, sitewide: true });
    });

    $('#mebounce-modal .modal-footer').on('click', function() {
        $('#mebounce-modal').hide();
        //_ouiBounce.disable({ cookieExpire: 5, sitewide: true });
    });

    $('#mebounce-modal .modal').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>