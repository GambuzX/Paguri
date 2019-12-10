'use strict'

class FilterState {

    constructor(dateFrom, dateTo, priceFrom, priceTo, ratingFrom, ratingTo, type, nBeds, capacity, commodities) {

        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
        this.priceFrom = priceFrom;
        this.priceTo = priceTo;
        this.ratingFrom = ratingFrom;
        this.ratingTo = ratingTo;
        this.type = type;
        this.nBeds = nBeds;
        this.capacity = capacity;
        this.commodities = commodities;
    }

}

function getCurentFilterState() {

    let nBeds = document.getElementById("nBeds").value
    let capacity = document.getElementById("capacity").value
    let dateFrom = document.getElementById("check_in").value
    let dateTo = document.getElementById("check_out").value
    let type = document.getElementById("housing_type").value
    let priceFrom = document.getElementById("minPrice").value
    let priceTo = document.getElementById("maxPrice").value
    let ratingFrom = document.getElementById("minRating").value
    let ratingTo = document.getElementById("maxRating").value
    let commodities = document.getElementsByName("commodity")
    let commoditiesObj = {};

    for (let i = 0; i < commodities.length; i++) {
        let commodity = commodities[i];
        commoditiesObj[commodity.value] = commodity.checked;
    }

    return new FilterState(dateFrom, dateTo, priceFrom, priceTo, ratingFrom, ratingTo, type, nBeds, capacity, commoditiesObj)
}

function buildResidenceHTML(property){

    let resultHTML = "";

    resultHTML = 
        '<section class="result">' +    
        '<section class="image">' +
        '<img src="../resources/house_image_test.jpeg">' +
        '</section>' +  
        '<section class="info">' + 
        '<h1 class="info_title">' + property['title'] + '</h1>' +
        '<h2 class="info_type_and_location">' + property['typeStr'] + ' &#8226 ' + property['address']  + '</h2>' +
        '<p class="info_description">'  + 'teste' + '</p>' +
        '<p class="info_ppd">' + 'teste' +'</p>' +
        '<p class="info_score">'+ property['rating']+'</p>' +
        '<p class="info_capacity">' + property['capacity']+'</p>' +
        '<p class="info_bedrooms"> '+ property['nBedrooms']+' </p>' +
        '</section>' +
        '</section>' 

    return resultHTML;
}

function buildResidenceListHTML(properties){


    let innerHTML = "";

    for(let i = 0; i < properties.length;i++){

        innerHTML += buildResidenceHTML(properties[i]);

    }

    return innerHTML;
}


function updateSearchResults() {

    // Array that contains the properties that match the filters
    let response = JSON.parse(this.responseText);
    let results_section = document.getElementById("results");
    results_section.innerHTML =  buildResidenceListHTML(response);
}


function filterUpdateHandler() {

    var filterState = getCurentFilterState();

    let request = new XMLHttpRequest();
    let encodedData = encodeURIComponent(JSON.stringify(filterState));

    request.onload = updateSearchResults;
    request.open("get", "../ajax/residence_search.php?filter_data=" + encodedData);
    request.send();

}


document.getElementById("filter_button").addEventListener("click", filterUpdateHandler);