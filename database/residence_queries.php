<?php
    include_once('../database/connection.php');
    include_once('../database/comment_queries.php');
    include_once('../database/reservation_queries.php');

    function getAllResidences()
    {
        global $dbh;

        $stmt = $dbh->prepare(
            'SELECT residence.*, residencetype.name as typeStr , rating
                FROM residence JOIN residencetype ON residence.type = residenceTypeID
                                LEFT OUTER JOIN (SELECT lodge, avg(rating) as rating
                                    FROM comment JOIN reservation ON (comment.booking = reservation.reservationID)
                                    GROUP BY lodge) as avgRatingPerResidence
                                ON residence.residenceID = avgRatingPerResidence.lodge'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function getResidenceTypes()
    {
        global $dbh;

        $stmt = $dbh->prepare('SELECT residenceTypeID, name FROM residenceType');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function getAllCommodities()
    {
        global $dbh;

        $stmt = $dbh->prepare('SELECT * FROM commodity');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function getResidenceTypeWithID($typeID)
    {
        global $dbh;

        $stmt = $dbh->prepare('SELECT name FROM residenceType WHERE residenceTypeID = ?');
        $stmt->execute(array($typeID));

        $res = $stmt->fetch();
        return $res === FALSE ? FALSE : $res['name'];
    }




function getResidenceInfo($residenceID)
{
    global $dbh;

    $stmt = $dbh->prepare(
        'SELECT residence.*, residenceTypeID, residencetype.name as type
            FROM residence JOIN residencetype
            ON residence.type = residenceTypeID
            WHERE residenceID = ?'
    );

    $stmt->execute(array($residenceID));
    return $stmt->fetch();
}

function getResidencePhotos($residenceID)
{
    global $dbh;

    $stmt = $dbh->prepare('SELECT filepath, priority FROM residencePhoto WHERE lodge = ?');
    $stmt->execute(array($residenceID));
    return $stmt->fetchAll();
}


function getResidenceCommodities($residenceID)
{
    global $dbh;

    $stmt = $dbh->prepare(
        'SELECT *
            FROM residenceHasCommodity JOIN commodity
            ON item = commodityID
            WHERE lodge = ?'
    );

    $stmt->execute(array($residenceID));
    return $stmt->fetchAll();
}

function getResidencesWith($capacity, $nBeds, $type, $minPrice, $maxPrice, $minRating, $maxRating)
{
    global $dbh;

    // Some injection safety for the rating variables. Since the query is not working properly we need this workaround

    $minRating = floatval($minRating);
    $maxRating = floatval($maxRating);

    $stmt = $dbh->prepare(
        'SELECT residence.*, residencetype.name as typeStr , rating
        FROM residence JOIN residencetype
                        ON residence.type = residenceTypeID
                       LEFT JOIN (SELECT lodge, avg(rating) as rating
                             FROM comment JOIN reservation ON (comment.booking = reservation.reservationID)
                             GROUP BY lodge
                            ) as avgRatingPerResidence
                        ON residence.residenceID = avgRatingPerResidence.lodge
            WHERE capacity >= ? AND nBeds >= ? AND  ( pricePerDay BETWEEN ? AND ?  ) AND typeStr = ? and ( (rating BETWEEN ' . $minRating . ' AND ' . $maxRating .') or (rating IS NULL))');

    $stmt->execute(array($capacity, $nBeds, $minPrice, $maxPrice , $type));

    return $stmt->fetchAll();
}



    function getResidencesRatings() {
        global $dbh;

        $stmt = $dbh->prepare(
            'SELECT lodge, avg(rating) as rating
            FROM comment JOIN reservation ON (comment.booking = reservation.reservationID)
            GROUP BY lodge'

        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    function getResidenceRating($residenceID) {
        global $dbh;

        $stmt = $dbh->prepare(
            'SELECT rating
             FROM residence LEFT JOIN (SELECT lodge, avg(rating) as rating
                             FROM comment JOIN reservation ON (comment.booking = reservation.reservationID)
                             GROUP BY lodge
                            ) as avgRatingPerResidence
                        ON residence.residenceID = avgRatingPerResidence.lodge
             WHERE residence.residenceID = ?
            '
        );

        $stmt->execute(array($residenceID));

        return $stmt->fetchAll()[0]['rating'];
    }

    function createResidence($residenceObj)
    {
        global $dbh;

        $stmt = $dbh->prepare(
            'INSERT INTO
                residence(owner, title, description, pricePerDay, capacity, nBedrooms,
                nBathrooms, nBeds, type, address, city, country, latitude, longitude)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

        try{
            $stmt->execute(array(
                    $residenceObj['owner'],
                    $residenceObj['title'],
                    $residenceObj['description'],
                    $residenceObj['pricePerDay'],
                    $residenceObj['capacity'],
                    $residenceObj['nBedrooms'],
                    $residenceObj['nBathrooms'],
                    $residenceObj['nBeds'],
                    $residenceObj['type'],
                    $residenceObj['address'],
                    $residenceObj['city'],
                    $residenceObj['country'],
                    $residenceObj['latitude'],
                    $residenceObj['longitude']
                )
            );

            $residenceID = $dbh->lastInsertId();

            $commodities = json_decode($residenceObj['commodities']);

            foreach ($commodities as $commodity) {
                $commodityStmt = $dbh->prepare('INSERT INTO residencehascommodity VALUES (?, ?)');
                $commodityStmt->execute(array($residenceID, $commodity));
            }
        }
        catch(PDOException $Exception) {
            return FALSE;
        }

        if ($stmt->rowCount() <= 0) return FALSE;

        return $residenceID;
    }


    function deleteResidence($residenceID) {
        global $dbh;

        $residence = getResidenceInfo($residenceID);
        if ($residence == FALSE) return FALSE;

        deleteResidenceReservations($residenceID);

        $stmt = $dbh->prepare('DELETE FROM residence WHERE residenceID = ?');
        try {
            $stmt->execute(array($residenceID));
        }
        catch(PDOException $Exception) {
            return FALSE;
        }

        return $residence;
    }

    function deleteResidencePhotos($residenceID) {
        global $dbh;

        $stmt = $dbh->prepare('DELETE FROM residencePhoto WHERE lodge = ?');
        $stmt->execute(array($residenceID));
    }

    function deleteResidenceComments($residenceID) {
        $comments = getResidenceComments($residenceID);
        foreach($comments as $comment) {
            deleteComment($comment['commentID']);
        }
    }

    function deleteResidenceCommodities($residenceID) {
        global $dbh;

        $stmt = $dbh->prepare('DELETE FROM residenceHasCommodity WHERE lodge = ?');
        $stmt->execute(array($residenceID));
    }

    function deleteResidenceAvailabilities($residenceID) {
        global $dbh;

        $stmt = $dbh->prepare('DELETE FROM availability WHERE lodge = ?');
        $stmt->execute(array($residenceID));
    }

    function deleteResidenceReservations($residenceID) {
        $reservations = getResidenceReservations($residenceID);
        foreach($reservations as $reservation) {
            deleteReservation($reservation['reservationID']);
        }
    }

    function updateResidence($updatedRes) {
        global $dbh;

        $stmt = $dbh->prepare(
            'UPDATE residence
                SET owner = ?,
                    title = ?,
                    description = ?,
                    pricePerDay = ?,
                    capacity = ?,
                    nBedrooms = ?,
                    nBathrooms = ?,
                    nBeds = ?,
                    type = ?,
                    address = ?,
                    city = ?,
                    country = ?,
                    latitude = ?,
                    longitude = ?
            WHERE residenceID = ?');

        try {

            $stmt->execute(array(
                    $updatedRes['owner'],
                    $updatedRes['title'],
                    $updatedRes['description'],
                    $updatedRes['pricePerDay'],
                    $updatedRes['capacity'],
                    $updatedRes['nBedrooms'],
                    $updatedRes['nBathrooms'],
                    $updatedRes['nBeds'],
                    $updatedRes['type'],
                    $updatedRes['address'],
                    $updatedRes['city'],
                    $updatedRes['country'],
                    $updatedRes['latitude'],
                    $updatedRes['longitude'],
                    $updatedRes['id']
                )
            );
        }
        catch(PDOException $Exception) {
            return FALSE;
        }

        if ($stmt->rowCount() <= 0) return FALSE;

        return TRUE;
    }

    function getUserResidences($userID) {
        global $dbh;

        $stmt = $dbh->prepare('SELECT residence.*
                               FROM residence, user
                               WHERE userID = owner AND userID = ?');

        $stmt->execute(array($userID));

        return $stmt->fetchAll();
    }

    function getCommoditiesAsKeysString($id) {
        $commodities = getResidenceCommodities($id);
        $commoditiesKeys = array();

        foreach ($commodities as $commodity) {
            array_push($commoditiesKeys, $commodity['commodityID']);
        }

        return json_encode($commoditiesKeys);
    }