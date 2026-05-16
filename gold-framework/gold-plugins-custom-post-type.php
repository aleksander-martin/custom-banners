<?php 
/* This file is part of A Gold Plugin

Gold Plugins are free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

A Gold Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with a Gold Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!class_exists("GoldPlugins_CustomPostType")):
class GoldPlugins_CustomPostType
{
	public $customFields = false;
	public $customPostTypeName = 'custompost';
	public $customPostTypeSingular = 'customPost';
	public $customPostTypePlural = 'customPosts';
	public $prefix = '_ikcf_';
	public $customPostTypeArgs = array();
	
	public function setupCustomPostType($postType)
	{
		$singular = ucwords($postType['name']);
		$plural = isset($postType['plural']) ? ucwords($postType['plural']) : $singular . 's';

		$this->customPostTypeName = strtolower($singular);
		$this->customPostTypeSingular = $singular;
		$this->customPostTypePlural = $plural;

		if ($this->customPostTypeName != 'post' && $this->customPostTypeName != 'page')
		{		
			$labels = array
			(
				'name' => _x($plural, 'post type general name'),
				'singular_name' => _x($singular, 'post type singular name'),
				'add_new' => _x('Add New ' . $singular, strtolower($singular)),
				'add_new_item' => __('Add New ' . $singular),
				'edit_item' => __('Edit ' . $singular),
				'new_item' => __('New ' . $singular),
				'view_item' => __('View ' . $singular),
				'search_items' => __('Search ' . $plural),
				'not_found' =>  __('No ' . strtolower($plural) . ' found'),
				'not_found_in_trash' => __('No ' . strtolower($plural) . ' found in Trash'), 
				'parent_item_colon' => ''
			);
			
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true, 
				'query_var' => true,
				'rewrite' => array( 'slug' => $postType['slug'], 'with_front' => (strlen($postType['slug'])>0) ? false : true),
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => array('title','editor','author','thumbnail','excerpt','comments','custom-fields'),
				'menu_icon' => 'dashicons-images-alt', 
			); 
			$this->customPostTypeArgs = $args;
	
			// FIX: removed &$this
			add_action( 'init', array( $this, 'registerPostTypes' ), 0 );
		}
	}

	public function registerPostTypes()
	{
	  register_post_type($this->customPostTypeName,$this->customPostTypeArgs);
	}
	
	public function setupCustomFields($fields)
	{
		$this->customFields = array();
		foreach ($fields as $f)
		{
			$this->customFields[] = array
			(
				"name"			=> $f['name'],
				"title"			=> $f['title'],
				"description"	=> isset($f['description']) ? $f['description'] : '',
				"type"			=> isset($f['type']) ? $f['type'] : "text",
				"scope"			=>	array( $this->customPostTypeName ),
				"capability"	=> "edit_posts"
			);
		}
		// FIX: removed &$this
		add_action( 'admin_menu', array( $this, 'createCustomFields' ) );
		add_action( 'save_post', array( $this, 'saveCustomFields' ), 1, 2 );
	}
		
	public function createCustomFields() 
	{
		if ( function_exists( 'add_meta_box' ) ) 
		{
            // FIX: removed &$this
			add_meta_box( 'my-custom-fields'.md5(serialize($this->customFields)), $this->customPostTypeSingular . ' Information', array( $this, 'displayCustomFields' ), $this->customPostTypeName, 'normal', 'high' );
		}
	}

    public function get_metabox_id()
    {
        return 'my-custom-fields'.md5(serialize($this->customFields));
    }
}
endif;
?>
