(function ($) {

  var map = new google.maps.Map(document.getElementById('map_canvas'), {
    zoom: 2,
    center: new google.maps.LatLng(5, -179),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    scrollwheel: false
  });

  var pos, marker, content, infowindow;

  $.getJSON('/events.json', function(markers) {
    renderMap(markers);
  });

  function renderMap(markers) {
    markers.forEach(function(markerInfo) {

      marker = new google.maps.Marker({
        position: new google.maps.LatLng(markerInfo.lat, markerInfo.lng),
        map: map,
        animation: google.maps.Animation.DROP
      });

      google.maps.event.addListener(marker, 'click', (function(marker) {
        return function() {

          if (infowindow) {
            infowindow.close();
          }

          if(typeof(markerInfo.event) != "undefined") {
            title = '<a href="' + markerInfo.event + '" target="_blank">' + markerInfo.title + '</a>';
          } else {
            title = markerInfo.title;
          }
          infowindow = new google.maps.InfoWindow({
            content: '<img src="' + markerInfo.img + '" style="float:left;margin-right:2px"><div>' + title + '<div style="font-weight:normal;font-size:x-small;clear: both;padding-top: 5px;">' + markerInfo.details + '</div></div>'
          });
          infowindow.open(map, marker);
        };
      })(marker, markerInfo));
    });
  }
})(jQuery);