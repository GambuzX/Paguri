function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

}

/**
 * Simplified the value of the price by adding the prefixes K, M or B (for thousands, millions or billions)
 * @param {integer/string} price 
 */
function simplifyPrice(price) {

    if ((price / 1000000000).toFixed(2) >= 1)
        return (price / 1000000000).toFixed(2) + 'B';
    else if ((price / 1000000).toFixed(3) >= 1)
        return (price / 1000000).toFixed(3) + 'M';
    else if ((price / 1000).toFixed(3) >= 1)
        return (price / 1000).toFixed(3) + 'K';
    else
        return price;
}

/**
 * Builds the HTML summary representation of a residence
 * @param {residence object} property 
 */
function buildResidenceHTML(property){

    // The description is trimmed in order to avoid overflows
    let descriptionTrimmed =property['description'].length > 180 ? property['description'].substr(0, 180) + "..." : property['description'];

    // The price is simplified to be desplayed with the prefixes K, M or B if needed
    let priceSimple = simplifyPrice(property['pricePerDay']);

    let resultHTML = "";

    let photoPath = property['photoPaths'].length == 0 ? "../resources/medium-none.jpg" : "../images/properties/medium/" + property['photoPaths'][0];

    property['rating'] = (property['rating'] == null ? '--' : (property['rating']/2).toFixed(2));

    resultHTML = 
        '<a href="../pages/view_house.php?id=' + property['residenceID'] + '">' +
        '<section class="result">' +    
            '<section class="image">' +
            '<img src="'+ photoPath+ '">' +
            '</section>' +  
            '<section class="info">' + 
                '<h1 class="info_title">' + htmlEntities(property['title']) + '</h1>' +
                '<h2 class="info_type_and_location">' + htmlEntities(property['type']) + ' &#8226 ' + property['address']  + '</h2>' +
                '<p class="info_description">'  + htmlEntities(descriptionTrimmed) + '</p>' +
                '<p class="info_ppd">' + htmlEntities(priceSimple) +'</p>' +
                '<p class="info_score">'+ htmlEntities(property['rating']) +'</p>' +
                '<p class="info_capacity">' + htmlEntities(property['capacity'] )+'</p>' +
                '<p class="info_bedrooms"> '+ htmlEntities(property['nBedrooms']) +' </p>' +
            '</section>' +
        '</section>' +
        '</a>'    

    return resultHTML;
}


function updateResidenceSummaryDisplay(){

    let result_section = document.getElementById("residence_summary");

    let response = JSON.parse(this.responseText);
    result_section.innerHTML += buildResidenceHTML(response);

}

/**
 * Draw the HTML summary representation of a residence in the correct place
 * @param {integer} id 
 */
function drawResidenceSummary(id){

    let request = new XMLHttpRequest();
    request.onload = updateResidenceSummaryDisplay;
    request.open("get", "../ajax/residence_info_fetch.php?residence_id=" + id);
    request.send();
}

drawResidenceSummary(document.getElementById('residenceID').value);