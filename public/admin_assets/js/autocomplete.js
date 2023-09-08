var isPlaceChanged = false;
var addListner;

// function initMap() {  alert('vb')
//   var options = {
//     types: ["geocode"],
//     componentRestrictions: {
//       country: "uk",
//     }, //UK only
//   };

//   if (document.getElementById("input_address") != null) { alert('sd')
//     // autocomplete6 = new google.maps.places.Autocomplete(
//     //   document.getElementById("input_address"),
//     //   options
//     // );
//     // autocomplete6.setFields(["address_component", "geometry", "icon", "name"]);
//     // autocomplete6.addListener("place_changed", userfillInAddress);
//     // googleInputOnfocusout("input_address", autocomplete6);
//   }
// }






$(document).on("focusout", "#input_address", function () {
  if (isPlaceChanged == false && $("#input_address").val() == null) {
    $("#input_address").val("");
    $("#input_address").valid();
  }
});

function googleInputOnfocusout(id, autocomplete) {
  // Select first address on enter in input
  $("document").on("keydown", "input#" + id, function (e) {
    if (e.keyCode == 13) {
      isPlaceChanged = true;
      selectFirstAddress(this, autocomplete);
      autocomplete.addListener("place_changed", userfillInAddress);
    }
  });

  // Select first address on focusout
  $("body").on("focusout", "input#" + id, function () {
    selectFirstAddress(this, autocomplete);
  });
}

function selectFirstAddress(input, autocomplete) {
  var isPlaceChanged = true;

  google.maps.event.trigger(input, "keydown", {
    keyCode: 40,
  });
  google.maps.event.trigger(input, "keydown", {
    keyCode: 13,
  });
}

function userfillInAddress() {
  var isPlaceChanged = true;
  const place = autocomplete6.getPlace();
  console.log(place,'place');
  if (!place.geometry) {
    window.alert("No details available for input: '" + place.name + "'");
    return;
  }

  $("#long").val(place.geometry.location.lng());
  $("#lat").val(place.geometry.location.lat());

  for (const component of place.address_components) {
    const componentType = component.types[0];
    switch (componentType) {
      case "country":
        $("#country").val(component.long_name);
        break;
      case "route":
        $("#meet_location_route").val(component.long_name);
        break;
      case "postal_code":
        $("#postal_code").val(component.long_name);
        break;
      case "locality":
        $("#city").val(component.long_name);
        break;
      case "administrative_area_level_1":
        $("#state").val(component.long_name);
        break;
    }
  }
}