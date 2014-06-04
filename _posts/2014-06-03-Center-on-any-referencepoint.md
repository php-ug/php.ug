---
layout: post
title: Center the map on any reference point
category: Community
---

So there is a lot of information hidden on the map at [php.ug](http://php.ug).

There are three different layers hidden in the select-field on the lower left
corner. It allows you to see

- all the PHP-Usergroups currently registered for the map
- all events that are marked as upcomming on [joind.in](http://joind.in) and
- all the people currently taking aprentices or seeking mentors that are listed
   at [phpmentoring.org](http://phpmentoring.org).

But that's rather a lot of stuff on the map. And perhaps you want to directly
link to **your** entry.

Here is how you do it.

## Usergroups

You did provide a shortname for your usergroup when you promoted it. You can use
that shortname to center the map to your groups location by calling
```http://php.ug?center=<yourShortName>```. Easy as that.

## Events

Your event has an ID on joind.in which is used for identifying your event. So all
you have to do is call ```http://php.ug?center=<YourEventsJoindInId>#joindin```
and you are ready to go.

## PHPMentoring

You had to provide a github-account when you registered to PHPMentoring. (By the way: the
location you provided there is what we take to get your geographic position. So
if you fell you are in the wrong place, consider changing that location) That
github-account can be used to center the map around your area. So you'll have to
call ```http://php.ug/center=<YourGithubAccount>#phpmentoring``` to get **your**
part of the map.

Any questions? Feel free to contact us via [our contact-form](http://php.ug/contact)