=== Epoch - A native Disqus alternative with a focus on speed and privacy ===
Contributors: Postmatic, Desertsnowman, Shelob9, ronalfy, cyberhobo
Donate link: https://gopostmatic.com/epoch
Tags: ajax comments, comments, lightweight commenting, cdn, cache, moderate, engagement, postmatic, live update, wordpress comments, comment template, ajax commenting, better comments, disqus, discussion, seo, mobile commenting, chat, performance, site speed, chatting, email commenting, comment notifications
Requires at least: 3.9
Tested up to: 4.4
Stable Tag: 1.0.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Epoch - 100% realtime chat and commenting in a tiny little package that is fully CDN and cache compatible.

== Description ==

Epoch is a new plugin from the creators of [Postmatic](http://gopostmatic.com). The goal: To provide a realtime commenting/chat experience using fully native comments while being compatible with page caching, cdns, mobile, other comment plugins, and seo best practices. A tall order? For sure. Try it out.

Epoch provides an amazing commenting experience to your users **while improving site performance at the same time**.

**Key Features**

- WordPress *native comments*
- Realtime with minimal server load
- Fully ajax commenting for both sending and receiving comments
- Integrates with any theme while still blending in with your brand
- Front end moderation with realtime updates. It's insanely fast.


= Epoch solves three big native commenting problems =

**User Experience**

Comment forms and display are often neglected by theme developers. We've been there ourselves. The functions and templates involved in WordPress commenting are complicated, frustrating, and no fun to work with. Every day we see fantastic themes with ridiculously poor commenting experiences. So that's the first thing Epoch fixes.

**Speed and context**

No more submitting comments and watching the page reload. All Epoch comments are submitted via ajax and post instantly in their proper place within the conversation.

And, as it should be, new comments from other users show up like magic automatically. It doesn't matter if they are posted from the web, or from email via [Postmatic](http://gopostmatic.com). The conversation is instantly updated.

**Cacheing, page load, and CDNs**

Epoch is hands down the fastest commenting system available for WordPress, all the while supporting page cacheing and CDNs. Epoch comments lazy load into a placeholder container only when needed. This means your post will load instantly, your comments will load instantly, and your server won't blink. Comments are magically injected into your page so your full SEO mojo stays in tact.


= Success through compatibility =
Epoch is built for compatibility. It uses fully native WordPress commenting, plays nicely with most all other commenting plugins, and employs WordPress coding standards. Use it with your favorite social login, voting, moderation, and other commenting plugins.

**Epoch is fully integrated with Postmatic**

When running Epoch along with [Postmatic](http://gopostmatic.com) the magic really happens. The comment stream is updated in realtime with comments being posted from web as well as email. Subscribing to new comments is quick and simple while users are intelligently invited to become site subscribers as well. All without ugly checkboxes and obtrusive forms.

**Want to jump into the ring and help with development?**

This is just 1.0. We're hoping the WordPress community will lend strength, ideas, and code to this project. That's why we've kept it open-source. You can find Epoch on Github and jump right in. We can't wait to see where commenting goes next.

= Combine Epoch with [Postmatic](http://gopostmatic.com) for the ultimate engagment package =

Postmatic is a free plugin which lets users subscribe to comments **but also reply right from their inbox by hitting reply**. Postmatic and Epoch integrate perfectly to let you take the conversation on the road and keep the conversation going months after you hit publish.

== Installation ==

Install the plugin through the WordPress Plugins Installer or upload `plugin-name.php` to the plugins directory of your content directory
1. Activate the plugin through the 'Plugins' menu in WordPress

Epoch will then override the comment template that came with your theme. 

== Frequently Asked Questions ==

= Does Epoch override my native comments template? =

Only if you want it to. You can choose to style your comments using of our built in themes or use the comments template that is already in place on your site.

= Are comments displayed in ascending, or descending order? =

You choose. Epoch can go either way.

= Will Epoch kill my server with all of these ajax requests? =

Nope! Epoch is smarter than smart. No need to worry about that.

== Screenshots ==
1. Epoch integrates with your existing theme by matching the colors, typography, and width of your content area. Automatically.
2. The options are simple. Installation is as simple as activating the plugin.
3. Epoch is 100% mobile friendly and fully responsive. Nested comments 6 levels deep still look awesome.
4. Epoch is compatible with other 3rd party commenting plugins such as Postmatic (enable commenting by email), WordPress Social Login (for logging in via Twitter, Facebook, and more), WP-reCAPTCHA, Aksimet, and WordPress Zero Spam (Spam protection), and WYSIWYG Comment Form (for adding a toolbard to the comment area).



== Changelog ==

= 1.0.14 =

- This is the real deal. What 10.0.11/12 was meant to be! Nice fast comment posting even on your broken down dinohost server using your homebrewed ie6 port on that galaxy 3 you dumpstered in brooklyln. Because you can. Well, now we can too. So let's get along and be kind to each other. Mmmk? 
- There probably won't be a lot more Epoch development until we get 2.0 out the door. Which involves a huge bucket of awesome like REST and maybe Angular or something top secret and better. Be in touch if you want to help out.

= 1.0.13 =

This release was really just a rollback to 10.0.10 because everything after that broke wptavern. I wish I was allowed to put expletives in changelogs.

= 1.0.12 =

Sorry for two releases in one day. Jason's schedule cleared a bit and, well, we got ambitious. 

- A slight improvement over the previous release for making comments post licketly split.
- We cleaned things up a little bit more for improved 4.4 layouts and compatibility with [Postmatic Social Commenting](https://wordpress.org/plugins/postmatic-social-commenting/).

= 1.0.11 =

- Ooooh so fast. Or at least it looks that way. Submitting comments on slowpoke servers (i'm not pointing fingers... you know who you are) is now quick quick quick. We are injecting the comment directly into the DOM for the user... which lets the server sit back and think about it as long as it wants.
- Tested and beaten up thoroughly. Works well on 4.4. 

= 1.0.10 =

- Ajax requests on some server environments are taking too long when submitting a comment. We've put in an animation to fade the comment form out while the user waits for the comment to post. 
- The yellow fade when a new comment arrives has returned!
- Fixed a bug that would display &amp when someone used an amperstand in their name.

= 1.0.9 =

- Added support for our new plugin: [Postmatic Social Commenting](http://wordpress.org/plugins/postmatic-social-commenting). Lightweight, simple, and fast social authentication. Read about it [on our blog](http://gopostmatic.com/2015/11/weve-finally-f…ocial-networks/).
- We've improved validation alerts when filling out the comment form.

= 1.0.8 =

The first of a series of releases which will be focusing on speed and performance. 

- We've come up with a low-tech & high performance solution to polling the server for new comments. It's experimental depending on your host but you can read all about how to activate it [here](http://docs.gopostmatic.com/article/192-how-to-enable-even-lighter-weight-server-polling). Official support from varying hosts coming soon.
- Somewhere in the depths of earlier development Epoch stopped being smart about when to load and when to lay low. That's been fixed, improved upon, and had a serious IQ lift.
- There was a horrible war between Epoch and some antispam plugins on certain configurations. 'Twas ugly but now has been fixed. This resolves the *your comment was not accepted, please verify that everything is filled out correctly* problem.
- We made a fancy loading spinner that tricks your brain into not being bored when you are waiting for comments to load. Silly humans. We have a lot of work to do regarding loading performance on posts with 100+ comments. We have plans.
- There was an issue with the trackbacks/pings setting not saving/rendering correctly. Fixed.
- We've added some special styles for [Rating System](https://wordpress.org/plugins/rating-system/). Go rate some comments.

= 1.0.7.1 =

- A quick fix for a fatal error on activation that was present in 1.0.7. Plus Jason had another cool idea for rendering Crowd Control on mobile after sleeping on it. :)

= 1.0.7 =

- Refined the styles on pending comments. Less gray, more black. New clock icon.
- Improved iframe filters for 3rd party plugins to integrate in iframe mode.
- Updated French translation.
- Integration with Crowd Control for showing/hiding report link on hover (desktop only).
- Added security to moderation calls.
- Adding the groundwork for some major performance improvements. More coming soon.

= 1.0.6.1 =

- We caught a bug in which adding a comment as an admin would throw a funky error. Fixed.

= 1.0.6 =

- Sweet. A small release to roll out a huge feature: front-end moderation of comments. Brought to you by the brilliant @ronalfy. Be careful. It's powerfully fun.
- We fixed things up so that if you do not want to use avatars we're cool with that.
- Improved styling of post author comments.

= 1.0.5 =

A great group effort with seriously nice contributions from @ronalfy and @cklosowski. There is still more to be done! [Want to help out?](https://github.com/postmatic/epoch/labels/help%20wanted)

- Comments which are being held for moderation are now visible to the user even after the page has refreshed.
- Comments which are being held for moderation are visible to the post author/admin on the front end. Front-end moderation coming next!
- We did some custom styling for [Basic Comment Quicktags](https://wordpress.org/plugins/basic-comment-quicktags/). It looks awesome now. If you haven't installed it you really should.
- Epoch more persistenly remembers users name, email, and website.
- Cleaner styling of all form fields in all views. Everything in its right place.
- Pingbacks and trackbacks are now hidden by default. There is an option if you'd like them back on.
- Comment permalinks now work in all browsers on all themes, in all Epoch modes. Nice.
- Updates to the French translation.
- Oooh a new German translation.

= 1.0.4 =

- Better notifications and styling for comments being held in moderation.
- Better styling of author comments across all themes.
- Improved support for Simple Comment Editing.
- Added support for the very cool [Basic Comment Quicktags](https://wordpress.org/plugins/basic-comment-quicktags/).

= 1.0.3.1 =

- Minification did not properly occur in 1.0.3. Sorry for the sloppy releases. We're going to work on automating it. 

= 1.0.3 =

- Fixed a bug in which installing Postmatic from the dashboard did not work.
- Fixed a bug in which bypostauthor comment styling was inherited by the children of the author comment.
- Fixed a bug keeping Simple Comment Editing from working correctly. Sorry for all that.

= 1.0.2 =

- Fixed the nasty bug that kept descending comments from sorting in the correct order. Again. Sorry.
- Added support for [Simple Comment Editing](https://wordpress.org/plugins/simple-comment-editing/). Tons of thanks to @ronalfy for making that happen.
- Fixed - various iframe security issues.
- Removed the headline while in iframe mode (Join the Conversation) as there was no way to properly link it to the comment form.
- Added special styling for comments left by the post author.
- Added the user role as a class to the comment. That way you can style comments differently depending on if they are admins, contributors, subscribers, etc.
- Added German translation by R. Koller. Thanks!
- Made it easier to download and install Postmatic right from within Epoch.

= 1.0.1 =

The response to Epoch has been epic! Thanks everyone for your kind words, contributions, and encouragement. Here's a bunch of bug fixes and tweaks.

- We're ready for localization. Spanish translation is in place, provided by @telesemana. POT file is in /languages. Submit translations via support@gopostmatic.com.
- Fixed conflicts with WP-Rocket and similar minification plugins. This clears up the b.replace errors.
- Gravatars on comments without an author URL no longer link to #.
- Added a filter to completely disable live updating. Useful for testing and development.
- Comments are now properly attributed to the user nicename in all circumstances.
- Improved permalink flushing - this will help things work better somehow but nobody explained to me what it really means. Flush away!
- Plugin authors - Epoch iframe mode is now ready for hooking into. We've some documentation on how to do that over at http://docs.gopostmatic.com.
- We caught a bug that was leading to runaway server requests in some environments. Squashed.

= 1.0.0 =

- Epoch 1.0 is here and now adds compatibilty with any WordPress site runing any crazy theme! We've added a third integration option which offers a beautiful (and fast!) solution for even the most troubled of themes.
- Fixed a bug in which Epoch did not respect comment depth.
- Tightned up styles all around.
- Names of authors that do not have associated URLS are now not linked.
- The *Join the Discussion* headline hides until there is actually a discussion to join (more than 3 comments).
- New admin screens and misc icons.

= 0.3.4 =

- Quick fix for text color on input fields

= 0.3.3 =

- Improved mobile reply forms
- More persistent disabling to submit button

= 0.3.2 =

- Just a quick bug fix for handlebars dependencies. Buggers. 

= 0.3.1 =

- Cleaned up dependencies a bit
- Misc bug fixes

= 0.3 =

- Added support for WP-Markdown for making a nice little toolbar on your comment form. This brings the count of officially supported integrations to 4: WP-reCAPTCHA, WP-Markdown, WordPress Social Login, and Postmatic. That's a nice lineup.
- Cleaned up descending order a bit on the presentation side
- The submit button now greys out if the comment is taking longer than normal to post
- Pretty icons and screenshots.

= 0.2.4 =

- Fixed a bug in descending order sorting. Try again if you love descending.
- Added support for captcha plugins, specifically WP-reCAPTCHA
- Took care of a bug which would show *there are no comments* even on pages
- Added support for the native WordPress *you're commenting too quickly* and *duplicate comment* errors

= 0.2.3 =

- Fixed the width bug which was causing the comment area to be enormous
- Better compatibility with all themes
- Sweet new integration with WordPress Social Login
- Compatibility with front-end spam plugins such as WordPress Zero Spam
- Misc bugs bugs bugs

= 0.2.2 =

- Added a notice for when comments are closed
- Added a header with an overview of the number of comments and anchor to the comment form
- URLs in comments are now clickable
- Fixed up an errant div tag
- More duplicate-comment killing
- Misc css tweaks to the light theme

= 0.2.1 =

- Fixed up all bugs and descending order as well as duplicating comments
- Added in some more css to the light theme for increased compatability

= 0.2.0 =

- There are still some bugs in the Descending order. It's probably best to stick to Ascending for now.
- Major improvements to the native comment template functionality. If you want to use Epoch with the comment template that shipped with your theme it may be closer to possible now. Give it a try.
- Lots of improvements to the light theme. Specifically the comment forms should be more standardized and typography better matching your theme.
- Added a timestamp along with datestamp

= 0.1.0 =
Initial Version

== Upgrade Notice ==
Nothing to report
