<?php
    include_once('../database/connection.php');
    include_once('../database/comment_queries.php');

    function getResidenceReservations($residenceID) {
        global $dbh;

        $stmt = $dbh->prepare(
            'SELECT *
            FROM reservation
            WHERE lodge = ?');
        $stmt->execute(array($residenceID));
        return $stmt->fetchAll();
    }

    function deleteReservation($reservationID) {
        global $dbh;

        $stmt = $dbh->prepare('DELETE FROM reservation WHERE reservationID = ?');
        $stmt->execute(array($reservationID));
    }

    function deleteReservationComments($reservationID) {
        $comments = getReservationComments($reservationID);
        foreach($comments as $comment) {
            deleteComment($comment['commentID']);
        }
    }

    function getUserReservations($username) {
        global $dbh;

        $stmt = $dbh->prepare('SELECT *
                               FROM reservation, user, residence
                               WHERE username =  ? and customer = userID and lodge = residenceID');
        $stmt->execute(array($username));

        return $stmt->fetchAll();
    }

?>