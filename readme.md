## Generic Library Catalog

![screenshot](https://raw.githubusercontent.com/lamereb/generic-library-catalog/master/library.png)

This is code for a simple interface for a public library system. The backend is written in PHP in communication with a SQL database, with some Javascript functionality and CSS styling on the front.

There are 3 tiers of access to the page: 
+ public: a browsable display of the system's catalog contents, that can be filtered by branch
+ user: same as public, but with ability to check-out & return books
+ admin: ability to edit the catalog, add branches to the library system, and check-out/return items for any user.

New user-level accounts can be created by anyone.

A live version of the site is hosted [here](https://http://web.engr.oregonstate.edu/~lamereb/lib) (and if you want to log in as an administrator there, since this is just a test site, I've set up what is pretty much the generic home-router login credentials to do so (should be easy to figure out). 

For inserting book items in the catalog, the public [WorldCat xISBN API](http://xisbn.worldcat.org/xisbnadmin/doc/api.htm#getmetadata) has been implemented for retrieving book metadata (with regex matching on the ISBN number so that numbers can be just copy/pasted from other sources like Amazon).

