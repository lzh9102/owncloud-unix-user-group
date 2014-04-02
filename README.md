# Owncloud Unix user/group backend

This plugin allows Unix local users to login owncloud with their
username/password pair.

Tested on OwnCloud 6.0.2.

## Installation

Copy this directory (`user_pwauth`) to `<owncloud_path>/apps`. Then, login
owncloud as administrator and click `Apps` on the bottom-left corner. Find
`Unix user and group backend`, click `Enable`. The last step is to go to the
`Admin` page (from the top-right dropdown menu) and set the ranges of uids that
are allowed to access owncloud.

If the plugin is successfully enabled, switch to the user management and you
will see a list of permitted unix users and their groups. The four fields
`Username`, `Full Name`, `Password` and `Groups` are managed by this plugin,
you will not be able to change them.  You can still change `Group Admin` and
`Storage` fields.

## License

The code is based on [Unix user backend](http://apps.owncloud.com/content/show.php/Unix+user+backend?content=148406)
written by Sam Hocevar, with the following improvements:

- Display the user's full name in addition to username
- Use unix groups as owncloud groups

The original code is licensed under [WTFPL](http://en.wikipedia.org/wiki/WTFPL).
My modifications are released to the public domain. See the header of each file
for information. The authors are not liable for any lost or damage caused by
using this software.
