=== BuddyClients ===
Contributors: Victoria Griffin
Requires at least: 4.9.1
Tested up to: 6.6.2
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BuddyClients is a versatile and feature-rich platform designed to streamline and enhance operations for service-based businesses. Features include a payment integration, support for complex service rate structures, team management, and an affiliate program.

= Documentation =

- [Documentation](https://buddyclients.com/docs/)
- [User Guides](https://buddyclients.com/help/)
- [Roadmap](https://buddyclients.com/roadmap/)

== Requirements ==

To run BuddyClients, we recommend your host supports:

* PHP version 7.2 or greater.
* MySQL version 5.6 or greater, or, MariaDB version 10.0 or greater.
* HTTPS support.

== Installation ==

1. Make sure you have either 'BuddyPress' or 'BuddyBoss Platform' installed.
2. Then visit 'Plugins > Add New'
3. Click 'Upload Plugin'
4. Upload the file 'buddyclients.zip'
5. Activate 'BuddyClients' from your Plugins page.

== Screenshots ==

1. Screenshot 1: The checkout page where clients pay for services.
   Screenshot URL: /assets/media/screenshots/screenshot-1.png

2. Screenshot 2: The form that clients use to book services.
   Screenshot URL: /assets/media/screenshots/screenshot-2.png

3. Screenshot 3: An affiliate's data, including clicks and converted clients.
   Screenshot URL: /assets/media/screenshots/screenshot-3.png
   
4. Screenshot 4: The admin area bookings overview, displaying the gross and net values for completed and abandoned bookings for a certain time period.
   Screenshot URL: /assets/media/screenshots/screenshot-4.png


5. Screenshot 5: The bookings dashboard in the admin area.
   Screenshot URL: /assets/media/screenshots/screenshot-5.png

== Banner ==

The banner image used for the plugin page:
Banner URL: /assets/media/banner-772x250/banner.png

== Changelog ==

= 1.0.8 - September 26, 2024 =
* Improvement: Modified file names for compatibility outside Linux environment.
* Improvement: Corrected path creation logic in autoloader.
* Bug: Allowed for initial license check in LicenseHandler.

= 1.0.7 - September 25, 2024 =
* Improvement: Checks for BuddyClients Free plugin.

= 1.0.6 - September 25, 2024 =
* Bug: Suppressed session-related errors.

= 1.0.5 - September 20, 2024 =
* New Feature! Automatic updates.

= 1.0.4 - September 19, 2024 =
* Improvement: Added AlertManager class to handle all alerts.
* Improvement: Added ExtensionManager class to handle all profile extensions.
* Bug: Fixed link comparison in Alert class.

= 1.0.3 - September 17, 2024 =
* New Feature! Preview briefs for brief types in the admin area.
* Improvement: Modified AdminNotice to accept arrays of links.
* Improvement: Added help docs link to admin tips.
* Improvement: Display the email log setting in an admin notice.
* Improvement: Added ParamManager class to modify url params.
* Improvement: Improved formatting of admin tips content.
* Improvement: Pass items per page value to AdminTable.
* Bug: Added settings check before requiring team member agreement.
* Bug: Registered file upload post type.
* Bug: Removed escaping on settings checkbox labels.

= 1.0.2 - September 16, 2024 =
* Improvement: Added charts to admin dashboard.

= 1.0.1 - September 12, 2024 =
* Bug: Fixed type in team and client type functions.
* Bug: Wrapped content in profile extension table.
* Bug: Renamed javascript copy to clipboard function to avoid conflicts.
* Bug: Re-added methods to AdminTableItem class.

= 1.0.0 - September 6, 2024 =
* Initial Release

= 0.4.3 - September 2, 2024 =
* Improvement: Handles account creation and payment processing dependently.
* Improvement: Throws an error when social groups are disabled.
* Improvement: Added sanity checks.
* Bug: Fixed bug resetting booking form submit text.
* Bug: Fixed output after sales booking creation.
* Bug: Fixed file and email log cleanup methods.

= 0.4.2 - August 29, 2024 =
* New Feature! Added support for Meta/Facebook ads events.

= 0.4.1 - August 27, 2024 =
* Improvement: Added anti-spam measures to forms.

= 0.4.0 - August 16, 2024 =
* Changes during BuddyEvents development.
* New Feature! User-specific Legal agreement modifications.
* Improvement: Added filters throughout.
* Improvement: Modified service validation behavior.
* Improvement: Added loading indicator.
* Improvement: Modified popup to accept urls.

= 0.3.4 - August 1, 2024 =
* Improvement: Added filter to admin nav tabs.
* Improvement: Added filter to admin pages.
* Improvement: Removed Singleton pattern from MetaManager.
* Improvement: Updated database and object manager to search arrays.
* Improvement: Added support for directly outputting form fields.

= 0.3.3 - July 25, 2024 =
* Improvement: Added method to update Payment property.
* Improvement: Added method to make admin columns sortable.
* Bug: Fixed error preventing attachment of project ID to Payment.

= 0.3.2 - July 23, 2024 =
* Improvement: Added admin columns to brief fields.
* Bug: Fixed typo in brief submitted email trigger.
* Bug: Sanitize html when populating brief form.
* Bug: Added support for 'text_area' type to FormField class.
* Bug: Updated FormField class to populate textarea with value.

= 0.3.1 - July 19, 2024 =
* New Feature! Manually check for succeeded payments.

= 0.3.0 - July 16, 2024 =
* New Feature! Added admin tips to help navigate the plugin.
* Improvement: Added mobile-styled summary to booking and checkout.
* Bug: Updates htaccess file on change to site url.
* Bug: Updates file urls on change to site url.

= 0.2.12 - July 12, 2024 =
* Bug: Makes sure booked services do not exist before creating them.
* Bug: Fixed bug preventing brief creation on successful booking.
* Bug: Prevents duplicate payment groups.

= 0.2.11 - July 7, 2024 =
* Improvement: Removed the deprecated AdjustmentLineItem class.
* Improvement: Updated class documentation.
* Bug: Makes sure post exists before generating PDF.

= 0.2.10 - July 5, 2024 =
* Improvement: Added reference post type.
* Improvement: Added displayed explanation of team member dropdown and availability.
* Bug: Fixed error in RepairButton class on post archive admin screen.
* Bug: Fixed issue populating file upload field with correct label.

= 0.2.9 - June 29, 2024 =
* Improvement: Added Options class.

= 0.2.8 - June 28, 2024 =
* Bug: Added required component to quote post type.
* Bug: Disables floating contact when Contact component is disabled.
* Bug: Adds email link instead of contact form when Contact component is disabled.
* Bug: Disables availability components when disabled.

= 0.2.7 - June 27, 2024 =
* Improvement: Updated tags for better documentation organization.

= 0.2.6 - June 24, 2024 =
* New Feature! Users list in admin area.
* New Feature! Download legal agreement PDFs.

= 0.2.5 - June 23, 2024 =
* Improvement: Removed loader and added trailing slash to link comparison in Alert class.
* Improvement: Updated admin menu order.
* Improvement: Deletes associated Payments and Booked Services when Booking Intent is deleted.
* Bug: Defined ServiceType class in Quote class.
* Bug: Fixed duplicate admin column values.
* Bug: Fixed timing when validating custom quote post.
* Bug: Fixed payment updated email.
* Bug: Uncommented line that prevents duplicate project groups.
* Bug: Fixed issue with service fees over $1,000.
* Bug: Fixed formatting issue affecting team payment calculation.

= 0.2.4 =
* New Feature! Manually create previously paid bookings.
* New Feature! Delete bookings in the admin area.
* Bug: Fixed issue with team filtering.
* Bug: Fixed errors on booking form submission.

= 0.2.3 =
* New Feature! Attach rate numbers to individual services.
* Improvement: Filters available team members by team member agreement status.
* Improvement: Displays message if no team members are available.
* Improvement: Adds payment method selection to affiliate form.
* Improvement: Adjustment options allows values with decimals.
* Improvement: Disables booking form submit button during processing.
* Improvement: Improved checkout table styling.
* Improvement: Added nicename to client dropdown in sales form.
* Bug: Fixed function to retrieve all clients.
* Bug: Fixed floating contact button display.

= 0.2.2 =
* Improvement: Adds availability to booking form.
* Bug: Fixed issue preventing availability alert from displaying.

= 0.2.1 =
* Improvement: New admin plugin links.

= 0.2.0 =
* New Feature! Booking form filters team members.
* New Feature! Dependent services are disabled until selected or booked.
* New Feature! Team members for each role can be locked for a project.

= 0.1.3 =
* Bug: Fixed typo in FloatingContact.
* Bug: Role label defaults to title, eliminating error when singular label is missing.
* Bug: XprofileField was creating duplicate fields.
* Bug: Fixed upload field display on booking form.
* Improvement: Checks whether default pages exist before creating them.
* Improvement: Adjustment options require only a label to be valid.

= 0.1.1 =
* Bug: Fixed compatibility with BuddyPress.

= 0.1.0 =
* Initial Beta Version

                                       ~~~
                                     /     \
                                    /       \
                                   /         \
                                  /  *******  \
                                  \           /
                                   |         |
                                  /           \
                                 |      |      |
                                /       |       \
                               |        |        |
                               |        |        |
                               |        |        |
                               |        |        |
                               |        |        |
                               |        |        |
                               |                 |
    _______                   /                   \                   _______
  /        \                 /                     \                 /        \
 /   ****   \               /                       \               /   ****   \
|            \_____________/                         \_____________/            |
|                                                                               |      
|            |                      *********                      |            |
|  {      }  |                     ***********                     |  {      }  |                                    
|  {      }  |      ******        *************        ******      |  {      }  |                                            
|            |                   ***************                   |            |                                     
|            |                   ***************                   |            |                                     
|            |                    *************                    |            |                                
|            | \                   ***********                   / |            |                                                
| *********  |    ~                                           ~    | *********  |                                     
|            |       ~                                     ~       |            |                                                
|============|          '~~~        ---------         ~~~'         |============|
                             \                      /
                           ___                     ___
                          {                           }
                          {                           }         
                           '--       -------       --'
                             |                     |
                             /                     \
                             /        +++++        \
                             {         +++         }
                              \         +         /
                               \                 /
                                \               /
                                  ~~~~~|~|~~~~~