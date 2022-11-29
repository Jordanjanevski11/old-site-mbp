=== Plugin Name ===
Contributors: crus007
Donate link: http://www.haytabay.de
Tags: widget, menu hierarchy
Requires at least: 2.1.2
Tested up to: 2.2
Stable tag: 1.1

SubPages is a small widget which you can use to show all pages from a root page as a menu in the sidebar.

== Description ==
The Sidebar Widget Plugin is easy to integrate and maintainable content provider for your page, 
but one aspect was missing from my side. The pages widget displays all pages, but I liked only to display the 
pages which are children from the current root page. 
For this reason I wrote the SubPages widget for the Sidebar Widget. 

Usage: In the Themes->Sidebar Widget you see in the available widgets section the Sub Page Widget, 
which you can drag and drop on your side bar. You 
can add a Title which should be displayed in the title of the widget. 
This will be combined with the title of the Root Page. 
If you leave the title empty only the root page title is displayed. 

If a page does not have any child pages this widget is not displayed.

== Configuration ==
Configuration 

    * Use Root
      You can decide from which level the sub pages are displayed. The choice you have is from the root or from the current page. To show the sub pages from the root check the “Use Root” checkbox. To use the current page uncheck this option.
      If the “Use Root” option is not checked the options are evaluated. 
      
    If it “Use Root” is checked this option will ignored and are without any usage. 
    This should avoid that the user get lost in the page navigation.
    * First level only
      You can decide to show the full hierarchy or only the first level from the current page.
    * Add Parent Page
      You can add a link to the Parent Page to the Sub Pages list. If this option is used the options “Parent Icon” and “Position” are evaluated.
    * Parent Icon
      You can set what should be displayed when the parent page is added to the list. If you want to add an image you need the image tag e.g.
      <img src="/logo.gif" border="0" />
    * Position
      You can select where the parent page should be added to the beginning of the list or at the end. 

== Installation ==

* Backup your current version of SubPages.php if installed
* Unpack the SubPages.zip and override the SubPages.php with the version in the zip file.
* May you need to active the PlugIn again in the WordPress PlugIns administration
* May you need to add the SubPages to your layout in the WordPress Sidebar Widgets administration
* If your WordPress is not starting please replace the new version through the version you have backuped.  

== Screenshots ==

1. On the first page of the blog you don't see the Sub Pages widget, because the page does not have child pages
2. Selecting a root page with child pages will displyes the Sub Pages widget with list of sub pages
3. Configuration Screen 

== Frequently Asked Questions ==
= I added the standard menu widget, how can I hide the sub pages? =
Thank's to JT, he has found some usefull tools to do this.<br>
http://www.thephppro.com/plugins/tppSidebarPageSections/ or 
http://gmurphey.com/2006/10/05/wordpress-plugin-page-link-manager/

Thank you again (http://www.johnta.com/)
