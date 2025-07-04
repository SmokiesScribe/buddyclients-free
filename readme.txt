=== BuddyClients Lite ===
Contributors: victoriagrif7
Tags: buddypress-integration, service-business, business-tools, team-management, client-management
Requires at least: 4.9
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.0.33
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BuddyClients is a flexible and comprehensive platform for any service-based business. This free version includes core functionality.

== Documentation ==

- [Documentation](https://buddyclients.com/docs/)
- [User Guides](https://buddyclients.com/help/)
- [Roadmap](https://buddyclients.com/roadmap/)
- [Source Code on GitHub](https://github.com/SmokiesScribe/buddyclients-lite)
- [Terms of Service](https://buddyclients.com/buddyclients-lite-terms/)

== External Services ==

The BuddyClients Lite plugin connects to external services to protect forms from spam. These services are optional and disabled by default.

### Google reCAPTCHA Integration (Optional)

If enabled, Google reCAPTCHA is used to protect forms within the plugin from spam and abuse. When reCAPTCHA is active:

- The user’s IP address and browser information are transmitted to Google for validation.
- Google may set cookies or track user interactions as outlined in their [Privacy Policy](https://policies.google.com/privacy) and [Terms of Service](https://policies.google.com/terms).
- reCAPTCHA is used only for form security and does not collect additional user data beyond what is required for spam prevention.
- Users can enable or disable the reCAPTCHA integration through the plugin settings. When disabled, no data is transmitted to Google.
- The reCAPTCHA integration is disabled by default.

For more details, please review our [Privacy Policy](https://buddyclients.com/privacy).

== Requirements ==

To run BuddyClients, we recommend your host supports:

* PHP version 7.2 or greater.
* MySQL version 5.6 or greater, or, MariaDB version 10.0 or greater.
* HTTPS support.

== Installation ==

1. Ensure that you have either BuddyPress or BuddyBoss Platform installed on your WordPress site.
2. Navigate to Plugins > Add New in your WordPress admin dashboard.
3. In the search bar, type BuddyClients Lite and press Enter.
4. Locate the BuddyClients Lite plugin in the search results and click the Install Now button.
5. Once the installation is complete, click Activate to enable the BuddyClients Lite plugin on your site.
6. After activation, go to the BuddyClients settings to configure the plugin as needed.

== Screenshots ==

1. Screenshot 1: The form that clients use to book services.
   Screenshot URL: /assets/media/screenshots/screenshot-2.png
   
2. Screenshot 2: The admin area bookings overview, displaying the gross and net values for completed and abandoned bookings for a certain time period.
   Screenshot URL: /assets/media/screenshots/screenshot-4.png


3. Screenshot 3: The bookings dashboard in the admin area.
   Screenshot URL: /assets/media/screenshots/screenshot-5.png

== Banner ==

The banner image used for the plugin page:
Banner URL: /assets/media/banner-772x250/banner.png

== Changelog ==

= 1.0.34 - July 4, 2025 =
* Improvement: Hardcoded available form submission classes to improve security.
* Fixed: Corrected error when checking recaptcha response.

= 1.0.33 - July 3, 2025 =
* Renamed from BuddyClients Lite to BuddyClients Lite.
* Improvement: Removed deprecated code in File class.

= 1.0.32 - June 17, 2025 =
* Improvement: Centralized upgrade link logic.
* Fixed: Removed registration code preventing checkout form submission.
* Fixed: Correctly display availability on booking form.
* Fixed: Fixed issue preventing service posts metaboxes from displaying.
* Fixed: Fixed issues applying adjustments to bookings.
* Fixed: Fixed bug displaying wrong content in admin dashboard.

= 1.0.29 - March 3, 2025 =
* Improvement: Organized meta data into separate classes.
* Improvement: Modified database queries.
* Fixed: Fixed typo in generating admin columns percentages.

= 1.0.28 - March 3, 2025 =
* Improvement: Refactored settings page methods for security and cleaner output.
* Improvement: Organized data for admin tables.

= 1.0.27 - February 27, 2025 =
* New Feature! Accept payments in two installments.
* New Feature! See when all services for a booking have been completed.
* Improvement: Added caching to improve plugin speed.
* Improvement: Added scheduler to improve abandoned booking email delivery.
* Fixed: Allow form submissions when reCAPTCHA is disabled.

= 1.0.26 - February 23, 2025 =
* Fixed: Bug in email templates.

= 1.0.25 - February 22, 2025 =
* New Feature! Google reCAPTCHA integration to reduce spam.
* New Feature! Acquire prospects through a lead generation popup form.
* Improvement: Allow WP registration to be disabled.
* Improvement: Generate contact form on booking page.
* Improvement: Added caching to improve plugin speed.
* Improvement: Incorporated Singleton patterns to eliminate multiple database calls.
* Improvement: Improved admin asset handling.
* Fixed: Ensured auto password generator is always strong.
* Fixed: Corrected translation issues.
* Synced versioning between Free and Premium plugins.

= 1.0.13 - February 8, 2025 =
* Improvement: Updated email log.
* Corresponding Premium Version: 1.0.24

= 1.0.12 - January 31, 2025 =
* Improvement: Added no posts message in archive.
* Fixed: Error retrieving booking form link for emails.
* Corresponding Premium Version: 1.0.23

= 1.0.11 - January 27, 2025 =
* Improvement: Made admin tables mobile responsive.
* Improvement: Responsive mobile styling for booked service list.
* Improvement: Improved styling for service list.
* Improvement: Display payment status in service list.
* Improvement: Added list of incomplete brief fields.
* Improvement: Use dynamically generated templates.
* Improvement: Removed inline styles.
* Fixed: Fixed date range filter in overview.
* Fixed: Correct net fee calculated for new BookingIntents.
* Fixed: Fixed issue preventing file upload fields from displaying.
* Corresponding Premium Version: 1.0.21

= 1.0.10 - December 17, 2024 =
* New Feature! Support for paid services without Stripe integration.
* Improvement: Combined missing page notices.
* Improvement: Load CSS variables separately.
* Improvement: Define icon urls in css.
* Improvement: Updated enqueue process.
* Improvement: Trigger admin loading indicator with html attributes.
* Improvement: Added functions to handle inline scripts and styles.
* Improvement: Handle case where no checkout page is set.
* Improvement: Use scoped vendor libraries.
* Improvement: Replaced ABSPATH with WP functions.
* Fixed: BuddyPress compatibility bugs.
* Fixed: Bug preventing booking status change.
* Fixed: Bug preventing assignment of user ID to bookings.
* Corresponding Premium Version: 1.0.20

= 1.0.9 - October 30, 2024 =
* Improvement: Filter constant email variables.
* Improvement: New function to echo color hex.
* Corresponding Premium Version: 1.0.19

= 1.0.8 - October 27, 2024 =
* Improvement: Trimmed TCPDF library.
* Corresponding Premium Version: 1.0.18

= 1.0.6 - October 25, 2024 =
* Improvement: Consistent method to generate testimonial content.
* Improvement: Modified create account text on login page.
* Improvement: Improved method for processing user file deletions.
* Improvement: Added loading indicator on booking form.
* Improvement: Automatically update table structures when necessary.
* Improvement: Added database caching.
* Improvement: Updated language files.
* Fixed: Addressed bugs created by new security measures.
* Fixed: Correctly handles empty checkboxes in settings fields.
* Fixed: Added hidden menu to fix null titles in admin area.
* Fixed: Adjusted commission list filtering.
* Fixed: Fixed file deletion form.
* Corresponding Premium Version: 1.0.17

= 1.0.6 - October 10, 2024 =
* Fixed: Updated text domain to buddyclients-lite.
* Improvement: Updated language files.
* Corresponding Premium Version: 1.0.16

= 1.0.5 - October 10, 2024 =
* Improvement: Implemented plugin-wide fixes for security and performance in accordance with Wordpress repo guidelines.
* Corresponding Premium Version: 1.0.16

= 1.0.4 - October 7, 2024 =
* Fixed: Fixed errors and warnings from Plugin Check.

= 1.0.3 - October 3, 2024 =
* Improvement: Require enabled components for admin info notices.
* Improvement: Added logic to handle version switches.
* Improvement: Validate all services on version switch.

= 1.0.2 - October 2, 2024 =
* Improvement: Removed Stripe vendor files.
* Improvement: Updated autoloader logic to prevent unnecessary file checks.
* Fixed: Fallback to empty string for parent slug when creating admin submenu item.

= 1.0.1 - October 2, 2024 =
* Improvement: Add contact link to Checkout when payments disabled.

= 1.0.0 - September 30, 2024 =
* Initial Release

                                @@@%@@#                            
                             @@@@@@@@                              
                           @@@@@@@@                                
                        @@@@@@@@@@                                 
                       @@@@@@@@@@                                  
                      @@@@@@@@@@@                                  
                     @@@@@@@@@@@                                   
                    @@@@@@#*#@@                                    
                    @@@@@@##%@@                                    
               @@  @@@@@@@@%%@@                                    
               @@@ @@@%%@@@@@@@                  @%*#@@            
              %#%@ @@@%%%@@@@@@             @@@@@@@@@@@@           
              @@@@@@@@@*=#%@@%%        @@@@@@@@@@@@@@@@            
              @%###%@@@*=+#%@%*     @@@@@@@@@%#%@@@@@              
              @@%#*#%%@%#*%@@%+       @@@@@@@%%#@@@@               
              @@@@%@@@@@##%%%%#%      @@@@%%@@%#=*%*               
              @@@%%@@@@%%%%@@%@@@     %#*%%#%%@#==#%               
              @%#+*#*=-++=*%%%%##=    %#**#+*#%%###@               
               %#***=--+++#%#%#---+   @%#%%*+**#%%%%#              
                ***+--+++==+#%%%##%@  ###++*#+*@%#%%*+             
                 ##*==+--==+*#%%%%%###=++===*##%##@%*+*            
                   %%%@+=++*+-:-##=++==++=-:=**%%#%%****           
                --    #=-++++-:=#**#*==++---+###%*+***++           
                 *#%#*-:-===++=+#*==+*%%*---==-+*+*##+::           
                  %%%#=--+#%%+:=*+*###%%*-=+++***++*#+=+           
                   %%#=::+#%#+-+##%%%####+**####*++#%#**           
                     **++**#*--=**#%%#*#%#*+*##***###%#            
                        #*##*==+++#######%%%######%#%%%            
        @%%**%@              ###+=+#*#%#%##%%#**#%%#%              
      %%%%%**%@%#              %#+**+*#%%##%%*+*%#*##              
    %##%%%%  @%##%@@@            +*###%%%#%%%#+*%%%%%@             
   @@#*#       @@@@@@@                     %%%%%%%%%##             
   @@%#        %%@@@@@@         %#%%%@%%%@  %%#*##%%@%#            
   @@@@         @@@@@@@@@    ####%%%##%@@%%% #*+*%%%@%#            
   @@@@@           @@@@   @@%%%%%#@%##%@@@%%@ ++#%%@@%%%           
   @%%@@@              @#%%@@@@%%@@%%%@@@%%%@  %%%%@@@%%%          
    %%%%@@@         %%%@%%@@@@%%%@@@@@@@@@@@@   @%#%@@@@@          
     @%%@@@@@@%%%%%@##%@@@@@%%%%@@@@@@@@@@@@     %%%@@@@@@         
       @@@@@@@%%#%%@%#%@@@@%%@@@@@@@@@@@@%        @@@@@@@@         
           @@@@%%@@@ %@@@@@@@@@@@@@@@@@@@@@@@@@    @@@@@%##@@      
                    @@@@@@@@@@@@@@@@@@%@@@@@@@@@@   @@@@##%@@@@    
                   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  @@@@@@@@@@@    
                    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@     @@@@@@@@     