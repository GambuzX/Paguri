<?php
    include_once('../includes/config.php');
    include_once('../templates/common/header.php');
    include_once('../templates/common/footer.php');

    draw_header('search_results');

    draw_not_found();

    draw_footer();
?>


<?php function draw_not_found() { ?>

    <section id="not_found_card" >
        <h1> Page not found :( </h1>
    </section>

<?php } ?>