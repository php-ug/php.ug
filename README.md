# php.ug

## An international meeting-point for the PHP-Community.

### Purpose

This Website provides a location for PHP usergroups worldwide to advertise 
their existence, their meetings and their contact-details.

For that we include a map on the frontpage that displays every registered 
location worldwide. The User can then select the nearest UG and see the
relevant informations like next meeting and contact-informations.

From there you can jump to their website immediately.

Also a shortlink is provided for every UG so their website can be reached
using a URL like http://php.ug/ffm or http://ffm.php.ug for the Usergroup at 
Frankfurt/Main in Germany or http://php.ug/benelux or http://benelux.php.ug
for the UG in the Benelux-Countries.

For that every Usergroup can register by providing a unique identifier 
(ffm or benelux), a website and some contact-informations (mainly email, 
but twitter, facebook or Chat are also possible) and a location where (or 
around where) the meetings are held. Currently simply provide the informations
via the contact-form on the website.

You will be able to log in using any social plattform account associated with 
your usergroup to edit entries of your usergroup. As long as this is not 
implemented, please also use the contact-form.

All Usergroups will have to verify their existence at least once a year so we
can archive inactive groups.

The meeting schedule will be retrieved on a regualr basis from an iCalendar-File 
the usergroup can provide on their Website. That makes it easier to maintain
a consistent Dataset of the schedules as the UG only needs to maintain their 
local calendar and this website will retrieve that information. If no URL to
that calendar is given, then no meeting-schedule will be displayed. **This 
feature is not yet implemented. Any volunteers are welcome!**

NO statistics will be provided. 

### Costs

The registering usergroups will not have to pay for this service! 

All costs are currently covered by the team-members. But - of course - donations
to pay the bills are always welcome. For more information on donating either use
the contact form or contact any one of the team by PM.

Also help by coding the stuff behind is highly appreciated!

### Technical

This website will run with PHP (really?) Version 5.3+. I intend to build the
app using the recent version of ZendFramework 2 which is (as far as I recall 
from memory) ZF2.0.4. But other Ideas are welcome!

Used Modules currently are:

* DoctrineORMModule
* DoctrineModule,
* OrgHeiglMailproxy and
* OrgHeiglContact

This project is hosted on github at http://github.com/php-ug/php.ug
