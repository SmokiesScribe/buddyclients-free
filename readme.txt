=== BuddyClients Free ===
Contributors: victoriagrif7
Tags: buddypress-integration, service-business, business-tools, team-management, client-management
Requires at least: 4.9.1
Tested up to: 6.6.2
Requires PHP: 7.2
Stable tag: 1.0.10
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BuddyClients is a flexible and comprehensive platform for any service-based business. This free version includes core functionality.

== Documentation ==

- [Documentation](https://buddyclients.com/docs/)
- [User Guides](https://buddyclients.com/help/)
- [Roadmap](https://buddyclients.com/roadmap/)
- [Source Code on GitHub](https://github.com/SmokiesScribe/buddyclients-free)
- [Terms of Service](https://buddyclients.com/buddyclients-free-terms/)

== Requirements ==

To run BuddyClients, we recommend your host supports:

* PHP version 7.2 or greater.
* MySQL version 5.6 or greater, or, MariaDB version 10.0 or greater.
* HTTPS support.

== Installation ==

1. Ensure that you have either BuddyPress or BuddyBoss Platform installed on your WordPress site.
2. Navigate to Plugins > Add New in your WordPress admin dashboard.
3. In the search bar, type BuddyClients Free and press Enter.
4. Locate the BuddyClients Free plugin in the search results and click the Install Now button.
5. Once the installation is complete, click Activate to enable the BuddyClients Free plugin on your site.
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

= 1.0.10 - Unreleased =
* Improvement: Combined missing page notices.
* Improvement: Load CSS variables separately.
* Improvement: Define icon urls in css.
* Improvement: Updated enqueue process.
* Improvement: Trigger admin loading indicator with html attributes.
* Improvement: Added functions to handle inline scripts and styles.
* Improvement: Handle case where no checkout page is set.
* Improvement: Use scoped vendor libraries.
* Improvement: Replaced ABSPATH with WP functions.
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
* Fixed: Updated text domain to buddyclients-free.
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