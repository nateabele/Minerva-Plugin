<?php
/**
* Block Helper 
*
* Blocks are an inefficient way to solve design problems. Their usage can be quite flexible though and inneficiency may not be 
* a real big deal depending on where and when and for what. It is the equivelant of making a SQL JOIN call, but for PHP instead
* in order to "join" html/css to make up the page. Unfortunately, it's sometimes (often) necessary.
*
* RENDER METHOD
* --------------
* There are two main methods for blocks to be rendered. First is the "php" method which will call external/local URLs using cURL
* or render view templates from the views folder (by default located in: minerva/views/blocks/static). 
*
* Rendering view templates is perhaps your most basic use for blocks. It renders a "static" template from the views/blocks folder.
* Of course those templates can also call Block->render() again or Block->requestAction(). This can help with organization. 
* 
* As you can start to see, with all these classes (both View and your controllers) being instantiated over and over,
* pulling in other data is not the most effecient thing for a site/CMS and breaks MVC. It's a necessary evil though.
*
* Not to worry! There's caching for the "php" method! So once your block template (which may make several other calls that make
* several other calls) has been rendered, it can be cached to disk (or memory, depending) using Lithium's Cache class.
* That way, you won't be instantiating all those classes and running all those methods on each request.
* What's even better is that you can also, more than likely, cache the parent/calling view template! Caching for everyone!
*
* In the other corner...
* We have our "ajax" method. This method will always make another request to the web server.
* AJAX can not call external URLs. So why on earth would you use it? Well, maybe you don't care about how many requests
* are coming to your server. Maybe you really need to ensure that this block is never cached. Maybe you are calling another 
* page on your site that already is cached and you're just doing this so that your page can load first and have all your 
* blocks come in last for user experience. Again, note that is another request to the server, but you may weigh the options
* and decide that you're ok with that and it's more important to have the main part of the page load as fast as possible.
* It is like using cURL, but it is done on the client side so it doesn't hold up the main page from loading.
*
* 
* REQUEST ACTION METHOD
* ----------------------
* You also have another method in this helper. Called "requestAction"
* The serves as a shortcut to instantiating controller classes and calling specific methods/actions from within those controllers 
* to return data to the page. That means those methods should be built with the beforehand knowledge that they are to return 
* data instead of render views. This data can then be used directly within the view template.
* Using this in conjunction with the render() method should give you the ability to load whatever content you need into blocks.
* 
* So, you have flexibility while at the same time maintaining a small footprint and allowing you to write modular code.
* Remember that blocks can really hurt your site's performance if not cached and there's a bunch of them.
*
*
* @author Tom Maiaroto
* @website http://www.shift8creative.com
* @modified 2011-01-13 21:37:50 
* @created 2010-06-10 16:17:41 
*
*/
namespace minerva\extensions\helper;
use \lithium\template\View as View;
use \lithium\util\Inflector as Inflector;

class Block extends \lithium\template\Helper {
	
	/**
	 * Shortcut helper method for rendering an admin static block from a view template.
	 * Admin templates are always pulled from minerva/views/blocks/static/...
	 *
	 * @param $template string[required] The name of the template file
	 * @return Mixed the html/css from the rendered page/view template
	*/
	public function render_admin_block($template=null) {
		if(empty($template)) {
			return '';
		}
		$options = array('url' => null, 'curl_options' => array(), 'method' => 'php', 'library' => 'common', 'template' => $template, 'layout' => 'blank', 'type' => 'html', 'admin' => true);
		return $this->render($options);
	}
	
	/**
	 * Shortcut helper method for rendering a static block from a view template.
	 * Static templates are pulled from minerva/libraries/static/views/blocks/...
	 * If the path needs to be changed, use render() instead.
	 *
	 * @param $template string[required] The name of the template file
	 * @return Mixed the html/css from the rendered page/view template
	*/
	public function render_block($template=null) {
		if(empty($template)) {
			return '';
		}
		$options = array('url' => null, 'curl_options' => array(), 'method' => 'php', 'library' => 'common', 'template' => $template, 'layout' => 'blank', 'type' => 'html');
		return $this->render($options);
	}
	
	/**
	 * render() allows you to render a view template or external URL's content inline with the template it was called from.
	 *
	 * @param $options array[required]
	 * @return Mixed the html/css from the rendered page/view template or JavaScript code with an AJAX call to load local content or false if something went wrong
	*/
	public function render($options=array()) {
		$defaults = array('url' => null, 'curl_options' => array(), 'method' => 'php', 'library' => 'common', 'views_folder' => 'blocks' . DIRECTORY_SEPARATOR . 'static', 'template' => null, 'layout' => 'blank', 'type' => 'html', 'admin' => false);
		$options += $defaults;
		
		/** 
		 *  Method by default is set to php, meaning we are going to get the content for the block now and render it with the page.
		 *  This allows us to cache the block content because the server is aware of it.
		 *  Both the normal "File" renderer to render templates can be used and also a custom "Curl" renderer that can be used 
		 *  to load any URL using the cURL library.
		 */
		if($options['method'] == 'php') {
			// By default, missing
			$template_path = array(
				MINERVA_APP_PATH . DIRECTORY_SEPARATOR .  'libraries' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_missing' . DIRECTORY_SEPARATOR . 'missing_block_template.{:type}.php'
			);
			
			// We're going to try to grab the block template from common (note by default it's checking blocks/static)
			array_unshift($template_path, MINERVA_APP_PATH . DIRECTORY_SEPARATOR .  'libraries' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $options['views_folder'] . DIRECTORY_SEPARATOR . '{:template}.{:type}.php');
			// If requesting a template from a specific library, put that ahead in the list to check first
			if(!empty($options['library'])) {
				array_unshift($template_path, MINERVA_APP_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $options['library'] . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $options['views_folder'] . DIRECTORY_SEPARATOR . '{:template}.{:type}.php');				
			} 
			
			// For admin blocks, check the admin paths first IF the admin flag was set to true (can not render admin blocks without explicitly saying to do so)
			if($options['admin'] === true) {
				// "admin" blocks have templates saved in minerva/views/blocks/static and this is the default place to look
				array_unshift($template_path, MINERVA_APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $options['views_folder'] . DIRECTORY_SEPARATOR . '{:template}.{:type}.php');
				// but we can override those in the common library's "_admin" folder
				array_unshift($template_path, MINERVA_APP_PATH . DIRECTORY_SEPARATOR .  'libraries' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_admin' . DIRECTORY_SEPARATOR . $options['views_folder'] . DIRECTORY_SEPARATOR . '{:template}.{:type}.php');
			}
			
			// Now a similar thing for the layout templates, but we want to use empty layouts because we don't want <html> tags etc. but we will still cascade and check a few locations
			// NOTE: This layout isn't a "static" layout, it's just an empty "blank" one... It can be changed but the paths aren't looking in the layouts/static folder.
			// This is because a block isn't necessarily "static" content. It can be, but doesn't have to be. Static folders are used for the templates because blocks can be static and menus are ALWAYS static.
			$layout_path = array(
				MINERVA_APP_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . '{:layout}.{:type}.php',
				MINERVA_APP_PATH . DIRECTORY_SEPARATOR . 'views'. DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . '{:layout}.{:type}.php',
				MINERVA_APP_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_missing' . DIRECTORY_SEPARATOR . 'missing_layout.{:type}.php'
			);
			
			// If a template was specified, we need to set the paths and our renderer becomes File.
			if(!empty($options['template'])) { 
				$viewOptions['renderer'] = 'File'; // Should be by default, but ensure it is.
				$viewOptions['library'] = $options['library']; // The defaults will set this to null or 'static', but each library can have its own
				$viewOptions['paths'] = array(
			        //'template' => '{:library}/views/'.$options['folder'].'/{:template}.{:type}.php',
				'template' => $template_path,
			        //'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
				'layout' => $layout_path
			    );			    
			}
			// var_dump($viewOptions['paths']); // <-- helpful info
			
			// If a URL was specified, then we definitely don't want to use the File renderer, we want to use the Curl adapter.
			// Also ensure that both a url and template weren't specified, if so, use the template File renderer (above).
			if((!empty($options['url'])) && (empty($options['template']))) {
				$viewOptions['renderer'] = 'Curl'; 
			}
			
			// Instantiate a View class instance with the options we'll need for the renderer, paths, etc.
			$view = new View($viewOptions);
			
			return $view->render('all', array('content' => 'this is not used'), $options);
		}
		
		/** 
		 *  However, we can also set the method to ajax, meaning JavaScript is written to the page instaed and the user gets  
		 *  the data. This won't allow us to cache the block content, but it also allows the rest of the page to load first 
		 *  instead of waiting on the block content to load before continuing on to other parts of the page. 
		 *  
		 *  NOTE: You can't call remote hosts due to security restrictions. So this may not be the method for everyone.
		 *  But if multiple http get requests to the web server isn't particularly a problem, this could make for a very
		 *  fast loading, nice user experience, while at the same time being a very easy way to load content into your block area.
		 */ 
		if($options['method'] == 'ajax') {	
			
			// Find the URL to load() with JavaScript 
			//if(!empty($options['requestAction'])) { 
				
			//}
			
			// TODO: add a spinner graphic, use a $.ajax() and make the success remove the spinner
			// jQuery should be included in the layout already in noConflict mode			
			$ajax_code = '<script type="text/javascript">jQuery(document).ready(function($) {';					
			$ajax_code .= '$(\'#result\').load(\''.$options['url'].'\');'; // NOTE: this can be used to load an external URL			
			$ajax_code .= '});</script>';
			
			return $ajax_code;

		}
		
	}
	
	/**
	 *  requestAction() is a shortcut method to pulling back return data from any controller's method.
	 *  Normally, you'd have to manually instantiate the class, call the method, and pass arguments...
	 *  Which really isn't a big deal, but this is a convience to that. It also let's you pass a conveient library option.
	 *
	 *  @param $options array[required] This is your basic controller/action url in array format, you can also pass 'library'
	 *  @return Mixed data from the controller's method to use in your view template
	*/
	public function requestAction($options=array()) {
		$defaults = array('library' => null, 'args' => null);
		$options += $defaults;
		
		if((!isset($options['controller'])) || (!isset($options['action']))) {
			return false;
		}
		
		$controller_name = Inflector::camelize($options['controller']);
		if(empty($options['library'])) {
			$class = '\minerva\controllers\\'.$controller_name.'Controller';
		} else {
			$class = '\minerva\libraries\\'.$options['library'].'\controllers\\'.$controller_name.'Controller'; 			
		}
		$controller = new $class();		
		
		return $controller->{$options['action']}($options['args']);		
	}
	
	/**
	 *  request() is simply a shortcut method to the shortcut method of pulling back data from the block controller
	 *  In this case...We're talking about getting a dynamic block from the database.
	 *  While the requestAction() can call any controller/action, request() just calls the blocks controller's read method.
	 *  This leaves just one simple argument to be passed, the "URL" of the block.
	*/	
	public function request($url=null) {
		if(!$url) {
			return false;
		}
		return $this->requestAction(array('controller' => 'blocks', 'action' => 'read', 'args' => $url));		
	}
	
}
?>