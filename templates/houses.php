<?php
include_once('../database/residence_queries.php');
include_once('../database/user_queries.php');
?>

<?php function draw_add_house()
{ ?>
    <section id="add_place" class="card">
        <h1>Add place</h1>
        <form id="add_place_form" action="" method="post">
            <input id="user_id" type="hidden" value=<?= getUserID($_SESSION['username']) ?>>
            <section class="form_entry" id="title">
                <label for="title_input">Title</label>
                <input id="title_input" type="text" name="title" value="">
            </section>
            <section class="form_entry" id="house_type">
                <label for="house_type_input">Type</label>
                <select id="house_type_input" name="type">
                    <?php draw_house_type_options() ?>
                </select>
            </section>
            <section class="form_entry" id="price">
                <label for="price_input">Price per night</label>
                <input type="number" id="price_input" value="1" min="1">
            </section>
            <section class="form_entry" id="description">
                <label for="description_input">Description</label>
                <textarea id="description_input" type="text" name="description" rows="6"></textarea>
            </section>
            <section class="form_entry" id="location_wrapper">
                <label for="location">Location</label>
                <input id="location" type="text" name="location" value="">
            </section>
            <section id="map"></section>
            <section class="form_entry" id="commodities">
                <label>Commodities</label>
                <section id="commodity_list">
                    <?php draw_commodity_checkboxes() ?>
                </section>
            </section>
            <section class="form_entry" id="capacity">
                <label for="capacity_input">Capacity</label>
                <input id="capacity_input" type="number" name="capacity" value="1" min="0" max="10" step="1">
            </section>
            <section class="form_entry" id="num_bedrooms">
                <label for="num_bedrooms_input">Number of bedrooms </label>
                <input id="num_bedrooms_input" type="number" name="num-bedrooms" value="1" min="0" max="10" step="1">
            </section>
            <section class="form_entry" id="num_bathrooms">
                <label for="num_bathrooms_input">Number of bathrooms</label>
                <input id="num_bathrooms_input" type="number" name="num-bathrooms" value="1" min="0" max="10" step="1">
            </section>
            <section class="form_entry" id="num_beds">
                <label for="num_beds_input">Number of beds</label>
                <input id="num_beds_input" type="number" name="num-beds" value="1" min="0" max="10" step="1">
            </section>

            <input id="latitude" type="hidden">
            <input id="longitude" type="hidden">
            <input id="city" type="hidden">
            <input id="country" type="hidden">
            <input class="button" id="submit_button" type="submit" value="Add">
        </form>
    </section>
<?php } ?>

<?php
    function draw_house_type_options()
    {
        $types = getResidenceTypes();

        foreach ($types as $type) {
?>
        <option value=<?= $type['residenceTypeID'] ?>><?= ucfirst($type['name']) ?></option>
<?php
        }
    }
?>


<?php
    function draw_commodity_checkboxes()
    {
        $commodities = getAllCommodities();
        foreach ($commodities as $commodity) {
?>
        <label>
            <input type="checkbox" class="commodities" name="commodities[]" value="<?= $commodity['commodityID'] ?>"> <?= ucfirst($commodity['name']) ?>
        </label>
<?php
        }
    }
?>


<?php
function draw_list_user_places($userID)
{
    $user = getUserInfoById($userID);
    $userLoggedIn = (isset($_SESSION['username']) and $_SESSION['username'] == $user['username']);
    $places = getUserResidences($userID);
?>
    <section id="places_list" class="card">

        <?php if ($userLoggedIn) { ?>
            <div class="my_places_title">
                <h1>My places</h1>
                <a class="button" href="add_house.php">Add place</a>
            </div>
        <?php } else { ?>
            <h1><?= $user['firstName'] ?> <?= $user['lastName'] ?>'s places</h1>
        <?php }
            if ($places == 0) {
        ?>
            <p class="empty_message">No listed places yet.</p>
            <?php } else {

            foreach ($places as $place) { ?>
                <section class="places_list_entry">
                    <?php
                        draw_place_summary($place);
                        if ($userLoggedIn)
                            draw_place_operations($place);
                    ?>
                </section>
        <?php }
                                                                                                                    } ?>
    </section>
<?php
                                                                                                                }
?>

<?php
                                                                                                                function draw_place_summary($place)
                                                                                                                {
?>
    <section class="result">
        <section class="image">
            <img src="../resources/house_image_test.jpeg">
        </section>
        <section class="info">
            <h1 class="info_title"><?= $place['title'] ?> </h1>
            <h2 class="info_type_and_location"><?= $place['type'] . ' &#8226 ' . $place['address'] ?></h2>
            <p class="info_description"> <?= $place['description'] ?></p>
            <p class="info_ppd"><?= $place['pricePerDay'] ?></p>
            <p class="info_score">4.5</p>
            <p class="info_capacity"> <?= $place['capacity'] ?></p>
            <p class="info_bedrooms"> <?= $place['nBedrooms'] ?></p>
        </section>
    </section>
<?php
    }
?>

<?php
    function draw_place_operations($place) {
?>
    <section class="place_operations">
        <input type="hidden" value=<?= $place['residenceID'] ?>>
        <a class="button" id="" href="">Add availability</a>
        <a class="button" href="">Reservations</a>
        <a class="button" href="../pages/edit_place.php?id=<?=$place['residenceID'] ?>">Edit</a>
        <button class="button remove_reservation">Remove</button>
    </section>
<?php } ?>


<?php
function draw_reservations($reservations)
{
?>
    <section id="user_reservations" class="card">
        <h1>My reservations</h1>
    </section>
<?php
}
?>



<?php
function draw_edit_place($place)
{
    $photos = getResidencePhotos($place['residenceID']);
?>
    <section class="card" id="edit_place">
        <h1>Edit place information</h1>

        <form id="edit_place_form" action="" method="">
            <section class="form_entry" id="title">
                <label for="title_input">Title</label>
                <input id="title_input" type="text" name="title" value="<?= $place['title'] ?>">
            </section>
            <section class="form_entry" id="description">
                <label for="description_input">Description</label>
                <textarea id="description_input" type="text" name="description" rows="6"><?= $place['description'] ?></textarea>
            </section>

            <input id="user_id" type="hidden" value="<?= $place['owner'] ?>">
            <input id="place_id" type="hidden" value="<?= $place['residenceID'] ?>">
            <input id="commodities" type="hidden" value="<?= getCommoditiesAsKeysString($place['residenceID']) ?>">
            <input id="capacity" type="hidden" value="<?= $place['capacity'] ?>">
            <input id="num_bathrooms" type="hidden" value="<?= $place['nBathrooms'] ?>">
            <input id="num_bedrooms" type="hidden" value="<?= $place['nBedrooms'] ?>">
            <input id="num_beds" type="hidden" value="<?= $place['nBeds'] ?>">
            <input id="location" type="hidden" value="<?= $place['address'] ?>">
            <input id="price" type="hidden" value="<?= $place['pricePerDay'] ?>">
            <input id="type" type="hidden" value="<?= $place['residenceTypeID'] ?>">
            <input id="latitude" type="hidden" value="<?= $place['latitude'] ?>">
            <input id="longitude" type="hidden" value="<?= $place['longitude'] ?>">
            <input id="city" type="hidden" value="<?= $place['country'] ?>">
            <input id="country" type="hidden" value="<?= $place['city'] ?>">

            <section id="edit_place_images">
                <?php foreach ($photos as $photo) { ?>
                    <section class="image_preview" id="<?= $photo['photoID'] ?>">
                        <img src="../images/properties/medium/<?= $photo['filepath'] ?>">
                        <div class="remove_image fas fa-trash-alt"></div>
                    </section>
                <?php } ?>
                <label class="choose_photo button fas fa-plus">
                    <input class="choose_photo_input" type="file" name="image" multiple>
                </label>
            </section>

            <input class="button" id="submit_button" type="submit" value="Update">
        </form>
    </section>
<?php
}
?>