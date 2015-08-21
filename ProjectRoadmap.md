Releases of this client library follow a `<major>.<minor>.<revision>` numbering scheme.

  * Changes to the `<revision>` number indicate a small change, usually to fix a bug.
  * `<minor>` version changes may add new functionality but should be backwards-compatible with the previous minor version.
  * `<major>` numbering changes indicate a version of the library that is significantly different from the previous release, and will not be guaranteed to be backwards-compatible with any previous releases.

To get a feature listed in this roadmap, please [file a bug](http://code.google.com/p/opensocial-php-client/issues/list) on our issue tracker and we will prioritize it appropriately.

Our current roadmap for releases is:



# 1.0.1 #
  * Features
    * People
      * Retrieve
    * Activities
      * Retrieve
      * Create
    * AppData
      * Retrieve
      * Create
      * Delete
    * Messages
      * Retrieve
      * Create
    * Requests
      * Fields (people, app data)
      * Paging
      * Filtering
    * Auth
      * 2-Legged OAuth
      * 3-Legged OAuth
      * Security token
      * fcauth (For Google Friend Connect)
  * Supported Containers (succeed or return a reasonable error):
    * MySpace (people only)
    * Orkut
    * iGoogle
    * Plaxo (contacts)
    * Google Friend Connect
  * Tests:
    * 70% coverage

# 1.1.0 #
  * Features
    * Groups
      * Retrieve
    * StatusMood (MySpace only)
      * Retrieve
      * Update
    * Notifications (MySpace only)
      * Create
  * Supported Containers
    * MySpace (full)

# 1.1.1 (Current) #
  * Bug Fixes
    * Activity support for MySpace

# 1.2.0 #
  * Features
    * Full 0.9 support
      * Invalidation
      * App Data as a Person field
      * Updated messaging structure
  * Supported Containers
    * Hi5
    * Netlog
    * Hyves