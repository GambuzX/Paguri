<?php

include_once('../database/residence_queries.php');

?>

<?php function draw_residence_summary($residence)
{

    $typeStr = getResidenceTypeWithID($residence['type']);

    ?>

    <section class="result">

        <section class="image">
            <img src="../resources/house_image_test.jpeg">
        </section>

        <section class="info">
            <h1 class="info_title"><?= $residence['title'] ?> </h1>
            <h2 class="info_type_and_location"><?= $typeStr.' &#8226 '.$residence['location'] ?></h2>

            <?php

                // TODO Change this
                $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vulputate eu tortor quis rutrum. Cras tincidunt turpis et euismod condimentum. Praesent eget tempus erat. Morbi id bibendum eros. Vivamus sit amet commodo nisl, et imperdiet est. Cras id lacus quis purus convallis dignissim luctus et ante. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed imperdiet mauris tellus, non efficitur ante aliquam vitae.';

                $descriptionTrimmed = strlen($description) > 160 ? substr($description,0,160)."..." : $description;
            ?>
                <p class="info_description">  <?=$descriptionTrimmed ?></p>
                <p class="info_ppd"><?= $residence['pricePerDay'] ?> </p>
                <p class="info_score">4.5&#9733 </p>
                <p class="info_capacity"> <?= $residence['capacity'] ?></p>
                <p class="info_bedrooms"> <?= $residence['nBedrooms'] ?></p>

        </section>

    </section>
<?php
}
?>

<?php

function draw_main()
{ ?>

    <section id="main">
        <?php
            draw_left_side();
            draw_right_side();
            ?>

    </section>

<?php }

function draw_left_side()
{
    // TODO: change this to reflect the search results
    $result_residences = getAllResidences();
    ?>

    <section id="left_side">

        <header>
            <h1>Showing places near '<?= $_GET["location"] ?>'</h1>
            <h2><?= count($result_residences) ?> resuls found (Wow!) </h2>
        </header>

        <section id="results">
            <?php
                foreach ($result_residences as $residence) {
                    draw_residence_summary($residence);
                }

                // draw_residence_summary(($result_residences[1]))
                ?>

        </section>

    </section>

<?php }

function draw_right_side()
{ ?>

    <section id="right_side">
        <section id="filters">
            <section id="comodities">

            </section>
        </section>
        <section id="map">
            <img src="../resources/map_example.png" />
        </section>
    </section>
<?php }

?>