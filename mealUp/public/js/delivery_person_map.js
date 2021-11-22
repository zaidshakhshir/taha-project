// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIwzALxUPNbatRBj3Xi1Uhp0fFzwWNBkE&libraries=places">
var circle;

$(document).ready(function ()
{
    $('.model_select2').select2(
    {
        width: '100%',
        height: '100%',
        dropdownParent: $('#staticBackdrop')
    });

    // $('.model_select2_1').select2(
    // {
    //     width: '100%',
    //     height: '100%',
    //     dropdownParent: $('#edit_delivery_zone')
    // });

    var show_lat = parseFloat($('#show_lat').val());
    var show_lang = parseFloat($('#show_lang').val());

    var myLatlng = new google.maps.LatLng(show_lat, show_lang);

    const show_map = new google.maps.Map(document.getElementById("abcd"),
    {
        center : myLatlng,
        zoom : 13,
    });

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(show_lat,show_lang),
        title: $('#show_name').val(),
        map: show_map,
        draggable: false
    });

    show_circle = new google.maps.Circle({
        center: new google.maps.LatLng(show_lat,show_lang),
        map: show_map,
        radius: parseFloat($('#show_radius').val()) * 1000,
        fillColor: '#29aa30',
        fillOpacity: 0.3,
        // strokeColor: "#29aa30",
        strokeWeight: 1
    });
});

function initMap()
{
    var lat = parseInt($('#lat').val());
    var lang = parseInt($('#lang').val());

    const map = new google.maps.Map(document.getElementById("map"),
    {
        center: { lat: lat, lng: lang },
        zoom: 13,
    });
    const card = document.getElementById("pac-card");
    const input = document.getElementById("pac-input");
    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo("bounds", map);

    autocomplete.setFields(["address_components", "geometry", "icon", "name"]);
    const infowindow = new google.maps.InfoWindow();
    const infowindowContent = document.getElementById("infowindow-content");
    const marker = new google.maps.Marker({
        map,
        anchorPoint: new google.maps.Point(0, -29),
    });

    autocomplete.addListener("place_changed", () =>
    {
        infowindow.close();
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }
        $('#lat').val(place.geometry.location.lat().toFixed(5));
        $('#lang').val(place.geometry.location.lng().toFixed(5));

        var latlng = new google.maps.LatLng(place.geometry.location.lat().toFixed(5), place.geometry.location.lng().toFixed(5));

        var radius = $('#radius').val();

        circle = new google.maps.Circle({
            center: latlng,
            map: map,
            radius: radius * 1000,
            fillColor: '#29aa30',
            fillOpacity: 0.3,
            // strokeColor: "#29aa30",
            strokeWeight: 1
        });
        if (place.geometry.viewport)
        {
            map.fitBounds(place.geometry.viewport);
        }
        else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        let address = "";

        // infowindowContent.children["place-icon"].src = place.icon;
        // infowindowContent.children["place-name"].textContent = place.name;
        // infowindowContent.children["place-address"].textContent = address;
        console.log(infowindow.open(map, marker));
        infowindow.open(map, marker);
    });
}

function remove_coordinates()
{
    circle.setMap(null);
    $('#pac-input').val('');
    $('#name').val('');
    $('#radius').val('');
}

function add_area()
{
    $.ajax(
    {
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        url: base_url + '/admin/delivery_zone_area',
        data:
        {
            radius : $('#radius').val(),
            name : $('#name').val(),
            lat : $('#lat').val(),
            lang : $('#lang').val(),
            location : $('#pac-input').val(),
            delivery_zone_id : $('#delivery_zone_id').val(),
            vendor_id : $('.select2').val(),
        },
        success: function (result)
        {
            if(result.success == true)
            {
                location.reload();
            }
        },
        error: function (err)
        {
            console.log('err ', err)
            for (let v1 of Object.keys(err.responseJSON.errors))
            {
                $('.show_alert').show();
                $('.display').text(err.responseJSON.errors[v1]);
            }
        }
    });
}

function update_area()
{
    var delivery_zone_area = $('input[name=delivery_zone_area_id]').val();
    $.ajax(
    {
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "PUT",
        url: base_url + '/admin/delivery_zone_area/'+delivery_zone_area,
        data:
        {
            radius : $('#edit_radius').val(),
            name : $('#edit_name').val(),
            lat : $('#edit_lat').val(),
            lang : $('#edit_lang').val(),
            location : $('#edit_pac-input').val(),
            delivery_zone_id : $('#delivery_zone_id').val(),
            vendor_id : $('#edit_vendor_id').val(),
        },
        success: function (result)
        {
            if(result.success == true)
            {
                location.reload();
            }
        },
        error: function (err)
        {
            console.log('err ', err)
            for (let v1 of Object.keys(err.responseJSON.errors))
            {
                $('.show_alert').show();
                $('.display').text(err.responseJSON.errors[v1]);
            }
        }
    });
}

function edit_delivery_person(id)
{
    $.ajax(
    {
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        url: base_url + '/admin/delivery_zone_area/'+id+'/edit',

        success: function (result)
        {
            if(result.success == true)
            {
                $('#update_zone_area_form').attr("action", base_url + "/admin/delivery_zone_area/" + result.data.id);
                $('input[name=delivery_zone_area_id]').val(result.data.id);
                $('#edit_name').val(result.data.name);
                $('#edit_radius').val(result.data.radius);
                $('#edit_pac-input').val(result.data.location);
                $('#edit_lat').val(result.data.lat);
                $('#edit_lang').val(result.data.lang);

                $("#select_multiple").find('option').attr("selected",false);

                if(result.data.vendor_id.length > 0){
                    var vendor = result.data.vendor_id.split(',');
                    $("#edit_vendor_id").val("");
                    $("#edit_vendor_id").trigger("change");

                    vendor.forEach(element =>
                    {
                        $('select[id="edit_vendor_id"] option[value='+element+']').attr("selected",true);
                        $('select[id="edit_vendor_id"] option[value='+element+']').trigger('change');
                    });
                }

                var edit_lat = parseFloat(result.data.lat);
                var edit_lang = parseFloat(result.data.lang);

                const edit_map = new google.maps.Map(document.getElementById("edit_map"),
                {
                    center: { lat: edit_lat, lng: edit_lang },
                    zoom: 13,
                });

                const edit_card = document.getElementById("edit-pac-card");
                const edit_input = document.getElementById("edit_pac-input");
                const edit_autocomplete = new google.maps.places.Autocomplete(edit_input);

                edit_autocomplete.bindTo("bounds", edit_map);

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(edit_lat,edit_lang),
                    title: 'hello',
                    map: edit_map,
                    draggable: false
                });

                edit_circle = new google.maps.Circle({
                    center: new google.maps.LatLng(edit_lat,edit_lang),
                    map: edit_map,
                    radius: parseInt($('#edit_radius').val()) * 1000,
                    fillColor: '#29aa30',
                    fillOpacity: 0.3,
                    strokeWeight: 1
                });

                edit_autocomplete.addListener("place_changed", () =>
                {
                    edit_circle.setMap(null);
                    const edit_place = edit_autocomplete.getPlace();

                    if (!edit_place.geometry) {
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }
                    $('#edit_lat').val(edit_place.geometry.location.lat().toFixed(5));
                    $('#edit_lang').val(edit_place.geometry.location.lng().toFixed(5));

                    var edit_latlng = new google.maps.LatLng(edit_place.geometry.location.lat().toFixed(5), edit_place.geometry.location.lng().toFixed(5));

                    var edit_radius = $('#edit_radius').val();

                    update_circle = new google.maps.Circle({
                        center: edit_latlng,
                        map: edit_map,
                        radius: edit_radius * 1000,
                        fillColor: '#29aa30',
                        fillOpacity: 0.3,
                        // strokeColor: "#29aa30",
                        strokeWeight: 1
                    });


                    if (edit_place.geometry.viewport)
                    {
                        edit_map.fitBounds(edit_place.geometry.viewport);
                    }
                    else
                    {
                        edit_map.setCenter(edit_place.geometry.location);
                        edit_map.setZoom(17);
                    }
                    var edit_marker = new google.maps.Marker({
                        position: new google.maps.LatLng(edit_place.geometry.location.lat().toFixed(5), edit_place.geometry.location.lng().toFixed(5)),
                        title: 'hello',
                        map: edit_map,
                        draggable: false
                    });
                });
            }
        },
        error: function (err) {
            console.log('err ', err)
            for (let v1 of Object.keys(err.responseJSON.errors)) {
                $('.show_alert').show();
                $('.display').text(err.responseJSON.errors[v1]);
            }
        }
    });
}

function delivery_zone_area_map(id)
{
    $.ajax(
    {
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        url: base_url + '/admin/delivery_zone_area/delivery_zone_area_map/'+id,
        success: function (result)
        {
            if(result.success == true)
            {
                $('#show_lat').val(result.data.lat);
                $('#show_lang').val(result.data.lang);
                $('#show_name').val(result.data.name);
                $('#show_radius').val(result.data.radius);
                var show_lat = parseFloat($('#show_lat').val());
                var show_lang = parseFloat($('#show_lang').val());

                var myLatlng = new google.maps.LatLng(show_lat, show_lang);

                const show_map = new google.maps.Map(document.getElementById("abcd"),
                {
                    center : myLatlng,
                    zoom : 13,
                });

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(show_lat,show_lang),
                    title: $('#show_name').val(),
                    map: show_map,
                    draggable: false
                });

                show_circle = new google.maps.Circle({
                    center: new google.maps.LatLng(show_lat,show_lang),
                    map: show_map,
                    radius: parseFloat($('#show_radius').val()) * 1000,
                    fillColor: '#29aa30',
                    fillOpacity: 0.3,
                    strokeWeight: 1
                });
            }
        },
        error: function (err) {

        }
    });
}
