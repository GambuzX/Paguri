<?php function draw_search_box()
{ ?>

    <section id="search_box" class="formCard">
        <h1>Find a dream place to stay anywhere.</h1>
        <form action="search_results.php" method="get">
            <section class="form_entry" id="location">
                <label for="location_input">Where to?</label>
                <input id="location_input" type="text" name="location" placeholder="Anywhere" required>
            </section>
            <section class="form_entry" id="check_in">
                <label for="checkin_input">Check-in</label>
                <input id="checkin_input" type="date" name="checkin_date" placeholder="dd-mm-yyyy" value="" required>
            </section>
            <section class="form_entry" id="check_out">
                <label for="checkout_input">Checkout</label>
                <input id="checkout_input" type="date" name="checkin_date" placeholder="dd-mm-yyyy" value="" required>
            </section>
            <section class="form_entry" id="guests">
                <label for="guests_input">Guests</label>
                <input id="guests_input" type="number" name="guest_cnt" value="1" min="0" max="10" step="1">
            </section>
            <input id="submit_button" class="button" type="submit" value="Search">
        </form>

    </section>

<?php } ?>