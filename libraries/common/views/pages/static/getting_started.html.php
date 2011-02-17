<h2>Getting Started &amp; Overview</h2>
<p>
    At this point you've already changed your .htaccess file so that it redirects to the "minerva" folder instead of the default "app" folder (or you downloaded a zip file of Minerva, but it's suggested to use Git so you can clone both a copy of Lithium and Minerva since both are changing often).
</p>
<p>
    So congratulations! Minerva is pretty much setup, but there's no data at all yet. Go to <a href="/users/register">register a user</a> and since it's the first user created, it will become an administrator user. Note that any user created after the first will not have any administrative rights. The register page is a public facing page and is intended to be one, so the user role is manually set to avoid any possibilty of a user becoming an administrator. You can also create different user types, but we'll get into that later. Eventually, a simple installation wizard will probably be created.
</p>
<p>
    After you have your first user, click here to go to the <a href="/admin">admin area.</a> You'll then be presented with a dashboard and menu where you can add pages, blocks, more users, etc.
</p>
<p>
    <strong>Brief Overview</strong><br />
    There are a ton of CMS' available and there wouldn't be much point to adding another one to list unless there was something very different about it. So Minerva was created because it's goal is to be a more "heavy duty" CMS. It's built using the very latest technologies in order to scale and provide a suitable platform for a traffic intense website. It's also a CMS that's built for a developer to work with, not so much someone with less development experience. The admin interface and managing content, sure enough, is suitable for anyone...But customizing this CMS and building new add-ons for it and designing the site really assumes a certain level of knowledge. Out of the box (not saying that someone may not create them in the future), you won't find various "templates" or "themes" to switch the site to use. Most likely, a person using this CMS does not want that typical "cookie-cutter" site. Minerva also assumes the developer is familiar with the framework that is backing the CMS; <a href="http://lithify.me">Lithium</a>, or at least another MVC framework. With this "entry level" in mind, Minerva is able to provide a developer with a great (and familiar) starting point for a site that will be able to keep up with demand. Minerva provides a simple, yet structured approach to building a site and manging content, but also maintains the flexibility that Lithium provides. This is what other CMS' lack. Due this foundation, you'd find it extremely difficult to find another CMS out there has as much flexibility or scalability as Minerva.
</p>
<p>
    <strong>How About These Static Pages?</strong><br />
    The static page you see here, the home page, and all other static pages can be found in the "static" library under the directory: minerva/libraries/static/views/pages<br />
    Note that you never want edit any "core" Minerva file or put templates within minerva/views. Unless you are using Git and are ok with forking the project and remember to stay on top of things when it comes to updates. It's just safer if you restrict your work to specific folders so that upgrades are easier. You can also make your own new libraries to put your work. Note that "static" library also includes the default layout for all front-end (non "admin") pages as well.
</p>
<p>
    <strong>Wait, I Don't See URLs With "/admin" ...</strong><br />
    Well, yea...Minerva doesn't have to route like that. Within the routes, there's an "admin" key that is set true when the page is considered an "admin" / back-end page. This basically just tells the system to render with a layout template from the core minerva/views/layouts folder instead. The route name doesn't have anything to do with it. You could route "/foo" to some admin page if you wanted. If you REALLY want all admin pages to start with admin, you can change the routes. Keep in mind though that the access settings are defined elsewhere and are not dependant upon the "admin" parameter in the routes.
</p>
<p>
    <strong>Don't Like the Admin Pages? Need to Add Something?</strong><br />
    One reason you may be tempted to alter view templates within the core is so that you can change the create, update, and/or index pages for working with content. These are generalized pages, though they can be controlled just by setting "form" properties on your model schema, and every library can actually have their own templates that will be used instead if present. So note that "create.html.php" in your minerva/libraries/my_page_type/views/pages folder will be used instead of the normal create template from the system. So again, there's no real need to edit any "core" file. 
</p>
<p>
    <strong>How to Make Another Page Type</strong><br />
    Page types are the basics behind how the CMS works. That's not to say you need to use them at all, but keep in mind that by using page types, you save a lot of time. So to make a page type, you will add a new library under minerva/libraries and then within a "models" folder (ie. minerva/libraries/my_page_type/models) you will add a Page.php model file. This Page class extends Minerva's core Page class. Here's where the magic comes into play. You will not be created a controller as you might expect with traditional MVC. Instead, the core PagesController will be used from Minerva. You will be defining things within your library's Page model that will be used throughout various areas of the codebase. For example, the $_schema property. The admin templates take into account the schema for a page type and then loop through the fields to display the form to manage the content. You will define additional fields to include on a Page as well as validation rules and what types of data these fields take. You will be able to define what the form elements look like, their labels, default values, etc. You can even specify how these fields are to behave when it comes to searching for content. You also specify security settings within the model. Sounds crazy, right? Not so much when you are working with a schemaless database like Mongo! Due to the use of Mongo, we don't have to worry about what data goes on a page document; however, we can rely on certain fields to always be there. Minerva's core Page model will ensure that there is always fields like _id, title, created, modified, owner_id, and url available. The internal workings of the CMS needs to rely on these fields and don't worry, you can't mess them up and you don't need to define them in your own Page model. After you have your Page model setup, you'll simply create the view templates for your new page type under minerva/libraries/your_page_type/views/pages ... If you're ok with how the admin create and update templates look already then you'll probably just want to create a "read.html.php" file in that folder. This will be the front-end template to view the page content. Ok, one final step - hook it up. You need to define a routes.php file within minerva/libraries/your_page_type/config/routes.php and within this file you will add a route that will set a URL to be able to access your read template. You can follow the core routes.php file for examples of how this works. This is where you'll really be able to take control of how URLs are formed on your site per page type giving you a ton of flexibility.
</p>
<p>
    You can follow the "Blog" page type library for reference. It's a basic page type that comes bundled with Minerva, but you can feel free to edit it or simply copy it to a new library folder and comment out all the routes in it's routes.php file. Then your new library will have all of the routes instead and the only place you would see the difference is within the "page_type" field on the Page documents and within the admin backend.
</p>
<p>
    <strong>But I Have my Own Libraries!!</strong><br />
    Chill! Remember Minerva uses Lithium. Adding your own libraries and classes is no problemo! You can add classes and libraries in several ways, but you may wish to make them available to the rest of the application by putting them in Lithium libraries. So under your minerva/libraries folder, you can add say "my_stellar_library" and within that have all sorts of classes. Now you'll need to add in the bootstrap process a call to include that library...Or simply include() somewhere in your code, but that's not really perferred since we have this awesome library loading feature. Within minerva/config/bootstrap/libraries.php you can add a line like: Libraries::add('my_stellar_library'); and voila! You may need to specify a second argument, an array, that has some options in order to instantiate something or set some configuration options...But you get the idea, using your own classes is no big deal and thanks to namespaces in PHP 5.3+ you shouldn't run into any conflicts. For more information, reference the Lithium docs/API.
</p>