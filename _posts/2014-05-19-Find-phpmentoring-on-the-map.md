---
layout: post
title: Find PHPmentoring on the map
category: Community
---

Today we introduced a PHPMentoring-Layer at [php.ug](http://php.ug/#phpmentoring).

For that we parse the PHPMentoring-site and retrieve a geolocation based on the
location everyone has provided in their github-account. Therefore some locations
might be a bit *off* somehow, as the service used to find a geolocation can not resolve everything.
There might be locations as *moon* or */dev/null* that will not result in a correct
geolocation and might therefore be somewhat displaced or not shown at all.

Also some places are ambiguous and as the process of getting geolocations is an
automated one not always the right place is chosen.

But for most of the mentors and apprentices it works out quite well.

If you want to know more about the process of getting geolocations like that, drop me a line.